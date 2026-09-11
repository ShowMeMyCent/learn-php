<!-- .slide: id="part-0" -->
<p class="part-label">Part 0 · Opening</p>

# Dari C ke PHP

## Membangun Backend CRUD

<p class="fineprint">Peminatan Backend &middot; Sesi Hands-on &amp; Live Coding</p>

Note: **🗣️ Ngomong ke Peserta:**
"Halo semuanya, selamat datang! Hari ini kita bakal belajar cara kerja web backend lewat PHP, berangkat dari apa yang sudah kalian kuasai di praktikum C kemarin. Sebelum kita mulai, kita pastikan dua server lokal kalian sudah siap. Deck slide ini jalan di port 8000, dan aplikasi PHP kita nanti jalan di port 8001 atau Laragon. Kita pastikan database `schema.sql` dan `seed.sql` sudah siap di Laragon ya."

**🎯 Poin Kunci di Layar:**
- Pastikan mereka buka slide lewat server `http://localhost:8000/phpdeck.html`, bukan klik ganda `file://`.
- Cek cepat apakah Apache & MySQL di laptop mereka sudah aktif.



<p class="part-label">Part 0 · Opening</p>

## Kamu sudah punya bekalnya

<div class="cmp">
<div class="cmp-c">
<h4>Sudah dikuasai</h4>

- HTML &amp; CSS
- C: variable, tipe data
- Conditional, looping, function
- Alur program terminal
- SQL dasar

</div>
<div class="cmp-php">
<h4>Yang ditambahkan hari ini</h4>

- PHP sebagai *server-side*
- HTTP request &amp; response
- PHP &harr; MySQL lewat PDO
- CRUD utuh
- Keamanan dasar

</div>
</div>

<p class="fineprint">Hari ini bukan kursus PHP dari nol. Hari ini adalah <b>delta</b>.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Kalian di sini sudah paham logika dasar dari bahasa C: apa itu variabel, if-else, looping, dan fungsi. Jadi santai saja, hari ini kita gak bakal buang waktu buat ngulang apa itu looping atau tipe data. Yang kita pelajari hari ini cuma bedanya: bagaimana program kalian yang tadinya jalan di layar hitam terminal, sekarang dijalankan oleh server buat melayani request dari browser."

**🎯 Poin Kunci di Layar:**
- Tunjuk kolom kiri: itu modal mereka dari C.
- Tunjuk kolom kanan: itu fokus kita bersama (HTTP + Database + Security).



<p class="part-label">Part 0 · Opening</p>

## Peta Alur Belajar

<table class="plain">
<tr><th>#</th><th>Part</th><th>Fokus Materi</th><th></th></tr>
<tr><td>0</td><td>Opening</td><td>Model Mental Backend</td><td></td></tr>
<tr><td>1</td><td>Survival Guide</td><td>Sintaks PHP &amp; Delta dari C</td><td></td></tr>
<tr><td>2</td><td>HTTP &amp; Form</td><td>Request/Response, GET &amp; POST</td><td><span class="badge badge-hands">Live #1</span></td></tr>
<tr><td>3</td><td>PDO + READ</td><td>Koneksi MySQL &amp; Tampilkan Data</td><td><span class="badge badge-hands">Live #2</span></td></tr>
<tr><td>4+5</td><td>CREATE + PRG</td><td>Form Tambah Data &amp; Session Flash</td><td></td></tr>
<tr><td>6</td><td>Security</td><td>Cegah SQL Injection &amp; XSS</td><td><span class="badge badge-hands">Live #3</span></td></tr>
<tr><td>7+8</td><td>UPDATE &amp; DELETE</td><td>Edit Data &amp; Operasi Hapus via POST</td><td></td></tr>
<tr><td>9+10</td><td>Validasi &amp; Capstone</td><td>Filter Input &amp; Tugas Mandiri</td><td></td></tr>
</table>

Note: **🗣️ Ngomong ke Peserta:**
"Ini peta alur materi kita. Kita bagi jadi topik-topik terarah dari pengenalan sampai pengamanan. Di sepanjang sesi, akan ada 3 demonstrasi live coding langsung di layar depan untuk melihat bagaimana kodenya dibuat dari nol. Kalian cukup fokus memperhatikan layar proyektor dan memahami alur logikanya. Di slide ini juga ada materi mandiri yang bisa kalian baca setelah sesi selesai."

**🎯 Poin Kunci di Layar:**
- Tekankan: Cuma tekan panah kanan (Core Track) selama presentasi. Jangan tekan panah bawah saat sesi live agar tidak keluar jalur.



<p class="part-label">Part 0 · Opening</p>

## Satu model mental untuk semuanya

