<!-- .slide: id="part-7" -->
<p class="part-label">Part 7 · UPDATE <span class="badge badge-core">Core</span></p>

# UPDATE

## Sintesis dari semua yang sudah dipelajari

Note: Part ini dan Part 8 sengaja dibuat SANGAT cepat (masing-masing sekitar 9 menit) karena secara sengaja dijual sebagai "pola yang sama, cuma query-nya beda" — bukan konsep baru. Kalau terasa terburu-buru, itu memang tujuannya: membuktikan bahwa fondasi Part 2-6 sudah cukup untuk menaklukkan sisa CRUD dengan cepat.

Tidak ada hands-on baru di sini — murni menonton, karena semua elemen penyusunnya sudah pernah mereka ketik sendiri sebelumnya (form, prepared statement, PRG, flash).



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

Note: Tekankan bahwa UPDATE bukan konsep baru — ia adalah GABUNGAN dari READ (Part 3, untuk mengambil data yang akan diedit) dan CREATE (Part 4, pola form + prepared statement + PRG), ditambah satu hal baru: query-nya menyasar SATU baris spesifik lewat `WHERE id = :id`, bukan menyisipkan baris baru.

Satu file `edit.php` akan menangani DUA request berbeda: GET (menampilkan form terisi data lama) dan POST (memproses perubahan) — pola if-guard `REQUEST_METHOD` dari Part 2 dipakai lagi persis di sini.



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

Note: `$id` berasal dari `$_GET['id']` — INI juga input user, jadi query pengambilannya WAJIB `prepare()`, persis alasan yang sama seperti fitur search di Part 6. Tidak ada pengecualian "khusus form edit boleh langsung".

`fetch()` (bukan `fetchAll()`) dipakai karena kita tahu hasilnya PALING BANYAK satu baris — `id` adalah PRIMARY KEY, unik.

Perhatikan `value="<?= htmlspecialchars($student['name']) ?>"` — INI penerapan langsung pelajaran Part 6: data dari database (yang mungkin pernah diisi lewat input user) dicetak ke ATRIBUT HTML `value`, tetap harus di-escape. Kalau tidak, nama yang mengandung tanda kutip bisa merusak struktur HTML form ini.



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

Note: Minta peserta membandingkan sendiri dengan slide INSERT di Part 4 — perbedaannya HANYA: kata kunci `UPDATE ... SET` alih-alih `INSERT INTO ... VALUES`, dan tambahan klausa `WHERE id = :id` untuk menyasar baris yang tepat.

`WHERE id = :id` adalah bagian PALING KRITIS di query ini — TANPA klausa ini, `UPDATE students SET name = ...` akan mengubah SEMUA baris di tabel menjadi nilai yang sama. Ini kesalahan yang cukup umum bagi pemula: lupa `WHERE`, dan seluruh tabel "tertimpa" data yang sama.

`$_GET['id']` dipakai di sini (bukan `$_POST['id']`) karena id dikirim lewat URL form action (`edit.php?id=5`), bukan sebagai field form tersembunyi — kedua pendekatan valid, dibahas perbandingannya di self-study.



<p class="part-label">Part 7 · UPDATE</p>

## PRG + flash, lagi

```php [1-3]
setFlash('Data siswa berhasil diperbarui.');
header('Location: index.php');
exit;
```

<p class="fineprint">Persis pola Part 5. Tidak ada yang baru di sini &mdash; itulah intinya.</p>

Note: Tunjukkan bahwa TIGA baris ini identik dengan pola yang dipakai di `create.php` — hanya isi pesannya berbeda ("ditambahkan" vs "diperbarui"). Ini bukti konkret bahwa pola PRG+flash yang dipelajari di Part 5 benar-benar universal, bukan trik khusus untuk CREATE saja.

Kalau helper function `setFlash()`/`getFlash()` dari Part 5 self-study sudah dipakai, di sinilah manfaatnya terasa — tidak perlu menulis ulang `$_SESSION['flash'] = ...` di setiap file, cukup panggil function-nya.



<p class="part-label">Part 7 · UPDATE</p>

## SEE IT RUN + recap

<div class="checkpoint">
<b>UPDATE = READ (ambil data lama) + CREATE (form + prepared + PRG) + <code>WHERE id</code></b><br>
Tidak ada konsep baru &mdash; hanya kombinasi baru dari yang sudah dikuasai.
</div>

<p class="fineprint">Demo: klik "Edit" di salah satu baris index.php &rarr; ubah data &rarr; simpan &rarr; kembali ke daftar dengan data ter-update + pesan flash.</p>

<p class="downhint">3 slide tambahan: hidden input vs query string untuk id, row not found, partial update</p>

Note: Demokan alur lengkap end-to-end sekali: dari `index.php`, klik link/tombol "Edit" pada salah satu baris (yang mengarah ke `edit.php?id=N`), ubah salah satu field, submit, kembali ke `index.php` dan tunjukkan data sudah berubah PLUS pesan flash "berhasil diperbarui" muncul.

Jembatan cepat ke Part 8: "Update selesai. Sisa satu huruf terakhir dari CRUD — dan ini yang paling singkat dari semuanya."


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
