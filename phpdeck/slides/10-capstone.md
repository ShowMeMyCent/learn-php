<!-- .slide: id="part-10" -->
<p class="part-label">Part 10 · Capstone <span class="badge badge-core">Core</span></p>

# Giliran Kamu

## Bangun CRUD tanpa dituntun

Note: **🗣️ Ngomong ke Peserta:**
"Selamat teman-teman! Kita sudah menyelesaikan semua materi dasar dari Part 0 sampai Part 9. Dari yang tadinya cuma kenal C, sekarang kalian sudah paham siklus HTTP, koneksi database PDO, pola PRG, sampai pengamanan data dan validasi.
Nah, sekarang giliran kalian: saatnya Capstone Project! Di sini kalian akan membuktikan bahwa kalian bukan cuma bisa meniru kode di layar, tapi benar-benar paham cara membangun sistem CRUD dari nol."

**🎯 Poin Kunci di Layar:**
- Beri apresiasi dan motivasi: overview capstone & Q&A.
- Tegaskan bahwa ini bukan tugas yang menakutkan, tapi pembuktian pemahaman mereka.



<p class="part-label">Part 10 · Capstone</p>

## Courses Management System

```sql
CREATE TABLE courses (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    code    VARCHAR(20)  NOT NULL,
    credits INT          NOT NULL
);
```

<div class="imgph">
<b>Image placeholder</b>
Diagram ERD sederhana tabel courses (id, name, code, credits), gaya visual serupa dengan yang dipakai untuk tabel students sebelumnya.<br>
<i>Buat sendiri dengan tool ERD sederhana, atau gambar tangan yang difoto</i>
</div>

<p class="fineprint">Struktur SENGAJA mirip <code>students</code> &mdash; kamu sudah tahu polanya.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Untuk proyek capstone ini, kalian akan membuat **Courses Management System** (Sistem Manajemen Mata Kuliah).
Lihat struktur tabelnya di layar: ada `id`, `name` (nama matkul), `code` (kode matkul seperti CS101), dan `credits` (jumlah SKS).
Perhatikan bahwa strukturnya mirip sekali dengan tabel `students` yang tadi kita buat. Ini sengaja! Kita tidak ingin membebani kalian dengan relasi database yang rumit dulu. Tujuannya adalah menguji apakah kalian bisa mentransfer pola yang sudah dipelajari ke entitas baru.
Boleh intip kode `students`, tapi target akhirnya: kalian harus bisa menjelaskan fungsi setiap baris kodenya, bukan sekadar asal copas dan jalan."

**🎯 Poin Kunci di Layar:**
- Tunjukkan tabel `courses`: 3 field utama (`name`, `code`, `credits`).
- Polanya 100% sama: Form -> `$_POST` -> Prepared Statement -> PRG -> Validasi.



<p class="part-label">Part 10 · Capstone</p>

## Milestone

<table class="plain">
<tr><th>#</th><th>Target</th></tr>
<tr><td>1</td><td>Schema + koneksi + tampilkan daftar courses (READ)</td></tr>
<tr><td>2</td><td>Form tambah course baru (CREATE + PRG)</td></tr>
<tr><td>3</td><td>Edit course (UPDATE)</td></tr>
<tr><td>4</td><td>Hapus course, wajib POST (DELETE)</td></tr>
<tr><td>5</td><td>Validasi + prepared statement + escape di SEMUA fitur</td></tr>
</table>

<p class="fineprint">Detail lengkap tiap milestone: <code>phpdeck/docs/capstone-guide.md</code></p>

Note: **🗣️ Ngomong ke Peserta:**
"Supaya tidak pusing, jangan langsung bikin semuanya sekaligus. Kerjakan bertahap lewat 5 milestone ini:
1. **Milestone 1:** Buat tabelnya di MySQL, buat file koneksi `db.php`, dan tampilkan daftar course (READ).
2. **Milestone 2:** Bikin form tambah course dengan pola PRG (CREATE).
3. **Milestone 3:** Bikin form edit yang otomatis terisi data lama (UPDATE).
4. **Milestone 4:** Bikin tombol hapus dengan form POST (DELETE).
5. **Milestone 5:** Pasang validasi input dan pastikan `htmlspecialchars()` serta prepared statement terpasang di semua fitur.
Panduan detail dan checklist lengkap per milestone ada di file `phpdeck/docs/capstone-guide.md`."

**🎯 Poin Kunci di Layar:**
- Urutan milestone sama persis dengan urutan materi workshop: READ -> CREATE -> UPDATE -> DELETE -> Validasi & Keamanan.



<p class="part-label">Part 10 · Capstone</p>

## Definition of Done

```text
[ ] Semua query dengan input user pakai prepare()+execute()
[ ] Semua output ke HTML di-escape htmlspecialchars()
[ ] Create/Update/Delete pakai POST, diikuti PRG + flash message
[ ] Form tervalidasi di server: required, panjang, format
[ ] Bisa jelaskan SETIAP baris tanpa membuka source code students/
```

