<?php
/**
 * index.php — READ (Part 3) + fitur search AMAN (Part 6) + flash message (Part 5).
 *
 * Alur file ini:
 *   1. session_start() + koneksi + helper
 *   2. baca kata kunci pencarian dari $_GET['q'] (boleh kosong)
 *   3. SELECT dengan prepared statement (AMAN, lihat catatan di bawah)
 *   4. render tabel HTML dengan foreach + escape output
 */

session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/helpers.php';

// --- Search (GET, karena ini operasi BACA, bukan ubah data — Part 2) ---
$q = trim($_GET['q'] ?? '');

// AMAN: prepared statement, meski $q berasal dari input user (Part 6).
// Untuk perbandingan dengan versi RENTAN yang sengaja dibuat untuk demo
// keamanan, lihat demo-vulnerable/search_vulnerable.php — JANGAN pernah
// menyalin pola di file itu ke aplikasi nyata.
if ($q !== '') {
    $stmt = $pdo->prepare(
        "SELECT id, name, email, major FROM students WHERE name LIKE :q ORDER BY name"
    );
    $stmt->execute(['q' => '%' . $q . '%']);
} else {
    $stmt = $pdo->query("SELECT id, name, email, major FROM students ORDER BY name");
}

$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Student Management System</h1>

<?php if ($pesan = getFlash()): ?>
    <p class="flash"><?= e($pesan) ?></p>
<?php endif; ?>

<p class="top-nav"><a href="create.php">+ Tambah Siswa</a></p>

<form method="GET" action="index.php" class="search-box">
    <input type="text" name="q" placeholder="Cari nama siswa..." value="<?= e($q) ?>">
    <button type="submit">Cari</button>
    <?php if ($q !== ''): ?><a href="index.php">Reset</a><?php endif; ?>
</form>

<?php if (empty($students)): ?>
    <p>Belum ada data siswa<?= $q !== '' ? ' yang cocok dengan pencarian.' : '.' ?></p>
<?php else: ?>
    <table>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Jurusan</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?= e($s['name']) ?></td>
                <td><?= e($s['email']) ?></td>
                <td><?= e($s['major']) ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?= (int) $s['id'] ?>">Edit</a>
                    <form class="inline" method="POST" action="delete.php?id=<?= (int) $s['id'] ?>"
                          onsubmit="return confirm('Yakin hapus data ini?')">
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
