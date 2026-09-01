<?php
/**
 * Koneksi database — ditulis LIVE bersama peserta di Part 3.
 *
 * File ini adalah versi REFERENSI untuk dibagikan SETELAH sesi, kalau
 * ada peserta yang tertinggal atau ingin mencocokkan hasil ketikan
 * mereka sendiri. Selama sesi, file ini ditulis dari nol di layar.
 *
 * Di-require dari SETIAP file lain yang butuh akses database:
 *   require __DIR__ . '/config/database.php';
 *
 * Sesudah require, variable $pdo tersedia untuk dipakai.
 */

// Mode belajar: tampilkan error langsung di browser.
// JANGAN pernah aktif seperti ini di server production.
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host    = '127.0.0.1'; // bukan "localhost" -- hindari masalah socket di Windows
$db      = 'student_db';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$user = 'root';
$pass = ''; // sesuaikan dengan setup lokalmu (XAMPP default: kosong)

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
