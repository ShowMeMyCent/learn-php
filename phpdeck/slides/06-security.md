<!-- .slide: id="part-6" data-background="#2a0a0a" -->
<p class="part-label">Part 6 · Security Checkpoint <span class="badge badge-core">Core</span></p>

# Jangan Percaya Input Siapa Pun

## SQL Injection &amp; XSS

Note: Ini bagian yang paling penting diingat dari SELURUH sesi hari ini — lebih penting daripada sintaks CRUD itu sendiri. Sampaikan itu secara eksplisit di awal.

Aturan keamanan sebelum mulai: SEMUA demo di Part ini HANYA dilakukan di database lokal, throwaway, milik masing-masing peserta di laptop sendiri. Tidak pernah dicoba ke aplikasi orang lain, apalagi yang online. Sampaikan ini SEBELUM menunjukkan payload apa pun.



<p class="part-label">Part 6 · Security Checkpoint</p>

## CONCEPT — dua ancaman, satu akar masalah

<div class="imgph">
<b>Image placeholder</b>
Ilustrasi dua panah dari "Input User" — satu menuju kotak "SQL Query" berlabel SQL Injection, satu menuju kotak "HTML Output" berlabel XSS.<br>
<i>Cari: "SQL injection and XSS attack diagram web security basics"</i>
</div>

```text
SQL Injection  ->  input MENJADI bagian dari QUERY (masalah di SQL)
XSS            ->  input MENJADI bagian dari HTML  (masalah di output)
```

Note: Akar masalah keduanya IDENTIK: kode kita memperlakukan input user sebagai sesuatu yang bisa dipercaya begitu saja, lalu menggabungkannya LANGSUNG ke dalam sesuatu yang akan "dieksekusi" — SQL Injection saat input digabung ke query SQL, XSS saat input digabung ke HTML yang browser render.

Prinsip yang akan terus diulang: **input dari luar (form, URL, header, apa pun yang bisa dikontrol orang lain) TIDAK PERNAH dipercaya secara default.** Ini bukan paranoia berlebihan — ini adalah asumsi kerja standar setiap backend developer.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Fitur search yang (sengaja) rentan

<div class="danger">
<span class="label">index.php &mdash; versi rentan, JANGAN dipakai</span>

```php [1|2-3|4]
$q = $_GET['q'] ?? '';
$sql = "SELECT id, name, email, major FROM students
        WHERE name LIKE '%$q%'";
$students = $pdo->query($sql)->fetchAll();
```

</div>

<p class="fineprint">Perhatikan: <code>$q</code> langsung DISAMBUNG ke string SQL. Tidak ada <code>prepare()</code>.</p>

Note: Ini file DEMO terpisah, bukan `index.php` final yang sudah dibangun — tunjukkan dengan jelas kepada peserta bahwa ini adalah versi yang SENGAJA ditulis salah untuk keperluan demonstrasi.

Bandingkan dengan pola `query()` di Part 3: di sana AMAN karena tidak ada input user. Di sini, `$q` berasal LANGSUNG dari `$_GET['q']` (bisa diisi siapa saja lewat URL) dan disambung memakai tanda kutip biasa ke tengah string SQL. Inilah persis pola yang di Part 3 self-study sudah diperingatkan: "ada tanda `$` milik user di dalam string SQL → wajib `prepare()`". Di sini kita LANGGAR aturan itu dengan sengaja, untuk melihat akibatnya.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Kenapa ini bisa dibobol

```text
Input normal:  q = budi
SQL jadi:      ... WHERE name LIKE '%budi%'                    -> aman

Input jahat:   q = %' OR '1'='1
SQL jadi:      ... WHERE name LIKE '%%' OR '1'='1%'             -> SELALU true!
```

<div class="ask"><b>Perhatikan:</b> tanda kutip <code>'</code> di dalam input berhasil "keluar" dari tempatnya, mengubah STRUKTUR query, bukan sekadar isinya.</div>

Note: Jelaskan pelan-pelan, ini titik paling krusial untuk dipahami: MySQL tidak tahu bedanya "kutip yang kita tulis di kode" dengan "kutip yang datang dari input user" — begitu digabung jadi satu string SQL, semuanya terlihat sama sebagai teks SQL murni bagi MySQL.

Payload `%' OR '1'='1` sengaja mengandung tanda kutip (`'`) yang menutup string `'%...%'` LEBIH AWAL dari yang diharapkan kode kita, lalu menambahkan kondisi `OR '1'='1'` yang NILAINYA SELALU BENAR. Karena `OR` dengan kondisi yang selalu benar, seluruh `WHERE` menjadi selalu benar — MySQL mengembalikan SEMUA baris di tabel, bukan hasil pencarian yang dimaksud.

