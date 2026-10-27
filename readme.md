# Projek Rekap Nilai Mahasiswa

Aplikasi web sederhana untuk mengelola nilai mahasiswa berbasis PHP Native, MySQL, Bootstrap 5 (Dark Mode), dan fitur Upload Foto Profil.

---

## 🌟 Fitur Utama
1. **Pencarian & Filter Predikat**: Cari data berdasarkan Nama/NIM serta saring berdasar Predikat (Sangat Memuaskan, Memuaskan, dll).
2. **Sorting Nilai Akhir**: Urutkan data secara Ascending (A-Z) atau Descending (Z-A).
3. **Persistensi Page Limit**: Pilihan limit data per halaman (5, 10, 15) tersimpan aman di `$_SESSION` dan tidak akan kembali ke 10 saat melakukan aksi Edit, Hapus, Sorting, atau Paginasi.
4. **Upload Foto Profil**: Mendukung upload foto profil mahasiswa dengan penanganan otomatis (*fallback* ke UI-Avatars jika foto kosong).
5. **Konfirmasi Hapus Data**: Modal konfirmasi bergaya Dark Theme.
6. **Validasi NIM Duplikat**: Menolak input NIM yang sudah ada di database.

---

## 📁 Struktur File
```text
/
├── connection.php   # Konfigurasi koneksi MySQL database (db5e)
├── convertnilai.php # Fungsi konversi Nilai Angka -> Huruf & Predikat
├── createnilai.php  # Handling proses tambah data & upload foto
├── updatenilai.php  # Handling proses update data & update foto
├── deletenilai.php  # Handling proses hapus data
├── index.php        # Tampilan utama Dashboard (Paginasi, Table, Search)
├── readnilai.php    # Halaman alternatif tampilan tabel standar
├── uploads/         # Folder tempat menyimpan file fisik foto yang di-upload
└── README.md        # Documentation file
```

---

## 🗄️ Struktur Database (`db5e`)

Jalankan query berikut di phpMyAdmin untuk menyiapkan tabel `nilaimhsw`:

```sql
CREATE DATABASE IF NOT EXISTS db5e;
USE db5e;

CREATE TABLE IF NOT EXISTS nilaimhsw (
    nim VARCHAR(20) PRIMARY KEY,
    namalengkap VARCHAR(100) NOT NULL,
    nilaiakhir DOUBLE NOT NULL,
    foto VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Jika tabel sudah ada, jalankan perintah penambahan kolom `foto`:
```sql
ALTER TABLE nilaimhsw ADD COLUMN foto VARCHAR(255) NULL AFTER namalengkap;
```

---

## 🚀 Cara Menggunakan Projek
1. Salin folder projek ini ke dalam direktori `htdocs/` (XAMPP) atau `www/` (Laragon).
2. Buat database `db5e` di phpMyAdmin, lalu jalankan query SQL di atas.
3. Buka browser dan kunjungi `http://localhost/NAMA_FOLDER/index.php`.