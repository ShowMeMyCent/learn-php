# Outline Slide-by-Slide — "Dari C ke PHP: Membangun Backend CRUD"

Dokumen perencanaan untuk deck reveal.js di `phpdeck.html` / `phpdeck/slides/*.md`.
Bacaan cepat (scan) — untuk narasi speaker notes LENGKAP, buka deck-nya langsung
(tekan `S` untuk speaker view) karena versi di sana ditulis lebih detail.

Legenda: `[CORE]` = dipresentasikan dalam 150 menit &middot; `[SELF]` = dibaca mandiri
setelah sesi &middot; `[HANDS-ON]` = peserta ikut mengetik.

Total: **125 slide** — 76 CORE (~95 menit presentasi + 39 menit hands-on + 10 menit
live-coding koneksi + 6 menit opening &asymp; 150 menit) + 49 SELF-STUDY.

---

## Part 0 — Opening `[CORE: 6]`

### SLIDE 1
Title: Dari C ke PHP — Membangun Backend CRUD
Main Content: Judul, konteks peminatan Backend, 150 menit, sesi hybrid.
[IMAGE PLACEHOLDER]
Diagram/hero image tema "backend development", opsional dekoratif ringan di judul.
Speaker Notes: Pastikan dua server (deck + aplikasi PHP) sudah menyala dan `schema.sql`/`seed.sql` sudah di-import SEBELUM mulai. Selesaikan kendala setup di 2 menit pertama.

### SLIDE 2
Title: Kamu sudah punya bekalnya
Main Content: Tabel dua kolom — yang sudah dikuasai (HTML/CSS, C, SQL dasar) vs yang ditambahkan hari ini (PHP server-side, HTTP, PDO, CRUD, security dasar).
Speaker Notes: Tekankan ini bukan kursus PHP dari nol — ini *delta*. Yang belum dikuasai bukan syntax, tapi model mental request-response.

### SLIDE 3
Title: Peta 150 menit
Main Content: Tabel jadwal per Part dengan menit dan badge hands-on.
Speaker Notes: Jelaskan dua track: CORE (horizontal, dipresentasikan) dan SELF-STUDY (vertical, dibaca mandiri). Jangan pernah tekan panah bawah saat presentasi.

### SLIDE 4
Title: Satu model mental untuk semuanya
Main Content: Diagram flow Browser → HTTP Request → PHP → Validation → SQL → MySQL → PHP → HTML Response → Browser.
[IMAGE PLACEHOLDER]
Diagram client-server: browser, web server+PHP, MySQL, dengan panah request/response.
Speaker Notes: Ini benang merah seluruh sesi, akan muncul berulang dengan tahap yang sedang dibahas disorot. Minta peserta memotret slide ini. Tekankan HTML ada di ujung KANAN (output), bukan di awal.

### SLIDE 5
Title: Tujuan akhir: ini yang akan jadi
Main Content: Mockup browser menampilkan Student Management System (list/create/edit/delete).
[IMAGE PLACEHOLDER]
Screenshot aplikasi CRUD jadi, atau demo langsung (lebih disarankan).
Speaker Notes: Demokan aplikasi jadi SUNGGUHAN sekarang (90 detik) — tambah, edit, hapus data. Plain PHP + MySQL, tanpa framework, sengaja, supaya tiap tahap mental model terlihat sebagai kode nyata.

### SLIDE 6
Title: Aturan main
Main Content: 3 momen hands-on (form→$_POST, SELECT→foreach→tabel, coba SQL Injection sendiri), aturan tutup laptop di luar sesi ketik, dua server (:8000 deck, :8001 app).
Speaker Notes: Aturan "tutup laptop saat bukan sesi ketik" menyelamatkan jadwal 150 menit. Sebutkan aturan parking lot untuk pertanyaan yang melebar (OOP, Laravel, REST API).

---

## Part 1 — PHP Survival Guide `[CORE: 8, SELF: 6]`

### SLIDE 7 `[CORE]`
Title: PHP dalam 12 menit
Main Content: Bagian ini cepat by design — bukan kursus syntax, hanya delta dari C.
Speaker Notes: "Saya tidak akan mengajarkan apa itu variable/if/loop — kalian sudah tahu dari C. Hanya yang BEDA."

### SLIDE 8 `[CORE]`
Title: CONCEPT — PHP itu apa?
Main Content: Server-side, interpreted, `<?php ?>` disisipkan ke HTML.
Code: `<?php $pesan = "Halo dari server"; ?> <h1><?= $pesan ?></h1>`
Speaker Notes: `<?= $x ?>` = singkatan `<?php echo $x; ?>`. Baris PHP murni tidak menghasilkan output; baris HTML biasa menyisipkan nilai variable.

### SLIDE 9 `[CORE]`
Title: WHY — kenapa beda dari C?
Main Content: Diagram: C dikompilasi, proses hidup terus vs PHP dijalankan ulang tiap request, mati setelah kirim response.
Speaker Notes: Konsep paling penting Part 1. PHP tidak "mengingat" apa pun dari request sebelumnya secara otomatis — motivasi untuk database (data permanen) dan session (data sementara, Part 5).

### SLIDE 10 `[CORE]`
Title: SEE IT RUN
Main Content: `hello.php` dijalankan, hasilnya di mock browser, lalu View Page Source untuk buktikan tidak ada kode PHP di HTML yang diterima browser.
Code: `<?php $nama = "Dunia"; ?> <h1>Halo, <?= $nama ?>!</h1>`
Speaker Notes: Jalankan sungguhan, buka View Page Source — bukti visual model mental "PHP → HTML sebelum sampai ke browser".

