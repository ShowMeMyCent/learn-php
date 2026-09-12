<!-- .slide: id="part-1" -->
<p class="part-label">Part 1 · PHP Survival Guide <span class="badge badge-core">Core</span></p>

# PHP Survival Guide

## Bukan kursus syntax &mdash; hanya delta dari C

Note: **🗣️ Ngomong ke Peserta:**
"Di bagian ini kita bahas secara padat dan to the point. Kenapa? Karena saya gak akan ngajarin ulang apa itu variabel, if-else, atau looping. Kalian sudah hafal itu dari bahasa C. Yang bakal saya tunjukkan di sini murni hal-hal yang BEDA antara C dan PHP."

**🎯 Poin Kunci di Layar:**
- Jangan berlama-lama di sintaks dasar, jaga tempo.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## CONCEPT — PHP itu apa?

- Bahasa **server-side**, dijalankan di server, bukan di browser
- **Interpreted**, bukan dikompilasi ke binary seperti C
- Kode ditulis di antara `<?php ... ?>`, boleh disisipkan ke tengah HTML

```php [1-2|4|6]
<?php
    $pesan = "Halo dari server";
?>

<h1><?= $pesan ?></h1>
```

Note: **🗣️ Ngomong ke Peserta:**
"PHP itu bahasa server-side yang sifatnya interpreted. Kodenya ditulis di dalam tag `<?php ... ?>` dan bisa diselipkan langsung di tengah dokumen HTML. Lihat baris 6: `<?= $pesan ?>` itu singkatan resmi dari `<?php echo $pesan; ?>`. Pola ini yang bakal kita pakai terus buat nampilin data mahasiswa ke dalam tabel web."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 1-2: PHP memproses data.
- Tunjuk baris 6: HTML mencetak hasilnya.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## WHY — kenapa beda dari C?

<div class="flow-v">
<span>Program C</span>
<span class="arrow">&darr;</span>
<span>gcc main.c -o main</span>
<span class="arrow">&darr;</span>
<span>./main &nbsp;(proses hidup, kamu kontrol penuh)</span>
</div>

<div class="flow-v" style="margin-top:0.8em">
<span>File .php</span>
<span class="arrow">&darr;</span>
<span>Request masuk &rarr; PHP dijalankan ULANG dari awal</span>
<span class="arrow">&darr;</span>
<span>Output HTML dikirim &rarr; proses MATI</span>
</div>

Note: **🗣️ Ngomong ke Peserta:**
"Ini perbedaan paling penting hari ini. Di C, kalian compile jadi file binary, dijalankan, dan prosesnya hidup terus di komputer kalian sampai kalian stop. PHP itu beda: setiap kali ada browser minta halaman, server ngebaca file PHP dari baris pertama sampai akhir, ngirim HTML ke browser, lalu **PHP langsung mati dan memorinya dihapus**. PHP itu pelupa — dia gak ingat apa-apa dari request sebelumnya. Makanya nanti kita butuh Database buat nyimpan data permanen, dan Session buat nginget user."

**🎯 Poin Kunci di Layar:**
- Tunjuk kata 'MATI': tekankan sifat stateless PHP. Inilah alasan mendasar kenapa web butuh database.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## SEE IT RUN

<p class="filename">hello.php</p>

```php [1-3]
<?php
    $nama = "Dunia";
?>
<h1>Halo, <?= $nama ?>!</h1>
```

<div class="mock-browser">
<div class="bar">localhost:8001/hello.php</div>
<div class="body"><h1 style="margin:0">Halo, Dunia!</h1></div>
</div>

<p class="fineprint">Klik kanan browser &rarr; "View Page Source". Cari kata <code>php</code> di situ.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita buktikan. Kalau file ini dibuka di browser, terus kalian klik kanan -> 'View Page Source', kalian gak bakal nemu satu pun tag `<?php`. Semuanya sudah berubah jadi teks HTML polos `<h1>Halo, Dunia!</h1>`. Kenapa? Karena browser cuma ngerti HTML. Semua kode PHP kalian sudah selesai dieksekusi di server sebelum dikirim."

**🎯 Poin Kunci di Layar:**
- Tekankan: Browser klien tidak pernah mengeksekusi PHP.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## Variable: C vs PHP

<div class="cmp">
<div class="cmp-c">
<h4>C</h4>

```c
int umur = 20;
char nama[20] = "Ayu";
float ipk = 3.75;

// tipe WAJIB dideklarasikan
// ukuran string ditentukan di awal
```

</div>
<div class="cmp-php">
<h4>PHP</h4>

