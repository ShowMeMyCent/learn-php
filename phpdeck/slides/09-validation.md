<!-- .slide: id="part-9" -->
<p class="part-label">Part 9 · Validation <span class="badge badge-core">Core</span></p>

# Validation

## Lubang terakhir yang belum ditutup

Note: Part ini SENGAJA dipadatkan (sekitar 14 menit) — bukan karena tidak penting, tapi karena polanya cukup mudah dipahami setelah Part 6-8, dan waktu tersisa perlu dijaga untuk briefing capstone yang menentukan hasil belajar mandiri peserta setelah sesi.

Sampaikan di awal: "CRUD kita sudah AMAN dari serangan (Part 6), tapi belum MENOLAK data yang aneh — nama kosong, email tanpa @, dst. Itu beda masalah, dan itu Part ini."



<p class="part-label">Part 9 · Validation</p>

## CONCEPT — client-side bukan pengganti server-side

```html
<input type="email" required>
```

<div class="ask"><b>Pertanyaan jebakan:</b> kalau HTML sudah punya <code>required</code> dan <code>type="email"</code>, kenapa masih perlu validasi di PHP juga?</div>

<div class="imgph">
<b>Image placeholder</b>
Diagram: browser dengan validasi HTML dicoret/dilewati dengan panah langsung ke server, menunjukkan request bisa dikirim tanpa lewat browser sama sekali (mis. lewat curl/Postman).<br>
<i>Cari: "client side vs server side validation bypass diagram"</i>
</div>

Note: **Jawaban:** validasi HTML (`required`, `type="email"`, dsb) berjalan di BROWSER — dan browser sepenuhnya dikendalikan oleh user. Siapa pun bisa mengirim request POST langsung ke `create.php` TANPA lewat form sama sekali — pakai `curl`, Postman, atau bahkan mengubah/menghapus atribut `required` lewat DevTools sebelum submit.

Validasi client-side (HTML/JavaScript) itu berguna — untuk UX, memberi feedback instan tanpa menunggu round-trip ke server. Tapi ia HANYA lapisan kenyamanan, bukan lapisan keamanan. **Validasi yang benar-benar menentukan apakah data boleh masuk database harus selalu berjalan di server**, karena server adalah satu-satunya pihak yang bisa dipercaya mengeksekusi aturan itu tanpa bisa dilewati.



<p class="part-label">Part 9 · Validation</p>

## Pola: kumpulkan error, jangan langsung proses

```php [1-2|4-8|10-13]
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<?php
    $errors = [];
    if (empty($_POST['name']))  $errors[] = 'Nama wajib diisi.';
    if (empty($_POST['email'])) $errors[] = 'Email wajib diisi.';
    if (empty($_POST['major'])) $errors[] = 'Jurusan wajib diisi.';

    if (empty($errors)) {
        // baru lakukan INSERT di sini
    }
?>
<?php endif; ?>
```

Note: Pola intinya: siapkan array `$errors` KOSONG, isi dengan pesan setiap kali aturan dilanggar, lalu SEBELUM melakukan `INSERT`, cek `empty($errors)` — kalau masih ada isinya, JANGAN simpan ke database sama sekali.

Ini alasan kenapa `$errors[]` dikumpulkan SEMUA dulu, bukan langsung `die()` di error pertama yang ditemukan — supaya user bisa melihat SEMUA masalah sekaligus (nama kosong DAN email salah format, misalnya), bukan harus submit berkali-kali menemukan satu error setiap kali.

`empty($_POST['name'])` sudah dibahas di Part 1 self-study — kembali dipakai di sini, penerapan langsung.



<p class="part-label">Part 9 · Validation</p>

## Required, trim, panjang

```php [1-2|4-5|7-8]
$name = trim($_POST['name'] ?? '');
if ($name === '') $errors[] = 'Nama wajib diisi.';

if (strlen($name) > 100) $errors[] = 'Nama maksimal 100 karakter.';
// cocok dengan VARCHAR(100) di schema.sql

if (strlen($name) < 2) $errors[] = 'Nama terlalu pendek.';
```

Note: `trim()` menghapus spasi di awal/akhir SEBELUM validasi — tanpa ini, input yang hanya berisi spasi (`"   "`) akan lolos cek `empty()` (karena string berisi spasi TIDAK dianggap kosong oleh `empty()`), padahal secara makna itu sama saja dengan kosong.