Tekankan: user yang mengirim ini tidak "meretas" MySQL atau PHP secara teknis rumit — mereka hanya memanfaatkan bahwa kode kita mempercayai input mentah sebagai bagian dari perintah.



<p class="part-label">Part 6 · Security Checkpoint</p>

## <span class="badge badge-hands">Ketik/coba bareng #3</span> Buktikan sendiri

Buka fitur search (versi rentan), coba dua input ini satu-satu:

```text
1. budi                    -> hasil pencarian normal
2. %' OR '1'='1            -> perhatikan JUMLAH baris yang muncul
```

<p class="fineprint">~8 menit. Lokal saja, database throwaway milikmu sendiri.</p>

Note: Minta peserta mengetik payload ini SENDIRI di kotak search browser mereka masing-masing (bukan menonton layar instruktur saja) — pengalaman mengetik dan melihat hasilnya sendiri jauh lebih berkesan daripada menonton demo.

Sebelum mereka mulai, ingatkan sekali lagi batasan etisnya: teknik ini HANYA dicoba di aplikasi/database milik sendiri untuk tujuan belajar. Menggunakan teknik yang sama di sistem milik orang lain tanpa izin adalah tindakan ilegal, bukan sekadar "iseng belajar". Ini bukan basa-basi formalitas — tegaskan dengan serius.

Berkeliling saat mereka mencoba, pastikan semua berhasil melihat perbedaan jumlah baris sebelum lanjut ke slide checkpoint berikutnya.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Checkpoint — SEBELUM diperbaiki

<div class="mock-browser">
<div class="bar">localhost:8001/search.php?q=%25%27+OR+%271%27%3D%271</div>
<div class="body">
<p style="margin:0 0 0.5em">Hasil pencarian untuk: <code>%' OR '1'='1</code></p>
<table><tr><th>Nama</th><th>Email</th></tr>
<tr><td>Ayu Lestari</td><td>ayu.lestari@campus.ac.id</td></tr>
<tr><td>Bagas Prakoso</td><td>bagas.prakoso@campus.ac.id</td></tr>
<tr><td>&hellip; SEMUA 6 baris &hellip;</td><td></td></tr>
</table>
</div>
</div>

<div class="imgph">
<b>Image placeholder</b>
Screenshot nyata hasil percobaan di laptop instruktur, menunjukkan seluruh isi tabel students bocor lewat kotak pencarian.<br>
<i>Ambil screenshot sendiri saat demo langsung, jangan pakai gambar dari internet</i>
</div>

Note: Ini bukti visual yang harus benar-benar terjadi di layar, bukan hanya diceritakan. Tunjukkan: mencari "budi" (yang mungkin tidak ada di data) mengembalikan 0 hasil seperti wajar, tapi payload `%' OR '1'='1` mengembalikan SELURUH isi tabel — termasuk data yang seharusnya tidak relevan dengan pencarian sama sekali.

Ini adalah bentuk PALING RINGAN dari akibat SQL Injection (hanya membaca data yang memang ada di tabel yang sama). Sebutkan singkat bahwa dalam kasus nyata, teknik serupa bisa diperluas untuk membaca tabel LAIN (misalnya tabel `users` berisi password), memodifikasi data, bahkan menghapus seluruh tabel — tidak perlu didemokan, cukup disebut sebagai gambaran skala bahayanya.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Perbaikan: kembali ke prepared statement

<div class="safe">
<span class="label">search.php &mdash; versi aman</span>

```php [1|2-4|5]
$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare(
    "SELECT id, name, email, major FROM students WHERE name LIKE :q"
);
$stmt->execute(['q' => '%' . $q . '%']);
$students = $stmt->fetchAll();
```

</div>

Note: Bandingkan baris demi baris dengan versi rentan sebelumnya: `$q` sekarang TIDAK PERNAH disambung langsung ke string SQL — string SQL-nya (`WHERE name LIKE :q`) SELALU sama persis, apa pun yang diketik user. Nilai `$q` (dengan tanda `%` sudah ditambahkan di PHP, baris 5) dikirim TERPISAH lewat `execute()`.

