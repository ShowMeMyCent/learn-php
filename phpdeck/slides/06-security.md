<!-- .slide: id="part-6" data-background="#2a0a0a" -->
<p class="part-label">Part 6 · Security Checkpoint <span class="badge badge-core">Core</span></p>

# Jangan Percaya Input Siapa Pun

## SQL Injection &amp; XSS

Note: **🗣️ Ngomong ke Peserta:**
"Nah, teman-teman, ini bagian paling krusial dari seluruh sesi kita hari ini: Keamanan Web! Jangan pernah percaya input siapapun. Tapi sebelum kita mulai, ada satu aturan mutlak: semua demo peretasan ini HANYA boleh kalian coba di database lokal laptop kalian sendiri. Jangan pernah dicoba ke sistem kampus atau web orang lain!"

**🎯 Poin Kunci di Layar:**
- Tekankan etika: lab lokal hanya untuk pemahaman bertahan (*defense*).



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

Note: **🗣️ Ngomong ke Peserta:**
"Ada 2 momok terbesar web developer pemula: SQL Injection dan XSS. Akar masalahnya sama persis: kita terlalu percaya sama apa yang diketik user!
Bedanya cuma arahnya:
- Kalau SQL Injection: input user masuk dan ngerusak perintah query di database MySQL.
- Kalau XSS: input user masuk dan ngerusak tampilan HTML di browser orang lain."

**🎯 Poin Kunci di Layar:**
- Tunjuk dua panah: SQLi di database, XSS di browser klien.



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

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita bedah file pencarian yang sengaja dibuat rentan. Perhatikan baris 3: variabel `$q` ditempel mentah-mentah pakai petik ke dalam query SQL. Di Part 3 tadi kita bilang: kalau ada tanda dollar milik user di dalam string SQL, haram hukumnya pakai `query()` langsung! Sekarang kita buktikan kenapa cara ini sangat berbahaya."

**🎯 Poin Kunci di Layar:**
- Tunjuk `$q` di dalam petik `'%$q%'`: ini celah fatalnya.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Kenapa ini bisa dibobol

```text
Input normal:  q = budi
SQL jadi:      ... WHERE name LIKE '%budi%'                    -> aman

Input jahat:   q = %' OR '1'='1
SQL jadi:      ... WHERE name LIKE '%%' OR '1'='1%'             -> SELALU true!
```

<div class="ask"><b>Perhatikan:</b> tanda kutip <code>'</code> di dalam input berhasil "keluar" dari tempatnya, mengubah STRUKTUR query, bukan sekadar isinya.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Perhatikan logika ini: kalau user ngetik 'budi', query-nya wajar. Tapi kalau hacker ngetik `%' OR '1'='1`, tanda kutip satu dari input user berhasil kabur dan menutup string query kita lebih cepat! Akibatnya apa? MySQL membaca perintah baru: `OR '1'='1'`.
Karena 1 selalu sama dengan 1, kondisi pencarian jadi SELALU BENAR! Database bakal memuntahkan SEMUA baris yang ada di tabel tanpa peduli siapa namanya."

**🎯 Poin Kunci di Layar:**
- Tunjuk `'1'='1'`: kutip jahat mengubah struktur logika query database.



<p class="part-label">Part 6 · Security Checkpoint</p>

## <span class="badge badge-hands">Live Demo #3</span> Simulasi Serangan SQL Injection

Pemateri mendemokan kotak pencarian (versi rentan) dengan dua input:

```text
1. budi                    -> hasil pencarian normal (filter nama berjalan)
2. %' OR '1'='1            -> SQL Injection: seluruh data tabel bocor!
```

<p class="fineprint">Simulasi Live: Memperlihatkan bahaya fatal jika input user disambung langsung ke string SQL.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang perhatikan ke layar proyektor. Saya akan mendemokan Live Demo #3: bagaimana serangan SQL Injection bekerja secara nyata.
Pertama, saya ketik 'budi'. Hasilnya wajar, hanya muncul data yang mengandung kata budi.
Sekarang, perhatikan kalau saya masukkan payload ini: `%' OR '1'='1`. Begitu saya tekan Enter... BOOM! Seluruh isi tabel langsung tumpah ke layar!
Kenapa bisa begitu? Karena tanda kutip dari input berhasil membajak struktur perintah SQL di server kita. Sekarang mari kita pelajari bagaimana cara menutup celah ini menggunakan Prepared Statement!"

**🎯 Poin Kunci di Layar:**
- Tunjukkan perbedaan hasil pencarian biasa vs payload SQL Injection.
- Tunjukkan kepanikan data bocor sebagai motivasi pentingnya Prepared Statement.



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

