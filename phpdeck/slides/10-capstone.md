<!-- .slide: id="part-10" -->
<p class="part-label">Part 10 · Capstone <span class="badge badge-core">Core</span></p>

# Giliran Kamu

## Bangun CRUD tanpa dituntun

Note: Ini Part penutup CORE — sekitar 8 menit, jangan sampai molor karena ini juga waktu untuk Q&A yang biasanya dibutuhkan peserta sebelum sesi berakhir.

Nada slide ini penting: bukan "materi tambahan", tapi UJIAN SESUNGGUHNYA dari 150 menit yang baru saja dilalui. Sampaikan dengan nada menantang tapi memberi semangat, bukan menakut-nakuti.



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

Note: Entity `courses` dipilih SENGAJA karena bentuknya paralel dengan `students` — tiga kolom teks/angka sederhana, tanpa relasi kompleks. Ini bukan kebetulan: tujuannya BUKAN menguji hal baru, tapi menguji apakah pola yang sudah dipelajari (form → $_POST → prepared statement → PRG → validasi) bisa DITRANSFER ke konteks berbeda tanpa menyalin-tempel dari `students`.

Tekankan: menyalin file `index.php` milik `students` lalu mengganti nama variable adalah AWAL yang wajar, tapi target sesungguhnya adalah bisa menjelaskan SETIAP baris yang ditulis — bukan hanya berhasil menjalankannya.



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

Note: Lima milestone ini SENGAJA mengikuti urutan Part yang baru saja dipelajari (READ dulu, baru CREATE, dst) — pola yang sama yang membuat sesi hari ini terasa terarah juga berlaku untuk cara mereka membangun capstone sendiri.

Dokumen `capstone-guide.md` (dibagikan terpisah, lebih detail dari slide ini) berisi acceptance criteria konkret per milestone dan hint bertingkat kalau mereka benar-benar buntu — tapi dorong mereka mencoba dulu tanpa membuka hint, itu yang membuat transfer pemahaman benar-benar terjadi.



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

Note: Checklist ini sengaja menggabungkan checklist keamanan dari Part 6 self-study dengan satu kriteria tambahan yang paling menentukan: KEMAMPUAN MENJELASKAN, bukan hanya keberhasilan menjalankan program.

Ini kriteria yang membedakan "mengikuti tutorial" dari "memahami backend" — persis tujuan yang disampaikan mentor sejak awal perancangan materi ini. Sarankan peserta mencoba menjelaskan kode mereka ke teman sebangku/rekan belajar sebagai bentuk verifikasi mandiri sebelum menganggap capstone selesai.



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

Note: Ingatkan sekali lagi bahwa deck ini TIDAK berhenti di slide CORE yang baru saja dilalui — setiap Part punya slide tambahan (ditandai badge kuning "Self-Study") yang bisa diakses dengan menekan panah bawah, lengkap dengan penjelasan yang ditulis selengkap mungkin untuk dibaca tanpa pengajar, plus pertanyaan checkpoint beserta jawabannya di speaker notes.

Sebutkan juga bahwa speaker notes (tombol "S" di keyboard untuk membuka speaker view) berisi narasi lengkap seolah instruktur sedang menjelaskan — dorong peserta membuka speaker notes saat belajar mandiri, bukan hanya membaca slide-nya saja.



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

Note: Tutup dengan mengembalikan diagram yang sama persis dari slide Opening — full lingkaran selesai. Ini penutup yang secara sengaja SIMETRIS dengan pembukaan, memberi rasa "perjalanan yang utuh" ke peserta.

Buka sesi Q&A di sini. Kalau ada pertanyaan dari "parking lot" (Opening, aturan main) yang belum terjawab, ini waktunya. Kalau waktu benar-benar habis, arahkan mereka ke slide self-study yang relevan atau ke `capstone-guide.md`.


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