### SLIDE 11 `[CORE]`
Title: Variable: C vs PHP
Main Content: Perbandingan berdampingan (`.cmp`) — `int umur=20` vs `$umur=20`, dynamic typing, tanda `$`.
Speaker Notes: Dynamic typing itu pedang bermata dua — cepat ditulis, tapi bug tipe baru ketahuan saat runtime. Alasan Part 9 (Validation) penting.

### SLIDE 12 `[CORE]`
Title: String: interpolasi
Main Content: `"Halo $nama"` interpolasi vs `.` concat, C `printf`/`strcat`.
Speaker Notes: `.` adalah operator concat, bukan `+`. Typo umum: `"Halo " + $nama`.

### SLIDE 13 `[CORE]`
Title: Associative array — yang paling penting hari ini
Main Content: `$mhs = ['name'=>..., 'email'=>..., 'major'=>...]` vs array indeks C.
Question: (implisit, ditandai `.ask`) Ingat baik-baik — bentuk ini persis hasil query database nanti.
Speaker Notes: Slide TERPENTING Part 1. `$row['kolom']` akan muncul di hampir setiap file PHP hari ini.

### SLIDE 14 `[CORE]`
Title: foreach — pola yang akan dipakai terus
Main Content: `foreach ($mahasiswa as $mhs) { echo $mhs["name"]; }` vs C `for` loop.
Speaker Notes: Gabungan associative array + foreach = pola menampilkan data siswa di Part 3.

### SLIDE 15 `[CORE]`
Title: Recap kilat
Main Content: 4 poin ringkasan; jembatan ke Part 2 ("dari mana data $_POST datang?").
Speaker Notes: Kalau waktu longgar, ini titik aman singgah ke self-study `==` vs `===`.

### SLIDE 16 `[SELF]`
Title: `==` vs `===`, dan `var_dump`
Code: `0 == "abc"` vs `0 === "abc"`; `var_dump($_POST)`.
Question: Kenapa `===` lebih aman untuk membandingkan input form?
Speaker Notes: Jawaban — input form selalu string, `==` mengonversi tipe otomatis (kejutan), `===` tidak.

### SLIDE 17 `[SELF]`
Title: Function: nyaris sama seperti C
Code: `function hitungRataRata(array $nilai): float { ... }`
Speaker Notes: Beda dari C: kata kunci `function`, tipe parameter/kembalian opsional (type hinting).

### SLIDE 18 `[SELF]`
Title: Array toolkit yang sering dipakai
Code: `count()`, `in_array()`, `array_push()`/`[]=`, `empty()`, `isset()`, `array_map()`, `array_filter()`.
Speaker Notes: `isset()` vs `empty()` sering tertukar — `empty()` juga menganggap `""`, `0`, `[]` sebagai kosong.

### SLIDE 19 `[SELF]`
Title: Superglobals — pratinjau Part 2
Main Content: `$_GET`, `$_POST`, `$_SERVER`, `$_SESSION`, `$_FILES`.
Speaker Notes: Semuanya associative array biasa. Murni pratinjau nama, dibahas detail di Part 2.

### SLIDE 20 `[SELF]`
Title: include / require
Code: `require 'config/database.php';` vs `include 'partials/header.php';`
Speaker Notes: Beda dari C `#include` (compile-time) — PHP runtime, bisa di dalam `if`/loop. `require` untuk file yang wajib ada (koneksi DB).

### SLIDE 21 `[SELF]`
Title: error_reporting saat belajar
Code: `error_reporting(E_ALL); ini_set('display_errors', 1);`
Question: Kenapa berbahaya kalau `display_errors` menyala di production?
Speaker Notes: Jawaban — membocorkan path server, nama tabel, potongan query ke publik. Mode belajar saja, bukan kebiasaan production.

---

## Part 2 — PHP, HTML & HTTP `[CORE: 8, SELF: 6]` — HANDS-ON #1

### SLIDE 22 `[CORE]`
Title: Dari Form ke PHP
Main Content: Bagian paling fundamental — bagaimana form terhubung ke PHP.
Speaker Notes: Bongkar "sihir" form → PHP jadi mekanisme HTTP request yang konkret.

### SLIDE 23 `[CORE]`
Title: CONCEPT — program C kamu vs PHP
Main Content: `.cmp` — input dari keyboard/scanf (C) vs input dari HTTP request (PHP).
Speaker Notes: PHP tidak "menunggu" seperti scanf — hanya membaca apa yang sudah dikirim browser.

### SLIDE 24 `[CORE]`
Title: GET vs POST
Main Content: Tabel perbandingan — data di URL vs body, terlihat vs tidak, baca vs ubah.
[IMAGE PLACEHOLDER]
Diagram GET (data di URL) vs POST (data di body).
Speaker Notes: Aturan praktis: GET untuk baca, POST untuk ubah/hapus/kirim. Relevan lagi di Part 5 & 8.

### SLIDE 25 `[CORE]`
Title: Form HTML: `name` menjadi kunci
Code: `<input type="text" name="nama">`
Question: (ask) Atribut `name` BUKAN sekadar label — jadi KUNCI array di PHP.
Speaker Notes: `name="nama"` di HTML → `$_POST['nama']` di PHP. Sambungan langsung ke associative array Part 1.

