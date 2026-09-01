# Student Management System — Reference Project

Ini adalah implementasi REFERENSI/SOLUSI lengkap untuk materi "Dari C ke PHP".
Dibagikan ke peserta **setelah** sesi 150 menit selesai — selama sesi,
`config/database.php` ditulis live dan `index.php` ditulis sebagian bersama
peserta (lihat HANDS-ON #2 di `phpdeck/slides/03-pdo-read.md`).

## Setup

1. Jalankan XAMPP (atau server MySQL lokal lain) + PHP.
2. Import `phpdeck/starter/schema.sql`, lalu `phpdeck/starter/seed.sql`
   (lewat phpMyAdmin atau `mysql -u root -p < schema.sql`).
3. Sesuaikan kredensial di `config/database.php` kalau setup lokalmu
   berbeda dari default XAMPP (`root`, tanpa password).
4. Jalankan folder ini dengan PHP built-in server:
   ```
   php -S localhost:8001 -t phpdeck/project
   ```
   atau taruh folder ini di `htdocs` XAMPP dan akses lewat Apache.
5. Buka `http://localhost:8001/index.php`.

**Catatan:** ini SERVER TERPISAH dari deck reveal.js (yang jalan di
`npm start`, port 8000). Keduanya harus menyala bersamaan saat sesi.

## Struktur

```
project/
├── config/database.php          koneksi PDO (ditulis live di Part 3)
├── helpers.php                  setFlash()/getFlash()/e() (Part 5 & 6)
├── style.css                    styling minimal
├── index.php                    READ + search aman (Part 3, 6)
├── create.php                   CREATE + validasi + PRG (Part 4, 5, 9)
├── edit.php                     UPDATE (Part 7)
├── delete.php                   DELETE, wajib POST (Part 8)
├── handson/hello_form.php       solusi HANDS-ON #1 (Part 2)
└── demo-vulnerable/             HANYA untuk demo Part 6 — lihat peringatan di dalamnya
    ├── search_vulnerable.php    SENGAJA rentan SQL Injection
    └── search_fixed.php         versi perbaikannya, untuk perbandingan
```

## Keamanan yang diterapkan (Part 6 & 9)

- Semua query dengan input user pakai `prepare()` + `execute()` — tidak ada
  string SQL yang disambung langsung dari `$_GET`/`$_POST`.
- Semua output ke HTML dibungkus `e()` (alias `htmlspecialchars`).
- `create.php`/`edit.php`/`delete.php` menolak selain method yang sesuai
  dan melakukan validasi server-side sebelum menyentuh database.
- `delete.php` menolak request selain POST.

`demo-vulnerable/` sengaja berisi kebalikannya (kode yang SALAH) — dipakai
satu kali untuk demo langsung, lalu jangan dibuka lagi di luar konteks itu.