Batas panjang (`strlen($name) > 100`) bukan aturan sembarangan — ini SENGAJA disamakan dengan `VARCHAR(100)` di `schema.sql` dari Part 3. Kalau validasi PHP tidak selaras dengan batas kolom database, data yang lolos validasi PHP tapi melebihi batas kolom akan menyebabkan error database yang membingungkan (atau di beberapa konfigurasi MySQL, data dipotong diam-diam tanpa pemberitahuan) — koordinasi antara validasi aplikasi dan constraint database itu penting.



<p class="part-label">Part 9 · Validation</p>

## Format email

```php [1-2|4-6]
$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}
```

<p class="fineprint"><code>filter_var()</code> &mdash; function bawaan PHP untuk validasi/sanitasi berbagai jenis data.</p>

Note: `filter_var($nilai, FILTER_VALIDATE_EMAIL)` adalah cara PHP bawaan memvalidasi format email — mengembalikan alamat email itu sendiri kalau valid, atau `false` kalau tidak. Jauh lebih andal daripada mencoba menulis sendiri regex untuk validasi email (yang ternyata jauh lebih rumit dari yang terlihat — standar format email punya banyak kasus tepi).

Ini BUKAN validasi bahwa email tersebut BENAR-BENAR ada dan aktif (misalnya `budi@tidakada.xyz` bisa lolos validasi format meski domainnya tidak nyata) — hanya memastikan STRUKTURNYA sesuai format email yang valid (ada `@`, ada domain, dst). Verifikasi bahwa email benar-benar aktif adalah topik lain (biasanya lewat kirim email konfirmasi), di luar cakupan hari ini.



<p class="part-label">Part 9 · Validation</p>

## Tampilkan error + jangan hilangkan yang sudah diketik

```php [1-4|6-9]
<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<input type="text" name="name"
       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
<!-- input diisi ULANG dengan apa yang tadi diketik user -->
```

Note: Dua hal digabung di sini. Pertama, menampilkan daftar error — pola `foreach` yang SAMA seperti mencetak baris tabel di Part 3, hanya sumber datanya array `$errors`, bukan hasil query. Dan `htmlspecialchars($e)` dipakai lagi (Part 6) karena pesan error, meski kita yang menulis teksnya, tetap praktik baik untuk selalu escape output.

Kedua, dan ini yang sering dilewatkan pemula: `value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"` mengisi ULANG field dengan apa yang SEBELUMNYA diketik user, supaya kalau validasi gagal (misalnya email salah format), user TIDAK PERLU mengetik ulang SEMUA field dari awal — hanya perlu memperbaiki yang salah. Ini disebut "old input retention", dan `htmlspecialchars()` di sini WAJIB, persis alasan yang sama seperti value attribute di form edit Part 7 — kalau tidak, input yang mengandung tanda kutip bisa merusak HTML form ini sendiri.



<p class="part-label">Part 9 · Validation</p>

## Trio yang membuat backend "aman dan benar"

<table class="plain">
<tr><th>Lapisan</th><th>Alat</th><th>Melindungi dari</th></tr>
<tr><td>Query</td><td><code>prepare()</code> + <code>execute()</code></td><td>SQL Injection</td></tr>
<tr><td>Output</td><td><code>htmlspecialchars()</code></td><td>XSS</td></tr>
<tr><td>Input</td><td><code>$errors[]</code> + <code>filter_var()</code></td><td>data yang tidak masuk akal</td></tr>
</table>

<div class="checkpoint">Tiga lapisan ini dipakai di SETIAP fitur yang menerima input user &mdash; tanpa kecuali, termasuk di capstone kamu.</div>

<p class="downhint">6 slide tambahan: katalog filter_var, whitelist major, UNIQUE email, error per-field, form reusable, CSRF sekilas</p>

Note: Ini penutup Part 9 sekaligus rangkuman untuk SELURUH sesi keamanan/kualitas data. Ketiga baris tabel ini sebaiknya benar-benar melekat sebagai "tiga pertanyaan wajib" setiap kali menulis fitur backend baru: apakah query ini pakai prepared statement? Apakah output ini di-escape? Apakah input ini divalidasi?

