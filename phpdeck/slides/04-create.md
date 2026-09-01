<!-- .slide: id="part-4" -->
<p class="part-label">Part 4 · CREATE <span class="badge badge-core">Core</span></p>

# CREATE

## Data baru: dari form ke tabel

Note: Part ini lebih cepat dari Part 3 — pondasinya (form → $_POST, PDO, prepared statement teaser) sudah dibahas. Fokus di sini murni menyambungkan yang sudah ada ke perintah `INSERT`.

Ingatkan singkat: file yang dipakai sekarang `create.php`, terpisah dari `index.php`. Nanti keduanya akan saling terhubung lewat link/redirect.



<p class="part-label">Part 4 · CREATE</p>

## CONCEPT — C di CRUD

<div class="imgph">
<b>Image placeholder</b>
Diagram siklus CRUD sebagai lingkaran/urutan: Create &rarr; Read &rarr; Update &rarr; Delete, dengan "Create" disorot/highlight.<br>
<i>Cari: "CRUD lifecycle diagram create read update delete"</i>
</div>

<div class="flow">
<span class="on">Form</span><span class="arrow">&rarr;</span>
<span class="on">POST</span><span class="arrow">&rarr;</span>
<span class="on">PHP</span><span class="arrow">&rarr;</span>
<span>Validation</span><span class="arrow">&rarr;</span>
<span class="on">INSERT</span><span class="arrow">&rarr;</span>
<span class="on">MySQL</span>
</div>

Note: Sorot bahwa alur ini adalah PERPANJANGAN dari yang sudah dibahas — form dan POST sudah dikuasai sejak Part 2. Yang baru hanya dua hal: apa yang terjadi dengan data setelah `$_POST` diterima (di sini: `INSERT`), dan bagaimana mengeksekusinya dengan aman lewat PDO.

"Validation" muncul di diagram tapi belum dibahas detail — cukup disebut posisinya ada DI ANTARA menerima input dan menjalankan INSERT. Pembahasan penuhnya di Part 9, tapi versi minimal (cek field tidak kosong) akan disentuh sekilas di sini juga.



<p class="part-label">Part 4 · CREATE</p>

## Form `create.php`

```html [1|2-5|6]
<form method="POST" action="create.php">
    <input type="text" name="name" placeholder="Nama">
    <input type="email" name="email" placeholder="Email">
    <input type="text" name="major" placeholder="Jurusan">
    <button type="submit">Simpan</button>
</form>
```

<p class="fineprint">Tidak ada field untuk <code>id</code> &mdash; MySQL yang mengisi otomatis (<code>AUTO_INCREMENT</code>).</p>

Note: Tunjukkan lagi koneksi `name="..."` ke `$_POST['...']`, sudah kali kesekian tapi pengulangan ini yang membuatnya benar-benar tertanam.

Tekankan eksplisit kenapa TIDAK ADA input untuk `id`: itu tanggung jawab MySQL (`AUTO_INCREMENT PRIMARY KEY` dari `schema.sql`). Kalau peserta mencoba mengisi `id` secara manual, berpotensi bentrok dengan id yang sudah ada — biarkan database yang mengatur.



<p class="part-label">Part 4 · CREATE</p>

## INSERT dengan prepared statement

```php [1-2|4-7|8]
<?php require 'config/database.php'; ?>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<?php
    $stmt = $pdo->prepare(
        "INSERT INTO students (name, email, major)
         VALUES (:name, :email, :major)"
    );
    $stmt->execute([
        'name'  => $_POST['name'],
        'email' => $_POST['email'],
        'major' => $_POST['major'],
    ]);
?>
<?php endif; ?>
```

Note: Bandingkan dengan `query()` yang dipakai di Part 3: di sana tidak ada input user sama sekali di dalam SQL. Di sini, TIGA nilai berasal langsung dari `$_POST` — inilah momen `prepare()` menjadi WAJIB, bukan pilihan.

`:name`, `:email`, `:major` disebut *named placeholder* — penanda di dalam SQL yang nanti diisi lewat array di `execute()`. PDO (dengan `EMULATE_PREPARES => false` yang sudah diset di Part 3) mengirim SQL dan nilai-nilai ini SECARA TERPISAH ke MySQL — MySQL yang menggabungkannya dengan aman di sisi server, bukan PHP yang menyambung string.

Jangan jelaskan detail "kenapa ini aman" secara teknis di sini — cukup sebutkan hasilnya dan pindah ke slide berikutnya yang membandingkan langsung dengan cara yang SALAH.



