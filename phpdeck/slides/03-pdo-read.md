<!-- .slide: id="part-3" -->
<p class="part-label">Part 3 · PDO + READ <span class="badge badge-core">Core</span></p>

# PHP bertemu MySQL

## READ dulu, sebelum CREATE

Note: **🗣️ Ngomong ke Peserta:**
"Nah, sekarang kita masuk ke Part 3: PHP ketemu MySQL. Kita bakal bikin fitur READ duluan sebelum CREATE. Kenapa? Biar kalian langsung bisa lihat data muncul di browser secara nyata."

**🎯 Poin Kunci di Layar:**
- Pastikan MySQL di XAMPP sudah aktif dan seed data 6 mahasiswa sudah ter-import.



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

Note: **🗣️ Ngomong ke Peserta:**
"Kenapa kita pakai PDO bukan `mysqli`? Anggap PDO ini adapter colokan universal. Hari ini kita pakai MySQL, besok lusa kalau kalian pindah ke PostgreSQL atau SQLite, kodingan PHP kalian hampir gak perlu diubah sama sekali. Selain itu, PDO punya fitur pengamanan query yang jauh lebih rapi dan konsisten."

**🎯 Poin Kunci di Layar:**
- Tunjuk PDO di tengah: PDO adalah penerjemah antara PHP dan database engine.



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

Note: **🗣️ Ngomong ke Peserta:**
"Ini struktur tabel `students` yang sudah kita import tadi: ada id, name, email, dan major. Perhatikan: `id` itu AUTO_INCREMENT. Artinya nomor id bakal dibikin otomatis sama MySQL. Kita gak perlu dan gak boleh ngisi id manual saat nambah data nanti."

**🎯 Poin Kunci di Layar:**
- Tunjuk nama-nama kolom (`name`, `email`, `major`). Nama kolom inilah yang jadi key array nanti.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live Coding</span> `config/database.php` — DSN

```php [1-2|4-8]
<?php
// DSN = Data Source Name, "alamat" database

$host   = '127.0.0.1';
$db     = 'student_db';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
```

<p class="fineprint">Pakai <code>127.0.0.1</code>, bukan <code>localhost</code> &mdash; hindari masalah socket di sebagian setup Windows.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang perhatikan ke layar depan. Saya akan mendemokan penulisan file koneksi `config/database.php` dari nol. DSN itu singkatan dari Data Source Name, gampangnya ini 'alamat rumah' database kita. Kenapa kita tulis IP `127.0.0.1` bukan `localhost`? Karena di Windows, kata `localhost` kadang bikin MySQL bingung nyari jalur socket. Pakai `127.0.0.1` itu jaminan aman lewat TCP."

**🎯 Poin Kunci di Layar:**
- Tunjuk variabel `$dsn`: gabungan host, nama DB `student_db`, dan charset `utf8mb4`.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live Coding</span> Opsi PDO yang wajib

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

Note: **🗣️ Ngomong ke Peserta:**
"Tiga baris opsi ini bukan hafalan kosong, masing-masing ada fungsinya:
1. `ERRMODE_EXCEPTION`: Biar kalau query kita salah ketik, PHP langsung teriak ngasih pesan error yang jelas, gak diam-diam gagal.
2. `FETCH_ASSOC`: Biar semua data dari database otomatis jadi Associative Array berlabel nama kolom (`$row['name']`), bukan angka 0, 1, 2.
3. `EMULATE_PREPARES => false`: Ini senjata rahasia kita buat mencegah SQL Injection nanti di Part 6."

**🎯 Poin Kunci di Layar:**
- Tunjuk `$options`: ini konfigurasi standar industri untuk PDO.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live Coding</span> Menangkap kegagalan koneksi

```php [1-2|3-5]
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}
```

<p class="filename">config/database.php sekarang lengkap. File lain tinggal <code>require</code> ini.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Kita bungkus koneksinya pakai `try-catch`. Kalau MySQL mati atau password salah, eksekusi langsung berhenti di `die()` dengan pesan yang jelas. Sekarang file `config/database.php` kita sudah beres! File lain nanti tinggal manggil `require 'config/database.php'` dan variabel `$pdo` siap dipakai kapan saja."

