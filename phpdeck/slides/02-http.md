<!-- .slide: id="part-2" -->
<p class="part-label">Part 2 · PHP, HTML &amp; HTTP <span class="badge badge-core">Core</span></p>

# Dari Form ke PHP

## Bagaimana data benar-benar berpindah

Note: Ini bagian paling fundamental dari seluruh sesi. Sebelum bicara database sama sekali, peserta harus paham: bagaimana caranya sebuah isian di form HTML bisa "sampai" ke variable PHP?

Banyak peserta pemula membayangkan form "langsung terhubung" ke PHP, semacam sihir. Tujuan Part ini adalah membongkar sihir itu jadi mekanisme konkret: HTTP request.



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## CONCEPT — program C kamu vs PHP

<div class="cmp">
<div class="cmp-c">
<h4>Program C</h4>

```text
$ ./program
Masukkan nama: _
```

Input datang dari **keyboard**,
langsung ke `scanf()`.
Satu proses, satu user.

</div>
<div class="cmp-php">
<h4>Program PHP</h4>

```text
Browser --HTTP Request--> Server
Server  --HTTP Response-> Browser
```

Input datang lewat **HTTP request**
yang dikirim browser.
Server bisa melayani ribuan user.

</div>
</div>

Note: Tarik paralel eksplisit: `scanf()` di C menunggu input dari terminal yang sama. PHP tidak pernah "menunggu" seperti itu — PHP hanya membaca apa yang SUDAH dikirim browser di dalam HTTP request, memprosesnya, lalu selesai. Tidak ada dialog interaktif seperti CLI.

Ini juga jawaban kenapa satu server PHP bisa melayani banyak orang sekaligus: setiap request independen, tidak ada proses yang "menunggu" user tertentu mengetik.



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## GET vs POST

<div class="imgph">
<b>Image placeholder</b>
Diagram membandingkan GET (data di URL, terlihat di address bar, contoh <code>?id=5</code>) vs POST (data di body request, tidak terlihat di URL).<br>
<i>Cari: "HTTP GET vs POST request diagram"</i>
</div>

<table class="plain">
<tr><th></th><th>GET</th><th>POST</th></tr>
<tr><td>Data di</td><td>URL (query string)</td><td>body request</td></tr>
<tr><td>Terlihat di address bar?</td><td>Ya</td><td>Tidak</td></tr>
<tr><td>Dipakai untuk</td><td>membaca / filter / search</td><td>kirim / ubah / hapus data</td></tr>
</table>

Note: Aturan praktis yang akan mereka pakai terus sepanjang sesi: **GET untuk membaca data, POST untuk mengubah data (create/update/delete)**. Ini bukan aturan mutlak dari sisi teknis — secara teknis form apapun BISA pakai GET — tapi ini konvensi yang penting untuk keamanan dan perilaku browser yang benar (nanti dijelaskan kenapa, di Part 5 dan Part 8: delete lewat GET itu berbahaya).