<p class="part-label">Part 4 · CREATE</p>

## Kenapa BUKAN begini

<div class="danger">
<span class="label">Jangan pernah</span>

```php
$sql = "INSERT INTO students (name, email, major)
        VALUES ('$_POST[name]', '$_POST[email]', '$_POST[major]')";
$pdo->query($sql);
```

</div>

<div class="safe">
<span class="label">Yang tadi kita tulis</span>

```php
$stmt = $pdo->prepare("INSERT INTO students (name, email, major)
                        VALUES (:name, :email, :major)");
$stmt->execute([...]);
```

</div>

<p class="fineprint">Alasan lengkap + demo langsung ada di Part 6 (Security Checkpoint).</p>

Note: Ini sengaja hanya "teaser" — jangan jelaskan detail mekanisme SQL Injection sekarang, itu momennya di Part 6 dengan demo langsung yang jauh lebih berkesan daripada penjelasan abstrak di sini.

Yang perlu ditekankan sekarang HANYA satu hal praktis: **prepared statement adalah kebiasaan default kita mulai hari ini, bukan sesuatu yang ditambahkan belakangan setelah "versi sederhana" berjalan**. Setiap query yang melibatkan input user — INSERT ini, dan UPDATE/DELETE/search nanti — akan selalu ditulis dengan pola `prepare()` + `execute()` sejak awal.



<p class="part-label">Part 4 · CREATE</p>

## SEE IT RUN

<div class="mock-browser">
<div class="bar">localhost:8001/create.php</div>
<div class="body">
Nama: <b>Guntur Wibowo</b><br>
Email: <b>guntur.w@campus.ac.id</b><br>
Jurusan: <b>Teknik Komputer</b><br><br>
<i>[Simpan diklik]</i>
</div>
</div>

<p class="fineprint">Cek di <code>index.php</code> &mdash; baris baru muncul di tabel (yang dibuat di Part 3).</p>

Note: Demokan langsung: submit form di `create.php`, lalu buka `index.php` di tab lain untuk membuktikan datanya benar-benar tersimpan — bukan sekadar tampil di halaman `create.php` itu sendiri.

Ini adalah bukti nyata bahwa data melintasi seluruh jalur: form → `$_POST` → `prepare/execute` → MySQL → (lalu di halaman lain) `query/fetchAll` → tabel HTML. Dua Part yang tadinya terasa terpisah (Part 3 dan Part 4) sekarang terbukti terhubung lewat database yang sama.



<p class="part-label">Part 4 · CREATE</p>

## Tapi coba refresh halaman ini...

<div class="ask"><b>Setelah submit berhasil, tekan F5 (refresh) di browser. Apa yang terjadi?</b></div>

<div class="mock-browser">
<div class="bar">localhost:8001/create.php</div>
<div class="body">"Konfirmasi pengiriman ulang formulir" &mdash; atau: data <b>tersimpan LAGI</b>, jadi duplikat.</div>
</div>

<p class="fineprint">Ini bukan bug di kode kita. Ini perilaku browser terhadap POST. Solusinya: Part 5.</p>

Note: Biarkan peserta mencoba sendiri refresh setelah submit — ini pengalaman yang jauh lebih efektif daripada dijelaskan lewat kata-kata. Sebagian browser akan menampilkan dialog konfirmasi ("Confirm Form Resubmission"), sebagian lain (terutama kalau memakai `curl`/testing tool) akan langsung mengirim ulang tanpa tanya, menghasilkan data duplikat di tabel.

Jangan langsung kasih solusi. Tanya balik ke kelas: "Menurut kalian kenapa ini terjadi?" — biarkan mereka menghubungkan ke apa yang sudah dipelajari: browser "mengingat" body POST terakhir dan mengirim ulang PERSIS request yang sama saat di-refresh, termasuk data form-nya. Ini jembatan sempurna ke Post/Redirect/Get di Part 5.



<p class="part-label">Part 4 · CREATE</p>

## Recap CREATE

- Form POST &rarr; `$_POST` &rarr; `prepare()` + `execute()` &rarr; `INSERT`
- **Prepared statement sejak baris pertama**, bukan ditambahkan belakangan
- Masalah baru ditemukan: refresh = submit ulang &rarr; **Part 5**

<p class="downhint">4 slide tambahan: lastInsertId(), named vs positional parameter, rowCount(), ide-ide mencegah double-submit</p>

Note: Rangkum cepat, karena Part 5 langsung menyambung dari masalah yang baru saja ditemukan (double-submit). Jangan beri jeda terlalu lama antara slide ini dan Part 5 — momentumnya penting.


