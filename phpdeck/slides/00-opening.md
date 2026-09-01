<!-- .slide: id="part-0" -->
<p class="part-label">Part 0 · Opening</p>

# Dari C ke PHP

## Membangun Backend CRUD

<p class="fineprint">Peminatan Backend &middot; 150 menit &middot; sesi hybrid</p>

Note: Selamat datang. Sebelum mulai, dua hal operasional yang perlu dipastikan sekarang juga, bukan nanti di tengah sesi.

Pertama, deck ini dijalankan lewat `npm start` dan dibuka di `http://localhost:8000/phpdeck.html`. Kalau dibuka langsung lewat `file://`, file markdown-nya tidak akan termuat dan slide akan kosong.

Kedua, dan ini yang sering bikin kacau: **ada dua server berbeda hari ini.** Deck jalan di port 8000 (vite). Aplikasi PHP yang kita bangun jalan di server lain — XAMPP atau `php -S localhost:8001`. Dua-duanya harus sudah menyala sebelum menit pertama. Pastikan peserta juga sudah menjalankan `schema.sql` dan `seed.sql`.

Tanyakan sekarang: "Siapa yang belum berhasil import SQL?" Selesaikan di 2 menit pertama, jangan dibiarkan sampai Part 3.



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

Note: Tekankan kalimat terakhir. Peserta sudah tahu apa itu loop, apa itu function, apa itu tipe data. Mengulang itu buang-buang waktu dan membosankan.

Yang belum mereka punya bukan "syntax PHP", melainkan **model mental tentang bagaimana program dijalankan oleh server, satu request pada satu waktu**. Program C mereka selama ini: jalan dari `main()`, hidup terus, selesai, mati. Program PHP: lahir saat request datang, mati saat response terkirim. Itu lompatan konseptualnya, bukan `$` di depan variable.

Kalau ada satu hal yang harus mereka bawa pulang hari ini, itu adalah model mental di slide berikutnya — bukan hafalan fungsi.



<p class="part-label">Part 0 · Opening</p>

## Peta 150 menit

<table class="plain">
<tr><th>Menit</th><th>Part</th><th></th></tr>
<tr><td>0&ndash;8</td><td>Opening &amp; model mental</td><td></td></tr>
<tr><td>8&ndash;20</td><td>1 &middot; PHP Survival Guide</td><td></td></tr>
<tr><td>20&ndash;40</td><td>2 &middot; HTTP, GET/POST, form</td><td><span class="badge badge-hands">Ketik #1</span></td></tr>
<tr><td>40&ndash;68</td><td>3 &middot; PDO + READ</td><td><span class="badge badge-hands">Ketik #2</span></td></tr>
<tr><td>68&ndash;90</td><td>4+5 &middot; CREATE + PRG</td><td></td></tr>
<tr><td>90&ndash;110</td><td>6 &middot; Security</td><td><span class="badge badge-hands">Ketik #3</span></td></tr>
<tr><td>110&ndash;128</td><td>7+8 &middot; UPDATE + DELETE</td><td></td></tr>
<tr><td>128&ndash;150</td><td>9 &middot; Validation + capstone</td><td></td></tr>
</table>

Note: Tampilkan peta ini dan biarkan sebentar. Peserta perlu tahu bahwa sesi ini punya bentuk, bukan aliran materi tanpa ujung.

Sampaikan juga secara eksplisit bahwa **materi ini lebih panjang dari 150 menit**, dan itu memang disengaja. Ada dua jalur di deck ini:

- **CORE** — yang kita bahas bersama hari ini, jalur kiri-kanan.
- **SELF-STUDY** — slide tambahan di bawah setiap topik (tekan panah bawah), lengkap dengan penjelasan panjang untuk dibaca sendiri setelah sesi.

Jangan pernah menekan panah bawah saat presentasi. Jalur CORE dirancang supaya mulus kalau kamu hanya menekan panah kanan dari awal sampai akhir.



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

Note: Ini adalah benang merah seluruh sesi. Diagram ini akan muncul berulang kali, dan setiap kali satu tahap akan disorot untuk menunjukkan "kita sedang di sini".

Minta peserta memotret slide ini. Serius — suruh mereka foto. Di akhir sesi, target keberhasilan kita adalah mereka bisa menunjuk baris kode mana pun di aplikasi CRUD dan menjawab "ini ada di tahap mana pada diagram tadi".

Poin penting yang sering terlewat: perhatikan bahwa **HTML ada di ujung kanan, sebagai output**, bukan di awal. Peserta yang terbiasa HTML+CSS sering mengira PHP "ditempel" ke HTML. Yang benar sebaliknya: PHP yang berjalan, dan HTML adalah teks yang PHP hasilkan. Browser tidak pernah melihat kode PHP sama sekali.



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

Note: **Demokan aplikasi jadinya sekarang, sungguhan, bukan cuma slide ini.** Buka di browser, tambah satu data, edit, hapus. Total 90 detik.

Kenapa ini penting: peserta perlu melihat tujuan akhir sebelum dibawa melewati potongan-potongan kecil. Tanpa ini, Part 1 dan 2 terasa seperti materi acak. Dengan ini, setiap potongan punya tempat.

Sengaja tanpa framework. Kalau kita mulai dari Laravel, mereka akan bisa membuat CRUD tapi tidak tahu apa yang terjadi — dan saat error, mereka buntu. Plain PHP memaksa setiap tahap di diagram tadi terlihat sebagai kode yang bisa ditunjuk.

Sebutkan sekilas bahwa framework itu bagus dan akan dipelajari nanti; yang salah adalah mempelajarinya sebelum paham apa yang di-*abstract*.



<p class="part-label">Part 0 · Opening</p>

## Aturan main

<div class="checkpoint">
<b>3 kali kamu ikut mengetik</b><br>
Saat muncul badge <span class="badge badge-hands">Ketik bareng</span>, laptop dibuka, semua ikut. Di luar itu, tutup laptop dan perhatikan &mdash; supaya tidak tertinggal.
</div>

- Ketik #1 &mdash; form &rarr; `$_POST` <span class="fineprint">(Part 2)</span>
- Ketik #2 &mdash; `SELECT` &rarr; `foreach` &rarr; tabel HTML <span class="fineprint">(Part 3)</span>
- Ketik #3 &mdash; mencoba SQL Injection sendiri <span class="fineprint">(Part 6)</span>

<p class="fineprint"><b>Dua server:</b> deck di <code>:8000</code> &middot; aplikasi PHP di <code>:8001</code></p>

Note: Aturan "tutup laptop saat bukan sesi ketik" terdengar kaku tapi menyelamatkan sesi 150 menit. Penyebab nomor satu sesi seperti ini molor adalah peserta yang mengetik sambil mendengarkan, lalu tertinggal, lalu bertanya hal yang sudah dibahas.

Sampaikan bahwa kode lengkapnya akan dibagikan setelah sesi, jadi tidak perlu mengetik untuk "menyimpan". Mengetik hanya di tiga momen yang memang butuh pengalaman tangan sendiri.

Ulangi lagi soal dua server — ini sumber kebingungan yang paling sering muncul di menit ke-45. Deck bukan aplikasi. Aplikasi bukan deck. Tulis kedua URL di papan tulis kalau ada.

Terakhir, sebutkan aturan parkir: kalau ada pertanyaan yang melebar (OOP? Laravel? REST API?), catat di "parking lot" dan jawab di akhir kalau masih ada waktu. Jangan dijawab di tengah — itu pembunuh jadwal terbesar.
