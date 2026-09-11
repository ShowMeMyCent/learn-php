<!-- .slide: id="part-5" -->
<p class="part-label">Part 5 · Session + PRG <span class="badge badge-core">Core</span></p>

# Post/Redirect/Get

## Menyelesaikan masalah double-submit

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita selesaikan penyakit double-submit yang baru saja kita temukan. Solusinya adalah jurus klasik di web development bernama **Post/Redirect/Get (PRG)**."

**🎯 Poin Kunci di Layar:**
- PRG adalah pola standar industri untuk menangani mutasi data form.



<p class="part-label">Part 5 · Session + PRG</p>

## CONCEPT — kenapa refresh = submit ulang

<div class="flow-v">
<span>POST /create.php (data form)</span>
<span class="arrow">&darr;</span>
<span>Server: INSERT, lalu balas HTML "Berhasil!"</span>
<span class="arrow">&darr;</span>
<span>Browser MENGINGAT: request TERAKHIR = POST ini</span>
<span class="arrow">&darr;</span>
<span>Tekan F5 &rarr; browser kirim ULANG POST yang sama</span>
</div>

Note: **🗣️ Ngomong ke Peserta:**
"Kenapa bisa datanya dobel pas di-refresh? Sederhana: browser itu selalu mengingat request TERAKHIR yang dia kirim. Kalau setelah INSERT kita langsung bales pakai teks HTML, browser bakal nginget bahwa dia baru saja ngirim paket POST. Pas user pencet F5, browser ngirim ulang paket POST itu ke server. Server kita yang polos ya nge-INSERT data itu lagi."

**🎯 Poin Kunci di Layar:**
- Tekankan: Ini bukan error kode kita, tapi cara kerja bawaan browser terhadap request POST.



<p class="part-label">Part 5 · Session + PRG</p>

## Solusi: Post &rarr; Redirect &rarr; Get

<div class="imgph">
<b>Image placeholder</b>
Diagram urutan PRG: kotak "POST" panah ke "Server proses (INSERT)" panah ke "302 Redirect" panah ke "Browser GET halaman baru" panah ke "200 OK tampilkan hasil".<br>
<i>Cari: "Post Redirect Get pattern diagram sequence"</i>
</div>

```php [1-2|4]
$stmt->execute([...]);   // INSERT selesai

header('Location: index.php');
exit;
```

Note: **🗣️ Ngomong ke Peserta:**
"Solusinya pakai jurus 3 langkah: Post -> Redirect -> Get.
Begitu data selesai di-INSERT, kita JANGAN balas pakai HTML. Kita suruh browser pindah halaman pakai perintah `header('Location: index.php')` lalu pasang `exit;`.
Browser bakal langsung otomatis melakukan request baru dengan method GET ke `index.php`. Sekarang, request terakhir yang diingat browser adalah GET, bukan POST. Kalau user refresh 1000 kali pun, datanya gak bakal pernah dobel lagi!"

**⚠️ Catatan Penting:**
- Tunjuk baris `exit;`: ingatkan bahwa `header()` gak otomatis menghentikan PHP. Wajib pasang `exit;` biar kode di bawahnya gak lanjut jalan diam-diam.



<p class="part-label">Part 5 · Session + PRG</p>

## Tapi bagaimana user tahu "berhasil"?

<div class="ask"><b>Masalah baru:</b> kalau langsung redirect ke <code>index.php</code>, dari mana user tahu datanya BENAR-BENAR tersimpan?</div>

```php
session_start();               // WAJIB di baris paling atas, sebelum output apa pun

$_SESSION['flash'] = 'Data siswa berhasil ditambahkan.';
```

<p class="fineprint"><code>$_SESSION</code> &mdash; associative array yang BERTAHAN antar-request. Berbeda dari variable PHP biasa.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Nah, tapi muncul masalah baru: kalau kita langsung redirect, user taunya dari mana kalau datanya beneran sukses tersimpan? Padahal PHP itu pelupa kan? Variabel biasa langsung hilang begitu redirect.
Di sinilah kita panggil **Session**. `$_SESSION` itu brankas penyimpanan sementara di server yang bisa nyebrang antar-halaman. Kita titipkan pesan sukses di `$_SESSION['flash']` sebelum redirect."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 1: `session_start()` wajib ditaruh paling atas sebelum ada output HTML atau spasi kosong apapun.



<p class="part-label">Part 5 · Session + PRG</p>

## Flash message: tampil sekali, lalu hilang

