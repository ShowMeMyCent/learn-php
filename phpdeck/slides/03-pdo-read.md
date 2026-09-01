<!-- .slide: id="part-3" -->
<p class="part-label">Part 3 · PDO + READ <span class="badge badge-core">Core</span></p>

# PHP bertemu MySQL

## READ dulu, sebelum CREATE

Note: Ini Part terpanjang di Core Track — sekitar 28 menit, termasuk menulis koneksi database dari nol (live) dan hands-on kedua. Jaga tempo: jangan lebih dari 1 menit per slide konsep, sisakan waktu untuk live coding.

Sebelum mulai: pastikan SEMUA peserta sudah menjalankan `schema.sql` lalu `seed.sql`. Ini seharusnya sudah dipastikan di Opening, tapi cek ulang sekarang — kalau ada yang belum, ini akan memblokir HANDS-ON #2 nanti.



<p class="part-label">Part 3 · PDO + READ</p>

## CONCEPT — PDO sebagai jembatan

<div class="flow">
<span>PHP</span><span class="arrow">&harr;</span>
<span class="on">PDO</span><span class="arrow">&harr;</span>
<span>MySQL</span>
</div>

- **PDO** = PHP Data Objects, cara PHP "bicara" ke database
- Satu API yang sama untuk MySQL, PostgreSQL, SQLite, dst
- Alternatif: `mysqli` &mdash; hanya untuk MySQL, API berbeda

<p class="fineprint">Kita pakai PDO sepanjang sesi ini. Konsisten &mdash; tidak dicampur dengan mysqli.</p>

Note: PDO bukan database itu sendiri — ia adalah lapisan penghubung (mirip driver) yang menerjemahkan perintah PHP menjadi sesuatu yang dipahami MySQL, dan menerjemahkan balik hasil dari MySQL menjadi array PHP yang sudah mereka kenal dari Part 1.

Kenapa PDO dipilih dibanding `mysqli`: sintaksnya lebih konsisten untuk prepared statement (dibahas beberapa slide lagi), dan kalau suatu saat proyek pindah database (misalnya ke PostgreSQL), sebagian besar kode PDO tidak perlu ditulis ulang — hanya connection string-nya yang berubah. `mysqli` terikat khusus ke MySQL.

Tidak perlu dijelaskan sebagai perdebatan panjang — cukup satu kalimat keputusan, lalu lanjut praktik.



<p class="part-label">Part 3 · PDO + READ</p>

## Yang sudah kamu punya: schema saja

```sql [1-6]
CREATE TABLE students (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    major VARCHAR(80)  NOT NULL
);
```

<p class="fineprint">6 baris data sudah di-seed. Koneksi PDO akan kita tulis BERSAMA, sekarang.</p>

Note: Ingatkan bahwa starter yang dibagikan sebelum sesi HANYA `schema.sql` dan `seed.sql` — sengaja, supaya proses menulis koneksi database dialami langsung, bukan sekadar "sudah disediakan". Ini bagian yang akan ditulis live di 3 slide berikutnya.

Sebutkan sekilas: `id INT AUTO_INCREMENT PRIMARY KEY` berarti MySQL yang mengisi angka `id` otomatis — mereka tidak perlu (dan tidak boleh) mengisinya sendiri saat INSERT nanti di Part 4.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live bareng</span> `config/database.php` — DSN

```php [1-2|4-8]
<?php
// DSN = Data Source Name, "alamat" database

$host   = '127.0.0.1';
$db     = 'student_db';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
```

<p class="fineprint">Pakai <code>127.0.0.1</code>, bukan <code>localhost</code> &mdash; hindari masalah socket di sebagian setup Windows.</p>

Note: Ini bagian LIVE CODING pertama — peserta menonton dulu (bukan hands-on), karena ini konfigurasi yang hanya ditulis sekali dan rawan typo. Ketik perlahan sambil menjelaskan tiap bagian DSN.

`127.0.0.1` vs `localhost`: keduanya SERING sama-sama berfungsi, tapi di beberapa setup Windows/XAMPP, `localhost` bisa memicu MySQL mencoba koneksi lewat named pipe/socket alih-alih TCP, yang kadang gagal. `127.0.0.1` memaksa koneksi TCP eksplisit — lebih dapat diprediksi untuk keperluan belajar. Ini murni tip praktis, bukan aturan mutlak.

`charset=utf8mb4` disebut lagi di sini karena sudah muncul di `schema.sql` — konsistensi charset antara koneksi dan database penting untuk menghindari karakter aneh/mojibake saat menyimpan nama dengan karakter non-ASCII.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live bareng</span> Opsi PDO yang wajib

