<!-- .slide: id="part-7" -->
<p class="part-label">Part 7 · UPDATE <span class="badge badge-core">Core</span></p>

# UPDATE

## Sintesis dari semua yang sudah dipelajari

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita masuk ke UPDATE (edit data). Kabar baiknya: di bagian ini gak ada materi baru yang bikin pusing! UPDATE itu cuma gabungan dari READ 1 baris buat nampilin data lama ke form, lalu diproses pakai query UPDATE. Polanya 100% sama kayak yang sudah kita pelajari."

**🎯 Poin Kunci di Layar:**
- Alur ringkas: murni kombinasi konsep yang sudah dipelajari.



<p class="part-label">Part 7 · UPDATE</p>

## UPDATE = CREATE + WHERE id

<div class="imgph">
<b>Image placeholder</b>
Diagram siklus CRUD dengan "Update" disorot/highlight, posisinya di antara Create dan Delete.<br>
<i>Cari: "CRUD lifecycle diagram create read update delete"</i>
</div>

<div class="flow">
<span>GET edit.php?id=5</span><span class="arrow">&rarr;</span>
<span>SELECT satu baris</span><span class="arrow">&rarr;</span>
<span>isi form</span><span class="arrow">&rarr;</span>
<span>POST</span><span class="arrow">&rarr;</span>
<span>UPDATE</span><span class="arrow">&rarr;</span>
<span>PRG</span>
</div>

Note: **🗣️ Ngomong ke Peserta:**
"Alurnya cuma 2 tahap:
Pertama (GET): Pas tombol Edit diklik, kita tarik 1 data mahasiswa dari database, lalu kita tempelkan ke dalam form biar user bisa lihat data lamanya.
Kedua (POST): Pas tombol Simpan diklik, kita jalankan query UPDATE dengan syarat `WHERE id = :id`. Habis itu, redirect lagi ke `index.php` pakai PRG."

**🎯 Poin Kunci di Layar:**
- Tunjuk alur flow: GET buat nampilin data lama, POST buat nyimpen data baru.



<p class="part-label">Part 7 · UPDATE</p>

## GET: ambil 1 baris, isi form

```php [1|2-5|7-11]
<?php $id = $_GET['id']; ?>
<?php
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);
$student = $stmt->fetch();
?>

<form method="POST" action="edit.php?id=<?= $id ?>">
    <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>">
    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>">
    <input type="text" name="major" value="<?= htmlspecialchars($student['major']) ?>">
</form>
```

Note: **🗣️ Ngomong ke Peserta:**
"Perhatikan kodenya:
1. Kita ambil id dari URL lewat `$_GET['id']`. Tetap wajib pakai `prepare()` ya!
2. Kita pakai `fetch()`, bukan `fetchAll()`. Kenapa? Karena ID itu unik, datanya pasti cuma 1 baris.
3. Di dalam tag input form, atribut `value` kita isi data lama dan dibungkus `htmlspecialchars()`. Jangan lupa bungkus ini biar karakter kutip gak ngerusak tag HTML."

**🎯 Poin Kunci di Layar:**
- Tunjuk `fetch()`: cuma 1 baris.
- Tunjuk `value=\"<?= htmlspecialchars(...) ?>\"`.



<p class="part-label">Part 7 · UPDATE</p>

## POST: `UPDATE ... WHERE id`

```php [1-2|4-9]
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<?php
    $stmt = $pdo->prepare(
        "UPDATE students SET name = :name, email = :email, major = :major
         WHERE id = :id"
    );
    $stmt->execute([
        'name' => $_POST['name'], 'email' => $_POST['email'],
        'major' => $_POST['major'], 'id' => $_GET['id'],
    ]);
?>
<?php endif; ?>
```

<p class="fineprint">Bandingkan dengan INSERT di Part 4 &mdash; strukturnya nyaris identik.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Pas form disubmit, perintah SQL-nya adalah `UPDATE students SET name = :name ... WHERE id = :id`.
Tolong perhatikan baik-baik: **JANGAN PERNAH LUPA KLAUSA `WHERE id = :id`**! Kalau kalian lupa nulis WHERE id, seluruh data mahasiswa di tabel bakal berubah namanya jadi nama yang kalian ketik. Ini mimpi buruk nomor satu developer basis data."

**⚠️ Peringatan Kritis:**
- Tekankan bahaya UPDATE tanpa WHERE.



<p class="part-label">Part 7 · UPDATE</p>

## PRG + flash, lagi

```php [1-3]
setFlash('Data siswa berhasil diperbarui.');
header('Location: index.php');
exit;
```

<p class="fineprint">Persis pola Part 5. Tidak ada yang baru di sini &mdash; itulah intinya.</p>

