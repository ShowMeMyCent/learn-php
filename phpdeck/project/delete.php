<?php
/**
 * delete.php — DELETE (Part 8).
 *
 * SENGAJA hanya menerima POST (lihat index.php: tombol Hapus dibungkus
 * <form method="POST">, BUKAN <a href="delete.php?id=...">). Kalau file
 * ini diakses lewat GET, tolak dan kembalikan ke daftar tanpa menghapus
 * apa pun -- lihat Part 8: kenapa delete tidak boleh dipicu GET.
 */

session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/helpers.php';

$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Bukan POST -- kemungkinan seseorang mencoba mengakses link ini
    // langsung lewat browser/GET. Jangan proses, arahkan balik saja.
    header('Location: index.php');
    exit;
}

if ($id === null || !filter_var($id, FILTER_VALIDATE_INT)) {
    setFlash('Data siswa tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() > 0) {
    setFlash('Data siswa berhasil dihapus.');
} else {
    setFlash('Data siswa tidak ditemukan (mungkin sudah terhapus sebelumnya).');
}

header('Location: index.php');
exit;