```php [1-2|4-8|10]
$user = 'root';
$pass = '';       // sesuaikan dengan setup lokalmu

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);
```

Note: Tiga opsi ini BUKAN sekadar boilerplate untuk dihafal — tiap baris punya alasan konkret, jelaskan satu-satu:

`ERRMODE_EXCEPTION` — tanpa ini, PDO akan GAGAL DIAM-DIAM saat query salah (mengembalikan `false`, bukan error yang jelas). Dengan ini, kesalahan langsung melempar exception yang bisa ditangkap `try/catch` — jauh lebih mudah didebug, terutama untuk pemula yang baru belajar.

`FETCH_ASSOC` — mengatur supaya SETIAP hasil query otomatis berbentuk associative array (`$row['name']`), bukan array bernomor (`$row[0]`) atau gabungan keduanya (default PDO). Ini yang membuat hasil fetch nanti terasa familiar — persis seperti associative array yang dibahas di Part 1.

`EMULATE_PREPARES => false` — ini yang membuat prepared statement (Part 4 dan seterusnya) benar-benar aman dari SQL Injection, karena MySQL sendiri yang memisahkan query dari data, bukan PHP yang mensimulasikannya. Sebutkan ini akan relevan lagi persis di Part 6 (Security) — jangan dibahas panjang sekarang, cukup ditanam sebagai "alasan sudah disiapkan dari awal".



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live bareng</span> Menangkap kegagalan koneksi

```php [1-2|3-5]
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
```

<p class="filename">config/database.php sekarang lengkap. File lain tinggal <code>require</code> ini.</p>

Note: `try/catch` menangkap `PDOException` yang bisa dilempar `new PDO(...)` kalau host salah, database tidak ada, atau kredensial salah. Tanpa `try/catch`, error PHP mentah akan tampil (dengan detail seperti password di traceback, tergantung setting) — untuk sekarang cukup ditangkap dan ditampilkan pesan sederhana.

`die()` menghentikan eksekusi PHP total, mirip `exit(1)` di C untuk kasus fatal yang tidak bisa dilanjutkan. Kalau koneksi gagal, TIDAK ADA gunanya melanjutkan kode di bawahnya (semua query pasti akan gagal juga), jadi berhenti total adalah keputusan yang tepat di sini — beda dengan validasi input di Part 9 nanti yang harus tetap "lanjut jalan" sambil menampilkan pesan error.

File ini sekarang lengkap dan akan di-`require` dari SEMUA file lain (`index.php`, `create.php`, `edit.php`, `delete.php`) — persis pola `require` yang dibahas di Part 1 self-study.



<p class="part-label">Part 3 · PDO + READ</p>

## READ pertama: `query()` &rarr; `fetchAll()`

```php [1|2]
<?php require 'config/database.php'; ?>
<?php $students = $pdo->query("SELECT * FROM students")->fetchAll(); ?>
```

<div class="ask"><b>Bentuk <code>$students</code> sekarang:</b> array berisi 6 associative array, satu untuk tiap baris di tabel.</div>

Note: `->query()` mengirim string SQL apa adanya ke MySQL — cocok untuk query TANPA input dari user (seperti ini, `SELECT *` tanpa filter). `->fetchAll()` mengambil SEMUA baris hasil sekaligus dan mengembalikannya sebagai array PHP.

Gambarkan bentuk data hasilnya di papan: `$students = [ ['id'=>1,'name'=>'Ayu',...], ['id'=>2,'name'=>'Bagas',...], ... ]` — array yang isinya array. Ini PERSIS bentuk data yang dibahas di Part 1 (associative array) digabung dengan konsep "banyak baris" yang mereka kenal dari SQL sebelumnya.

Penting: `query()` HANYA aman dipakai untuk SQL tanpa data dari user. Begitu ada input user yang masuk ke query (search, filter by id, dll), harus pakai `prepare()` — ini akan dibahas eksplisit di Part 4 dan didemokan kenapa penting di Part 6.



<p class="part-label">Part 3 · PDO + READ</p>

## Array &rarr; HTML table

```php [1-3|4-8|9]
<table>
  <tr><th>Nama</th><th>Email</th><th>Jurusan</th></tr>
  <?php foreach ($students as $s): ?>
    <tr>
      <td><?= $s['name'] ?></td>
      <td><?= $s['email'] ?></td>
      <td><?= $s['major'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
```

<div class="flow">
<span>MySQL</span><span class="arrow">&rarr;</span>
<span>fetchAll()</span><span class="arrow">&rarr;</span>
<span>array</span><span class="arrow">&rarr;</span>
<span>foreach</span><span class="arrow">&rarr;</span>
<span>HTML table</span>
</div>