### SLIDE 26 `[CORE]`
Title: PHP menerima: `$_POST`
Code: `$nama = $_POST['nama'];`
Speaker Notes: Tidak ada sintaks baru — hanya variable baru yang otomatis diisi PHP. Error umum: form pakai GET tapi baca `$_POST`.

### SLIDE 27 `[CORE]`
Title: Guard: pastikan ini benar-benar POST
Code: `if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... }`
Speaker Notes: Mencegah error "Undefined array key" saat halaman pertama dibuka (masih GET). Pola ini berulang di create.php/edit.php.

### SLIDE 28 `[CORE][HANDS-ON]`
Title: Ketik bareng #1 — Form → $_POST
Main Content: Buat `hello_form.php`, lengkapi TODO: if-guard, ambil `$_POST`, echo sapaan, `var_dump`.
Code: kerangka TODO (lihat `02-http.md`).
Speaker Notes: ~8 menit. Tulis checklist di papan, jangan didikte. Kesalahan umum: lupa `method="POST"`, `name` tidak cocok, lupa guard.

### SLIDE 29 `[CORE]`
Title: Checkpoint #1
Main Content: Mock browser: "Halo, Ayu! Jurusan Informatika." + `var_dump($_POST)`.
Speaker Notes: Minta 2-3 peserta share screen untuk kalibrasi. Array kosong = method bukan POST.

### SLIDE 30 `[CORE]`
Title: Recap & jembatan
Main Content: Diagram flow dengan 3 tahap pertama disorot.
Speaker Notes: Progres psikologis penting. Titik pemangkasan aman kalau tertinggal jadwal.

### SLIDE 31 `[SELF]`
Title: Isi `$_SERVER` yang berguna
Code: `REQUEST_METHOD`, `REQUEST_URI`, `HTTP_HOST`, `SCRIPT_NAME`, `REMOTE_ADDR`.
Speaker Notes: `REMOTE_ADDR` bisa dipalsukan lewat proxy — jangan jadi satu-satunya mekanisme keamanan.

### SLIDE 32 `[SELF]`
Title: Kapan GET, kapan POST — aturan main sebenarnya
Question: Kenapa search sebaiknya GET, padahal juga "mengirim data"?
Speaker Notes: GET = "safe method" HTTP — boleh di-bookmark/prefetch/refresh. Kalau delete lewat GET link, crawler/prefetch bisa tidak sengaja memicu (bahasan penuh di Part 8).

### SLIDE 33 `[SELF]`
Title: isset() dan null coalescing `??`
Code: `$q = $_GET['q'] ?? '';`
Speaker Notes: Pola default untuk parameter opsional. Beda `isset()` (ada/tidak) vs `empty()` (kosong/tidak).

### SLIDE 34 `[SELF]`
Title: Sekilas: redirect dengan header()
Code: `header('Location: index.php'); exit;`
Speaker Notes: Pratinjau syntax saja — konteks penuh (kenapa butuh redirect) baru masuk akal di Part 5.

### SLIDE 35 `[SELF]`
Title: Status code, sekilas
Main Content: 200, 302, 404, 500 + tip DevTools Network tab.
Speaker Notes: 500 akan sering muncul saat koneksi DB gagal di Part 3.

### SLIDE 36 `[SELF]`
Title: Trace satu roundtrip HTTP utuh
Main Content: 6 langkah GET→render→POST→proses→balas→render.
Question: Di langkah mana PHP benar-benar "berjalan"?
Speaker Notes: Jawaban — hanya saat menyusun response (server-side); render di browser PHP sudah "mati".

---

## Part 3 — PDO + READ `[CORE: 11, SELF: 7]` — Live coding + HANDS-ON #2

### SLIDE 37 `[CORE]`
Title: PHP bertemu MySQL
Main Content: READ diajarkan dulu, sebelum CREATE — closed-loop paling sederhana.
Speaker Notes: Part terpanjang Core Track (~28 menit). Cek ulang semua sudah import schema+seed.

### SLIDE 38 `[CORE]`
Title: CONCEPT — PDO sebagai jembatan
Main Content: PHP ↔ PDO ↔ MySQL; kenapa PDO bukan mysqli (satu API lintas database).
Speaker Notes: Keputusan satu kalimat, lanjut praktik — tidak diperdebatkan panjang.

### SLIDE 39 `[CORE]`
Title: Yang sudah kamu punya: schema saja
Code: `CREATE TABLE students (id, name, email, major)`
Speaker Notes: Starter hanya schema+seed, sengaja — koneksi ditulis live 3 slide berikutnya.

### SLIDE 40 `[CORE][HANDS-ON]`
Title: Live bareng — config/database.php — DSN
Code: `$dsn = "mysql:host=$host;dbname=$db;charset=$charset";`
Speaker Notes: Live coding, ketik perlahan. `127.0.0.1` bukan `localhost` (masalah socket Windows).

### SLIDE 41 `[CORE][HANDS-ON]`
Title: Live bareng — opsi PDO yang wajib
Code: `PDO::ATTR_ERRMODE`, `ATTR_DEFAULT_FETCH_MODE`, `ATTR_EMULATE_PREPARES => false`.
Speaker Notes: Tiap opsi punya alasan konkret — jelaskan satu-satu, bukan boilerplate hafalan. `EMULATE_PREPARES=false` relevan lagi di Part 6.

### SLIDE 42 `[CORE][HANDS-ON]`
Title: Live bareng — menangkap kegagalan koneksi
Code: `try { new PDO(...) } catch (PDOException $e) { die(...); }`
Speaker Notes: `database.php` selesai, di-require dari semua file lain.