Jembatan langsung ke Part 10: "Sekarang kalian punya semua alat untuk membangun CRUD sendiri, dari nol, tanpa dituntun. Itu ujian sesungguhnya — dan itu capstone kalian."


<p class="part-label">Part 9 · Self-Study</p>

## Katalog `filter_var()` yang berguna

```php
filter_var($email, FILTER_VALIDATE_EMAIL);   // format email
filter_var($umur,  FILTER_VALIDATE_INT);     // harus berupa integer
filter_var($url,   FILTER_VALIDATE_URL);     // format URL
filter_var($ip,    FILTER_VALIDATE_IP);      // format alamat IP

filter_var($teks, FILTER_SANITIZE_SPECIAL_CHARS); // mirip htmlspecialchars
```

Note: `filter_var()` punya banyak mode `FILTER_VALIDATE_*` untuk berbagai jenis data selain email — semuanya mengikuti pola sama: kembalikan nilai yang sudah "dibersihkan"/tervalidasi kalau valid, kembalikan `false` kalau tidak.

`FILTER_VALIDATE_INT` berguna untuk memastikan sebuah input BENAR-BENAR angka bulat sebelum dipakai — misalnya untuk memvalidasi `$_GET['id']` sebelum dipakai di query, sebagai lapisan tambahan di atas prepared statement (bukan pengganti, tapi pelengkap: mencegah nilai yang jelas-jelas tidak masuk akal masuk lebih jauh ke logic aplikasi).

Ada juga `FILTER_SANITIZE_*` yang MEMBERSIHKAN nilai (bukan sekadar validasi ya/tidak) — tapi untuk keperluan escape output ke HTML, `htmlspecialchars()` yang sudah dipelajari di Part 6 tetap pilihan yang lebih eksplisit dan umum dipakai.


<p class="part-label">Part 9 · Self-Study</p>

## Whitelist untuk pilihan terbatas

```php [1-2|4-6]
$jurusanValid = ['Informatika', 'Sistem Informasi', 'Teknik Komputer'];

if (!in_array($_POST['major'], $jurusanValid)) {
    $errors[] = 'Jurusan tidak valid.';
}
```

<p class="fineprint">Lebih baik ditampilkan sebagai <code>&lt;select&gt;</code> di HTML, TAPI tetap divalidasi ulang di server.</p>

Note: Kalau field punya pilihan yang TERBATAS dan sudah diketahui sebelumnya (seperti daftar jurusan), lebih baik ditampilkan sebagai dropdown `<select>` di HTML alih-alih input teks bebas — mengurangi kemungkinan typo dari user secara alami.

TAPI, konsisten dengan prinsip "jangan percaya client-side saja": bahkan kalau HTML-nya `<select>`, tetap validasi ulang di PHP dengan `in_array()` terhadap whitelist yang sama — karena `<select>` di HTML tetap bisa "dilewati" (POST langsung tanpa lewat form, atau memanipulasi value lewat DevTools sebelum submit). Ini penerapan langsung dari konsep whitelist yang sudah dibahas di Part 6 self-study untuk kasus `ORDER BY`.


<p class="part-label">Part 9 · Self-Study</p>

## Mencegah email duplikat: PHP + database, dua lapis

```php [1-4|6]
$stmt = $pdo->prepare("SELECT id FROM students WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) $errors[] = 'Email sudah terdaftar.';

-- Lapis kedua, di schema.sql: ALTER TABLE students ADD UNIQUE (email);
```

Note: Cek duplikat di PHP (baris 1-3) memberi PESAN YANG RAMAH ke user ("Email sudah terdaftar") sebelum mencoba menyimpan. Tapi ini punya celah tipis: kalau ADA dua request yang datang HAMPIR BERSAMAAN dengan email yang sama (kasus jarang tapi mungkin, disebut *race condition*), keduanya bisa lolos cek PHP sebelum salah satu sempat tersimpan.

Karena itu, lapisan KEDUA di level DATABASE (`UNIQUE` constraint pada kolom `email`, disinggung juga di catatan `schema.sql` Part 3) tetap penting sebagai jaring pengaman terakhir — MySQL sendiri yang akan MENOLAK insert kedua meski keduanya lolos cek PHP. Prinsipnya: validasi PHP untuk PESAN YANG RAMAH, constraint database untuk JAMINAN YANG PASTI.


<p class="part-label">Part 9 · Self-Study</p>