<p class="part-label">Part 4 · Self-Study</p>

## `lastInsertId()` — dapatkan id yang baru dibuat

```php [1-3|5]
$stmt = $pdo->prepare("INSERT INTO students (name, email, major)
                        VALUES (:name, :email, :major)");
$stmt->execute([...]);

$idBaru = $pdo->lastInsertId(); // id yang baru saja MySQL buat
```

Note: Berguna kalau setelah menyimpan data baru, kamu ingin langsung melakukan sesuatu dengan id tersebut — misalnya redirect ke halaman detail siswa yang baru dibuat (`edit.php?id=$idBaru`), atau menyimpan data terkait di tabel lain yang butuh id ini sebagai foreign key.

Tidak dipakai di CORE hari ini karena setelah CREATE kita hanya redirect kembali ke daftar (`index.php`), tapi ini alat yang akan sering dibutuhkan begitu aplikasi punya relasi antar-tabel — relevan untuk pengembangan capstone yang melibatkan lebih dari satu entity.


<p class="part-label">Part 4 · Self-Study</p>

## Named parameter (`:name`) vs positional (`?`)

```php [1-3|5-7]
// Named — urutan bebas, lebih mudah dibaca
$stmt = $pdo->prepare("INSERT INTO students (name, email) VALUES (:name, :email)");
$stmt->execute(['email' => $email, 'name' => $name]); // urutan boleh beda

// Positional — urutan HARUS cocok persis dengan tanda tanya
$stmt = $pdo->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
$stmt->execute([$name, $email]); // urutan HARUS: name dulu, baru email
```

Note: Keduanya sama-sama aman dari SQL Injection — perbedaannya murni gaya penulisan dan kemudahan membaca. Kita konsisten memakai NAMED placeholder (`:name`) sepanjang materi karena untuk pemula, membaca `'name' => $name` jauh lebih jelas maksudnya dibanding menghitung urutan tanda tanya yang harus cocok persis.

Risiko positional (`?`): kalau urutan array di `execute()` tertukar (misalnya `[$email, $name]` padahal query mengharapkan `[$name, $email]`), TIDAK ADA error yang muncul — data akan tersimpan tertukar tanpa pemberitahuan apa pun. Named parameter menghindari kelas bug ini sepenuhnya.


<p class="part-label">Part 4 · Self-Study</p>

## `rowCount()` — berapa baris terpengaruh

```php [1-4|6]
$stmt = $pdo->prepare("INSERT INTO students (name, email, major)
                        VALUES (:name, :email, :major)");
$stmt->execute([...]);

echo $stmt->rowCount(); // 1, kalau berhasil insert satu baris
```

<p class="fineprint">Lebih berguna lagi untuk UPDATE/DELETE (Part 7 &amp; 8) &mdash; memastikan sesuatu benar-benar berubah.</p>

Note: Untuk INSERT, `rowCount()` hampir selalu bernilai 1 (kecuali multi-row insert, di luar cakupan hari ini) — jadi tidak terlalu informatif di sini. Kegunaannya jauh lebih terasa nanti di UPDATE dan DELETE: `rowCount() === 0` bisa berarti "id yang dicari tidak ditemukan", sinyal berguna untuk menampilkan pesan yang tepat ke user alih-alih diam saja seolah berhasil.


<p class="part-label">Part 4 · Self-Study</p>

## Ide mencegah double-submit (selain PRG)

```html
<button type="submit" onclick="this.disabled=true; this.form.submit();">
    Simpan
</button>
```

<p class="fineprint">Ini hanya proteksi di sisi BROWSER (JavaScript) &mdash; bisa dilewati. PRG (Part 5) adalah proteksi di sisi SERVER, lebih andal.</p>

Note: Menonaktifkan tombol setelah diklik adalah perbaikan UX yang murah dan sering dipakai di aplikasi nyata, TAPI ini bukan solusi yang bisa diandalkan sendirian — JavaScript bisa dimatikan browser, atau request bisa dikirim lewat cara lain (misalnya menekan Enter dua kali dengan cepat sebelum JS sempat menonaktifkan tombol).

Ini murni pratinjau untuk membedakan "perbaikan UX" vs "solusi yang benar secara server". Post/Redirect/Get di Part 5 adalah solusi yang benar-benar menyelesaikan akar masalahnya di sisi server — teknik di slide ini sebaiknya dipakai SEBAGAI TAMBAHAN, bukan pengganti PRG.