### SLIDE 43 `[CORE]`
Title: READ pertama: query() → fetchAll()
Code: `$students = $pdo->query("SELECT * FROM students")->fetchAll();`
Speaker Notes: Gambarkan bentuk data di papan. `query()` HANYA aman tanpa input user.

### SLIDE 44 `[CORE]`
Title: Array → HTML table
Code: `foreach ($students as $s): ?><tr><td><?= $s['name'] ?></td>...`
Speaker Notes: Gabungan langsung Part 1 (assoc array+foreach) + hasil query. Jelaskan syntax alternatif `foreach(...): ... endforeach;`.

### SLIDE 45 `[CORE][HANDS-ON]`
Title: Ketik bareng #2 — SELECT → foreach → tabel
Main Content: Buat `index.php` lengkap: require, query, foreach → tabel HTML.
Speaker Notes: ~10 menit. Kesalahan umum: lupa require, path salah, nama kolom typo, MySQL belum jalan.

### SLIDE 46 `[CORE]`
Title: Checkpoint #2
Main Content: Tabel dengan TEPAT 6 baris sesuai seed.sql.
Speaker Notes: Rayakan momen ini — closed-loop pertama yang benar-benar melibatkan database.

### SLIDE 47 `[CORE]`
Title: Recap: pola baca
Main Content: require → query+fetchAll → foreach = "rumus" yang diulang sepanjang sesi.
Speaker Notes: Jembatan ke Part 4: "bagaimana data BISA ada di database sejak awal?"

### SLIDE 48 `[SELF]`
Title: fetch() vs fetchAll()
Speaker Notes: `fetchAll()` cukup untuk data kecil-menengah; `fetch()` dalam while-loop untuk dataset sangat besar.

### SLIDE 49 `[SELF]`
Title: DSN dibedah lebih detail
Question: Kalau MySQL jalan di port lain, apa yang ditambahkan ke DSN?
Speaker Notes: Jawaban — `;port=NNNN`. Juga bahas utf8 vs utf8mb4.

### SLIDE 50 `[SELF]`
Title: PDOException — cara membacanya
Main Content: Pola pesan umum: Access denied, Unknown database, Connection refused.
Speaker Notes: Latih membaca pesan error, bukan panik — skill debugging dasar.

### SLIDE 51 `[SELF]`
Title: query() vs prepare() — kapan pakai yang mana
Main Content: Aturan: ada `$` milik user di SQL → wajib prepare().
Speaker Notes: Pratinjau Part 4, pembahasan penuh di Part 6.

### SLIDE 52 `[SELF]`
Title: Bagaimana kalau tabel kosong?
Code: `if (empty($students)): ... pesan "Belum ada data" ...`
Speaker Notes: `fetchAll()`/`foreach` pada array kosong tidak error — hanya nol iterasi. Tanda aplikasi matang.

### SLIDE 53 `[SELF]`
Title: Pratinjau: LIMIT untuk data besar
Code: `SELECT * FROM students ORDER BY name LIMIT 10 OFFSET 20;`
Speaker Notes: Dasar pagination. Arah pengembangan capstone (Part 10).

### SLIDE 54 `[SELF]`
Title: Struktur file proyek
[IMAGE PLACEHOLDER]
Screenshot VS Code file tree proyek PHP.
Speaker Notes: Struktur sengaja datar (flat), bukan MVC — motivasi framework baru masuk akal setelah merasakan batasnya sendiri.

---

## Part 4 — CREATE `[CORE: 7, SELF: 4]`

### SLIDE 55 `[CORE]`
Title: CREATE
Main Content: Data baru: dari form ke tabel. Fondasi sudah ada, tinggal sambungkan ke INSERT.
Speaker Notes: Part lebih cepat dari Part 3.

### SLIDE 56 `[CORE]`
Title: CONCEPT — C di CRUD
[IMAGE PLACEHOLDER]
Diagram siklus CRUD, "Create" disorot.
Main Content: Flow Form→POST→PHP→Validation→INSERT→MySQL.
Speaker Notes: Perpanjangan dari yang sudah dikuasai; hanya INSERT + eksekusi aman lewat PDO yang baru.

### SLIDE 57 `[CORE]`
Title: Form create.php
Code: `<form method="POST" action="create.php"> <input name="name">...`
Speaker Notes: Tidak ada input `id` — MySQL AUTO_INCREMENT yang mengisi.

### SLIDE 58 `[CORE]`
Title: INSERT dengan prepared statement
Code: `$stmt = $pdo->prepare("INSERT INTO students (...) VALUES (:name,:email,:major)"); $stmt->execute([...]);`
Speaker Notes: `prepare()` WAJIB karena 3 nilai dari `$_POST`. Named placeholder dijelaskan, detail keamanan ditunda ke Part 6.

### SLIDE 59 `[CORE]`
Title: Kenapa BUKAN begini
Main Content: `.danger`/`.safe` — string interpolation vs prepared statement berdampingan.
Speaker Notes: Hanya teaser, jangan jelaskan mekanisme detail sekarang. Tekankan: prepared statement kebiasaan default sejak awal.

### SLIDE 60 `[CORE]`
Title: SEE IT RUN
Main Content: Submit create.php → cek index.php, baris baru muncul.
Speaker Notes: Bukti nyata data melintasi seluruh jalur form→$_POST→INSERT→MySQL→query→tabel.