<div class="flow">
<span>Browser</span><span class="arrow">&rarr;</span>
<span>HTTP Request</span><span class="arrow">&rarr;</span>
<span>PHP</span><span class="arrow">&rarr;</span>
<span>Validation</span><span class="arrow">&rarr;</span>
<span>SQL</span><span class="arrow">&rarr;</span>
<span>MySQL</span>
</div>
<div class="flow">
<span>MySQL</span><span class="arrow">&rarr;</span>
<span>PHP</span><span class="arrow">&rarr;</span>
<span>HTML Response</span><span class="arrow">&rarr;</span>
<span>Browser</span>
</div>

<div class="imgph">
<b>Image placeholder</b>
Diagram client&ndash;server: browser di kiri, web server + PHP di tengah, database MySQL di kanan, dengan panah request dan response bolak-balik.<br>
<i>Cari: "client server architecture diagram web application php mysql"</i>
</div>

Note: **🗣️ Ngomong ke Peserta:**
"Boleh minta tolong difoto slide ini sekarang? Ini peta jalan seluruh backend yang kita bahas hari ini. Alurnya selalu berputar: Browser ngirim request -> diterima PHP -> divalidasi -> dioper ke MySQL -> hasilnya diracik jadi HTML oleh PHP -> dikirim balik ke browser. Perhatikan: HTML itu adanya di ujung kanan sebagai output akhir. Browser kalian gak pernah tahu apa isi kode PHP di server, yang dia terima murni cuma HTML."

**🎯 Poin Kunci di Layar:**
- Tunjuk alur bolak-balik: Request datang dari kiri, berputar di server/MySQL, balik lagi ke browser sebagai response HTML.



<p class="part-label">Part 0 · Opening</p>

## Tujuan akhir: ini yang akan jadi

<div class="mock-browser">
<div class="bar">localhost:8001/index.php</div>
<div class="body">
<b>Student Management System</b>
<table>
<tr><th>Nama</th><th>Email</th><th>Jurusan</th><th></th></tr>
<tr><td>Ayu Lestari</td><td>ayu.lestari@campus.ac.id</td><td>Informatika</td><td>Edit &middot; Hapus</td></tr>
<tr><td>Bagas Prakoso</td><td>bagas.prakoso@campus.ac.id</td><td>Sistem Informasi</td><td>Edit &middot; Hapus</td></tr>
<tr><td>Citra Maharani</td><td>citra.m@campus.ac.id</td><td>Informatika</td><td>Edit &middot; Hapus</td></tr>
</table>
</div>
</div>

<p class="fineprint">Plain PHP + MySQL. Tanpa framework, tanpa Composer, tanpa OOP berat.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Nah, ini aplikasi yang bakal kita selesaikan hari ini: Student Management System. Kita sengaja pakai PHP murni tanpa framework dulu. Kenapa? Biar kalian paham jeroannya: cara nyambung database, cara nangani request form, dan cara ngamanin data. Begitu kalian paham fondasi ini, pindah ke Laravel atau framework apapun bakal jauh lebih gampang."

**🎯 Poin Kunci di Layar:**
- [Aksi Live]: Pindah tab ke browser, buka `http://localhost:8001/index.php`, coba tambah 1 data dan hapus 1 data (demo cepat 60 detik).



<p class="part-label">Part 0 · Opening</p>

## Aturan main

<div class="checkpoint">
<b>Fokus Menyimak &amp; Memahami Alur</b><br>
Sesi koding akan didemokan secara <span class="badge badge-hands">Live Coding</span> di layar depan. Fokus perhatikan alur logikanya &mdash; kamu tidak perlu panik mengetik atau takut tertinggal.
</div>

- Live #1 &mdash; Form HTML &rarr; pemrosesan `$_POST` di server <span class="fineprint">(Part 2)</span>
- Live #2 &mdash; Koneksi database PDO &rarr; `SELECT` &rarr; tabel data <span class="fineprint">(Part 3)</span>
- Live #3 &mdash; Simulasi serangan SQL Injection &amp; solusinya <span class="fineprint">(Part 6)</span>

<p class="fineprint"><b>Source code lengkap</b> akan dibagikan setelah sesi untuk bahan latihan dan capstone.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Aturan main kita hari ini sangat santai dan fokus: kalian gak perlu panik buru-buru ngetik di laptop. Semua bagian implementasi kode penting bakal saya demokan langsung lewat live coding di layar depan. Tugas utama kalian adalah memperhatikan alur logika, bagaimana alur data dari browser sampai ke database, dan mencatat hal-hal penting. Kodingan lengkapnya sudah disiapkan dan akan dibagikan setelah sesi, jadi kalian gak usah takut ketinggalan. Kalau ada pertanyaan soal topik lanjutan kayak OOP atau Laravel, kita catat dulu dan bahas tuntas di sesi tanya-jawab akhir ya."

**🎯 Poin Kunci di Layar:**
- Tekankan: Peserta fokus menyimak alur, pemateri yang live coding.
- Tenangkan peserta: Semua source code lengkap dibagikan di akhir sesi.
