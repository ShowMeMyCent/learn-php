<?php
/**
 * Helper kecil yang dipakai bersama di beberapa file — dibahas di
 * Part 5 self-study (flash message) dan dipakai berulang di
 * create.php / edit.php / delete.php / index.php.
 *
 * require setelah session_start() dan config/database.php.
 */

/**
 * Simpan pesan flash sebelum redirect. Dipanggil SEBELUM header('Location: ...').
 */
function setFlash(string $pesan): void
{
    $_SESSION['flash'] = $pesan;
}

/**
 * Ambil pesan flash (kalau ada) dan langsung hapus dari session,
 * supaya tidak tampil lagi di kunjungan berikutnya.
 */
function getFlash(): ?string
{
    $pesan = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $pesan;
}

/**
 * Singkatan untuk htmlspecialchars() dengan opsi yang konsisten.
 * Dipakai untuk SETIAP data yang dicetak ke HTML (Part 6).
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
