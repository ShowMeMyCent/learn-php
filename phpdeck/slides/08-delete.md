<!-- .slide: id="part-8" -->
<p class="part-label">Part 8 · DELETE <span class="badge badge-core">Core</span></p>

# DELETE

## Query terpendek, tapi paling berbahaya

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita masuk ke DELETE: operasi dengan baris kode paling pendek, tapi paling mematikan kalau kita ceroboh. Begitu Part 8 ini selesai, aplikasi CRUD kita sudah 100% lengkap berdiri!"

**🎯 Poin Kunci di Layar:**
- Fokus: Kenapa aksi hapus HARUS pakai POST, bukan link biasa.



<p class="part-label">Part 8 · DELETE</p>

## DELETE = query terpendek

<div class="imgph">
<b>Image placeholder</b>
Diagram siklus CRUD dengan "Delete" disorot/highlight, melengkapi lingkaran Create-Read-Update-Delete.<br>
<i>Cari: "CRUD lifecycle diagram create read update delete"</i>
</div>

```sql
DELETE FROM students WHERE id = :id;
```

<p class="fineprint">Satu baris SQL. Tapi query terpendek bukan berarti paling sederhana untuk dirancang dengan aman.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Query-nya cuma sebaris: `DELETE FROM students WHERE id = :id`. Pendek banget. Tapi karena efeknya permanen dan gak ada tombol Ctrl+Z di database, kita harus sangat hati-hati merancang cara user memicu tombol hapus ini."

**🎯 Poin Kunci di Layar:**
- Ingatkan lagi: jangan sampai ketinggalan `WHERE id = :id`, atau seisi tabel terhapus bersih!



<p class="part-label">Part 8 · DELETE</p>

## Kenapa BUKAN link biasa

<div class="danger">
<span class="label">Jangan pernah</span>

```html
<a href="delete.php?id=5">Hapus</a>
```

</div>

<div class="ask"><b>Ingat dari Part 2:</b> link <code>&lt;a href&gt;</code> selalu memicu request GET. GET seharusnya TIDAK mengubah data apa pun.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Kenapa tombol hapus DILARANG keras pakai link biasa `<a href=\"delete.php?id=5\">`?
Ingat pelajaran Part 2: link `<a>` itu jalurnya GET! Dan jalur GET itu bisa dibuka otomatis sama siapa saja:
1. Web crawler kayak Google Bot bakal ngeklik semua link yang ada di halaman buat diindeks.
2. Fitur prefetch browser modern suka ngebuka link diam-diam di background biar loading lebih cepat.
Kalau tombol hapus kalian pakai link GET, data user bisa terhapus sendiri tanpa ada orang yang beneran ngeklik tombol hapus!"

**🎯 Poin Kunci di Layar:**
- Tunjuk kotak merah: link `<a>` memicu GET, fatal untuk mutasi/hapus data.



<p class="part-label">Part 8 · DELETE</p>

## Solusi: form POST kecil

```html [1-3]
<form method="POST" action="delete.php?id=5">
    <button type="submit">Hapus</button>
</form>
```

```php [1-2|4-7]
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<?php
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $stmt->execute(['id' => $_GET['id']]);
?>
<?php endif; ?>
```

Note: **🗣️ Ngomong ke Peserta:**
"Solusi yang benar: tombol Hapus harus dibungkus tag `<form method=\"POST\">`.
Kalau kalian mau tampilannya mirip link biru biasa, itu urusan CSS. Tapi di balik layar, mekanismenya wajib paket POST.
Di file `delete.php`, kita pasang satpam: kalau ada orang iseng buka file ini lewat URL (GET), langsung tolak mentah-mentah! Dia cuma mau memproses kalau request-nya adalah POST."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 1: `<form method=\"POST\">`.
- Tunjuk baris 68: penjaga gerbang `if ($_SERVER['REQUEST_METHOD'] === 'POST')`.



<p class="part-label">Part 8 · DELETE</p>

## Konfirmasi + PRG

```html [1-3]
<form method="POST" action="delete.php?id=5"
      onsubmit="return confirm('Yakin hapus data ini?')">
    <button type="submit">Hapus</button>
</form>
```

```php [1-2]
setFlash('Data siswa berhasil dihapus.');
header('Location: index.php'); exit;
```

Note: **🗣️ Ngomong ke Peserta:**
"Biar user gak kepeleset jempolnya, kita pasang dialog konfirmasi pakai JavaScript bawaan: `onsubmit=\"return confirm('Yakin hapus data ini?')\"`.
Kalau user klik 'Cancel', form otomatis batal dikirim.
Kalau user klik 'OK', data dihapus, pasang flash message 'Data siswa berhasil dihapus', lalu lempar balik ke `index.php` pakai PRG. Bersih dan aman!"

**🎯 Poin Kunci di Layar:**
- Tunjuk `onsubmit`: proteksi UX agar user tidak sengaja menghapus data.



<p class="part-label">Part 8 · DELETE</p>

## CRUD lengkap

<div class="flow">
<span class="on">Create</span><span class="arrow">&rarr;</span>
<span class="on">Read</span><span class="arrow">&rarr;</span>
<span class="on">Update</span><span class="arrow">&rarr;</span>
<span class="on">Delete</span>
</div>

<div class="checkpoint">
Empat operasi. Satu pola prepared statement. Satu pola PRG+flash. Satu model mental Browser&ndash;HTTP&ndash;PHP&ndash;SQL&ndash;MySQL.
</div>