<p class="fineprint">Baris terakhir adalah yang paling penting &mdash; bukan sekadar "jalan".</p>

Note: **🗣️ Ngomong ke Peserta:**
"Kapan proyek kalian dianggap tuntas? Ini dia **Definition of Done**-nya:
Pertama, keamanan: semua query yang menerima input user wajib pakai prepared statement, dan semua output ke layar wajib pakai `htmlspecialchars()`.
Kedua, arsitektur: perubahan data wajib lewat method POST dan mengikuti pola PRG dengan flash message.
Ketiga, validasi: form divalidasi di PHP sisi server.
Dan yang paling penting adalah poin terakhir: kalian bisa menjelaskan cara kerja kode kalian ke orang lain tanpa bingung. Coba tes jelaskan ke teman di sebelah kalian: 'baris ini buat apa, alurnya gimana'. Kalau teman kalian paham, artinya kalian beneran menguasai backend!"

**🎯 Poin Kunci di Layar:**
- Tekankan baris terakhir: keberhasilan backend bukan cuma 'program jalan', tapi programmer-nya paham alur di balik layar.



<p class="part-label">Part 10 · Capstone</p>

## Setelah sesi ini: cara pakai deck

<div class="imgph">
<b>Image placeholder</b>
Screenshot deck reveal.js dengan indikator panah bawah (down arrow) yang menandakan ada slide self-study tambahan di bawah slide saat ini.<br>
<i>Screenshot langsung dari deck ini saat presentasi navigasi controls</i>
</div>

<div class="checkpoint">
Tekan <b>panah bawah</b> di slide mana pun untuk membuka slide <span class="badge badge-self">Self-Study</span> &mdash; penjelasan lebih dalam + checkpoint question, bisa dibaca sendiri.
</div>

Note: **🗣️ Ngomong ke Peserta:**
"Setelah sesi hari ini selesai, slide ini masih bisa kalian pakai untuk belajar mandiri di rumah.
Cara pakainya gampang:
Di slide mana pun, kalian bisa tekan tombol **panah bawah (↓)** pada keyboard untuk membuka slide **Self-Study** (yang ada badge kuning). Di situ pembahasannya jauh lebih dalam, ada studi kasus, dan ada pertanyaan checkpoint.
Kalian juga bisa tekan tombol **'S'** di keyboard untuk membuka **Speaker Notes**. Di situ ada rangkuman penjelasan lengkap dan contekan jawaban seolah-olah saya sedang mendampingi kalian belajar langsung."

**🎯 Poin Kunci di Layar:**
- Panah bawah (↓) = Buka materi Self-Study mendalam.
- Tombol 'S' = Buka Presenter View / Speaker Notes untuk narasi penjelasan mandiri.



<p class="part-label">Part 10 · Capstone</p>

## Penutup

<div class="flow">
<span class="on">Browser</span><span class="arrow">&rarr;</span>
<span class="on">HTTP Request</span><span class="arrow">&rarr;</span>
<span class="on">PHP</span><span class="arrow">&rarr;</span>
<span class="on">Validation</span><span class="arrow">&rarr;</span>
<span class="on">SQL</span><span class="arrow">&rarr;</span>
<span class="on">MySQL</span>
</div>
<div class="flow">
<span class="on">MySQL</span><span class="arrow">&rarr;</span>
<span class="on">PHP</span><span class="arrow">&rarr;</span>
<span class="on">HTML Response</span><span class="arrow">&rarr;</span>
<span class="on">Browser</span>
</div>

## Kalau kamu bisa menunjuk baris kode dan bilang "ini ada di tahap mana" &mdash; kamu sudah paham backend.

<p class="fineprint">Pertanyaan? Sekarang saatnya.</p>

<p class="downhint">4 slide tambahan: FAQ setup, cara submit capstone, rubrik penilaian, ide pengembangan lanjutan</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sebagai penutup, coba kita lihat diagram ini sekali lagi. Ini diagram yang sama persis dengan yang kita lihat di awal materi tadi:
Browser kirim HTTP Request -> masuk ke PHP -> divalidasi -> diolah jadi query SQL ke MySQL -> database kembalikan data -> PHP bungkus jadi respons HTML -> dikirim balik ke browser.
Bedanya, kalau tadi pagi diagram ini terasa asing, sekarang kalian sudah pegang kodenya langsung baris per baris.
Kalau kalian bisa melihat kode kalian dan tahu: 'oh, baris ini ada di tahap Request, baris ini di tahap Validasi, baris ini di tahap SQL', selamat! Fondasi backend kalian sudah kuat.
Sebelum kita akhiri, ada pertanyaan atau hal yang masih membingungkan? Silakan tanyakan sekarang."

**🎯 Poin Kunci di Layar:**
- Diagram alur request-response lengkap: siklus belajar hari ini sudah tuntas.
- Buka sesi tanya jawab (Q&A) untuk peserta.


