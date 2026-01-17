# 🎓 SIKONSEL Web (Sistem Informasi Konseling Sekolah)

![Language](https://img.shields.io/badge/Language-PHP%20Native-blue) ![Database](https://img.shields.io/badge/Database-MySQL-orange) ![Frontend](https://img.shields.io/badge/Frontend-Tailwind%20CSS-teal) ![Status](https://img.shields.io/badge/Status-Internal%20Use%20Only-red)

**SIKONSEL** adalah platform digital Bimbingan dan Konseling (BK) yang dirancang khusus untuk lingkungan **SMPN 4 Rancaekek**.

Web ini berfungsi sebagai **Core System** (Pusat Data) untuk pengelolaan layanan konseling sekolah. Karena sifatnya yang **privat dan internal**, sistem ini tidak membuka pendaftaran untuk umum. Akun siswa hanya dapat dibuat dan dikelola oleh Guru BK (Admin).

Aplikasi ini juga bertindak sebagai **Server API** yang melayani pertukaran data untuk aplikasi **Sikonsel Mobile**.

---

## 🔐 Informasi Akses & Akun Demo

Mengingat pendaftaran akun ditutup untuk publik (hanya Admin yang bisa input data siswa), silakan gunakan **Akun Dummy** di bawah ini untuk keperluan pengujian atau demonstrasi aplikasi:

### 1. Role Admin (Guru BK)
Memiliki hak akses penuh untuk mengelola data siswa, jadwal, dan membalas laporan.
* **Username:** `admin_bk`
* **Password:** `admin123`

### 2. Role Siswa (Akun Dummy)
Digunakan untuk simulasi pengajuan konseling, melihat riwayat, dan akses fitur siswa.
* **NISN / Username:** `12345`
* **Password:** `siswa123`

> **Catatan:** Mohon tidak menghapus akun demo ini agar penguji lain tetap bisa mengakses sistem.

---

## 🚀 Fitur Utama

### 🌐 Berbasis Web (Admin & Siswa)
* **🔒 Konseling Aman (Encrypted):** Curhatan siswa disimpan menggunakan enkripsi **AES-256**, menjamin privasi total.
* **📅 Penjadwalan Otomatis:** Siswa dapat mengajukan jadwal temu janji konseling secara online.
* **📊 Dashboard Guru BK:** Memantau statistik masalah siswa, riwayat laporan, dan tindak lanjut kasus.
* **👨‍👩‍👧 Akses Wali Murid:** Portal khusus orang tua untuk melaporkan kendala siswa dari rumah.
* **🖨️ Export Laporan:** Cetak laporan konseling bulanan ke PDF & Excel.

### 📱 Backend API (Untuk Mobile App)
* **Authentication:** Menangani login siswa via Token.
* **JSON Endpoints:** Menyediakan data riwayat, profil, dan info sekolah untuk aplikasi Android.
* **Firebase Integration:** Mengirim notifikasi real-time ke HP pengguna.

---

## 💻 Teknologi yang Digunakan

* **Backend:** PHP Native (Support PHP 8.x)
* **Frontend:** HTML5, Tailwind CSS
* **Database:** MySQL / MariaDB
* **Keamanan:** OpenSSL (AES-256 Encryption) untuk data sensitif
* **Server:** Apache (Wajib aktifkan `mod_rewrite` untuk API)

---

## ⚙️ Cara Instalasi (Localhost)

Ikuti langkah ini untuk menjalankan project di komputer lokal (XAMPP/Laragon):

### 1. Clone Repository
```bash
git clone [https://github.com/Salz30/sikonsel-web.git](https://github.com/Salz30/sikonsel-web.git)