```php [1-3|5-6]
$umur = 20;
$nama = "Ayu";
$ipk = 3.75;

// tanpa deklarasi tipe
// diawali tanda $, itu saja aturannya
```

</div>
</div>

Note: **🗣️ Ngomong ke Peserta:**
"Di PHP, semua variabel wajib diawali tanda dollar `$`. Gak perlu deklarasi `int`, `char`, atau `float` karena PHP itu dynamically typed. Enaknya: nulisnya cepat. Bahayanya: kalau ada salah tipe data, compiler gak bakal ngingetin kita kayak di C. Errornya baru meledak saat web dibuka. Makanya nanti di Part 9 kita wajib bikin validasi input sendiri."

**🎯 Poin Kunci di Layar:**
- Tunjuk tanda `$`: jangan sampai lupa nulis dollar di variabel PHP.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## String: interpolasi

<div class="cmp">
<div class="cmp-c">
<h4>C</h4>

```c
printf("Halo, %s! Umur %d",
       nama, umur);

// atau strcat/sprintf
// untuk menggabung
```

</div>
<div class="cmp-php">
<h4>PHP</h4>

```php [1|3]
echo "Halo, $nama! Umur $umur";

echo "Halo, " . $nama . "!";
```

</div>
</div>

<p class="fineprint">Petik ganda <code>"..."</code> membaca isi variable. Petik tunggal <code>'...'</code> tidak.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Buat gabung string, di PHP kita pakai tanda titik `.`, bukan tanda tambah `+` dan bukan `strcat()`. Kalau kalian tulis petik dua `\"...\"`, variabel di dalamnya otomatis diterjemahkan nilainya (interpolasi). Tapi kalau petik satu `'...'`, PHP bakal anggap itu teks mentah apa adanya."

**⚠️ Antisipasi Error:**
- Typo nomor satu peserta: menulis `"Halo " + $nama`. Ingatkan di PHP itu bakal dianggap penjumlahan angka matematika!



<p class="part-label">Part 1 · PHP Survival Guide</p>

## Associative array — yang paling penting hari ini

<div class="cmp">
<div class="cmp-c">
<h4>C</h4>

```c
int nilai[3] = {80, 90, 75};

// akses HANYA lewat index angka
nilai[0]; // 80
```

</div>
<div class="cmp-php">
<h4>PHP</h4>

```php [1-5|7]
$mhs = [
    "name"  => "Ayu Lestari",
    "email" => "ayu@campus.ac.id",
    "major" => "Informatika",
];

$mhs["name"]; // "Ayu Lestari"
```

</div>
</div>

<div class="ask"><b>Ingat baik-baik:</b> setiap baris hasil query database nanti berbentuk PERSIS seperti ini — array dengan kunci nama kolom.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Tolong perhatikan slide ini baik-baik. Kalau kalian paham slide ini, separuh materi CRUD hari ini sudah kalian kuasai. Di C, array cuma bisa dipanggil pakai angka: `nilai[0]`, `nilai[1]`. Di PHP ada yang namanya Associative Array: kuncinya pakai kata, kayak kamus. Nanti, setiap data mahasiswa yang kita ambil dari database bentuknya persis kayak gini: `$mhs['name']`, `$mhs['email']`."

**🎯 Poin Kunci di Layar:**
- Tunjuk sintaks panah `=>` dan cara akses `$mhs['name']`. Ini fondasi data sepanjang webinar.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## foreach — pola yang akan dipakai terus

<div class="cmp">
<div class="cmp-c">
<h4>C</h4>

```c
for (int i = 0; i < n; i++) {
    printf("%s\n", nama[i]);
}
```

</div>
<div class="cmp-php">
<h4>PHP</h4>

```php [1-3]
foreach ($mahasiswa as $mhs) {
    echo $mhs["name"];
}
```

</div>
</div>

<div class="ask"><b>Bayangkan:</b> <code>$mahasiswa</code> adalah HASIL QUERY dari database — daftar semua siswa. <code>foreach</code> akan menjadi cara kita mencetaknya sebagai baris tabel HTML.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Di C, kita muter array pakai counter `for (int i = 0; ...)`. Di PHP, kita cukup pakai `foreach ($mahasiswa as $mhs)`. Bayangkan `$mahasiswa` itu sekeranjang isi data seluruh mahasiswa dari database, dan `$mhs` itu satu orang mahasiswa yang lagi kita pegang di tiap putaran. Nanti di Part 3, kita tinggal bungkus ini ke dalam tag `<tr>` dan `<td>` tabel HTML."

