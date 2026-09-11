<!-- .slide: id="part-4" -->
<p class="part-label">Part 4 · CREATE <span class="badge badge-core">Core</span></p>

# CREATE

## Data baru: dari form ke tabel

Note: **🗣️ Ngomong ke Peserta:**
"Di Part 4 ini kita belajar CREATE: bagaimana data dari form bisa masuk jadi baris baru di tabel MySQL. Alurnya mirip dengan form yang tadi, bedanya sekarang data `$_POST` kita kirim ke query `INSERT`."

**🎯 Poin Kunci di Layar:**
- File latihan terpisah: `create.php`.



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

Note: **🗣️ Ngomong ke Peserta:**
"Lihat alurnya: Form ngirim data lewat method POST -> ditangkap PHP di `$_POST` -> dijalankan perintah query INSERT -> tersimpan rapi di MySQL. Di tengah-tengah ada kotak 'Validation' yang nanti kita bahas tuntas di Part 9 buat ngecek jangan sampai ada input kosong atau ngawur."

**🎯 Poin Kunci di Layar:**
- Tunjuk kotak INSERT: dari variabel PHP masuk ke database.



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

Note: **🗣️ Ngomong ke Peserta:**
"Ini form `create.php`. Ada input nama, email, dan jurusan. Perhatikan: gak ada input buat `id`. Kenapa? Karena ID itu nomor urut unik yang otomatis dibikin oleh MySQL lewat AUTO_INCREMENT. Jangan pernah bikin input ID manual di form tambah data."

**🎯 Poin Kunci di Layar:**
- Ingatkan kembali: atribut `name="name"` di HTML bakal jadi `$_POST['name']` di PHP.



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

Note: **🗣️ Ngomong ke Peserta:**
"Perhatikan cara kita menyimpan data ke database. Kita gak pakai `query()`, tapi pakai `prepare()` dan `execute()`. Tanda `:name`, `:email`, `:major` itu namanya placeholder. Query-nya disiapin dulu kerangkanya, baru nilainya disuntikkan lewat array di dalam `execute()`. Ini cara standar industri biar query kita kebal dari hacker dan SQL Injection."

**🎯 Poin Kunci di Layar:**
- Tunjuk baris 4: `prepare()` pisahkan perintah SQL dari data user.
- Tunjuk baris 8: `execute([...])` mengirim data user secara aman.



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

Note: **🗣️ Ngomong ke Peserta:**
"Lihat kotak merah di atas: ini kesalahan paling sering yang dibuat pemula di tutorial internet lama—variabel `$_POST` ditempel langsung ke dalam string SQL. Jangan pernah tiru cara itu! Nanti di Part 6 kita bakal demokan langsung bagaimana cara merah ini bisa dijebol cuma dengan trik satu baris input. Pokoknya pegang aturan emas ini: ada input dari user? Wajib pakai `prepare()`."

**🎯 Poin Kunci di Layar:**
- Kotak merah: bahaya celah keamanan.
- Kotak hijau: cara benar dan aman.



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

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita demokan: kita isi nama Guntur Wibowo di `create.php`, lalu klik Simpan. Pas kita buka tab `index.php` dan refresh, boom! Data Guntur langsung muncul di baris ke-7 tabel kita. Dua halaman yang tadinya terpisah sekarang sudah terhubung lewat database yang sama."

**🎯 Poin Kunci di Layar:**
- Tunjukkan bahwa data yang diinput di create.php otomatis muncul di index.php.



<p class="part-label">Part 4 · CREATE</p>

## Tapi coba refresh halaman ini...

<div class="ask"><b>Setelah submit berhasil, tekan F5 (refresh) di browser. Apa yang terjadi?</b></div>

<div class="mock-browser">
<div class="bar">localhost:8001/create.php</div>
<div class="body">"Konfirmasi pengiriman ulang formulir" &mdash; atau: data <b>tersimpan LAGI</b>, jadi duplikat.</div>
</div>

<p class="fineprint">Ini bukan bug di kode kita. Ini perilaku browser terhadap POST. Solusinya: Part 5.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Tapi tunggu dulu... coba setelah kalian submit, tekan tombol F5 (refresh) di browser kalian. Apa yang muncul? Browser bakal nampilin popup: 'Confirm Form Resubmission'. Dan kalau kalian klik Continue, data si Guntur bakal tersimpan dua kali jadi data dobel! Bayangkan kalau ini aplikasi checkout toko online, saldo kalian kepotong dua kali cuma gara-gara refresh halaman. Nah, gimana cara ngatasinnya? Jawabannya ada di Part 5: Post/Redirect/Get."

**🎯 Poin Kunci di Layar:**
- Ciptakan momen penasaran: masalah double-submit akibat refresh halaman POST.



<p class="part-label">Part 4 · CREATE</p>

## Recap CREATE

- Form POST &rarr; `$_POST` &rarr; `prepare()` + `execute()` &rarr; `INSERT`
- **Prepared statement sejak baris pertama**, bukan ditambahkan belakangan
- Masalah baru ditemukan: refresh = submit ulang &rarr; **Part 5**

<p class="downhint">4 slide tambahan: lastInsertId(), named vs positional parameter, rowCount(), ide-ide mencegah double-submit</p>

Note: **🗣️ Ngomong ke Peserta:**
"Recap singkat CREATE: form ngirim data POST, kita tangkap dan simpan pakai `prepare()` dan `execute()`. Data berhasil masuk, tapi ada penyakit baru: kalau di-refresh datanya dobel. Langsung kita obati penyakit ini di Part 5."

**🎯 Arah Presentasi:**
- Tekan panah kanan langsung ke Part 5.


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