Note: Ini gabungan LANGSUNG dari dua konsep Part 1 (associative array + foreach) dengan hasil query Part 3. Kalau ada peserta yang masih ragu di sini, kembali sebentar ke slide foreach di Part 1 — polanya identik, hanya sumber datanya berbeda (array manual → hasil query).

Perhatikan syntax alternatif `<?php foreach (...): ?> ... <?php endforeach; ?>` — ini bentuk khusus PHP untuk menulis control structure di TENGAH HTML tanpa kurung kurawal bertumpuk yang membingungkan. Fungsinya identik dengan `foreach (...) { ... }`, hanya lebih nyaman dibaca saat bercampur dengan tag HTML seperti ini. Akan dipakai terus sepanjang sesi.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Ketik bareng #2</span> SELECT &rarr; foreach &rarr; tabel

Buat `index.php`:

```php [1|2|4-12]
<?php require 'config/database.php'; ?>
<?php $students = $pdo->query("SELECT id, name, email, major FROM students")->fetchAll(); ?>

<table border="1">
  <tr><th>Nama</th><th>Email</th><th>Jurusan</th></tr>
  <?php foreach ($students as $s): ?>
    <tr>
      <td><?= $s['name'] ?></td>
      <td><?= $s['email'] ?></td>
      <td><?= $s['major'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
```

<p class="fineprint">~10 menit. Target: tabel HTML berisi tepat 6 baris, sesuai seed.sql.</p>

Note: Sebelum mulai, tulis di papan checklist yang harus dicapai: (1) `require` koneksi, (2) query SELECT, (3) `foreach` mencetak `<tr>`. Ketiganya sudah dibahas eksplisit di 2 slide sebelumnya — sesi ini murni menggabungkan.

Kegagalan paling umum yang akan muncul di 10 menit ini: (1) lupa `require` sehingga `$pdo` undefined, (2) path `require` salah (relatif terhadap folder mana file dijalankan — ingatkan ini), (3) nama kolom di `$s['...']` tidak cocok dengan nama kolom di database (typo `nama` vs `name`), (4) MySQL belum jalan / kredensial salah di `database.php` sehingga muncul `PDOException` — INI SEBENARNYA BAGUS, tunjukkan bahwa pesan errornya sekarang jelas berkat `try/catch` yang ditulis tadi, bukan halaman putih kosong.

Keliling ruangan, jangan duduk. 10 menit terasa singkat kalau banyak yang stuck di masalah yang sama — kalau begitu, hentikan sebentar dan bahas di depan kelas.



<p class="part-label">Part 3 · PDO + READ</p>

## Checkpoint #2

<div class="mock-browser">
<div class="bar">localhost:8001/index.php</div>
<div class="body">
<table>
<tr><th>Nama</th><th>Email</th><th>Jurusan</th></tr>
<tr><td>Ayu Lestari</td><td>ayu.lestari@campus.ac.id</td><td>Informatika</td></tr>
<tr><td>Bagas Prakoso</td><td>bagas.prakoso@campus.ac.id</td><td>Sistem Informasi</td></tr>
<tr><td>Citra Maharani</td><td>citra.m@campus.ac.id</td><td>Informatika</td></tr>
<tr><td>&hellip;</td><td>&hellip;</td><td>&hellip;</td></tr>
</table>
</div>
</div>

<div class="checkpoint"><b>Berhasil kalau:</b> tabel muncul dengan TEPAT 6 baris data, sama seperti isi <code>seed.sql</code>.</div>

Note: Angka "6" bukan angka sembarang — ini cara verifikasi objektif yang tidak butuh pengajar untuk mengecek satu-satu. Kalau baris kurang dari 6, kemungkinan `WHERE` yang tidak sengaja tertulis atau query salah tabel. Kalau 0 baris tapi tidak error, kemungkinan `seed.sql` belum dijalankan atau dijalankan ke database yang salah.

Ini closed-loop pertama yang benar-benar melibatkan database — rayakan momen ini sedikit. "Kalian baru saja membuat data mengalir dari MySQL sampai ke browser." Ini pondasi untuk SEMUA yang tersisa hari ini: CREATE, UPDATE, DELETE semuanya akan berakhir dengan pola READ ini untuk menampilkan hasilnya.



<p class="part-label">Part 3 · PDO + READ</p>

## Recap: pola baca

<div class="flow">
<span class="on">MySQL</span><span class="arrow">&rarr;</span>
<span class="on">SELECT</span><span class="arrow">&rarr;</span>
<span class="on">PHP</span><span class="arrow">&rarr;</span>
<span>Validation</span><span class="arrow">&rarr;</span>
<span>SQL</span><span class="arrow">&rarr;</span>
<span>MySQL</span>
</div>

