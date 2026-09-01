<?php
/**
 * create.php — CREATE (Part 4) + PRG/flash (Part 5) + validation (Part 9).
 *
 * GET  -> tampilkan form kosong (atau form terisi ulang kalau validasi gagal)
 * POST -> validasi, lalu INSERT kalau lolos, redirect ke index.php
 */

session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/helpers.php';

$jurusanValid = ['Informatika', 'Sistem Informasi', 'Teknik Komputer'];

$errors = [];
// Nilai default untuk mengisi ulang form kalau validasi gagal (Part 9: old input retention)
$name = $email = $major = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $major = trim($_POST['major'] ?? '');

    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Nama maksimal 100 karakter.';
    }

    if ($email === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    } else {
        // Cek duplikat -- lapisan kedua (UNIQUE constraint) ada di schema.sql
        $cek = $pdo->prepare("SELECT id FROM students WHERE email = :email");
        $cek->execute(['email' => $email]);
        if ($cek->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        }
    }

    if (!in_array($major, $jurusanValid, true)) {
        $errors[] = 'Jurusan tidak valid.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO students (name, email, major) VALUES (:name, :email, :major)"
        );
        $stmt->execute([
            'name'  => $name,
            'email' => $email,
            'major' => $major,
        ]);

        setFlash('Data siswa berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tambah Siswa — Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Tambah Siswa</h1>
<p class="top-nav"><a href="index.php">&larr; Kembali ke daftar</a></p>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="create.php">
    <div class="field">
        <label for="name">Nama</label>
        <input type="text" id="name" name="name" value="<?= e($name) ?>">
    </div>
    <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>">
    </div>
    <div class="field">
        <label for="major">Jurusan</label>
        <select id="major" name="major">
            <option value="">-- Pilih Jurusan --</option>
            <?php foreach ($jurusanValid as $opt): ?>
                <option value="<?= e($opt) ?>" <?= $major === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit">Simpan</button>
</form>

</body>
</html>
