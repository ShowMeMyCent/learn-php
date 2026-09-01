<?php
/**
 * =====================================================================
 * !! FILE DEMO KEAMANAN — SENGAJA DIBUAT RENTAN !!
 * =====================================================================
 *
 * JANGAN PERNAH menyalin pola di file ini ke aplikasi nyata.
 *
 * Dipakai HANYA untuk demo SQL Injection di Part 6 (Security Checkpoint).
 * Jalankan HANYA di server lokal, database lokal/throwaway milik sendiri.
 * JANGAN pernah di-deploy ke server yang bisa diakses publik/internet.
 *
 * Bandingkan dengan search_fixed.php di folder yang sama -- itulah
 * yang harus dipakai di aplikasi sungguhan (dan memang itulah pola
 * yang sudah dipakai di index.php pada folder project utama).
 * =====================================================================
 */

require __DIR__ . '/../config/database.php';

$q = $_GET['q'] ?? '';

// RENTAN: $q disambung LANGSUNG ke string SQL, tanpa prepared statement.
// Coba input:  %' OR '1'='1
$sql = "SELECT id, name, email, major FROM students WHERE name LIKE '%$q%'";
$students = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>[DEMO RENTAN] Search</title>
    <style>body{font-family:monospace;max-width:700px;margin:2rem auto;padding:0 1rem}
    .warn{background:#fee2e2;border:2px solid #f87171;padding:0.8rem 1rem;margin-bottom:1rem}
    table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:0.4rem 0.6rem;text-align:left}</style>
</head>
<body>

<div class="warn">
    &#9888; DEMO KEAMANAN — versi SENGAJA RENTAN. Jangan dipakai di luar sesi belajar ini.
</div>

<h1>Cari siswa (versi rentan)</h1>

<form method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" style="width:60%">
    <button type="submit">Cari</button>
</form>

<p>Query yang dijalankan:</p>
<pre><?= htmlspecialchars($sql) ?></pre>

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