### SLIDE 61 `[CORE]`
Title: Tapi coba refresh halaman ini...
Question: Setelah submit berhasil, tekan F5. Apa yang terjadi?
Speaker Notes: Biarkan peserta coba sendiri. Data dobel/dialog konfirmasi. Jembatan sempurna ke PRG (Part 5) — jangan langsung beri solusi.

### SLIDE 62 `[CORE]`
Title: Recap CREATE
Main Content: Form→$_POST→prepare/execute→INSERT. Masalah baru: refresh=submit ulang → Part 5.
Speaker Notes: Jangan beri jeda lama — momentum penting ke Part 5.

### SLIDE 63 `[SELF]`
Title: lastInsertId() — dapatkan id yang baru dibuat
Code: `$idBaru = $pdo->lastInsertId();`
Speaker Notes: Berguna untuk redirect ke detail/relasi tabel lain — relevan untuk capstone dengan relasi.

### SLIDE 64 `[SELF]`
Title: Named parameter (:name) vs positional (?)
Speaker Notes: Keduanya aman dari SQLi. Named lebih mudah dibaca, positional berisiko tertukar urutan tanpa error.

### SLIDE 65 `[SELF]`
Title: rowCount() — berapa baris terpengaruh
Speaker Notes: Kurang informatif untuk INSERT (selalu 1), jauh lebih berguna di UPDATE/DELETE.

### SLIDE 66 `[SELF]`
Title: Ide mencegah double-submit (selain PRG)
Code: `<button onclick="this.disabled=true;...">`
Speaker Notes: Hanya proteksi UX/browser, bisa dilewati — PRG (Part 5) solusi server-side yang benar.

---

## Part 5 — Session + PRG `[CORE: 6, SELF: 5]`

### SLIDE 67 `[CORE]`
Title: Post/Redirect/Get
Main Content: Menyelesaikan masalah double-submit yang baru ditemukan sendiri.
Speaker Notes: Sambungkan langsung, momentum penting.

### SLIDE 68 `[CORE]`
Title: CONCEPT — kenapa refresh = submit ulang
Main Content: Flow: POST→INSERT+balas HTML→browser ingat POST terakhir→F5=POST ulang.
Speaker Notes: Bukan bug kita — perilaku standar HTTP/browser yang harus ditangani sengaja di server.

### SLIDE 69 `[CORE]`
Title: Solusi: Post → Redirect → Get
[IMAGE PLACEHOLDER]
Diagram urutan PRG.
Code: `header('Location: index.php'); exit;`
Speaker Notes: Refresh mengulang GET terakhir (aman), bukan POST. `exit;` mencegah kode setelah header tetap jalan.

### SLIDE 70 `[CORE]`
Title: Tapi bagaimana user tahu "berhasil"?
Code: `session_start(); $_SESSION['flash'] = '...';`
Speaker Notes: `$_SESSION` = pengecualian dari "PHP lahir baru tiap request" (Part 1). `session_start()` wajib di baris paling atas tiap file.

### SLIDE 71 `[CORE]`
Title: Flash message: tampil sekali, lalu hilang
Code: `if (isset($_SESSION['flash'])): ... unset($_SESSION['flash']); endif;`
Speaker Notes: Tampilkan dulu, baru hapus — urutan penting.

### SLIDE 72 `[CORE]`
Title: SEE IT RUN
Main Content: create→redirect→pesan sukses tampil sekali; refresh→pesan hilang, tidak ada data dobel.
Speaker Notes: Momen "aha" Part 5 — masalah akhir Part 4 terbukti selesai.

### SLIDE 73 `[CORE]`
Title: Recap: pola untuk setiap aksi mutasi
Main Content: 3 langkah universal: set flash → redirect+exit → tampilkan & hapus flash.
Speaker Notes: Jembatan ke Part 6: "ada lubang keamanan yang belum dibahas."

### SLIDE 74 `[SELF]`
Title: Bagaimana session "mengingat" browser tertentu
[IMAGE PLACEHOLDER]
Diagram cookie session_id ↔ data di server.
Speaker Notes: Data session ada di SERVER, hanya ID-nya di cookie browser — user tidak bisa edit langsung lewat DevTools.

### SLIDE 75 `[SELF]`
Title: Kenapa exit; setelah header() wajib
Speaker Notes: `header()` tidak menghentikan eksekusi PHP — kode setelahnya tetap jalan tanpa `exit;`.

### SLIDE 76 `[SELF]`
Title: Merapikan: helper function untuk flash
Code: `function setFlash(...){} function getFlash(): ?string {}`
Speaker Notes: Refactor murni, dipakai lagi di create/edit/delete.php.

### SLIDE 77 `[SELF]`
Title: Session lifetime & session_destroy()
Speaker Notes: Tidak dipakai di CORE (tanpa login), tapi dasar fitur Logout kalau dikembangkan.

### SLIDE 78 `[SELF]`
Title: Opsional: login sederhana + session-gated CRUD
Question: Kenapa contoh ini TIDAK boleh dipakai di aplikasi nyata?
Speaker Notes: Jawaban — password dibandingkan plain text, semestinya di-hash (`password_hash`/`password_verify`). Pratinjau arah pengembangan capstone.

---

## Part 6 — Security Checkpoint `[CORE: 9, SELF: 5]` — HANDS-ON #3

### SLIDE 79 `[CORE]`
Title: Jangan Percaya Input Siapa Pun — SQL Injection & XSS
Main Content: Bagian paling penting dari seluruh sesi.
Speaker Notes: Tegaskan aturan etis: SEMUA demo hanya di database lokal throwaway, tidak pernah ke sistem orang lain.