**🎯 Poin Kunci di Layar:**
- Hubungkan langsung Associative Array di slide sebelumnya dengan `foreach` ini.



<p class="part-label">Part 1 · PHP Survival Guide</p>

## Recap kilat

- PHP = server-side, jalan ulang tiap request, mati setelah kirim response
- `$variable`, dynamic typing, `.` untuk concat
- **Associative array** `$arr['key']` &mdash; bentuk data query nanti
- **foreach** &mdash; cara mencetak hasil query nanti

<p class="downhint">6 slide tambahan di bawah: perbandingan == vs ===, function, array toolkit, superglobals, include/require, error_reporting</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sip, recap kilat: PHP hidup per-request, variabel pakai `$`, gabung string pakai `.`, dan datanya pakai array asosiatif. Nah, sekarang pertanyaannya: dari mana datangnya data itu pertama kali? Gimana caranya isian yang diketik user di form HTML bisa nyampe ke variabel PHP? Kita masuk ke Part 2."

**🎯 Transisi ke Part 2:**
- Langsung tekan panah kanan ke Part 2. Slide ke bawah untuk peserta baca mandiri nanti di rumah.


<p class="part-label">Part 1 · Self-Study</p>

## `==` vs `===`, dan `var_dump`

```php [1-2|4-5|7]
0 == "abc"     // true  di PHP lama, perilaku longgar
0 === "abc"    // false, tipe DAN nilai harus sama

var_dump($_POST);
// mencetak isi variable + TIPE datanya, alat debug utama

echo $tidakAda; // Warning, bukan crash seperti C
```

<div class="ask"><b>Checkpoint:</b> Kenapa `===` lebih aman dipakai daripada `==` saat membandingkan input dari form?</div>

Note: **Jawaban:** input dari form (`$_POST`, `$_GET`) SELALU berupa string, bahkan kalau usernya mengetik angka. `==` melakukan konversi tipe otomatis sebelum membandingkan, yang kadang menghasilkan kejutan (contoh klasik: `"0" == "abc"` bisa `true` di beberapa versi PHP karena keduanya dikonversi ke angka 0). `===` membandingkan tipe DAN nilai sekaligus, jadi tidak ada konversi tersembunyi — perilakunya lebih bisa diprediksi, terutama untuk membandingkan ID atau status dari input.

`var_dump()` akan jadi teman utama debugging sepanjang sesi ini, mirip `printf` yang biasa dipakai untuk debug di C. Biasakan peserta membuka `var_dump($_POST)` setiap kali ragu apa isi sebuah variable.

Beda dari C: `echo $tidakAda` pada variable yang belum didefinisikan tidak meng-crash-kan program, hanya memunculkan Warning dan melanjutkan dengan nilai kosong. PHP jauh lebih "toleran" terhadap error kecil dibanding C — ini nyaman tapi juga bisa menyembunyikan bug. Untuk lingkungan belajar, aktifkan `error_reporting(E_ALL); ini_set('display_errors', 1);` di baris paling atas file (dibahas juga di slide error_reporting).


<p class="part-label">Part 1 · Self-Study</p>

## Function: nyaris sama seperti C

```php [1-5|7-8]
function hitungRataRata(array $nilai): float {
    $total = array_sum($nilai);
    $n = count($nilai);
    return $total / $n;
}

$rata = hitungRataRata([80, 90, 75]);
echo $rata; // 81.666...
```

Note: Perbedaan dari C: kata kunci `function`, bukan menuliskan tipe kembalian di depan nama. Tipe parameter (`array $nilai`) dan tipe kembalian (`: float`) SIFATNYA OPSIONAL di PHP — bisa ditulis tanpa tipe sama sekali (`function hitungRataRata($nilai) { ... }`). Menuliskannya seperti contoh di atas disebut *type hinting*, praktik yang baik tapi tidak wajib seperti di C.

Tidak perlu file header terpisah seperti `.h` di C — definisi function langsung dipakai di file yang sama atau di-`include` dari file lain (lihat slide include/require).

Tidak perlu dibahas panjang di sini karena konsep function itu sendiri sudah mereka kuasai dari C. Cukup tunjukkan bentuknya sekali.


<p class="part-label">Part 1 · Self-Study</p>

## Array toolkit yang sering dipakai

```php [1-2|4-5|7-8|10-11]
count($arr);              // jumlah elemen
in_array("Ayu", $arr);    // true/false, mirip linear search

array_push($arr, "baru"); // tambah di akhir
$arr[] = "baru";          // cara yang lebih umum dipakai

empty($arr);              // true kalau array kosong ATAU var belum ada
isset($arr['name']);      // true kalau key ada DAN bukan null

array_map(fn($x) => $x*2, $arr);   // transformasi tiap elemen
array_filter($arr, fn($x) => $x>0); // saring elemen
```

