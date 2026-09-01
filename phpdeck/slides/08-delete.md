<!-- .slide: id="part-8" -->
<p class="part-label">Part 8 · DELETE <span class="badge badge-core">Core</span></p>

# DELETE

## Query terpendek, tapi paling berbahaya

Note: Meski ini Part paling singkat dari sisi kode, jangan tergesa-gesa di bagian "kenapa harus POST" — itu adalah pelajaran keamanan/desain yang berdiri sendiri, bukan sekadar detail teknis.

Setelah Part ini selesai, CRUD lengkap sudah berdiri sepenuhnya. Sampaikan itu di awal supaya peserta tahu mereka hampir sampai di garis akhir Core Track.



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

Note: Tunjukkan kontras: dari sisi PANJANG KODE, DELETE adalah yang paling singkat dari semua operasi CRUD — tidak ada banyak field untuk di-`bind`, tidak ada logic kompleks. Tapi justru karena efeknya PERMANEN dan LANGSUNG (data hilang, tidak ada "undo" bawaan), operasi ini butuh perhatian ekstra soal BAGAIMANA cara memicunya — inilah fokus slide berikutnya.



<p class="part-label">Part 8 · DELETE</p>

## Kenapa BUKAN link biasa

<div class="danger">
<span class="label">Jangan pernah</span>

```html
<a href="delete.php?id=5">Hapus</a>
```

</div>

<div class="ask"><b>Ingat dari Part 2:</b> link <code>&lt;a href&gt;</code> selalu memicu request GET. GET seharusnya TIDAK mengubah data apa pun.</div>

Note: Tarik kembali langsung ke slide "GET vs POST" di Part 2 self-study, yang saat itu masih terasa abstrak — sekarang konsekuensinya konkret. Kalau tombol "Hapus" berupa link `<a href="delete.php?id=5">`, itu adalah request GET, dan beberapa hal bisa memicunya TANPA sepengetahuan atau niat user:

Web crawler mesin pencari yang mengindeks halaman bisa mengikuti SEMUA link yang ditemukan, termasuk link hapus. Fitur "prefetch" di sebagian browser bisa memuat link yang terlihat di layar untuk mempercepat navigasi, bahkan tanpa diklik. Browser extension atau bahkan riwayat halaman yang di-refresh bisa memicu ulang GET yang sama.

Tanya balik ke kelas sebelum lanjut: "Kalau tombol hapus HARUS berupa `<a>` (karena alasan styling atau lainnya), bagaimana caranya tetap membuatnya memicu POST, bukan GET?" — biarkan mereka menebak (jawabannya di slide berikutnya: bungkus dengan form).



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

Note: Tombol "Hapus" dibungkus `<form method="POST">` kecil, bukan `<a href>` — secara visual bisa tetap dibuat terlihat seperti link biasa lewat CSS kalau diinginkan, tapi secara MEKANISME tetap mengirim request POST.

Query `DELETE FROM students WHERE id = :id` mengikuti pola yang SAMA PERSIS seperti SELECT/UPDATE sebelumnya: `id` dari `$_GET['id']` (URL), tetap WAJIB lewat `prepare()` karena tetap input user — tidak ada pengecualian untuk DELETE.

Tekankan lagi klausa `WHERE id = :id` — tanpa ini, `DELETE FROM students` akan menghapus SELURUH ISI TABEL. Ini kesalahan yang efeknya jauh lebih fatal dibanding lupa `WHERE` di UPDATE (yang "hanya" menimpa data, DELETE benar-benar menghilangkannya).



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

Note: `onsubmit="return confirm(...)"` adalah JavaScript bawaan browser (`confirm()`) yang menampilkan dialog Ya/Tidak SEBELUM form benar-benar disubmit — kalau user klik "Batal", `confirm()` mengembalikan `false`, dan `return false` dari `onsubmit` MEMBATALKAN pengiriman form.

Ini lapisan proteksi UX tambahan (mencegah klik tidak sengaja), BUKAN lapisan keamanan — sama seperti disable-button di Part 4 self-study, ini bisa dilewati kalau JavaScript dimatikan. Proteksi yang SESUNGGUHNYA tetap ada di sisi server: mewajibkan POST (bukan GET) untuk operasi ini.

PRG + flash sesudahnya identik dengan pola CREATE dan UPDATE — sekali lagi, bukti bahwa satu pola sudah cukup untuk seluruh operasi mutasi data.



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

Note: Ini momen jeda penting — CRUD sudah benar-benar lengkap dan berfungsi end-to-end. Beri waktu sebentar untuk ini terasa, jangan langsung lompat ke Part 9.

Ingatkan sekali lagi: SEMUA empat operasi ini memakai prepared statement TANPA KECUALIAN, dan tiga dari empatnya (Create, Update, Delete) memakai pola PRG+flash yang SAMA PERSIS. Ini bukan kebetulan — ini adalah bukti bahwa memahami POLA lebih penting daripada menghafal SETIAP baris kode secara terpisah.

Jembatan ke Part 9: "CRUD kita berfungsi dan aman dari SQL Injection/XSS. Tapi ada satu lubang lagi: sejauh ini, kita belum benar-benar MENOLAK input yang tidak masuk akal — nama kosong, email tanpa format yang benar. Itu Part 9."


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