### SLIDE 80 `[CORE]`
Title: CONCEPT — dua ancaman, satu akar masalah
[IMAGE PLACEHOLDER]
Ilustrasi input → SQL Query (SQLi) vs input → HTML Output (XSS).
Speaker Notes: Akar masalah sama: input tak dipercaya digabung langsung ke sesuatu yang "dieksekusi".

### SLIDE 81 `[CORE]`
Title: Fitur search yang (sengaja) rentan
Code: `$sql = "SELECT ... WHERE name LIKE '%$q%'"; $pdo->query($sql);` — lihat `demo-vulnerable/search_vulnerable.php`
Speaker Notes: File demo terpisah, jelas ditandai. `$q` langsung disambung ke SQL, tanpa prepare().

### SLIDE 82 `[CORE]`
Title: Kenapa ini bisa dibobol
Main Content: Trace payload `%' OR '1'='1'` → kondisi selalu true.
Speaker Notes: Titik krusial — kutip dari input "keluar" mengubah struktur query, bukan sekadar isinya.

### SLIDE 83 `[CORE][HANDS-ON]`
Title: Ketik/coba bareng #3 — Buktikan sendiri
Main Content: Coba input `budi` lalu `%' OR '1'='1` di search rentan, bandingkan jumlah baris.
Speaker Notes: ~8 menit. Minta peserta ketik sendiri. Tegaskan batas etis sebelum mulai.

### SLIDE 84 `[CORE]`
Title: Checkpoint — SEBELUM diperbaiki
[IMAGE PLACEHOLDER]
Screenshot nyata: seluruh tabel bocor lewat search.
Speaker Notes: Bukti visual harus benar-benar terjadi di layar. Sebutkan skala bahaya lebih luas (tabel lain, modifikasi, hapus) tanpa didemokan.

### SLIDE 85 `[CORE]`
Title: Perbaikan: kembali ke prepared statement
Code: `$stmt = $pdo->prepare("... WHERE name LIKE :q"); $stmt->execute(['q'=>'%'.$q.'%']);`
Speaker Notes: Bandingkan baris-demi-baris dengan versi rentan. SQL & data terpisah berkat `EMULATE_PREPARES=false`.

### SLIDE 86 `[CORE]`
Title: Checkpoint — SETELAH diperbaiki
Main Content: Payload sama persis → 0 baris.
Speaker Notes: Momen terpenting Part 6 — input identik, hasil berbeda total. Bukan filter kata kunci, tapi struktur yang sudah tidak bisa ditembus.

### SLIDE 87 `[CORE]`
Title: Ancaman kedua: XSS
Code: nama siswa diisi `<script>alert('...')</script>`
[IMAGE PLACEHOLDER]
Screenshot popup alert() akibat XSS, dari demo langsung.
Speaker Notes: Demokan langsung (instruktur). Arah kebalikan dari SQLi: data → OUTPUT HTML tanpa diproses.

### SLIDE 88 `[CORE]`
Title: Perbaikan: escape saat OUTPUT
Code: `htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8')`
Speaker Notes: Aturan bawa pulang: setiap cetak data ke HTML, bungkus htmlspecialchars(). Relevan lagi Part 9.

### SLIDE 89 `[CORE]`
Title: Dua prinsip, dibawa pulang
Main Content: Tabel SQLi→prepare() ; XSS→htmlspecialchars().
Speaker Notes: Pastikan tertanam sebelum lanjut — minta 2-3 peserta ulangi dengan kata sendiri. Jembatan ke Part 7.

### SLIDE 90 `[SELF]`
Title: Kenapa prepared statement "mematikan" SQLi secara teknis
Speaker Notes: Urutan waktu: struktur query dikirim & dipahami MySQL dulu, baru data — struktur sudah final sebelum data user tiba.

### SLIDE 91 `[SELF]`
Title: Detail: wildcard % di dalam LIKE
Speaker Notes: Bukan celah keamanan, hanya soal perilaku pencarian jika user mengetik `%` secara harfiah.

### SLIDE 92 `[SELF]`
Title: Jebakan tersembunyi: ORDER BY dinamis
Main Content: `.danger`/`.safe` — placeholder tidak bisa menggantikan nama kolom; solusi whitelist `in_array()`.
Speaker Notes: Jebakan yang sering terlewat bahkan developer berpengalaman.

### SLIDE 93 `[SELF]`
Title: Escaping berbeda untuk konteks berbeda
Main Content: HTML biasa, atribut, `<script>` (json_encode), URL (urlencode).
Speaker Notes: Prinsip umum: escaping harus sesuai konteks output, bukan satu ukuran untuk semua.

### SLIDE 94 `[SELF]`
Title: Checklist keamanan untuk capstone
Main Content: 5 item checklist, dipakai ulang di rubrik Part 10.
Speaker Notes: Sarankan disimpan terpisah untuk dibuka saat mengerjakan capstone.

---

## Part 7 — UPDATE `[CORE: 5, SELF: 3]`

### SLIDE 95 `[CORE]`
Title: UPDATE — sintesis dari semua yang sudah dipelajari
Speaker Notes: Sengaja sangat cepat (~9 menit) — pola sama, query beda. Tidak ada hands-on baru.

### SLIDE 96 `[CORE]`
Title: UPDATE = CREATE + WHERE id
[IMAGE PLACEHOLDER]
Diagram CRUD, "Update" disorot.
Main Content: Flow GET edit.php?id=5 → SELECT 1 baris → isi form → POST → UPDATE → PRG.
Speaker Notes: UPDATE = gabungan READ (Part 3) + CREATE (Part 4) + WHERE id spesifik.

