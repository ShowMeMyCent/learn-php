<?php
/**
 * Versi PERBAIKAN dari search_vulnerable.php — dipakai di Part 6 untuk
 * menunjukkan bahwa payload SAMA PERSIS menghasilkan 0 baris setelah
 * direfactor ke prepared statement.
 *
 * Ini bukan file terpisah yang baru -- polanya IDENTIK dengan search
 * yang sudah ada di project/index.php. Diletakkan di sini juga hanya
 * supaya bisa dibandingkan berdampingan dengan search_vulnerable.php
 * saat demo.
 */

require __DIR__ . '/../config/database.php';

$q = trim($_GET['q'] ?? '');

// AMAN: prepared statement, wildcard % ditambahkan sebagai bagian data.
$stmt = $pdo->prepare(
    "SELECT id, name, email, major FROM students WHERE name LIKE :q"
);
$stmt->execute(['q' => '%' . $q . '%']);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>[DEMO DIPERBAIKI] Search</title>
    <style>body{font-family:monospace;max-width:700px;margin:2rem auto;padding:0 1rem}
    .ok{background:#dcfce7;border:2px solid #4ade80;padding:0.8rem 1rem;margin-bottom:1rem}
    table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:0.4rem 0.6rem;text-align:left}</style>
</head>
<body>

<div class="ok">
    &#10003; Versi AMAN — prepared statement. Bandingkan hasilnya dengan search_vulnerable.php untuk payload yang sama.
</div>

<h1>Cari siswa (versi aman)</h1>

<form method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" style="width:60%">
    <button type="submit">Cari</button>
</form>

<p>Hasil: <?= count($students) ?> baris</p>

<table>
    <tr><th>Nama</th><th>Email</th><th>Jurusan</th></tr>
    <?php foreach ($students as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['major']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