Note: **🗣️ Ngomong ke Peserta:**
"Setelah UPDATE selesai, kita panggil lagi jurus PRG yang tadi: pasang flash message 'Data siswa berhasil diperbarui', redirect ke `index.php`, lalu pasang `exit;`. Polanya identik kan dengan yang tadi? Sekali kalian paham polanya, semua fitur backend tinggal ngulang jurus yang sama."

**🎯 Poin Kunci di Layar:**
- Tekankan konsistensi: PRG dipakai di setiap mutasi.



<p class="part-label">Part 7 · UPDATE</p>

## SEE IT RUN + recap

<div class="checkpoint">
<b>UPDATE = READ (ambil data lama) + CREATE (form + prepared + PRG) + <code>WHERE id</code></b><br>
Tidak ada konsep baru &mdash; hanya kombinasi baru dari yang sudah dikuasai.
</div>

<p class="fineprint">Demo: klik "Edit" di salah satu baris index.php &rarr; ubah data &rarr; simpan &rarr; kembali ke daftar dengan data ter-update + pesan flash.</p>

<p class="downhint">3 slide tambahan: hidden input vs query string untuk id, row not found, partial update</p>

Note: **🗣️ Ngomong ke Peserta:**
"Sekarang kita demokan: klik tombol Edit di baris Bagas Prakoso, kita ganti jurusannya jadi 'Teknik Informatika', klik Simpan. Halaman langsung balik ke `index.php` dan jurusannya sudah ter-update rapi!
UPDATE beres. Sekarang tinggal satu huruf terakhir di CRUD: huruf D alias DELETE di Part 8."

**🎯 Transisi ke DELETE:**
- Tekan panah kanan ke Part 8.


<p class="part-label">Part 7 · Self-Study</p>

## Kirim id: query string vs hidden input

```html [1-2|4-6]
<!-- Cara 1: id di URL action form (dipakai di CORE) -->
<form method="POST" action="edit.php?id=5">

<!-- Cara 2: id sebagai hidden input di dalam form -->
<form method="POST" action="edit.php">
    <input type="hidden" name="id" value="5">
```

Note: Kedua cara ini sama-sama valid dan sama-sama aman (asal tetap divalidasi/di-prepare saat dipakai di query). Cara 1 (dipakai di CORE) sedikit lebih sederhana untuk kasus form edit tunggal. Cara 2 sedikit lebih umum dipakai di aplikasi yang lebih kompleks karena semua data form (termasuk id) berada dalam satu tempat yang konsisten (`$_POST`), tidak tercampur antara `$_GET` dan `$_POST`.

Tidak ada yang "lebih benar secara mutlak" — ini murni pilihan gaya, bukan soal keamanan atau kebenaran fungsional.


<p class="part-label">Part 7 · Self-Study</p>

## Bagaimana kalau id tidak ditemukan?

```php [1-2|4-7]
$stmt->execute(['id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('Data siswa tidak ditemukan.');
    header('Location: index.php'); exit;
}
```

Note: `fetch()` pada query yang tidak menemukan baris apa pun akan mengembalikan `false` (bukan error/exception) — ini penting dicek SEBELUM mencoba mengakses `$student['name']` dan seterusnya, karena mengakses key dari `false` akan menghasilkan error.

Skenario ini bisa terjadi kalau user mengetik URL `edit.php?id=999` secara manual dengan id yang tidak ada, atau data sudah terlanjur dihapus orang lain sebelum link diklik. Menangani kasus ini dengan redirect + flash message (bukan membiarkan halaman error putih polos) adalah tanda aplikasi yang matang — pola yang sama juga relevan untuk `delete.php` di Part 8.


<p class="part-label">Part 7 · Self-Study</p>

## Pratinjau: partial update

```php [1-4|6]
$fields = [];
if (!empty($_POST['name']))  $fields['name']  = $_POST['name'];
if (!empty($_POST['email'])) $fields['email'] = $_POST['email'];
// ... bangun SET clause secara dinamis dari $fields yang terisi saja

// Di luar cakupan CORE hari ini -- UPDATE kita selalu menimpa SEMUA kolom.
```

Note: Di CORE, form edit kita SELALU mengirim dan menimpa SEMUA kolom (name, email, major) sekaligus — sederhana dan cukup untuk Student Management System.

Aplikasi yang lebih kompleks kadang butuh "partial update" — misalnya API yang hanya mengubah SATU field tanpa perlu mengirim ulang semua data. Ini butuh membangun klausa `SET` secara dinamis berdasarkan field mana saja yang dikirim, yang sedikit lebih rumit dan berisiko (harus hati-hati validasi nama kolom, mirip kasus `ORDER BY` dinamis di Part 6 self-study). Cukup diketahui sebagai konsep, tidak perlu diimplementasikan hari ini.
