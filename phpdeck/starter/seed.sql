-- =====================================================================
-- Student Management System — Seed data
--
-- Jalankan SETELAH schema.sql.
--
--   mysql -u root -p < seed.sql
--
-- 6 baris. Angka ini penting: di HANDS-ON #2 kamu memverifikasi bahwa
-- tabel HTML yang kamu render berisi tepat 6 baris. Kalau bukan 6,
-- ada yang salah di query atau koneksimu.
-- =====================================================================

USE student_db;

TRUNCATE TABLE students;

INSERT INTO students (name, email, major) VALUES
    ('Ayu Lestari',      'ayu.lestari@campus.ac.id',   'Informatika'),
    ('Bagas Prakoso',    'bagas.prakoso@campus.ac.id', 'Sistem Informasi'),
    ('Citra Maharani',   'citra.m@campus.ac.id',       'Informatika'),
    ('Dimas Ardiansyah', 'dimas.ardi@campus.ac.id',    'Teknik Komputer'),
    ('Eka Nurhaliza',    'eka.nur@campus.ac.id',       'Sistem Informasi'),
    ('Fajar Ramadhan',   'fajar.r@campus.ac.id',       'Informatika');

-- Verifikasi cepat — harus mengembalikan 6.
SELECT COUNT(*) AS total_seed FROM students;