**⚠️ Antisipasi Error:**
- Kalau muncul 'Access denied for user root': cek apakah password MySQL mereka kosong atau ada password-nya.



<p class="part-label">Part 3 · PDO + READ</p>

## READ pertama: `query()` &rarr; `fetchAll()`

```php [1|2]
<?php require 'config/database.php'; ?>
<?php $students = $pdo->query("SELECT * FROM students")->fetchAll(); ?>
```

<div class="ask"><b>Bentuk <code>$students</code> sekarang:</b> array berisi 6 associative array, satu untuk tiap baris di tabel.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Cuma butuh 2 baris buat ngambil seluruh isi database! Baris pertama: sambungin ke DB. Baris kedua: suruh `$pdo` jalanin query SELECT, lalu `fetchAll()` buat narik seluruh barisnya sekaligus ke dalam variabel `$students`. Bentuk `$students` sekarang adalah array isi 6 data mahasiswa."

**🎯 Poin Kunci di Layar:**
- Ingatkan: `query()` cuma boleh dipakai untuk query tanpa input user. Kalau ada filter dari user, wajib pakai `prepare()` (akan dibahas di Part 4).



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

Note: **🗣️ Ngomong ke Peserta:**
"Nah, ingat `foreach` dari Part 1 tadi? Sekarang kita kawinkan dengan HTML table! Kita buka `foreach ($students as $s):`, cetak baris `<tr>`, lalu ambil kolomnya: `$s['name']`, `$s['email']`, `$s['major']`, terus tutup pakai `endforeach`. Sintaks titik dua `foreach(...): ... endforeach;` ini gaya khas PHP biar kodingan HTML kita rapi tanpa kurung kurawal bertumpuk."

**🎯 Poin Kunci di Layar:**
- Tunjuk alur di bawah: MySQL -> fetchAll -> array -> foreach -> HTML table.



<p class="part-label">Part 3 · PDO + READ</p>

## <span class="badge badge-hands">Live Coding #2</span> SELECT &rarr; foreach &rarr; tabel HTML

Demonstrasi pembuatan `index.php`:

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

<p class="fineprint">Live Demo: Pemateri membuat index.php &rarr; query database dijalankan &rarr; 6 data mahasiswa tampil.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang perhatikan ke layar proyektor. Saya akan mendemokan Live Coding #2: membuat file `index.php`.
Pertama, kita hubungkan ke file database dengan `require 'config/database.php'`.
Kedua, kita tarik seluruh data mahasiswa lewat `$pdo->query()->fetchAll()`.
Ketiga, kita loop array datanya ke baris-baris tabel HTML menggunakan `foreach`.
Target live demo kita: begitu browser kita refresh, langsung muncul tepat 6 baris data mahasiswa sesuai seed data kita!"

**🎯 Poin Kunci di Layar:**
- Tunjukkan alur 3 langkah: require DB -> query fetchAll -> foreach ke tag `<tr>` HTML.



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

<div class="checkpoint"><b>Hasil Live Demo:</b> Tabel muncul dengan TEPAT 6 baris data mahasiswa dari database MySQL.</div>

Note: **🗣️ Ngomong ke Peserta:**
"Mantap! Lihat di layar proyektor: begitu di-refresh, langsung muncul tabel 6 baris data seperti ini. Ini adalah pencapaian besar pertama kita: data dari database MySQL berhasil kita tarik dan kita render utuh ke browser lewat PHP!"

**🎯 Transisi ke CREATE:**
- "Sekarang kita sudah bisa nampilin data. Tapi gimana caranya user bisa nambahin mahasiswa baru dari web? Kita masuk ke Part 4: CREATE."



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

Note: **🗣️ Ngomong ke Peserta:**
"Rumus nampilin data itu cuma 3 langkah: require koneksi, ambil datanya lewat query fetchAll, lalu loop pakai foreach. Rumus ini bakal kita pakai lagi nanti pas bikin fitur pencarian dan edit data. Sekarang kita lanjut bikin form tambah data di Part 4."

**🎯 Arah Presentasi:**
- Tekan panah kanan ke Part 4.


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