<p class="downhint">3 slide tambahan: soft delete vs hard delete, detail confirm() JS, foreign key ON DELETE</p>

Note: **🗣️ Ngomong ke Peserta:**
"Beri tepuk tangan dulu buat kita semua! Empat huruf CRUD sekarang sudah lengkap kita buat: Create, Read, Update, Delete.
Perhatikan: semuanya cuma mengulang rumus yang sama:
1. Ambil data: pakai `query()` atau `prepare()` -> `fetch()` -> loop `foreach`.
2. Ubah data: pakai form POST -> `prepare()` + `execute()` -> PRG redirect + flash message.
Tinggal satu langkah terakhir sebelum kalian saya beri tantangan mandiri: Validasi data di Part 9!"

**🎯 Transisi ke Part 9:**
- Tekan panah kanan ke Part 9.


<p class="part-label">Part 8 · Self-Study</p>

## Soft delete vs hard delete

```sql [1-2|4-6]
-- Hard delete (dipakai di CORE hari ini): data BENAR-BENAR hilang
DELETE FROM students WHERE id = :id;

-- Soft delete: data tetap ada, hanya ditandai "terhapus"
-- Butuh kolom tambahan: deleted_at DATETIME NULL
UPDATE students SET deleted_at = NOW() WHERE id = :id;
```

<p class="fineprint">Soft delete berarti SEMUA query SELECT lain juga harus ditambah <code>WHERE deleted_at IS NULL</code>.</p>

Note: Hard delete (yang dipakai di CORE) sederhana dan cukup untuk kebanyakan aplikasi belajar/prototipe — data yang dihapus benar-benar hilang dari tabel.

Soft delete adalah pola alternatif untuk aplikasi yang butuh jejak audit atau kemampuan "undo": alih-alih `DELETE`, baris ditandai dengan kolom seperti `deleted_at` (diisi timestamp saat "dihapus"), dan SEMUA query baca (`index.php`, search, dll) harus ditambah kondisi `WHERE deleted_at IS NULL` supaya data yang "terhapus" tidak lagi muncul di tampilan normal.

Trade-off-nya jelas: soft delete lebih aman dari kesalahan manusia (masih bisa dipulihkan), tapi menambah kompleksitas di SETIAP query baca. Untuk Student Management System sesederhana ini, hard delete sudah tepat — soft delete relevan untuk aplikasi dengan data yang lebih kritikal (misalnya data finansial).


<p class="part-label">Part 8 · Self-Study</p>

## `confirm()` bukan satu-satunya cara

```html
<!-- Alternatif: halaman konfirmasi terpisah, bukan dialog JS -->
<a href="delete-confirm.php?id=5">Hapus</a>
<!-- delete-confirm.php menampilkan detail data + tombol form POST -->
```

<p class="fineprint"><code>confirm()</code> browser sudah cukup untuk aplikasi sederhana &mdash; ini hanya alternatif untuk UX yang lebih kaya.</p>

Note: `confirm()` JavaScript (dipakai di CORE) adalah solusi tercepat dan cukup baik untuk kebanyakan kasus. Alternatifnya: halaman konfirmasi terpisah yang menampilkan detail lengkap data yang akan dihapus (misalnya "Yakin hapus Ayu Lestari, Informatika?") sebelum tombol POST final ditekan — UX yang lebih baik untuk data yang konsekuensinya besar, tapi butuh satu file/langkah tambahan.

Perhatikan bahwa link `<a href="delete-confirm.php?id=5">` DI SINI aman memakai GET, karena halaman ini HANYA menampilkan informasi (READ), belum benar-benar menghapus apa pun — penghapusan sesungguhnya tetap terjadi lewat form POST di halaman konfirmasi tersebut. Ini konsisten dengan aturan GET-untuk-baca, POST-untuk-ubah yang sudah dibahas sejak Part 2.


<p class="part-label">Part 8 · Self-Study</p>

## Foreign key: apa yang terjadi kalau data terkait?

```sql
-- Kalau nanti ada tabel courses yang terhubung ke students:
ALTER TABLE enrollments
    ADD FOREIGN KEY (student_id) REFERENCES students(id)
    ON DELETE CASCADE;   -- atau: ON DELETE RESTRICT
```

<p class="fineprint">Relevan kalau capstone (Part 10) menghubungkan students &amp; courses.</p>

Note: Tabel `students` hari ini berdiri sendiri, jadi menghapus satu baris tidak berdampak ke tabel lain. Tapi begitu ada relasi antar-tabel (misalnya siswa terdaftar di beberapa mata kuliah lewat tabel penghubung `enrollments`), menghapus siswa memunculkan pertanyaan: apa yang terjadi dengan data pendaftarannya?

`ON DELETE CASCADE` berarti MySQL otomatis ikut menghapus baris terkait di tabel lain. `ON DELETE RESTRICT` (atau default tanpa aturan) berarti MySQL akan MENOLAK penghapusan kalau masih ada data terkait — mencegah data "yatim" (`enrollments` yang menunjuk ke `student_id` yang sudah tidak ada).

Ini murni pratinjau konsep untuk pengembangan lebih lanjut — tidak relevan untuk tabel `students` yang berdiri sendiri hari ini, tapi akan relevan kalau capstone dikembangkan menghubungkan `students` dan `courses` (disinggung di Part 10).