### SLIDE 97 `[CORE]`
Title: GET: ambil 1 baris, isi form
Code: `$stmt=$pdo->prepare("SELECT * FROM students WHERE id=:id"); ... value="<?= htmlspecialchars($student['name']) ?>"`
Speaker Notes: `$id` dari `$_GET` tetap wajib prepare(). `fetch()` bukan `fetchAll()` karena id unik (PRIMARY KEY).

### SLIDE 98 `[CORE]`
Title: POST: UPDATE ... WHERE id
Code: `UPDATE students SET name=:name,... WHERE id=:id`
Speaker Notes: Bandingkan dengan INSERT Part 4 — nyaris identik + WHERE. Lupa WHERE = seluruh tabel tertimpa.

### SLIDE 99 `[CORE]`
Title: PRG + flash, lagi
Code: `setFlash('...diperbarui.'); header('Location: index.php'); exit;`
Speaker Notes: Identik pola Part 5 — bukti pola PRG+flash universal.

### SLIDE 100 `[CORE]`
Title: SEE IT RUN + recap
Main Content: Demo end-to-end edit dari index.php.
Speaker Notes: Jembatan cepat ke Part 8: "sisa satu huruf terakhir, paling singkat."

### SLIDE 101 `[SELF]`
Title: Kirim id: query string vs hidden input
Speaker Notes: Dua cara sama-sama valid, murni pilihan gaya, bukan soal keamanan.

### SLIDE 102 `[SELF]`
Title: Bagaimana kalau id tidak ditemukan?
Code: `if (!$student) { setFlash(...); header('Location: index.php'); exit; }`
Speaker Notes: `fetch()` mengembalikan `false` kalau tidak ada baris — wajib dicek sebelum akses key.

### SLIDE 103 `[SELF]`
Title: Pratinjau: partial update
Speaker Notes: Di luar cakupan CORE — form kita selalu menimpa semua kolom sekaligus.

---

## Part 8 — DELETE `[CORE: 5, SELF: 3]`

### SLIDE 104 `[CORE]`
Title: DELETE — query terpendek, tapi paling berbahaya
Speaker Notes: Setelah ini, CRUD lengkap berdiri sepenuhnya.

### SLIDE 105 `[CORE]`
Title: DELETE = query terpendek
[IMAGE PLACEHOLDER]
Diagram CRUD lengkap, "Delete" disorot.
Code: `DELETE FROM students WHERE id = :id;`
Speaker Notes: Query terpendek ≠ paling sederhana dirancang aman — fokus slide berikutnya.

### SLIDE 106 `[CORE]`
Title: Kenapa BUKAN link biasa
Main Content: `.danger` — `<a href="delete.php?id=5">Hapus</a>`
Speaker Notes: Tarik ke Part 2 (GET vs POST) — crawler/prefetch bisa memicu tanpa niat user.

### SLIDE 107 `[CORE]`
Title: Solusi: form POST kecil
Code: `<form method="POST" action="delete.php?id=5">` + `DELETE ... WHERE id=:id`
Speaker Notes: Bisa distyling seperti link lewat CSS, tapi mekanismenya tetap POST. Lupa WHERE = seluruh tabel terhapus.

### SLIDE 108 `[CORE]`
Title: Konfirmasi + PRG
Code: `onsubmit="return confirm('Yakin hapus?')"` + PRG+flash.
Speaker Notes: `confirm()` hanya proteksi UX, bukan keamanan — proteksi sesungguhnya tetap wajib-POST di server.

### SLIDE 109 `[CORE]`
Title: CRUD lengkap
Main Content: Flow Create→Read→Update→Delete disorot semua.
Speaker Notes: Beri jeda, biarkan terasa. Jembatan ke Part 9: "belum benar-benar menolak input tidak masuk akal."

### SLIDE 110 `[SELF]`
Title: Soft delete vs hard delete
Speaker Notes: Trade-off: soft delete lebih aman dari kesalahan tapi menambah kompleksitas di semua query baca.

### SLIDE 111 `[SELF]`
Title: confirm() bukan satu-satunya cara
Speaker Notes: Alternatif halaman konfirmasi terpisah untuk UX lebih kaya pada data berisiko tinggi.

### SLIDE 112 `[SELF]`
Title: Foreign key: apa yang terjadi kalau data terkait?
Code: `ON DELETE CASCADE` / `ON DELETE RESTRICT`
Speaker Notes: Relevan kalau capstone menghubungkan students & courses (Part 10).

---

## Part 9 — Validation `[CORE: 6, SELF: 6]`

### SLIDE 113 `[CORE]`
Title: Validation — lubang terakhir yang belum ditutup
Speaker Notes: Sengaja dipadatkan (~14 menit) — waktu dijaga untuk briefing capstone.

### SLIDE 114 `[CORE]`
Title: CONCEPT — client-side bukan pengganti server-side
[IMAGE PLACEHOLDER]
Diagram bypass validasi client-side (curl/Postman langsung ke server).
Question: Kalau HTML sudah punya required/type=email, kenapa masih perlu validasi PHP?
Speaker Notes: Jawaban — browser dikendalikan user, bisa dilewati sepenuhnya. Validasi server yang benar-benar menentukan.