```php [1-2|4-7]
<?php
session_start();
?>

<?php if (isset($_SESSION['flash'])): ?>
    <p class="alert"><?= $_SESSION['flash'] ?></p>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
```

<p class="filename">Ditaruh di bagian atas index.php, sebelum tabel data.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Kenapa dinamai 'Flash Message'? Karena kayak kilatan blitz kamera: muncul sekali, terus hilang selamanya. Di file `index.php`, kita cek: ada gak titipan pesan di session? Kalau ada, kita tampilkan di layar, terus langsung kita hapus pakai `unset($_SESSION['flash'])`. Begitu user refresh atau pindah halaman, pesannya sudah lenyap."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 6 lalu baris 7: urutannya wajib ditampilkan dulu, baru dihapus.



<p class="part-label">Part 5 · Session + PRG</p>

## SEE IT RUN

<div class="flow">
<span class="on">POST create.php</span><span class="arrow">&rarr;</span>
<span class="on">INSERT + set flash</span><span class="arrow">&rarr;</span>
<span class="on">redirect</span><span class="arrow">&rarr;</span>
<span class="on">GET index.php</span><span class="arrow">&rarr;</span>
<span class="on">tampil pesan</span>
</div>

<div class="mock-browser">
<div class="bar">localhost:8001/index.php</div>
<div class="body">
<p style="margin:0 0 0.6em;color:#4ade80">&#10003; Data siswa berhasil ditambahkan.</p>
<table><tr><th>Nama</th><th>Email</th><th>Jurusan</th></tr><tr><td>Guntur Wibowo</td><td>guntur.w@campus.ac.id</td><td>Teknik Komputer</td></tr></table>
</div>
</div>

<p class="fineprint">Coba refresh sekarang &mdash; pesan hilang, TIDAK ADA data dobel.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita coba tes: isi form di `create.php`, submit -> langsung kelempar ke `index.php` dengan pesan hijau 'Data siswa berhasil ditambahkan'. Sekarang coba tekan refresh F5 berkali-kali: pesannya hilang, dan datanya tetap 1, gak dobel sama sekali! Masalah terselesaikan dengan bersih."

**🎯 Poin Kunci di Layar:**
- Tunjukkan alur 5 langkah di atas diagram.



<p class="part-label">Part 5 · Session + PRG</p>

## Recap: pola untuk SETIAP aksi mutasi

<div class="checkpoint">
Setelah <b>INSERT / UPDATE / DELETE</b> berhasil:<br>
1. Set <code>$_SESSION['flash']</code><br>
2. <code>header('Location: ...'); exit;</code><br>
3. Halaman tujuan menampilkan &amp; menghapus flash
</div>

<p class="fineprint">Pola ini akan dipakai LAGI persis sama di Part 7 (UPDATE) dan Part 8 (DELETE).</p>

<p class="downhint">5 slide tambahan: cookie session, kenapa exit wajib, helper function flash(), session lifetime, login/session-gated CRUD (opsional)</p>

Note: **🗣️ Ngomong ke Peserta:**
"Ingat 3 langkah sakti ini: set flash -> redirect + exit -> tampilkan dan unset. Pola ini bakal kita pakai persis sama pas ngedit data (UPDATE) dan pas ngehapus data (DELETE) nanti. Sekarang, kita masuk ke babak paling ditunggu-tunggu: membobol web sendiri lewat SQL Injection di Part 6!"

**🎯 Transisi ke Part 6 (Klimaks Webinar):**
- Langsung tekan panah kanan ke Part 6.


<p class="part-label">Part 5 · Self-Study</p>

## Bagaimana session "mengingat" browser tertentu

<div class="imgph">
<b>Image placeholder</b>
Diagram: server menyimpan session data di file (session_id: xyz123 -> data), browser menyimpan cookie berisi session_id yang sama, dikirim di setiap request berikutnya.<br>
<i>Cari: "PHP session cookie mechanism diagram server side storage"</i>
</div>

Note: `session_start()` melakukan dua hal: (1) kalau browser belum punya session id, PHP membuat satu (angka/string acak panjang) dan mengirimkannya ke browser lewat cookie (biasanya bernama `PHPSESSID`); (2) kalau browser SUDAH mengirim cookie itu di request ini, PHP mencari data session yang cocok dengan id tersebut di server (biasanya disimpan sebagai file sementara).

Jadi `$_SESSION` sebenarnya TIDAK disimpan di browser — hanya ID-nya yang disimpan di cookie browser. Data sesungguhnya tetap di server. Ini penting untuk keamanan: user tidak bisa langsung mengedit isi `$_SESSION` mereka sendiri lewat DevTools browser, karena datanya tidak ada di sana.


