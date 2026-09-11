<!-- .slide: id="part-9" -->
<p class="part-label">Part 9 · Validation <span class="badge badge-core">Core</span></p>

# Validation

## Lubang terakhir yang belum ditutup

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita masuk ke Part 9: Validasi Input. CRUD kita memang sudah kebal dari SQL Injection dan XSS, tapi kita belum menolak data aneh: misalnya nama cuma spasi, atau email gak pakai tanda @. Di sinilah validasi server-side berperan."

**🎯 Poin Kunci di Layar:**
- Alur ringkas: fokus pada prinsip server-side validation.



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

Note: **🗣️ Ngomong ke Peserta:**
"Coba saya tanya: di HTML kan sudah ada atribut `required` dan `type=\"email\"`. Kenapa kita masih capek-capek bikin validasi lagi di PHP?
Jawabannya: karena HTML itu jalan di browser user!
User bisa klik kanan -> Inspect Element -> hapus kata `required` dalam 2 detik. Atau mereka bisa nembak data langsung pakai Postman tanpa buka form kita sama sekali.
Atribut di HTML itu cuma buat kenyamanan user (UX). Tapi satpam sesungguhnya yang menentukan data boleh masuk ke database atau tidak adalah **PHP di sisi server**."

**🎯 Poin Kunci di Layar:**
- Tunjuk diagram bypass: client-side bisa dimatikan, server-side adalah benteng mutlak.



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

Note: **🗣️ Ngomong ke Peserta:**
"Polanya gampang banget:
1. Kita siapkan array kosong `$errors = []`.
2. Setiap kali ada input yang salah atau kosong, kita tambahkan pesan ke array itu: `$errors[] = 'Nama wajib diisi'`.
3. Terakhir, kita cek pakai `if (empty($errors))`. Kalau array error-nya beneran kosong, baru jalankan INSERT! Kalau masih ada isinya, jangan sentuh database sama sekali."

**🎯 Poin Kunci di Layar:**
- Tunjuk `$errors[]`: kumpulkan semua error sekaligus, jangan matikan program di error pertama agar user tahu semua kesalahannya.



<p class="part-label">Part 9 · Validation</p>

## Required, trim, panjang

```php [1-2|4-5|7-8]
$name = trim($_POST['name'] ?? '');
if ($name === '') $errors[] = 'Nama wajib diisi.';

if (strlen($name) > 100) $errors[] = 'Nama maksimal 100 karakter.';
// cocok dengan VARCHAR(100) di schema.sql
```

Note: **🗣️ Ngomong ke Peserta:**
"Jangan lupa pasang fungsi `trim()`. Kenapa? Karena kalau ada user iseng yang cuma ngetik spasi 5 kali, tanpa `trim()` spasi itu dianggap ada isinya! `trim()` membuang spasi kosong di depan dan belakang.
Selain itu, kita batasi panjang karakter maksimal 100 huruf. Kenapa angka 100? Karena di tabel database MySQL tadi, kolom `name` kita set `VARCHAR(100)`. Jadi aturan di PHP harus sinkron dengan aturan di database."

**🎯 Poin Kunci di Layar:**
- Tunjuk `trim()`: cegah input iseng berisi spasi doang.
- Tunjuk `VARCHAR(100)`: sinkronkan batas string dengan skema DB.



<p class="part-label">Part 9 · Validation</p>

## Format email

```php [1-2|4-6]
$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}
```

<p class="fineprint"><code>filter_var()</code> &mdash; function bawaan PHP untuk validasi/sanitasi berbagai jenis data.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Buat ngecek format email, kalian gak perlu bikin rumus Regex aneh-aneh yang bikin pusing. PHP sudah menyediakan fungsi bawaan: `filter_var($email, FILTER_VALIDATE_EMAIL)`.
Fungsi ini otomatis ngecek apakah formatnya beneran email standar yang ada tanda @ dan nama domainnya."

**🎯 Poin Kunci di Layar:**
- Tunjuk `FILTER_VALIDATE_EMAIL`: alat standar bawaan PHP.



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

Note: **🗣️ Ngomong ke Peserta:**
"Ada 2 hal penting di slide ini:
Pertama: kalau ada error, kita loop array `$errors` di atas form biar user bisa baca apa saja yang salah.
Kedua (ini penting banget buat kenyamanan user): di tag `<input>`, atribut `value` kita isi ulang dengan `$_POST['name']`. Tujuannya apa? Supaya kalau user salah ketik email, nama dan jurusan yang sudah dia ketik panjang-panjang gak hilang terhapus! Jangan bikin user emosi karena harus ngetik ulang semuanya dari awal."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 8: old input retention (`value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"`).



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

Note: **🗣️ Ngomong ke Peserta:**
"Ini rangkuman Trio Pertahanan Backend:
1. Di lapisan Query: pakai `prepare()` + `execute()` biar aman dari SQL Injection.
2. Di lapisan Output: bungkus `htmlspecialchars()` biar aman dari XSS.
3. Di lapisan Input: pakai `$errors[]` dan `filter_var()` biar data bersih dan masuk akal.
Tiga pilar ini yang wajib kalian terapkan di Capstone Project kita sekarang!"

**🎯 Transisi ke Part 10 (Capstone):**
- Tekan panah kanan ke Part 10.


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