### SLIDE 115 `[CORE]`
Title: Pola: kumpulkan error, jangan langsung proses
Code: `$errors=[]; if(empty(...)) $errors[]='...'; if(empty($errors)) { INSERT }`
Speaker Notes: Kumpulkan SEMUA error dulu, bukan die() di error pertama — user lihat semua masalah sekaligus.

### SLIDE 116 `[CORE]`
Title: Required, trim, panjang
Code: `trim($_POST['name'] ?? ''); strlen($name) > 100`
Speaker Notes: Batas panjang disamakan dengan VARCHAR(100) di schema — koordinasi validasi PHP & constraint DB.

### SLIDE 117 `[CORE]`
Title: Format email
Code: `filter_var($email, FILTER_VALIDATE_EMAIL)`
Speaker Notes: Hanya validasi format, bukan bukti email aktif.

### SLIDE 118 `[CORE]`
Title: Tampilkan error + jangan hilangkan yang sudah diketik
Code: `foreach ($errors as $e)...` + `value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"`
Speaker Notes: "Old input retention" — htmlspecialchars() wajib di value attribute (sama alasan Part 6).

### SLIDE 119 `[CORE]`
Title: Trio yang membuat backend "aman dan benar"
Main Content: Tabel Query/Output/Input → prepare()/htmlspecialchars()/validasi.
Speaker Notes: Rangkuman seluruh sesi keamanan/kualitas data. Jembatan ke Part 10: "ujian sesungguhnya."

### SLIDE 120 `[SELF]`
Title: Katalog filter_var() yang berguna
Code: `FILTER_VALIDATE_INT/URL/IP`, `FILTER_SANITIZE_SPECIAL_CHARS`.
Speaker Notes: Referensi untuk dibuka kembali saat butuh.

### SLIDE 121 `[SELF]`
Title: Whitelist untuk pilihan terbatas
Code: `in_array($_POST['major'], $jurusanValid)`
Speaker Notes: Tetap validasi server meski HTML sudah `<select>` — bisa dilewati lewat DevTools/POST langsung.

### SLIDE 122 `[SELF]`
Title: Mencegah email duplikat: PHP + database, dua lapis
Speaker Notes: Cek PHP untuk pesan ramah; UNIQUE constraint DB untuk jaminan pasti (race condition).

### SLIDE 123 `[SELF]`
Title: Error per-field vs error ringkasan
Speaker Notes: Ringkasan lebih sederhana (dipakai CORE); per-field UX lebih baik untuk form panjang.

### SLIDE 124 `[SELF]`
Title: Satu form untuk create dan edit
Speaker Notes: Pratinjau DRY — refactor opsional, tidak wajib untuk capstone.

### SLIDE 125 `[SELF]`
Title: Sekilas: CSRF (di luar cakupan hari ini)
Speaker Notes: Kelas ancaman berbeda dari SQLi/XSS — alasan tambahan kenapa mutasi wajib POST.

---

## Part 10 — Capstone + Closing `[CORE: 5, SELF: 4]`

### SLIDE 126 `[CORE]`
Title: Giliran Kamu — Bangun CRUD tanpa dituntun
Speaker Notes: Nada menantang tapi memberi semangat — ini ujian sesungguhnya dari 150 menit.

### SLIDE 127 `[CORE]`
Title: Courses Management System
Code: `CREATE TABLE courses (id, name, code, credits)`
[IMAGE PLACEHOLDER]
Diagram ERD sederhana tabel courses.
Speaker Notes: Struktur sengaja paralel students — menguji transfer pemahaman, bukan hal baru.

### SLIDE 128 `[CORE]`
Title: Milestone
Main Content: Tabel 5 milestone (READ→CREATE→UPDATE→DELETE→validasi/keamanan menyeluruh).
Speaker Notes: Detail lengkap di `capstone-guide.md`. Dorong coba dulu tanpa buka hint.

### SLIDE 129 `[CORE]`
Title: Definition of Done
Main Content: Checklist 5 poin, poin terakhir = kemampuan menjelaskan.
Speaker Notes: Kriteria yang membedakan "ikut tutorial" dari "memahami backend".

### SLIDE 130 `[CORE]`
Title: Setelah sesi ini: cara pakai deck
[IMAGE PLACEHOLDER]
Screenshot indikator panah bawah di reveal.js.
Speaker Notes: Ingatkan slide self-study + speaker notes lengkap untuk belajar mandiri.

### SLIDE 131 `[CORE]`
Title: Penutup
Main Content: Diagram flow penuh, sama seperti Opening — simetris.
Speaker Notes: Buka Q&A, termasuk parking lot dari Opening.

### SLIDE 132 `[SELF]`
Title: FAQ setup XAMPP / server lokal
Speaker Notes: Kumpulan troubleshooting umum (blank page, connection refused, file:// vs http).

### SLIDE 133 `[SELF]`
Title: Cara submit capstone
Speaker Notes: Struktur pengumpulan meniru folder students/ — tanpa kejutan format baru.

### SLIDE 134 `[SELF]`
Title: Rubrik penilaian (gambaran umum)
Main Content: Tabel bobot 5 aspek, mencerminkan Definition of Done.
Speaker Notes: Sesuaikan bobot dengan kebijakan program; titik awal yang wajar.

### SLIDE 135 `[SELF]`
Title: Ke mana setelah capstone selesai?
Main Content: Login/session-gated, pagination, relasi students↔courses, baru OOP/MVC/Composer/framework.
Speaker Notes: Semua sudah disinggung sebagai pratinjau — urutan bukan kebetulan, fondasi dulu baru abstraksi.