Karena `EMULATE_PREPARES => false` (diset di Part 3), MySQL menerima SQL dan data lewat dua "saluran" yang benar-benar terpisah. MySQL memperlakukan `:q` sebagai SATU nilai data utuh, apa pun isinya — termasuk kalau isinya mengandung tanda kutip. Tanda kutip di dalam input TIDAK PERNAH bisa "keluar" mengubah struktur query, karena struktur query sudah final SEBELUM data dikirim.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Checkpoint — SETELAH diperbaiki

<div class="mock-browser">
<div class="bar">localhost:8001/search.php?q=%25%27+OR+%271%27%3D%271</div>
<div class="body">
<p style="margin:0">Hasil pencarian untuk: <code>%' OR '1'='1</code></p>
<p style="margin:0.5em 0 0;color:#f87171">Tidak ada siswa ditemukan.</p>
</div>
</div>

<div class="checkpoint"><b>Input SAMA PERSIS, hasil BERBEDA TOTAL:</b> 0 baris, karena PHP mencari nama siswa yang secara harfiah mengandung teks <code>%' OR '1'='1</code> &mdash; tidak ada yang cocok.</div>

Note: Ini momen paling penting di seluruh Part 6 — payload IDENTIK, tapi hasilnya sekarang 0 baris, bukan 6 baris. Perbandingan sebelum/sesudah dengan input yang sama persis inilah yang membuat konsep "data vs kode" benar-benar melekat, bukan sekadar dihafal sebagai aturan abstrak.

Tegaskan ulang: `prepare()` bukan "menyaring karakter berbahaya" atau "memblokir kata kunci tertentu" — MySQL memperlakukan payload itu sebagai TEKS PENCARIAN BIASA, sama seperti kalau user benar-benar mencari nama siswa yang aneh. Tidak ada satpam yang mendeteksi "wah ini mencurigakan" — strukturnya memang sudah tidak mungkin ditembus lagi.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Ancaman kedua: XSS

<div class="danger">
<span class="label">Coba simpan siswa baru dengan nama ini</span>

```text
<script>alert('Data kamu bisa disadap!')</script>
```

</div>

<div class="imgph">
<b>Image placeholder</b>
Screenshot popup alert() JavaScript yang muncul di halaman index.php akibat nama siswa yang mengandung tag script, diambil dari demo langsung.<br>
<i>Ambil screenshot sendiri saat demo, jangan pakai gambar generik dari internet</i>
</div>

Note: Demokan langsung (instruktur yang mengetik, tidak perlu semua peserta mencoba — waktu terbatas): buka `create.php`, isi field nama dengan `<script>alert('Data kamu bisa disadap!')</script>`, submit. Buka `index.php` — popup alert benar-benar muncul.

Jelaskan akar masalahnya: `index.php` mencetak `<?= $s['name'] ?>` LANGSUNG ke HTML tanpa memprosesnya lebih dulu. Kalau nilai `name` mengandung tag `<script>`, browser tidak bisa membedakan "ini teks yang harus ditampilkan apa adanya" dengan "ini kode HTML/JavaScript yang harus dijalankan" — browser menjalankannya begitu saja.

Ini kebalikan dari SQL Injection dalam hal ARAH: SQL Injection terjadi saat INPUT masuk ke query (di awal alur). XSS terjadi saat DATA (yang sudah tersimpan di database, mungkin dari input berbahaya sebelumnya) keluar sebagai OUTPUT HTML (di akhir alur, saat ditampilkan).



<p class="part-label">Part 6 · Security Checkpoint</p>

## Perbaikan: escape saat OUTPUT

<div class="cmp">
<div class="cmp-c">
<h4>Rentan</h4>

```php
<td><?= $s['name'] ?></td>
```

</div>
<div class="cmp-php">
<h4>Aman</h4>

```php
<td><?= htmlspecialchars(
    $s['name'],
    ENT_QUOTES,
    'UTF-8'
) ?></td>
```

</div>
</div>

<p class="fineprint"><code>&lt;script&gt;</code> berubah jadi teks <code>&amp;lt;script&amp;gt;</code> &mdash; tampil sebagai TEKS, bukan dijalankan sebagai kode.</p>

Note: `htmlspecialchars()` mengubah karakter-karakter HTML yang punya arti khusus (`<`, `>`, `"`, `'`, `&`) menjadi bentuk "entity" (`&lt;`, `&gt;`, dst) yang oleh browser DITAMPILKAN sebagai teks biasa, bukan diinterpretasikan sebagai tag HTML.

`ENT_QUOTES` memastikan tanda kutip tunggal DAN ganda ikut di-escape (bukan hanya salah satunya, yang merupakan default lama PHP) — penting kalau data ini nanti juga dipakai di dalam atribut HTML seperti `value="..."`. `'UTF-8'` memastikan encoding karakter konsisten dengan `charset=utf8mb4` yang sudah diatur sejak Part 3.

