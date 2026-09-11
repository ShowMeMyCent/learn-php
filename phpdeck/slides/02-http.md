<!-- .slide: id="part-2" -->
<p class="part-label">Part 2 · PHP, HTML &amp; HTTP <span class="badge badge-core">Core</span></p>

# Dari Form ke PHP

## Bagaimana data benar-benar berpindah

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita masuk ke bagian paling seru: bagaimana isian form di HTML bisa nyebrang dan sampai ke variabel PHP? Banyak orang mengira form dan PHP itu otomatis nyambung karena sihir. Padahal jalurnya murni lewat HTTP request."

**🎯 Poin Kunci di Layar:**
- Fokus ke perpindahan data dari browser (klien) ke server.



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

Note: **🗣️ Ngomong ke Peserta:**
"Di C kemarin, program kalian nungguin ketikan keyboard lewat `scanf()`. Di PHP gak ada `scanf()`. Server PHP gak pernah nungguin user ngetik. Dia cuma ngebaca data yang sudah terlanjur dikirim oleh browser lewat paket HTTP Request, lalu diproses secepat mungkin, dibalas, dan selesai. Makanya satu server PHP bisa melayani ribuan orang sekaligus tanpa macet."

**🎯 Poin Kunci di Layar:**
- Bandingkan: `scanf` nunggu di terminal vs HTTP request jalan independen.



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

Note: **🗣️ Ngomong ke Peserta:**
"Bedanya GET dan POST apa? Sederhana: GET naruh data di URL, kelihatan di address bar. Bagus buat fitur pencarian atau filter karena halamannya bisa di-bookmark atau dibagikan ke teman. Tapi kalau aksinya mengubah data—seperti nambah mahasiswa, ngedit, atau hapus—wajib pakai POST! Di POST, datanya dibungkus di badan request, gak kelihatan di URL, dan gak bakal dipicu sembarangan oleh browser."

**🎯 Poin Kunci di Layar:**
- Tunjuk aturan praktis di baris terakhir: GET untuk baca, POST untuk ubah/hapus.



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

Note: **🗣️ Ngomong ke Peserta:**
"Di HTML kalian pasti sudah sering nulis `<input type=\"text\" name=\"nama\">`. Nah, atribut `name` ini bukan cuma label pajangan. Nilai di dalam `name` inilah yang bakal jadi KUNCI kurung siku di PHP nanti. Kalau di HTML namanya `name=\"nama\"`, di PHP manggilnya `$_POST['nama']`. Kalau di HTML namanya `name=\"jurusan\"`, di PHP ya `$_POST['jurusan']`. Jangan sampai di HTML pakai bahasa Indo tapi di PHP dipanggil bahasa Inggris."

**🎯 Poin Kunci di Layar:**
- Tunjuk atribut `name="nama"` lalu hubungkan dengan `$_POST['nama']`.



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

Note: **🗣️ Ngomong ke Peserta:**
"Lihat kodenya: `$_POST` itu sebenarnya Associative Array biasa yang sudah otomatis disediain sama PHP. Isinya langsung diambil dari input form. Gak ada sintaks aneh-aneh. Kalau form-nya dikirim pakai method POST, datanya masuk ke `$_POST`. Tapi ingat: kalau di form kalian lupa nulis `method=\"POST\"`, HTML bakal default kirim pakai GET, dan `$_POST` kalian bakal kosong melompong."

**⚠️ Antisipasi Error:**
- Muncul 'Undefined array key': artinya form dikirim pakai GET, atau nama key di array beda huruf kapital/bahasa dari form HTML.



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

Note: **🗣️ Ngomong ke Peserta:**
"Kenapa kita butuh baris `if ($_SERVER['REQUEST_METHOD'] === 'POST')`? Karena saat user baru pertama kali buka halaman web, browser itu ngirim request GET biasa. Belum ada tombol submit yang dipencet! Kalau gak kita pasang satpam (guard) ini, PHP bakal langsung nyari `$_POST['nama']` yang belum ada, dan browser langsung keluar warning error merah. Dengan if ini, PHP cuma jalanin kode pemrosesan pas tombol submit beneran diklik."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 2: ini penjaga gerbang standar di setiap pemrosesan form PHP.



<p class="part-label">Part 2 · PHP, HTML &amp; HTTP</p>

## <span class="badge badge-hands">Live Coding #1</span> Form &rarr; `$_POST`

Struktur `hello_form.php`:

```php [1-2|4-8]
<!-- Form HTML mengirim request POST -->
<form method="POST">
    <input type="text" name="nama" placeholder="Nama kamu">
    <input type="text" name="jurusan" placeholder="Jurusan">
    <button type="submit">Kirim</button>
</form>

<?php
// Pemrosesan di sisi server:
// 1. Cek jika method POST, ambil $_POST['nama'] & $_POST['jurusan']
// 2. Tampilkan sapaan: "Halo, <nama>! Jurusan <jurusan>."
// 3. Inspeksi isi request dengan var_dump($_POST)
?>
```

<p class="fineprint">Live Demo: Pemateri mendemokan pengiriman form &rarr; data masuk ke <code>$_POST</code> &rarr; direspons server.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang perhatikan baik-baik ke layar depan. Saya akan mendemokan Live Coding #1. Kita akan buat satu file bernama `hello_form.php`. Di atasnya ada form HTML dengan method POST, dan di bawahnya kita tulis kode PHP untuk menangkap data yang dikirim user.
Perhatikan baris cek method POST: ini satpam pertama kita. Kalau user submit form, PHP membaca `$_POST['nama']` dan `$_POST['jurusan']`, lalu langsung mencetaknya kembali ke browser."

**🎯 Poin Kunci di Layar:**
- Tunjukkan atribut `method="POST"` di form HTML.
- Tunjukkan bagaimana data input otomatis masuk ke superglobal `$_POST`.



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

<div class="checkpoint"><b>Hasil Live Demo:</b> Sapaan muncul DAN <code>var_dump</code> menunjukkan array dengan key <code>nama</code> &amp; <code>jurusan</code> berisi data yang dikirim dari form.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Bisa kita lihat hasilnya di layar: saat form disubmit dengan nama 'Ayu' dan jurusan 'Informatika', PHP langsung memproses dan menampilkan sapaan serta isi array `var_dump`.
Inilah jembatan pertama kita: form HTML berhasil mengirim data ke memori PHP di sisi server.
Tapi ingat: begitu response HTML ini selesai dikirim ke browser, data di variabel PHP ini langsung hilang dan mati!
Lalu bagaimana caranya agar data ini tidak hilang dan tersimpan selamanya? Kita butuh database! Mari kita masuk ke Part 3: PDO dan MySQL."

**🎯 Transisi ke Database:**
- "Data dari user sudah berhasil masuk ke PHP. Sekarang pertanyaannya: gimana cara nyimpen data ini ke MySQL dan nampilin ulang ke layar? Kita masuk ke Part 3."



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

Note: **🗣️ Ngomong ke Peserta:**
"Lihat diagram ini: 3 panah pertama sudah beres kita taklukkan. Browser ngirim data lewat HTTP, diterima sama PHP. Sekarang kita lanjut ke panah berikutnya: menghubungkan PHP ke database MySQL."

**🎯 Arah Presentasi:**
- Lanjut tekan panah kanan ke Part 3.


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