Contoh konkret: mengetik kata kunci pencarian → GET (`index.php?q=budi`), karena boleh di-bookmark, boleh di-refresh berkali-kali tanpa efek samping. Menyimpan data siswa baru → POST, karena refresh setelah POST akan mengirim ulang data (masalah yang akan mereka lihat sendiri sebentar lagi di HANDS-ON #1, dan solusinya di Part 5).



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## Form HTML: `name` menjadi kunci

```html [1|2-3|4]
<form method="POST" action="proses.php">
    <input type="text" name="nama">
    <input type="text" name="jurusan">
    <button type="submit">Kirim</button>
</form>
```

<div class="ask"><b>Perhatikan:</b> atribut <code>name="nama"</code> BUKAN sekadar label. Itu akan jadi KUNCI array di sisi PHP.</div>

Note: Ini titik sambung paling penting antara HTML yang sudah mereka kuasai dan PHP yang baru. Atribut `name` pada `<input>` sudah mereka tulis berkali-kali sebelumnya (mungkin tanpa tahu fungsinya kalau belum pernah bikin backend) — sekarang jelaskan: nilai atribut inilah yang akan jadi KUNCI di associative array `$_POST` atau `$_GET`.

Tulis di papan/slide: `name="nama"` di HTML → `$_POST['nama']` di PHP. Kalau atributnya `name="jurusan"`, maka PHP-nya `$_POST['jurusan']`. Ini koneksi langsung ke slide associative array di Part 1 — sekarang mereka lihat DARI MANA array itu berasal.

`action="proses.php"` menentukan file PHP mana yang akan menerima request ini. Kalau kosong, form mengirim ke dirinya sendiri (dipakai nanti di HANDS-ON #1).



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## PHP menerima: `$_POST`

```php [1|2-3]
<?php
$nama = $_POST['nama'];
$jurusan = $_POST['jurusan'];
?>

<p>Halo, <?= $nama ?> dari <?= $jurusan ?></p>
```

<p class="fineprint"><code>$_POST</code> hanya terisi kalau form dikirim dengan <code>method="POST"</code>. <code>$_GET</code> untuk <code>method="GET"</code> atau parameter di URL.</p>

Note: Tekankan lagi: `$_POST` adalah associative array biasa, PERSIS seperti yang dibahas di Part 1. Tidak ada sintaks baru untuk dipelajari di sini — hanya nama variable baru yang sudah otomatis diisi PHP.

Ingatkan potensi error umum: kalau mencoba `$_POST['nama']` padahal form dikirim dengan `method="GET"`, akan muncul "Undefined array key" karena datanya ada di `$_GET`, bukan `$_POST`. Ini persis error yang mungkin akan mereka temui sendiri di HANDS-ON #1 — jangan buru-buru dijawab sekarang, biarkan mereka menemukannya sendiri nanti agar lebih nempel.



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## Guard: pastikan ini benar-benar POST

```php [1|2-4|6]
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    // ... proses data
}
// kalau bukan POST (mis. baru buka halaman), lewati blok ini
```

<p class="fineprint">Ingat <code>===</code> dari Part 1? Di sinilah gunanya.</p>

Note: Kenapa guard ini penting: satu file PHP yang sama sering menampilkan FORM (saat pertama dibuka, request GET) dan MEMPROSES form itu (saat disubmit, request POST). Tanpa guard ini, PHP akan mencoba membaca `$_POST['nama']` bahkan saat halaman baru dibuka dan belum ada data dikirim — hasilnya error "Undefined array key nama".

`$_SERVER['REQUEST_METHOD']` adalah salah satu isi dari superglobal `$_SERVER` yang disebut sekilas di Part 1 — sekarang mereka lihat kegunaan konkretnya: mengetahui APA JENIS request yang sedang diproses.

Pola if-guard ini akan muncul lagi persis sama di `create.php` dan `edit.php` nanti.



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## <span class="badge badge-hands">Ketik bareng #1</span> Form &rarr; `$_POST`

Buat `hello_form.php`, lengkapi TODO:

```php [1-2|4-8]
<!-- Form sudah tersedia, lengkapi bagian PHP -->
<form method="POST">
    <input type="text" name="nama" placeholder="Nama kamu">
    <input type="text" name="jurusan" placeholder="Jurusan">
    <button type="submit">Kirim</button>
</form>

<?php
// TODO: cek jika method POST, ambil $_POST['nama'] & $_POST['jurusan']
// TODO: echo "Halo, <nama>! Jurusan <jurusan>."
// TODO: var_dump($_POST) di baris terakhir
?>
```

<p class="fineprint">~8 menit. Target: isi form &rarr; submit &rarr; muncul sapaan + var_dump.</p>

Note: Sebelum peserta mulai, tulis ketiga TODO di papan sebagai checklist. Jangan didikte baris demi baris — biarkan mereka menyusun sendiri dari pola yang sudah dibahas 3 slide sebelumnya (if guard + `$_POST['key']` + echo).

Selama 8 menit ini, keliling ruangan. Kesalahan paling umum yang akan muncul: (1) lupa `method="POST"` di form sehingga tetap GET, (2) attribute `name` di HTML tidak cocok dengan key yang dipakai di `$_POST[...]`, (3) lupa if-guard sehingga error saat halaman pertama dibuka.

Jangan perbaiki langsung — tanyakan "coba var_dump($_POST), isinya apa?" supaya mereka belajar mendiagnosis sendiri lewat alat yang baru saja dipelajari.



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## Checkpoint #1

<div class="mock-browser">
<div class="bar">localhost:8001/hello_form.php</div>
<div class="body">
<p style="margin:0 0 0.6em">Halo, Ayu! Jurusan Informatika.</p>
<pre style="margin:0;font-size:0.9em">array(2) {
  ["nama"]=&gt;
  string(3) "Ayu"
  ["jurusan"]=&gt;
  string(11) "Informatika"
}</pre>
</div>
</div>

<div class="checkpoint"><b>Berhasil kalau:</b> sapaan muncul DAN <code>var_dump</code> menunjukkan array dengan key <code>nama</code> &amp; <code>jurusan</code> berisi teks yang kamu ketik.</div>

Note: Minta 2-3 peserta secara acak menunjukkan layar mereka lewat share screen atau maju ke depan sebentar — bukan untuk menilai, tapi supaya seluruh kelas kalibrasi "seperti inilah tampilan sukses".

Kalau ada yang array-nya kosong (`array(0) {}`), itu tandanya `REQUEST_METHOD` bukan POST — cek atribut `method` di tag form. Ini failure mode paling umum, siapkan jawaban ini di kepala sebelum sesi dimulai.

Setelah debrief singkat (60 detik), langsung sambungkan ke slide berikutnya: "Data sudah sampai ke PHP. Sekarang — bagaimana caranya data ini masuk ke DATABASE, dan bagaimana data yang SUDAH ADA di database ditampilkan?"



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## Recap &amp; jembatan

<div class="flow">
<span class="on">Browser</span><span class="arrow">&rarr;</span>
<span class="on">HTTP Request</span><span class="arrow">&rarr;</span>
<span class="on">PHP</span><span class="arrow">&rarr;</span>
<span>Validation</span><span class="arrow">&rarr;</span>
<span>SQL</span><span class="arrow">&rarr;</span>
<span>MySQL</span>
</div>

<p class="fineprint">Tiga panah pertama sudah kita kuasai. Sisanya: Part 3 dan seterusnya.</p>

<p class="downhint">6 slide tambahan: $_SERVER, GET-search vs POST-mutasi, isset/??, redirect dasar, status code, trace 1 form utuh</p>

Note: Sorot diagram ini dan tunjukkan secara eksplisit bahwa mereka baru saja menguasai tiga simpul pertama dari mental model yang ditunjukkan di Opening. Ini penting secara psikologis — peserta perlu tahu progres mereka, bukan hanya "materi berikutnya".

Kalau waktu berjalan sesuai jadwal (menit ke-40), lanjut langsung ke Part 3. Kalau tertinggal, ini titik pemangkasan paling aman: percepat slide "GET vs POST" dan "recap" tanpa mengurangi hands-on.


<p class="part-label">Part 2 · Self-Study</p>

## Isi `$_SERVER` yang berguna

```php
$_SERVER['REQUEST_METHOD']  // "GET" atau "POST"
$_SERVER['REQUEST_URI']     // "/index.php?q=budi"
$_SERVER['HTTP_HOST']       // "localhost:8001"
$_SERVER['SCRIPT_NAME']     // "/index.php"
$_SERVER['REMOTE_ADDR']     // IP pengirim request
```

Note: `$_SERVER` adalah array besar berisi puluhan informasi tentang request dan environment server. Tidak perlu dihafal semua — yang paling sering dipakai di aplikasi CRUD sederhana hanyalah `REQUEST_METHOD` (untuk guard, sudah dibahas) dan kadang `REQUEST_URI` (untuk mengetahui halaman/parameter saat ini).

`REMOTE_ADDR` kadang dipakai untuk logging kasar ("siapa yang mengakses ini"), tapi CATATAN PENTING untuk keamanan: nilai ini bisa dipalsukan lewat header tertentu di beberapa konfigurasi proxy, jadi jangan pernah dipakai sebagai satu-satunya mekanisme keamanan/otentikasi.


<p class="part-label">Part 2 · Self-Study</p>

## Kapan GET, kapan POST — aturan main sebenarnya

```text
GET  /index.php?q=budi         -> mencari, boleh di-refresh, boleh di-bookmark
POST /create.php                -> menyimpan siswa baru
POST /delete.php  (BUKAN GET!)  -> menghapus siswa
```

<div class="ask"><b>Checkpoint:</b> Kenapa fitur SEARCH sebaiknya pakai GET, padahal itu juga "mengirim data" dari user?</div>

Note: **Jawaban:** GET dianggap "safe method" dalam standar HTTP — artinya diasumsikan tidak mengubah apa pun di server, hanya membaca. Karena itu browser boleh melakukan hal-hal yang TIDAK aman dilakukan untuk POST: menyimpan di history, mengizinkan bookmark, mem-prefetch link, mengizinkan refresh tanpa peringatan.

Kalau fitur DELETE ditaruh di balik link `<a href="delete.php?id=5">` (yang otomatis jadi GET), maka: bookmark manager, crawler search engine, atau bahkan browser yang mem-prefetch link bisa TIDAK SENGAJA memicu penghapusan data. Ini akan dibahas lebih detail dengan contoh konkret di Part 8 (DELETE) — simpan alasan ini untuk nanti.


<p class="part-label">Part 2 · Self-Study</p>

## isset() dan null coalescing `??`

```php [1-3|5-6]
if (isset($_GET['q'])) {
    $q = $_GET['q'];
} else {
    $q = '';
}

$q = $_GET['q'] ?? '';   // baris di atas, dipersingkat
```

Note: Pola "kalau key ada, pakai; kalau tidak, pakai default kosong" SANGAT sering dipakai untuk parameter opsional seperti kata kunci pencarian (`$_GET['q']` mungkin tidak ada sama sekali kalau user belum mengisi form search).

Operator `??` (null coalescing) adalah singkatan yang akan sering muncul di kode CRUD nanti, termasuk di demo SQL Injection Part 6: `$q = $_GET['q'] ?? '';`. Kalau dieja panjang seperti baris 1-3, kodenya jadi bertele-tele untuk sesuatu yang sangat umum dilakukan.

Bedakan dari `empty()` yang dibahas di Part 1: `isset()` hanya cek "ada/tidak ada key", tidak peduli isinya kosong atau tidak. `$_GET['q'] = ""` akan lolos `isset()` (true) tapi dianggap kosong oleh `empty()`.


<p class="part-label">Part 2 · Self-Study</p>

## Sekilas: redirect dengan `header()`

```php [1-2|4]
<?php
header('Location: index.php');
exit; // WAJIB, hentikan eksekusi setelah redirect
```

<p class="fineprint">Dibahas penuh dengan konteksnya di Part 5 (Post/Redirect/Get).</p>

Note: Hanya pratinjau syntax. `header()` mengirim HTTP header mentah ke browser — `Location` adalah salah satu jenis header yang memberi tahu browser "pergi ke URL lain". Browser akan otomatis melakukan request BARU ke URL tersebut.

Aturan yang sering dilupakan pemula: `header()` HARUS dipanggil SEBELUM ada output apa pun (echo, HTML, bahkan spasi/baris kosong di luar tag PHP di awal file) — kalau tidak, akan muncul error "headers already sent". `exit;` sesudahnya penting supaya kode di bawahnya tidak ikut jalan meski browser sudah diberi tahu untuk pindah halaman.

Ini murni pengenalan istilah. Konteks LENGKAP kenapa kita butuh redirect (masalah double-submit) baru masuk akal setelah melihat CREATE di Part 4 — jangan dijelaskan detail di sini.


<p class="part-label">Part 2 · Self-Study</p>

## Status code, sekilas

```text
200 OK                    -> berhasil, ini responsnya
302 Found                 -> redirect, pergi ke URL lain
404 Not Found              -> halaman/resource tidak ada
500 Internal Server Error  -> error di kode PHP kamu
```

<p class="fineprint">Buka DevTools browser (F12) &rarr; tab Network &rarr; lihat kolom Status untuk tiap request.</p>

Note: Tidak perlu dihafal semua kode status HTTP (ada puluhan) — empat ini sudah menutup 90% kasus yang akan mereka temui selama sesi ini dan seterusnya. `500` khususnya akan sering muncul saat koneksi database gagal di Part 3 — kenali kode ini sebagai sinyal "ada error di kode PHP-mu, cek log atau aktifkan display_errors".

Kalau ada waktu luang, tunjukkan tab Network di DevTools secara langsung: buka `hello_form.php`, submit form, lihat request POST muncul dengan status 200, klik untuk lihat "Payload"/body request — ini cara paling nyata melihat HTTP request yang selama ini abstrak.


<p class="part-label">Part 2 · Self-Study</p>

## Trace satu roundtrip HTTP utuh

```text
1. User buka hello_form.php     -> GET  /hello_form.php
2. Server balas HTML form        -> 200 OK
3. User isi form, klik Kirim     -> POST /hello_form.php  (body: nama=Ayu&jurusan=Informatika)
4. PHP baca $_POST, proses       -> menyusun HTML baru
5. Server balas HTML hasil        -> 200 OK
6. Browser render "Halo, Ayu!"
```

<div class="ask"><b>Checkpoint:</b> Di langkah mana PHP benar-benar "berjalan"? Apakah PHP ikut berjalan di langkah 6?</div>

Note: **Jawaban:** PHP berjalan di server, HANYA di langkah 2 dan 4-5 (saat menyusun response). Langkah 6 murni terjadi di browser — merender HTML yang sudah jadi teks biasa, PHP sama sekali tidak terlibat dan bahkan sudah "mati" (proses selesai) sejak response terkirim di langkah 5.

Gunakan trace ini sebagai rangkuman visual penutup Part 2. Kalau peserta bisa mengurutkan enam langkah ini sendiri untuk skenario lain (misalnya submit form CREATE nanti), berarti model mental HTTP sudah tertanam dengan baik — inilah fondasi yang membuat Part 3 dan seterusnya jauh lebih mudah dicerna.