Note: Tidak perlu dihafal semua sekarang — ini referensi untuk dibuka kembali saat butuh. Yang paling sering muncul di kode CRUD nanti adalah `count()`, `empty()`, dan `isset()` — dua yang terakhir akan sangat sering dipakai untuk mengecek apakah input form ada isinya sebelum diproses (pratinjau untuk Part 9, Validation).

`isset()` vs `empty()` sering tertukar: `isset($x)` hanya cek "apakah variable ini ada dan bukan null", sedangkan `empty($x)` juga menganggap string kosong `""`, angka `0`, dan array kosong `[]` sebagai "kosong". Untuk validasi form, `empty($_POST['name'])` biasanya yang dimaksud ("apakah field ini benar-benar diisi").


<p class="part-label">Part 1 · Self-Study</p>

## Superglobals — pratinjau Part 2

```php
$_GET      // data dari query string URL (?id=5)
$_POST     // data dari form method="post"
$_SERVER   // info tentang request & server
$_SESSION  // data yang bertahan antar-request (Part 5)
$_FILES    // upload file (tidak dibahas hari ini)
```

<p class="fineprint">Semuanya adalah associative array biasa &mdash; pola yang sudah kamu kuasai.</p>

Note: "Superglobal" hanyalah istilah untuk array bawaan PHP yang otomatis tersedia di SETIAP file, tanpa perlu didefinisikan — PHP mengisinya sendiri berdasarkan request yang masuk. Tidak ada konsep setara langsung di C karena C tidak punya model "request" bawaan bahasa.

Ini murni pratinjau, jangan dibahas dalam sekarang — Part 2 akan membedah `$_GET` dan `$_POST` secara detail dengan contoh langsung. Tujuannya di sini hanya supaya nama-nama ini tidak terasa asing saat muncul nanti.


<p class="part-label">Part 1 · Self-Study</p>

## include / require

```php [1-2|4-5]
<?php require 'config/database.php'; ?>
<!-- semua variable di database.php sekarang tersedia di sini -->

<?php include 'partials/header.php'; ?>
<h1>Isi Halaman</h1>
```

<p class="fineprint"><code>require</code> menghentikan program kalau file tidak ditemukan. <code>include</code> hanya warning.</p>

Note: Analogi C yang paling dekat adalah `#include` untuk header — tapi bedanya cukup signifikan: `#include` di C disalin oleh preprocessor SEBELUM kompilasi (compile-time), sedangkan `require`/`include` PHP dijalankan saat program berjalan (runtime), dan bisa berada di dalam `if` atau loop.

Gunakan `require` (bukan `include`) untuk file yang program TIDAK BISA jalan tanpanya — contoh paling jelas adalah koneksi database. Kalau `config/database.php` gagal ditemukan, lebih baik program berhenti tegas daripada lanjut jalan tanpa koneksi dan error membingungkan di baris lain. Ini akan dipakai persis seperti itu di Part 3.

`require_once` / `include_once` juga ada — mencegah file yang sama di-load dua kali (berguna kalau beberapa file saling require file yang sama). Cukup disebut, tidak perlu didemokan.


<p class="part-label">Part 1 · Self-Study</p>

## error_reporting saat belajar

```php [1-2]
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

<p class="fineprint">Taruh di baris paling atas file saat development. JANGAN pernah aktif di server production.</p>

<div class="ask"><b>Checkpoint:</b> Kenapa berbahaya kalau <code>display_errors</code> menyala di aplikasi yang sudah live untuk publik?</div>

Note: **Jawaban:** pesan error PHP sering membocorkan informasi sensitif — path lengkap folder di server, nama tabel database, bahkan potongan query SQL. Ini adalah informasi berharga bagi orang yang mencoba menyerang aplikasi. Prinsipnya: tampilkan error detail ke DEVELOPER (lewat log file), tampilkan pesan generik ke USER ("Terjadi kesalahan, coba lagi nanti").

Selama sesi hari ini, aktifkan `display_errors` supaya error terlihat langsung di browser — ini mempercepat debugging saat hands-on. Tapi tekankan ini adalah pengaturan MODE BELAJAR, bukan kebiasaan yang dibawa ke aplikasi nyata. Sebutkan sekilas bahwa di server production sungguhan, pengaturan ini biasanya dikontrol lewat file konfigurasi server (`php.ini`), bukan ditulis di kode.
