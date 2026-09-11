# Panduan Docker — Workshop PHP Backend

Dengan setup Docker ini, kamu tidak perlu menginstall Node.js, PHP, ataupun XAMPP secara manual di laptopmu. Cukup pastikan **Docker Desktop** sudah berjalan.

---

## 🚀 Cara Menjalankan

Buka terminal di root repository ini (`learn-php`), lalu jalankan:

```bash
docker compose up -d --build
```

Docker akan secara otomatis:
1. Menyiapkan environment Node.js dan menjalankan dev server Reveal.js untuk slide materi.
2. Menyiapkan environment PHP 8.2 dengan extension `pdo_mysql` untuk backend aplikasi.
3. Menyiapkan MySQL 8.0 dan **secara otomatis mengimport schema & data awal** dari `phpdeck/starter/schema.sql` dan `seed.sql`.
4. Menyiapkan phpMyAdmin agar kamu bisa melihat isi database langsung dari browser.

---

## 🌐 Alamat Akses (URLs)

Setelah semua container aktif, kamu bisa membuka:

| Layanan | Alamat URL | Keterangan |
| :--- | :--- | :--- |
| **Slide Presentasi (Reveal.js)** | [http://localhost:8000/phpdeck.html](http://localhost:8000/phpdeck.html) | Tekan `S` untuk membuka **Speaker Notes** (Presenter View) |
| **Aplikasi PHP (CRUD)** | [http://localhost:8001/index.php](http://localhost:8001/index.php) | Student Management System |
| **phpMyAdmin (Web GUI Database)** | [http://localhost:8080](http://localhost:8080) | User: `root`, Password: `secret` |
| **MySQL Database (Port)** | `localhost:3306` | Database: `student_db`, Password: `secret` |

---

## 💡 Fitur & Kemudahan

- **Live Code Sync (Hot-Reload):** File kode PHP dan Slide Markdown di laptopmu terhubung langsung (mounted) ke dalam container. Saat kamu mengedit file di folder `phpdeck/project/` atau `phpdeck/slides/`, perubahannya langsung aktif seketika tanpa perlu rebuild image.
- **Auto Seed Database:** Tabel `students` dan 6 data awal sudah langsung terisi di database saat container pertama kali dinyalakan.

---

## 🛠️ Perintah Berguna

### 1. Melihat Log Container
```bash
# Semua log
docker compose logs -f

# Khusus PHP backend
docker compose logs -f backend

# Khusus Slide Reveal.js
docker compose logs -f slides
```

### 2. Menghentikan Container
```bash
docker compose down
```

### 3. Mereset Database ke Kondisi Semula
Jika database kotor karena demo/testing dan ingin di-reset ulang ke kondisi awal:
```bash
docker compose down -v
docker compose up -d
```
*(Flag `-v` akan menghapus volume database sehingga file `schema.sql` dan `seed.sql` diimport ulang dari nol).*