Aturan praktis untuk dibawa pulang: **setiap kali mencetak data yang berasal dari user (langsung dari `$_POST`/`$_GET`, ATAU dari database yang mungkin pernah diisi lewat input user) ke HTML, bungkus dengan `htmlspecialchars()`.** Ini akan dipakai lagi di Part 9 saat membahas repopulate form.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Dua prinsip, dibawa pulang

<table class="plain">
<tr><th>Ancaman</th><th>Terjadi di</th><th>Perbaikan</th></tr>
<tr><td>SQL Injection</td><td>input &rarr; QUERY</td><td><code>prepare()</code> + <code>execute()</code></td></tr>
<tr><td>XSS</td><td>data &rarr; HTML OUTPUT</td><td><code>htmlspecialchars()</code></td></tr>
</table>

<div class="checkpoint">Mulai sekarang: <b>SETIAP</b> query dengan input user pakai prepared statement. <b>SETIAP</b> data yang dicetak ke HTML di-escape.</div>

<p class="downhint">5 slide tambahan: kenapa prepared mematikan SQLi secara teknis, wildcard LIKE, whitelist ORDER BY, escaping di konteks lain, checklist keamanan capstone</p>

Note: Ini slide penutup Part 6 — pastikan dua baris di tabel ini benar-benar tertanam sebelum lanjut. Kalau perlu, minta 2-3 peserta mengulang dengan kata-kata sendiri: "Kapan pakai prepared statement? Kapan pakai htmlspecialchars?"

Jembatan ke Part 7: "Sekarang kita punya CREATE dan READ yang aman. Tapi CRUD kita baru setengah jalan — UPDATE dan DELETE masih perlu dibangun. Kabar baiknya: polanya HAMPIR SAMA PERSIS dengan yang sudah kalian kuasai."


<p class="part-label">Part 6 · Self-Study</p>

## Kenapa prepared statement "mematikan" SQLi secara teknis

```text
TANPA prepared (query digabung string):
  PHP menyusun teks SQL LENGKAP dulu (termasuk input user),
  BARU dikirim ke MySQL sebagai satu perintah utuh.
  -> MySQL tidak bisa membedakan "kode" dari "data milik user".

DENGAN prepared (EMULATE_PREPARES=false):
  PHP kirim STRUKTUR query dulu (dengan :q sebagai slot kosong).
  MySQL kompilasi/pahami strukturnya.
  BARU KEMUDIAN nilai data dikirim untuk mengisi slot itu.
  -> struktur query sudah FINAL sebelum data user tiba.
```

Note: Ini penjelasan teknis yang lebih dalam dari yang dibahas di CORE. Poin kuncinya adalah URUTAN WAKTU: pada prepared statement sungguhan (bukan emulasi), MySQL menerima dan memahami STRUKTUR query terlebih dahulu, sebelum nilai data apa pun dikirim. Karena strukturnya sudah "dikunci" lebih dulu, tidak ada cara bagi data yang dikirim belakangan untuk mengubah struktur itu — betapapun anehnya isi datanya.

Ini alasan kenapa `PDO::ATTR_EMULATE_PREPARES => false` yang diset di Part 3 bukan detail sepele — dengan emulasi (`true`, default lama PDO untuk MySQL), PDO sendiri yang menyusun string query lengkap di sisi PHP sebelum dikirim, mirip (walau lebih hati-hati) dengan menggabung string manual. Mematikan emulasi memastikan MySQL sendiri yang menangani pemisahan struktur dan data.


<p class="part-label">Part 6 · Self-Study</p>

## Detail: wildcard `%` di dalam LIKE

```php [1-2|4-5]
// Salah: user bisa memasukkan % sebagai bagian pencarian
$stmt->execute(['q' => $_GET['q']]);  // tanpa % ditambahkan manual

// Perlu ditambahkan eksplisit di PHP:
$stmt->execute(['q' => '%' . $q . '%']);
```

<p class="fineprint">Ini bukan celah SQL Injection &mdash; hanya soal PERILAKU pencarian, bukan keamanan.</p>

Note: Ini nuansa tambahan di luar isu keamanan utama: tanda `%` (wildcard "apa saja") dalam SQL `LIKE` punya arti khusus. Kalau user mengetik `%` sebagai bagian dari kata pencarian mereka (jarang terjadi, tapi mungkin), itu akan diperlakukan sebagai wildcard oleh MySQL, bukan karakter `%` yang harfiah.