Note: **🗣️ Ngomong ke Peserta:**
"Kalian lihat di layar? Seluruh 6 data mahasiswa bocor keluar! Ini baru contoh paling ringan. Bayangkan kalau hacker ganti payload-nya jadi perintah buat nyolong tabel password atau ngehapus seluruh database. Cuma gara-gara satu tanda kutip yang lupa kita amankan."

**🎯 Poin Kunci di Layar:**
- Tunjukkan bahwa filter nama sama sekali tidak berfungsi karena diakali oleh logika SQL Injection.



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

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita sembuhkan! Kodenya diubah jadi begini: kita pakai `prepare()` dengan placeholder `:q`. Lalu nilai pencariannya kita kirim lewat `execute(['q' => '%' . $q . '%'])`.
Karena di Part 3 tadi kita pasang `EMULATE_PREPARES => false`, MySQL bakal membedah struktur query-nya dulu sampai tuntas. Begitu input user masuk, tanda kutip apapun cuma bakal dianggap sebagai huruf biasa, bukan perintah kode!"

**🎯 Poin Kunci di Layar:**
- Tunjuk `:q`: placeholder yang membuat query dan data terpisah jalurnya.



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

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kalian coba tes lagi dengan payload yang sama persis: `%' OR '1'='1`. Hasilnya apa? Tepat 0 hasil! Kenapa? Karena MySQL sekarang nyari mahasiswa yang nama KTP-nya beneran bertuliskan `% OR 1=1`. Dan karena gak ada orang dengan nama seaneh itu, database membalas 'Tidak ada siswa ditemukan'. Celah SQL Injection resmi ditutup!"

**🎯 Poin Kunci di Layar:**
- Input identik, tapi sistem sekarang kebal.



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

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang ancaman kedua: **XSS (Cross-Site Scripting)**. Ini kebalikan dari SQL Injection. Kalau SQL Injection itu input merusak database, XSS itu data berbahaya keluar dan merusak browser user lain.
Coba perhatikan demo ini: saya nambah mahasiswa baru, tapi namanya saya isi tag script JavaScript: `<script>alert('Data kamu bisa disadap!')</script>`. Pas dibuka di `index.php`, popup alert-nya beneran muncul di layar!"

**🎯 Poin Kunci di Layar:**
- Demonstrasikan popup alert JavaScript yang terpicu karena kode HTML disuntik langsung ke halaman.



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

Note: **🗣️ Ngomong ke Peserta:**
"Kenapa bisa gitu? Karena saat kita tulis `<?= $s['name'] ?>`, browser gak tau mana teks nama manusia dan mana tag HTML. Semua langsung dieksekusi.
Obatnya: setiap kali kalian mencetak data ke layar HTML, bungkus pakai fungsi `htmlspecialchars()`. Fungsi ini bakal menyulap kurung siku `<` jadi `&lt;`. Hasilnya: teks `<script>` bakal dicetak murni sebagai tulisan biasa di layar, bukan dieksekusi sebagai program oleh browser."

**🎯 Poin Kunci di Layar:**
- Tunjuk kolom kanan: bungkus `htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8')`.



<p class="part-label">Part 6 · Security Checkpoint</p>

## Dua prinsip, dibawa pulang

<table class="plain">
<tr><th>Ancaman</th><th>Terjadi di</th><th>Perbaikan</th></tr>
<tr><td>SQL Injection</td><td>input &rarr; QUERY</td><td><code>prepare()</code> + <code>execute()</code></td></tr>
<tr><td>XSS</td><td>data &rarr; HTML OUTPUT</td><td><code>htmlspecialchars()</code></td></tr>
</table>

<div class="checkpoint">Mulai sekarang: <b>SETIAP</b> query dengan input user pakai prepared statement. <b>SETIAP</b> data yang dicetak ke HTML di-escape.</div>

<p class="downhint">5 slide tambahan: kenapa prepared mematikan SQLi secara teknis, wildcard LIKE, whitelist ORDER BY, escaping di konteks lain, checklist keamanan capstone</p>

Note: **🗣️ Ngomong ke Peserta:**
"Dua hukum wajib web backend yang harus kalian ingat seumur hidup:
1. Data masuk ke SQL? Wajib `prepare()` + `execute()`.
2. Data keluar ke HTML? Wajib `htmlspecialchars()`.
Pegang dua kunci ini, aplikasi kalian sudah lebih aman dari 80% tugas kuliah di luar sana. Sekarang kita gaspol ke Part 7 & 8: UPDATE dan DELETE!"

**🎯 Transisi ke Part 7:**
- Lanjut tekan panah kanan ke Part 7.


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
