<!-- .slide: id="part-1" -->
<p class="part-label">Part 1 · PHP Survival Guide <span class="badge badge-core">Core</span></p>

# PHP dalam 12 menit

## Bukan kursus syntax &mdash; hanya delta dari C

Note: Judul ini sengaja provokatif. Sampaikan eksplisit: "Saya tidak akan mengajarkan apa itu variable, apa itu if, apa itu loop — kalian sudah tahu dari C. Saya hanya akan tunjukkan apa yang BEDA."

Bagian ini cepat by design. Kalau ada yang bertanya detail syntax di luar yang ditampilkan, jawab singkat lalu lanjut — detailnya ada di slide self-study.



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

Note: `<?= $x ?>` adalah singkatan dari `<?php echo $x; ?>` — akan sering sekali dipakai untuk mencetak nilai ke tengah HTML nanti.

Tunjukkan urutan baca: baris 1-2 itu PHP murni (tidak menghasilkan output apa pun ke browser), baris 4 kosong, baris 6 adalah HTML biasa yang menyisipkan nilai `$pesan` di tengahnya. Ini pratinjau langsung untuk pola form/table yang akan mereka tulis sepanjang sesi.



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

Note: Ini konsep paling penting di seluruh Part 1, lebih penting dari syntax apa pun.

Program C yang mereka tulis di kelas sebelumnya: `main()` jalan sekali, mungkin looping menunggu input, lalu selesai. Ada satu proses, hidup selama kamu jalankan.

PHP di web server: **setiap request HTTP menjalankan ulang file PHP dari baris pertama**. Tidak ada variable yang "diingat" dari request sebelumnya secara otomatis — kalau butuh itu (misalnya status login), harus disimpan eksplisit (nanti: session, Part 5). Ini menjelaskan kenapa nanti kita butuh database untuk data permanen, dan session untuk data sementara antar-request.

Analogi yang membantu: C seperti orang yang tinggal di rumah dan ingat semua yang terjadi kemarin. PHP seperti orang yang lahir baru setiap kali pintu diketuk, membaca "buku catatan" (database/session) untuk tahu apa yang terjadi sebelumnya, lalu mati lagi setelah menjawab.



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

Note: Jalankan file ini sungguhan, lalu buka View Page Source dan tunjukkan: **tidak ada satu pun kata `php` di HTML yang diterima browser**. Semua sudah diproses jadi teks biasa di server.

Ini bukti visual dari model mental "PHP → HTML sebelum sampai ke browser". Kalau ada peserta yang masih menganggap browser "menjalankan" PHP, momen ini biasanya yang meluruskan.



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

Note: Dua hal yang perlu ditekankan: tanda `$` di depan SEMUA variable (tidak ada pengecualian, tidak seperti C yang variable-nya polos), dan **dynamic typing** — tipe ditentukan otomatis dari nilai, bisa berubah nilainya jadi tipe lain di baris berikutnya.

Ingatkan bahwa dynamic typing itu pedang bermata dua: cepat ditulis, tapi bug tipe data yang di C akan tertangkap compiler, di PHP baru ketahuan saat runtime. Ini alasan kenapa nanti kita akan rajin melakukan validasi eksplisit di Part 9 — PHP tidak akan menolak `$umur = "dua puluh"` seperti C akan menolak `int umur = "dua puluh";`.



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

Note: Titik (`.`) adalah operator concat di PHP, bukan `+` seperti bahasa lain dan bukan `strcat()` seperti C. Ini salah satu typo paling umum peserta baru: menulis `"Halo " + $nama` dan bingung kenapa hasilnya aneh — PHP akan mencoba operasi aritmatika, bukan error tegas.

Interpolasi (baris 1, petik ganda) jauh lebih sering dipakai daripada concat (baris 3) untuk string pendek. Tapi ingatkan: `$nama['key']` di dalam petik ganda kadang butuh kurung kurawal `{$arr['key']}` — cukup disebut sekilas, detail di self-study.



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

Note: Ini slide paling penting di seluruh Part 1. Jangan buru-buru.

Di C, array cuma bisa diakses dengan index angka (`nilai[0]`, `nilai[1]`). PHP associative array mengizinkan kunci berupa string — dan ini BUKAN fitur eksotis, ini adalah bentuk data yang akan mereka lihat berulang-ulang: `$_POST['name']`, `$_GET['id']`, dan (paling penting) setiap baris hasil `fetch()` dari database nanti di Part 3.

Sampaikan eksplisit: "Kalau kalian paham slide ini, separuh jalan menuju paham CRUD sudah selesai." Tidak berlebihan — pola `$row['kolom']` akan muncul di hampir setiap file PHP yang mereka tulis hari ini.



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

Note: `foreach` menggantikan pola `for (int i = 0; i < n; i++)` ketika yang kita mau hanyalah "lakukan sesuatu untuk setiap elemen", tanpa perlu index secara eksplisit (meski `foreach ($arr as $key => $val)` bisa memberi index/kunci juga — sebutkan sekilas, detail di self-study).

Tarik garis lurus ke depan: gabungan associative array + foreach ADALAH pola yang akan mereka tulis untuk menampilkan data siswa di Part 3. `foreach ($students as $s) { echo $s['name']; }`. Kalau slide ini dan slide sebelumnya sudah klik, Part 3 akan terasa seperti "oh, tinggal pakai yang tadi".



<p class="part-label">Part 1 · PHP Survival Guide</p>

## Recap kilat

- PHP = server-side, jalan ulang tiap request, mati setelah kirim response
- `$variable`, dynamic typing, `.` untuk concat
- **Associative array** `$arr['key']` &mdash; bentuk data query nanti
- **foreach** &mdash; cara mencetak hasil query nanti

<p class="downhint">6 slide tambahan di bawah: perbandingan == vs ===, function, array toolkit, superglobals, include/require, error_reporting</p>

Note: Jembatan ke Part 2: "Sekarang kalian sudah tahu bentuk data PHP. Pertanyaannya: DARI MANA data itu datang? Bagaimana caranya input dari form HTML sampai ke variable PHP?" — itu tepat yang akan dijawab Part 2.

Kalau waktu longgar (jarang terjadi, tapi kalau ya), ini titik aman untuk singgah ke satu-dua slide self-study seperti `==` vs `===` karena itu sumber bug umum. Kalau waktu ketat, langsung lanjut.


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