Ini BUKAN celah keamanan — prepared statement tetap membuat query 100% aman dari SQL Injection. Ini murni soal HASIL PENCARIAN yang mungkin tidak sesuai ekspektasi user dalam kasus tepi tertentu. Untuk aplikasi sederhana seperti Student Management System, ini cukup diketahui saja, tidak perlu ditangani secara khusus.


<p class="part-label">Part 6 · Self-Study</p>

## Jebakan tersembunyi: `ORDER BY` dinamis

<div class="danger">
<span class="label">Masih rentan, meski pakai prepared!</span>

```php
$kolom = $_GET['sort'] ?? 'name';
$stmt = $pdo->prepare("SELECT * FROM students ORDER BY $kolom");
```

</div>

<div class="safe">
<span class="label">Perbaikan: whitelist</span>

```php
$kolomValid = ['name', 'email', 'major'];
$kolom = in_array($_GET['sort'] ?? '', $kolomValid) ? $_GET['sort'] : 'name';
```

</div>

Note: Ini jebakan yang SERING terlewat bahkan oleh developer berpengalaman: nama KOLOM atau nama TABEL tidak bisa dijadikan parameter lewat `:placeholder` prepared statement — placeholder hanya bisa menggantikan NILAI data, bukan bagian dari struktur SQL seperti nama kolom. Kalau nama kolom perlu dinamis (misalnya fitur "urutkan berdasarkan..."), prepared statement TIDAK melindungi bagian ini.

Solusinya bukan prepared statement, melainkan WHITELIST — daftar nilai yang secara eksplisit diizinkan, dicek dengan `in_array()` (dibahas di Part 1 self-study) SEBELUM nilai itu dipakai di SQL. Kalau input tidak ada di whitelist, gunakan nilai default yang aman.

Tidak dipakai di CORE hari ini karena Student Management System tidak punya fitur sort dinamis — tapi ini pengetahuan penting kalau capstone dikembangkan lebih jauh (lihat Part 10).


<p class="part-label">Part 6 · Self-Study</p>

## Escaping berbeda untuk konteks berbeda

```text
Output ke HTML biasa      -> htmlspecialchars()
Output ke atribut HTML     -> htmlspecialchars() (dengan ENT_QUOTES)
Output ke dalam <script>   -> json_encode() untuk data, BUKAN htmlspecialchars
Output ke URL/query string -> urlencode() atau rawurlencode()
```

Note: `htmlspecialchars()` yang dibahas di CORE menyelesaikan kasus PALING UMUM (mencetak data ke tengah HTML biasa, seperti isi `<td>`), tapi bukan solusi universal untuk SEMUA konteks output.

Contoh: kalau data perlu disisipkan ke dalam blok `<script>` JavaScript (misalnya untuk dipakai variable JS), `htmlspecialchars()` tidak cukup dan malah bisa merusak data — konteks itu butuh `json_encode()`. Kalau data perlu disisipkan ke URL (misalnya `<a href="edit.php?search=$q">`), butuh `urlencode()` supaya karakter spesial URL (spasi, `&`, `?`) tidak merusak struktur link.

Prinsip umum yang berlaku di semua kasus: **escaping harus disesuaikan dengan KONTEKS tempat data itu akan muncul.** Untuk Student Management System hari ini, konteks yang dipakai hanya HTML biasa — cukup `htmlspecialchars()`. Tapi penting untuk tahu bahwa aturan ini bukan satu ukuran untuk semua situasi.


<p class="part-label">Part 6 · Self-Study</p>

## Checklist keamanan untuk capstone

```text
[ ] Setiap query dengan input user pakai prepare() + execute()
[ ] Setiap data yang dicetak ke HTML dibungkus htmlspecialchars()
[ ] Setiap operasi ubah/hapus data lewat POST, bukan GET
[ ] Form validasi di SISI SERVER, bukan hanya HTML/JavaScript
[ ] Pesan error tidak membocorkan detail teknis ke user biasa
```

Note: Checklist ini akan dipakai ulang secara eksplisit sebagai bagian dari rubrik penilaian capstone (Part 10) — bukan sekadar rangkuman, tapi kriteria konkret yang bisa dicek satu-satu terhadap kode yang mereka tulis sendiri untuk entity `courses`.

Sarankan peserta menyimpan checklist ini terpisah (screenshot atau catat) untuk dibuka kembali saat mengerjakan capstone, jauh setelah detail teknis di slide-slide sebelumnya mulai kabur diingatan.