<p class="part-label">Part 10 · Self-Study</p>

## FAQ: setup XAMPP / server lokal

```text
Q: Kenapa aplikasi PHP saya blank putih, tidak ada error sama sekali?
A: Aktifkan display_errors (Part 1 self-study) atau cek error log XAMPP.

Q: "Connection refused" saat konek database?
A: Pastikan service MySQL di XAMPP Control Panel berstatus "Running".

Q: File .md di deck ini tidak muncul, slide kosong?
A: Jalankan lewat "npm start", buka http://localhost:8000/phpdeck.html
   Jangan dibuka langsung dari file explorer (file://).
```

Note: Kumpulan pertanyaan yang paling sering muncul saat peserta mencoba setup ulang di rumah, di luar sesi live di mana instruktur bisa langsung membantu. Dorong peserta menambahkan pertanyaan mereka sendiri ke daftar ini kalau menemukan masalah baru — dokumen ini idealnya hidup dan berkembang seiring peserta lain juga mengalami kendala serupa.


<p class="part-label">Part 10 · Self-Study</p>

## Cara submit capstone

```text
1. Buat folder "courses" (sejajar dengan folder "students")
2. Sertakan: schema tambahan untuk tabel courses (courses.sql)
3. File: index.php, create.php, edit.php, delete.php
4. Tanpa framework, tanpa Composer -- sama seperti students/
5. Kumpulkan sebagai .zip ATAU link repository (kalau sudah familiar Git)
```

<p class="fineprint">Detail lengkap ada di <code>phpdeck/docs/capstone-guide.md</code>.</p>

Note: Struktur pengumpulan ini SENGAJA meniru persis struktur folder `students/` yang sudah mereka lihat contohnya sepanjang sesi (`phpdeck/project/`) — tidak ada kejutan format baru yang perlu dipelajari terpisah dari materi CRUD itu sendiri.

Kalau kelas ini sudah familiar dengan Git/GitHub dari materi lain, opsi link repository lebih disukai karena memudahkan pemberian feedback per baris kode. Kalau belum, .zip biasa sudah cukup — jangan jadikan tooling sebagai penghalang untuk menyelesaikan capstone.


<p class="part-label">Part 10 · Self-Study</p>

## Rubrik penilaian (gambaran umum)

<table class="plain">
<tr><th>Aspek</th><th>Bobot</th></tr>
<tr><td>CRUD berfungsi penuh (4 operasi)</td><td>40%</td></tr>
<tr><td>Prepared statement di semua query</td><td>20%</td></tr>
<tr><td>Escape output + validasi input</td><td>20%</td></tr>
<tr><td>PRG pattern pada create/update/delete</td><td>10%</td></tr>
<tr><td>Kemampuan menjelaskan kode sendiri</td><td>10%</td></tr>
</table>

Note: Rubrik ini sengaja MENCERMINKAN checklist "Definition of Done" yang sudah ditunjukkan sebelumnya — tidak ada kriteria tersembunyi atau mengejutkan. Sesuaikan bobot persentase ini dengan kebijakan penilaian program/kelas masing-masing; angka di atas adalah TITIK AWAL yang wajar, bukan aturan baku.

"Kemampuan menjelaskan kode sendiri" sengaja tetap diberi bobot eksplisit meski kecil (10%) — untuk menegaskan bahwa CRUD yang berfungsi tapi disalin tanpa dipahami TETAP dianggap belum sepenuhnya berhasil, sesuai filosofi materi sejak awal.


<p class="part-label">Part 10 · Self-Study</p>

## Ke mana setelah capstone selesai?

```text
- Login + session-gated CRUD (pratinjau: Part 5 self-study)
- Pagination dengan LIMIT/OFFSET (pratinjau: Part 3 self-study)
- Relasi students <-> courses lewat tabel enrollments
  (pratinjau: Part 8 self-study, foreign key ON DELETE)
- Baru SETELAH ini semua terasa lancar: OOP PHP, MVC, Composer, framework
```

<p class="fineprint">Urutan ini bukan kebetulan &mdash; setiap topik lanjutan lebih masuk akal SETELAH fondasi ini benar-benar melekat.</p>

Note: Ini peta jalan untuk peserta yang ingin melangkah lebih jauh setelah capstone — SEMUA sudah disinggung sebagai pratinjau di slide self-study sepanjang sesi ini, jadi tidak ada yang benar-benar baru, hanya perlu didalami.

Tutup dengan pesan yang konsisten sejak Opening: OOP, MVC, Composer, dan framework SENGAJA ditunda, bukan diabaikan selamanya. Alasannya sekarang seharusnya sudah terasa jelas bagi peserta sendiri, bukan hanya kata-kata instruktur — mereka baru saja mengalami langsung betapa berharganya memahami APA yang sebenarnya terjadi di balik setiap baris kode, sebelum nanti itu semua "disembunyikan" rapi oleh abstraksi framework.