## Error per-field vs error ringkasan

```php [1-3|5-6]
// Ringkasan: satu daftar di atas form (dipakai di CORE)
$errors = ['Nama wajib diisi.', 'Email tidak valid.'];

// Per-field: array asosiatif, error nempel di field masing-masing
$errors = ['name' => 'Nama wajib diisi.', 'email' => 'Email tidak valid.'];
// lalu: <?= $errors['name'] ?? '' ?> ditampilkan tepat di bawah input nama
```

Note: Pendekatan "ringkasan" (satu daftar `<ul>` di atas form, dipakai di CORE) lebih sederhana untuk diimplementasikan dan cukup untuk form pendek seperti Student Management System.

Pendekatan "per-field" (error ditampilkan TEPAT di bawah input yang bermasalah) memberi UX yang lebih baik untuk form yang lebih panjang, karena user tidak perlu mencocokkan sendiri pesan error mana untuk field mana. Butuh struktur `$errors` sebagai associative array (kunci = nama field) alih-alih array berurutan biasa — penerapan lanjutan dari associative array yang dipelajari di Part 1.


<p class="part-label">Part 9 · Self-Study</p>

## Satu form untuk create DAN edit

```php [1-4|6-9]
<?php
// Data lama untuk mengisi form: kosong (create) atau dari SELECT (edit)
$data = $student ?? ['name' => '', 'email' => '', 'major' => ''];
?>

<input type="text" name="name"
       value="<?= htmlspecialchars($_POST['name'] ?? $data['name']) ?>">
<!-- prioritas: input yang baru diketik ($_POST), lalu data lama ($data) -->
```

Note: Perhatikan bahwa `create.php` dan `edit.php` sebenarnya berbagi HAMPIR SEMUA struktur form yang sama — field input, aturan validasi, cara menampilkan error. Salah satu arah refactor yang natural (tapi TIDAK wajib untuk capstone) adalah menyatukan keduanya jadi satu file/template `form.php` yang di-`include` dari `create.php` dan `edit.php`, dengan variable `$data` yang berbeda sumbernya.

Ini murni pratinjau ide DRY (Don't Repeat Yourself) — konsep yang sudah pasti familiar dari pemrograman C (menghindari duplikasi function). Tidak perlu diimplementasikan hari ini; menulis `create.php` dan `edit.php` secara terpisah (seperti di CORE) sudah cukup untuk tujuan belajar dan lebih mudah ditelusuri untuk pemula.


<p class="part-label">Part 9 · Self-Study</p>

## Sekilas: CSRF (di luar cakupan hari ini)

```text
CSRF = situs LAIN menipu browser korban untuk mengirim request
       (yang sudah login) ke aplikasi kita, TANPA korban sadar.

Contoh: <img src="https://aplikasikita.com/delete.php?id=5">
        di halaman situs jahat -- kalau delete.php bisa dipicu GET,
        ini otomatis terpicu saat korban membuka situs jahat itu.
```

<p class="fineprint">Salah satu alasan LAIN kenapa operasi ubah/hapus data harus lewat POST, bukan GET.</p>

Note: CSRF (Cross-Site Request Forgery) adalah kelas ancaman yang BERBEDA dari SQL Injection dan XSS yang sudah dibahas di Part 6 — di sini, request yang dikirim itu SENDIRI "sah" secara teknis (datang dari browser yang sudah login), tapi TIDAK diinginkan oleh user, karena dipicu oleh situs lain tanpa sepengetahuan mereka.

Contoh di atas menunjukkan KENAPA aturan "operasi ubah/hapus wajib POST" (Part 2 dan Part 8) bukan hanya soal semantik HTTP — kalau `delete.php` bisa dipicu lewat GET, cukup dengan MENAMPILKAN gambar dari URL tersebut di halaman lain (tanpa perlu diklik sama sekali), request itu otomatis terkirim oleh browser korban.

Pertahanan penuh terhadap CSRF (biasanya lewat "CSRF token" — nilai acak unik per-sesi yang disisipkan di form dan diverifikasi server) DI LUAR CAKUPAN materi hari ini — disebutkan di sini murni supaya istilahnya tidak asing kalau ditemui nanti, dan sebagai penguat argumen kenapa aturan GET/POST yang sudah dipelajari itu penting.
