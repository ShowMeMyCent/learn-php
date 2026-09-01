-- =====================================================================
-- Student Management System — Schema
-- Materi PHP Backend
--
-- Cara pakai (pilih salah satu):
--
--   A. phpMyAdmin
--      1. Buka http://localhost/phpmyadmin
--      2. Tab "Import" -> pilih file ini -> Go
--
--   B. Terminal / CMD
--      mysql -u root -p < schema.sql
--
-- Jalankan schema.sql DULU, baru seed.sql.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS student_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE student_db;

DROP TABLE IF EXISTS students;

CREATE TABLE students (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    major VARCHAR(80)  NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Catatan untuk peserta:
--
-- utf8mb4 dipakai (bukan utf8) supaya karakter di luar Basic Multilingual
-- Plane — emoji, sebagian aksara — tersimpan utuh. "utf8" di MySQL
-- sebenarnya hanya menampung 3 byte per karakter, bukan 4.
--
-- email SENGAJA belum diberi UNIQUE. Nanti di Part 9 (Validation) kita
-- bahas: validasi di PHP saja tidak cukup, constraint di database adalah
-- lapisan pertahanan terakhir. Menambahkan UNIQUE adalah salah satu
-- latihan di sana.