- `require 'config/database.php'` &mdash; koneksi tersedia sebagai `$pdo`
- `$pdo->query($sql)->fetchAll()` &mdash; ambil semua baris jadi array
- `foreach ($rows as $row)` &mdash; cetak tiap baris jadi HTML

<p class="downhint">7 slide tambahan: fetch vs fetchAll, DSN dibedah, exception PDO, query vs prepare, empty state, LIMIT/OFFSET, struktur project</p>

Note: Pola tiga baris ini (require → query+fetchAll → foreach) akan diulang HAMPIR PERSIS di setiap fitur baca data sepanjang sisa sesi, termasuk fitur search di Part 6 dan form edit di Part 7. Tekankan ini sebagai "rumus" yang sudah mereka kuasai, bukan sesuatu yang perlu dihafal ulang tiap kali.

Jembatan ke Part 4: "Sekarang kita bisa MEMBACA data yang sudah ada. Pertanyaannya: bagaimana data itu bisa ADA di database sejak awal — selain lewat `seed.sql` yang saya siapkan?" → CREATE.


<p class="part-label">Part 3 · Self-Study</p>

## `fetch()` vs `fetchAll()`

```php [1-4|6-8]
// fetchAll(): ambil SEMUA baris sekaligus, jadi array of array
$rows = $pdo->query("SELECT * FROM students")->fetchAll();
// aman untuk data kecil (ratusan-ribuan baris)

// fetch(): ambil SATU baris per pemanggilan, dalam loop while
$stmt = $pdo->query("SELECT * FROM students");
while ($row = $stmt->fetch()) {
    echo $row['name'];
}
```

Note: Untuk aplikasi seperti Student Management System dengan data ratusan/ribuan baris, `fetchAll()` sudah lebih dari cukup dan lebih sederhana kodenya — ini yang dipakai sepanjang materi CORE.

`fetch()` dalam loop `while` berguna untuk dataset SANGAT besar (jutaan baris) di mana memuat semuanya ke memori sekaligus lewat `fetchAll()` bisa membuat PHP kehabisan memory. Ini optimasi untuk skala yang jauh di luar cakupan sesi hari ini — cukup tahu bahwa opsi ini ada.


<p class="part-label">Part 3 · Self-Study</p>

## DSN dibedah lebih detail

```php
"mysql:host=127.0.0.1;dbname=student_db;charset=utf8mb4"
 \___/      \_______/      \_________/       \______/
 driver       host          nama DB          encoding
```

<div class="ask"><b>Checkpoint:</b> kalau MySQL kamu jalan di port selain 3306, apa yang perlu ditambahkan ke DSN?</div>

Note: **Jawaban:** tambahkan `;port=NNNN` ke DSN, misalnya `"mysql:host=127.0.0.1;port=3307;dbname=student_db;charset=utf8mb4"`. Ini sering terjadi kalau ada lebih dari satu instalasi MySQL di komputer yang sama (misalnya XAMPP dan instalasi MySQL manual berjalan bersamaan) — salah satunya biasanya dipindah ke port lain untuk menghindari bentrok.

`utf8mb4` vs `utf8`: disebut juga di `schema.sql` — MySQL punya encoding bernama "utf8" yang sebenarnya hanya mendukung hingga 3 byte per karakter (tidak mendukung sebagian emoji dan karakter tertentu). "utf8mb4" adalah UTF-8 yang sebenarnya (hingga 4 byte). Untuk proyek baru, selalu pakai `utf8mb4`.


<p class="part-label">Part 3 · Self-Study</p>

## PDOException — cara membacanya

```php [1-3|5]
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

// contoh isi $e->getMessage():
// "SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'"
```

Note: Pesan exception PDO biasanya sudah cukup informatif untuk didiagnosis sendiri. Pola umum:

`Access denied` → username/password di `database.php` salah, atau user MySQL tidak punya izin ke database tersebut.

`Unknown database` → nama database di DSN tidak cocok dengan yang dibuat `schema.sql` (cek typo `student_db` vs `students_db`).

`Connection refused` / `No connection could be made` → MySQL server belum menyala sama sekali (XAMPP belum di-start, atau service MySQL mati).

Latih peserta untuk MEMBACA pesan error, bukan panik. Ini keterampilan debugging paling dasar yang akan terus dipakai jauh melampaui sesi hari ini.


<p class="part-label">Part 3 · Self-Study</p>

## `query()` vs `prepare()` — kapan pakai yang mana

