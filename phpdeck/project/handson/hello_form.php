<?php
/**
 * hello_form.php — referensi/solusi untuk HANDS-ON #1 (Part 2).
 *
 * Selama sesi, peserta mengetik bagian PHP dari nol berdasarkan
 * TODO yang ditampilkan di slide. File ini adalah versi lengkap untuk
 * dicocokkan SETELAH mereka mencoba sendiri -- jangan dibagikan sebelum
 * hands-on selesai.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hands-on #1 — Form ke PHP</title>
</head>
<body>

<form method="POST">
    <input type="text" name="nama" placeholder="Nama kamu">
    <input type="text" name="jurusan" placeholder="Jurusan">
    <button type="submit">Kirim</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <?php
        $nama    = $_POST['nama'];
        $jurusan = $_POST['jurusan'];
    ?>
    <p>Halo, <?= htmlspecialchars($nama) ?>! Jurusan <?= htmlspecialchars($jurusan) ?>.</p>
    <pre><?php var_dump($_POST); ?></pre>
<?php endif; ?>

</body>
</html>