<p class="part-label">Part 5 · Self-Study</p>

## Kenapa `exit;` setelah `header()` wajib

```php [1-4]
header('Location: index.php');
// TANPA exit, baris di bawah ini TETAP JALAN:
$pdo->exec("DELETE FROM students WHERE id = 1"); // bahaya!
exit;
```

Note: `header('Location: ...')` HANYA mengirim instruksi ke browser lewat HTTP header — ia TIDAK menghentikan eksekusi PHP di server. Kalau ada kode setelahnya, kode itu tetap berjalan penuh sebelum PHP benar-benar selesai, browser hanya akan pindah halaman SETELAH menerima response lengkap.

Contoh ekstrem di atas sengaja dibuat mencolok: bayangkan ada operasi berbahaya tertulis setelah redirect tanpa `exit` — ia akan tetap tereksekusi meski niatnya "sudah pindah halaman, harusnya berhenti di sini". `exit;` (atau `die;`, identik) memastikan TIDAK ADA baris kode lagi yang berjalan setelah keputusan redirect diambil.


<p class="part-label">Part 5 · Self-Study</p>

## Merapikan: helper function untuk flash

```php [1-8|10-11]
function setFlash(string $pesan): void {
    $_SESSION['flash'] = $pesan;
}

function getFlash(): ?string {
    $pesan = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $pesan;
}

// dipakai di create.php:  setFlash('Data tersimpan.');
// dipakai di index.php:   <?= getFlash() ?>
```

Note: Ini murni REFACTOR dari kode yang sama seperti sebelumnya — dibungkus jadi function supaya tidak perlu menulis ulang `isset()` + `unset()` di SETIAP file yang butuh flash message (`create.php`, `edit.php`, `delete.php` semuanya akan butuh ini di Part 7-8).

`?string` sebagai tipe kembalian berarti "boleh berupa string, ATAU null" — dipakai di sini karena `getFlash()` bisa saja tidak ada pesan sama sekali untuk ditampilkan (kembalikan `null`). Ini pratinjau kecil dari cara PHP menuliskan tipe nullable, tidak perlu didalami sekarang.


<p class="part-label">Part 5 · Self-Study</p>

## Session lifetime &amp; `session_destroy()`

```php
session_start();
$_SESSION = [];        // kosongkan semua data session
session_destroy();     // hapus session sepenuhnya (untuk logout, misalnya)
```

<p class="fineprint">Secara default, session PHP kedaluwarsa saat browser ditutup, atau setelah periode idle tertentu (setting <code>session.gc_maxlifetime</code>).</p>

Note: Tidak dipakai di CORE hari ini (Student Management System kita tidak punya login), tapi WAJIB diketahui kalau nanti mengembangkan fitur login (lihat slide berikutnya tentang login opsional) — `session_destroy()` adalah dasar dari tombol "Logout".

`session.gc_maxlifetime` adalah pengaturan di level server (`php.ini`), bukan sesuatu yang biasanya diatur per-aplikasi kecuali untuk kasus khusus. Cukup tahu bahwa session TIDAK bertahan selamanya — ada batas waktu, dan itu bisa dikonfigurasi.


<p class="part-label">Part 5 · Self-Study</p>

## Opsional: login sederhana + session-gated CRUD

```php [1-6|8-9]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'rahasia') {
        $_SESSION['logged_in'] = true;
        header('Location: index.php'); exit;
    }
}

// di index.php, create.php, edit.php, delete.php:
if (empty($_SESSION['logged_in'])) { header('Location: login.php'); exit; }
```

<div class="ask"><b>Checkpoint:</b> kenapa contoh di atas TIDAK boleh dipakai di aplikasi nyata?</div>

Note: **Jawaban:** password dibandingkan sebagai TEKS POLOS (`===`), padahal semestinya di-hash (`password_hash()` saat registrasi, `password_verify()` saat login) supaya kalau database bocor, password asli tidak langsung terbaca. Username/password juga hardcoded di kode, semestinya dari tabel `users` di database.

Ini murni PRATINJAU konsep session-gated access — ini arah pengembangan yang natural untuk dipelajari mandiri atau capstone: melindungi `create.php`/`edit.php`/`delete.php` supaya hanya bisa diakses setelah login. Detail keamanan password (hashing) SENGAJA di luar cakupan materi hari ini karena fokus utama adalah CRUD, bukan sistem otentikasi lengkap — disebutkan di sini hanya supaya peserta tahu ke mana harus mencari kalau ingin melangkah lebih jauh.
