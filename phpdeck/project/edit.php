<?php
/**
 * edit.php — UPDATE (Part 7) = READ (ambil data lama) + CREATE (form + PRG) + WHERE id.
 *
 * GET  ?id=N -> ambil 1 baris, isi form
 * POST ?id=N -> validasi, lalu UPDATE kalau lolos, redirect ke index.php
 */

session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/helpers.php';

$jurusanValid = ['Informatika', 'Sistem Informasi', 'Teknik Komputer'];

$id = $_GET['id'] ?? null;
if ($id === null || !filter_var($id, FILTER_VALIDATE_INT)) {
    setFlash('Data siswa tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('Data siswa tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$errors = [];
// Default: data lama dari database. Kalau validasi POST gagal, dipakai
// nilai yang BARU diketik ($_POST), bukan data lama (Part 9: old input retention).
$name  = $student['name'];
$email = $student['email'];
$major = $student['major'];

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
        // Cek duplikat, TAPI kecualikan baris ini sendiri (id != :id)
        $cek = $pdo->prepare("SELECT id FROM students WHERE email = :email AND id != :id");
        $cek->execute(['email' => $email, 'id' => $id]);
        if ($cek->fetch()) {
            $errors[] = 'Email sudah dipakai siswa lain.';
        }
    }

    if (!in_array($major, $jurusanValid, true)) {
        $errors[] = 'Jurusan tidak valid.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "UPDATE students SET name = :name, email = :email, major = :major WHERE id = :id"
        );
        $stmt->execute([
            'name'  => $name,
            'email' => $email,
            'major' => $major,
            'id'    => $id,
        ]);

        setFlash('Data siswa berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Edit Siswa — Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Edit Siswa</h1>
<p class="top-nav"><a href="index.php">&larr; Kembali ke daftar</a></p>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="edit.php?id=<?= (int) $id ?>">
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
            <?php foreach ($jurusanValid as $opt): ?>
                <option value="<?= e($opt) ?>" <?= $major === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit">Simpan Perubahan</button>
</form>

</body>
</html>