```php [1-2|4-6]
// query(): TANPA input dari user, aman
$pdo->query("SELECT * FROM students");

// prepare(): ADA input dari user, WAJIB
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);
```

<p class="fineprint">Aturan sederhana: ada tanda <code>$</code> milik user di dalam string SQL? &rarr; pakai <code>prepare()</code>.</p>

Note: Ini pratinjau untuk Part 4 dan pembahasan penuh di Part 6 (Security). Aturan praktis yang diberikan di sini SANGAT penting untuk ditanamkan sejak dini: begitu ada nilai yang berasal dari input pengguna (form, URL parameter, apa pun yang bisa dikontrol orang lain) yang perlu masuk ke query SQL, JANGAN PERNAH menyambungnya langsung ke string — selalu lewat `prepare()` + parameter terpisah.

`query("SELECT * FROM students")` di Part 3 aman karena TIDAK ADA input user sama sekali di dalamnya — string SQL-nya tetap sama persis setiap kali dipanggil siapa pun. Begitu ada `WHERE id = $id` yang nilainya dari `$_GET['id']`, aturan berubah total — inilah yang akan didemokan konsekuensinya secara langsung di Part 6.


<p class="part-label">Part 3 · Self-Study</p>

## Bagaimana kalau tabel kosong?

```php [1-3]
<?php if (empty($students)): ?>
    <p>Belum ada data siswa.</p>
<?php else: ?>
    <table>...</table>
<?php endif; ?>
```

Note: `fetchAll()` pada tabel kosong tidak menghasilkan error — ia mengembalikan array kosong `[]`. `foreach` pada array kosong juga tidak error, ia hanya tidak mengeksekusi apa pun di dalamnya sama sekali (nol iterasi).

Tapi tanpa pengecekan ini, tampilan ke user jadi janggal: tabel dengan header tapi tanpa baris sama sekali, atau bahkan tidak ada tabel tapi tidak ada penjelasan kenapa. Mengecek `empty($students)` (sudah dibahas di Part 1 self-study) dan menampilkan pesan yang jelas adalah tanda aplikasi yang matang — kecil, tapi sering dilewatkan pemula.


<p class="part-label">Part 3 · Self-Study</p>

## Pratinjau: `LIMIT` untuk data besar

```sql [1]
SELECT * FROM students ORDER BY name LIMIT 10 OFFSET 20;
-- ambil 10 baris, mulai dari baris ke-21 (halaman 3 jika 10/halaman)
```

<p class="fineprint">Tidak dipakai di capstone hari ini (data sedikit) &mdash; tapi wajib tahu untuk aplikasi nyata.</p>

Note: Untuk Student Management System dengan puluhan data, menampilkan semuanya di satu halaman (seperti yang kita lakukan) sudah cukup. Tapi bayangkan tabel dengan 50.000 baris — mengirim semuanya sekaligus ke browser akan lambat dan boros memori baik di server maupun browser.

`LIMIT` + `OFFSET` adalah dasar dari PAGINATION (halaman 1, 2, 3, dst). Ini di luar cakupan CORE hari ini, tapi kalau peserta ingin mengembangkan capstone mereka lebih jauh, ini adalah salah satu arah pengembangan yang natural — sudah disinggung juga di Part 10 (Capstone).


<p class="part-label">Part 3 · Self-Study</p>

## Struktur file proyek

```text
students/
├── config/
│   └── database.php   <- koneksi PDO (baru saja ditulis)
├── index.php          <- READ (daftar siswa)
├── create.php         <- CREATE (Part 4)
├── edit.php           <- UPDATE (Part 7)
└── delete.php          <- DELETE (Part 8)
```

<div class="imgph">
<b>Image placeholder</b>
Screenshot file explorer / VS Code menampilkan struktur folder di atas dengan file-file PHP terlihat jelas.<br>
<i>Cari/buat sendiri: screenshot VS Code file tree proyek PHP sederhana</i>
</div>

Note: Tunjukkan bahwa struktur ini SENGAJA datar (flat) — tidak ada folder `controllers/`, `models/`, `views/` seperti pola MVC yang akan mereka temui nanti di framework. Untuk aplikasi sekecil ini, satu file per "halaman/aksi" sudah cukup jelas dan mudah ditelusuri.

Sebutkan sekilas (tanpa mendalami): pola MVC, folder terpisah untuk logic vs tampilan, akan masuk akal justru SETELAH mereka merasakan sendiri titik di mana struktur datar seperti ini mulai terasa berantakan — biasanya saat aplikasi punya banyak entity atau logic yang berulang di banyak file. Itu adalah motivasi asli di balik framework, bukan sekadar "karena orang lain pakai".
