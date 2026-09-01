# Capstone Guide — Courses Management System

Panduan latihan mandiri setelah sesi "Dari C ke PHP". Tujuannya BUKAN
menghasilkan aplikasi baru dari nol secara konsep — tujuannya adalah
membuktikan kamu bisa **memindahkan pola yang sudah dipelajari** (dari
`students`) ke entity yang berbeda (`courses`), tanpa menyalin baris demi
baris tanpa paham.

> **Aturan emas:** boleh membuka `phpdeck/project/` sebagai REFERENSI kalau
> benar-benar buntu. Tapi target akhirnya adalah kamu bisa menjelaskan
> SETIAP baris kode `courses/` kamu sendiri, tanpa harus melihat `students/`
> di sampingnya.

## 0. Entity yang dikerjakan

```sql
CREATE TABLE courses (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    code    VARCHAR(20)  NOT NULL,
    credits INT          NOT NULL
);
```

- `name` — nama mata kuliah, contoh: "Basis Data"
- `code` — kode mata kuliah, contoh: "CS301"
- `credits` — jumlah SKS, angka bulat, contoh: 3

Struktur folder yang diharapkan:

```
courses/
├── config/database.php
├── helpers.php
├── style.css
├── courses.sql        (schema + minimal 5 baris seed data)
├── index.php
├── create.php
├── edit.php
└── delete.php
```

---

## Milestone 1 — READ

**Target:** `index.php` menampilkan seluruh isi tabel `courses` dalam
bentuk tabel HTML.

**Acceptance criteria:**
- [ ] `courses.sql` berisi `CREATE TABLE` + minimal 5 baris `INSERT` seed data
- [ ] `config/database.php` berhasil konek ke database (boleh database yang
      sama dengan `student_db`, atau database baru — bebas)
- [ ] `index.php` menampilkan name, code, credits dalam tabel HTML
- [ ] Kalau tabel kosong, tampil pesan "Belum ada data", bukan tabel kosong
      tanpa penjelasan

**Hint bertingkat** (buka hanya kalau stuck > 10 menit):
1. Query apa yang dipakai untuk mengambil SEMUA baris tanpa filter?
2. Bentuk data hasil `fetchAll()` itu array apa? Bagaimana cara mencetak
   tiap barisnya jadi `<tr>`?
3. Masih stuck? Bandingkan dengan `phpdeck/project/index.php` bagian READ
   saja (bukan search-nya) — jangan salin, ketik ulang sambil paham.

---

## Milestone 2 — CREATE

**Target:** Form untuk menambah course baru, tersimpan dengan aman.

**Acceptance criteria:**
- [ ] Form dengan field name, code, credits, method POST
- [ ] Query INSERT memakai `prepare()` + `execute()` dengan named parameter
- [ ] Setelah berhasil: redirect ke `index.php` (PRG), disertai flash message
- [ ] Field `credits` divalidasi sebagai angka (lihat `FILTER_VALIDATE_INT`
      di Part 9 self-study)

**Hint bertingkat:**
1. Apa isi query INSERT-nya? Ada berapa kolom yang perlu diisi?
2. Kenapa `credits` butuh validasi BERBEDA dari `name`/`code`? (petunjuk:
   tipe datanya di schema apa?)
3. Kalau lupa pola PRG, buka kembali `phpdeck/slides/05-session-prg.md`
   — bukan `project/create.php` dulu.

---

## Milestone 3 — UPDATE

**Target:** Form edit course yang sudah ada, terisi data lama, bisa diubah.

**Acceptance criteria:**
- [ ] GET `edit.php?id=N` menampilkan form terisi data course dengan id N
- [ ] POST ke file yang sama memproses `UPDATE ... WHERE id = :id`
- [ ] Kalau `id` tidak ditemukan, redirect ke `index.php` dengan flash
      pesan yang jelas (bukan halaman error putih)
- [ ] Value di form di-escape dengan `htmlspecialchars()`

**Hint bertingkat:**
1. Query apa untuk mengambil SATU baris berdasarkan id?
2. Apa yang WAJIB ada di klausa `WHERE` supaya tidak menimpa SEMUA baris?
3. Bagaimana cara mengecek "id ini tidak ada di database" setelah query
   SELECT dijalankan?

---

## Milestone 4 — DELETE

**Target:** Tombol hapus yang aman (bukan link GET biasa).

**Acceptance criteria:**
- [ ] Tombol hapus berupa `<form method="POST">`, BUKAN `<a href="delete.php?id=...">`
- [ ] `delete.php` menolak (redirect tanpa memproses) kalau diakses selain POST
- [ ] Ada konfirmasi (`confirm()` JavaScript, minimal)
- [ ] Setelah berhasil: PRG + flash message

**Hint bertingkat:**
1. Kenapa link `<a href>` biasa berbahaya untuk aksi hapus? (lihat Part 8)
2. Bagaimana cara mengecek `$_SERVER['REQUEST_METHOD']` dan menolak kalau
   bukan POST?

---

## Milestone 5 — Validasi & Keamanan Menyeluruh

**Target:** Checklist keamanan (Part 6 & 9) berlaku di SEMUA fitur di atas,
bukan hanya sebagian.

**Acceptance criteria (checklist final):**
- [ ] Setiap query dengan input user pakai `prepare()` + `execute()` — cek
      SEMUA file, termasuk kalau kamu menambahkan fitur search
- [ ] Setiap output ke HTML dibungkus `htmlspecialchars()`
- [ ] `name`/`code` wajib diisi, tidak boleh kosong setelah `trim()`
- [ ] `credits` wajib berupa angka bulat positif
- [ ] Create/Update/Delete semuanya lewat POST, diikuti PRG + flash message
- [ ] Form menampilkan error TANPA menghilangkan input yang sudah diketik user

---

## Kalau ingin melangkah lebih jauh (opsional, di luar Definition of Done)

Tidak wajib, tapi kalau capstone dasar sudah selesai dan ingin latihan
tambahan:

- **Search/filter course** berdasarkan nama atau kode — terapkan pola
  prepared statement `LIKE` yang sama seperti `index.php` di
  `phpdeck/project/`.
- **Pagination** dengan `LIMIT`/`OFFSET` (Part 3 self-study) kalau data
  course sudah banyak.
- **Relasi ke students** — buat tabel penghubung `enrollments(student_id,
  course_id)` untuk mencatat siswa mana ambil mata kuliah apa. Ini akan
  memunculkan pertanyaan `ON DELETE CASCADE` vs `RESTRICT` (Part 8
  self-study) — sengaja belum dijawab di materi utama, cari tahu sendiri
  dulu sebelum cek dokumentasi MySQL.
- **Login + session-gated CRUD** (Part 5 self-study) — lindungi
  `create.php`/`edit.php`/`delete.php` supaya hanya bisa diakses setelah
  login. INGAT: jangan simpan password sebagai teks polos, cari tahu
  `password_hash()`/`password_verify()`.

## Cara mengecek pemahaman sendiri (sebelum menganggap selesai)

Coba jelaskan ke teman (atau ke diri sendiri, keras-keras) untuk SETIAP
file yang kamu tulis:

1. "Baris ini ada di tahap mana dari Browser→HTTP→PHP→SQL→MySQL→Response?"
2. "Kalau baris `WHERE id = :id` ini dihapus, apa yang rusak?"
3. "Kalau ada orang mengirim `<script>` sebagai nama course, apa yang
   mencegahnya dieksekusi di browser?"

Kalau semua bisa dijawab tanpa membuka `phpdeck/project/students/` sebagai
contekan — capstone ini selesai, dalam artian yang sesungguhnya.
