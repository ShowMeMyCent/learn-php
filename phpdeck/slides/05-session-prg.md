<!-- .slide: id="part-5" -->
<p class="part-label">Part 5 · Session + PRG <span class="badge badge-core">Core</span></p>

# Post/Redirect/Get

## Menyelesaikan masalah double-submit

Note: Part ini langsung melanjutkan masalah yang BARU SAJA ditemukan sendiri oleh peserta di akhir Part 4 (refresh setelah submit = data dobel). Momentumnya penting — jangan beri jeda panjang, langsung sambungkan.

Part ini relatif singkat (~22 menit) karena masalahnya sudah dipahami dari pengalaman langsung, bukan dijelaskan dari nol.



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

Note: Ini penjelasan MENGAPA di balik apa yang mereka alami sendiri di akhir Part 4. Browser, secara desain, "mengingat" request terakhir untuk keperluan refresh — untuk GET ini tidak masalah (request GET seharusnya tidak mengubah apa pun, sudah dibahas di Part 2). Untuk POST, mengulang request berarti mengulang AKSI-nya juga — dalam kasus ini, INSERT lagi.

Ini bukan bug PHP, bukan bug MySQL, bukan bug kode kita. Ini adalah perilaku standar HTTP/browser yang harus kita TANGANI secara sengaja di sisi server.



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

Note: Pola PRG: setelah POST selesai memproses (INSERT/UPDATE/DELETE), server TIDAK langsung mengirim HTML hasil — server mengirim instruksi REDIRECT ke browser ("pergi ke `index.php`"), dan browser melakukan request BARU dengan method GET ke sana.

Sekarang kalau user menekan refresh, yang diulang adalah GET terakhir (menampilkan `index.php`) — bukan POST ke `create.php`. Refresh jadi aman, tidak ada lagi data dobel. Ini "sihir" sederhana: browser hanya mengingat request TERAKHIR, jadi kita pastikan request terakhir yang tercatat selalu berupa GET yang aman diulang.

`exit;` setelah `header()` sudah disinggung di Part 2 self-study — sekarang jelaskan alasannya lebih konkret: tanpa `exit;`, kode PHP setelahnya (kalau ada) akan TETAP berjalan di server meski browser sudah diberi tahu untuk pindah, berpotensi menyebabkan efek samping ganda yang tidak terlihat.



<p class="part-label">Part 5 · Session + PRG</p>

## Tapi bagaimana user tahu "berhasil"?

<div class="ask"><b>Masalah baru:</b> kalau langsung redirect ke <code>index.php</code>, dari mana user tahu datanya BENAR-BENAR tersimpan?</div>

```php
session_start();               // WAJIB di baris paling atas, sebelum output apa pun

$_SESSION['flash'] = 'Data siswa berhasil ditambahkan.';
```

<p class="fineprint"><code>$_SESSION</code> &mdash; associative array yang BERTAHAN antar-request. Berbeda dari variable PHP biasa.</p>

Note: Ingat model mental Part 1: setiap request PHP "lahir baru", variable biasa tidak bertahan ke request berikutnya. `$_SESSION` adalah PENGECUALIAN yang disengaja — PHP menyimpan datanya di server (biasanya file sementara) dan mengaitkannya ke browser tertentu lewat cookie, sehingga data ini BISA dibaca lagi di request selanjutnya, termasuk setelah redirect.

`session_start()` WAJIB dipanggil di SETIAP file yang ingin membaca atau menulis `$_SESSION`, dan harus di baris PALING ATAS sebelum ada output HTML apa pun (aturan yang sama seperti `header()` di Part 2) — kalau tidak, akan muncul error "headers already sent" atau session tidak berfungsi.

`'flash'` adalah nama kunci yang kita pilih sendiri, bukan kata kunci bawaan PHP — istilah "flash message" berarti pesan yang tampil SEKALI lalu hilang, akan diimplementasikan di slide berikutnya.



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

Note: Pola ini disebut "flash message" karena tampil SEKALI seperti kilatan (flash), lalu otomatis hilang di kunjungan berikutnya — `unset()` menghapus key `'flash'` dari `$_SESSION` SEGERA setelah ditampilkan, supaya kalau user refresh halaman `index.php` ini, pesan tidak muncul lagi berulang-ulang.

Ini gabungan `isset()` (Part 2 self-study) dengan konsep session yang baru dipelajari — pola yang sudah familiar, sumber data yang baru.

Tekankan urutannya: TAMPILKAN dulu (baris 6), baru HAPUS (baris 7) — kalau dibalik, pesan tidak akan pernah terlihat sama sekali.



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

Note: Demokan langsung dari awal: buka `create.php`, isi form, submit → otomatis pindah ke `index.php` dengan pesan sukses di atas tabel. LALU tekan refresh — buktikan pesan hilang (karena `unset`) dan yang lebih penting, TIDAK ADA baris data baru muncul (karena request terakhir sekarang GET, bukan POST).

Ini adalah momen "aha" untuk Part 5 — masalah yang mereka temukan sendiri di akhir Part 4 sekarang benar-benar terselesaikan, dan mereka bisa MEMBUKTIKANNYA sendiri lewat refresh.



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

Note: Tekankan bahwa pola tiga langkah ini bukan hanya untuk CREATE — ini adalah pola UNIVERSAL untuk setiap operasi yang MENGUBAH data. Saat Part 7 dan 8 nanti, peserta akan melihat pola yang PERSIS sama, hanya query SQL-nya yang berbeda (UPDATE, lalu DELETE).

Jembatan ke Part 6: "Sekarang CREATE kita sudah utuh dan aman dari masalah double-submit. Tapi ada satu lubang keamanan yang BELUM kita bahas — dan ini yang akan kita coba bobol sendiri di Part berikutnya."


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

Ini murni PRATINJAU konsep session-gated access — kalau ada waktu ekstra di luar 150 menit CORE, ini arah pengembangan yang natural untuk capstone: melindungi `create.php`/`edit.php`/`delete.php` supaya hanya bisa diakses setelah login. Detail keamanan password (hashing) SENGAJA di luar cakupan materi hari ini karena fokus utama adalah CRUD, bukan sistem otentikasi lengkap — disebutkan di sini hanya supaya peserta tahu ke mana harus mencari kalau ingin melangkah lebih jauh.
