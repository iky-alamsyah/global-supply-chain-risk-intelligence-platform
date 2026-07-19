<p align="center">
  <img src="public/images/logo.png" alt="Logo GSCRIP" width="120" height="120" onerror="this.src='https://placehold.co/120x120/0f172a/f1f5f9?text=GSCRIP';">
</p>

# GSCRIP — Global Supply Chain Risk Intelligence Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)](https://getbootstrap.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
[![REST API](https://img.shields.io/badge/API-Integrasi-0052CC?style=for-the-badge&logo=api)](https://github.com)
[![Lisensi](https://img.shields.io/badge/Lisensi-Edukasi-brightgreen?style=for-the-badge)](#lisensi)

**GSCRIP (Global Supply Chain Risk Intelligence Platform)** adalah platform berbasis web modern dan responsif yang dibangun menggunakan Laravel 12. Platform ini dirancang untuk memantau, menganalisis, dan mengestimasi risiko gangguan rantai pasok global di seluruh dunia dengan mengintegrasikan data real-time dari beberapa REST API eksternal serta dataset lokal untuk menghasilkan indeks risiko dinamis, visualisasi peta, dan estimasi rute pengiriman laut.

---

## Fitur

### Fitur Pengguna (User Features)
- **Executive Dashboard**: Kokpit terpadu yang menampilkan metrik real-time skor risiko global, pergerakan nilai mata uang, peringatan cuaca aktif, dan umpan berita rantai pasok global secara live.
- **Global Countries**: Direktori komprehensif seluruh negara di dunia yang dilengkapi dengan klasifikasi indeks tingkat risiko real-time.
- **Country Detail**: Profil mendalam masing-masing negara termasuk kondisi cuaca aktif, faktor makroekonomi, nilai tukar mata uang, indikator infrastruktur maritim, dan metrik risiko spesifik.
- **Weather Intelligence**: Pemantauan iklim lokal secara real-time dan indikator peringatan badai di sepanjang koridor perdagangan.
- **Currency Intelligence**: Pelacak nilai tukar mata uang terhadap USD yang dilengkapi dengan penilaian volatilitas pasar serta pemetaan risiko historis.
- **News Intelligence**: Agregator berita logistik internasional live yang dianalisis menggunakan pemetaan sentimen berbasis kamus leksikon (natural-language lexicon sentiment mapper).
- **Visualization**: Peta analitis interaktif dan grafik analitik yang menyajikan pola persebaran tingkat risiko berdasarkan wilayah (region).
- **Risk Engine**: Mesin kalkulasi deterministik yang menghitung indeks risiko negara menggunakan parameter multi-indikator (GDP, Inflasi, Cuaca, Mata Uang, Berita, dan Pelabuhan).
- **Port Dashboard**: Hub pusat yang menampilkan daftar pelabuhan aktif di seluruh dunia terintegrasi dengan penanda pin peta interaktif.
- **Shipment Route Estimation**: Mesin pencari rute laut dinamis menggunakan algoritma A* waypoints routing, mengombinasikan kalkulasi jarak Haversine, perkiraan cuaca laut Open-Meteo, gangguan berita logistik, estimasi emisi karbon, dan grafik stepper linimasa pengiriman.
- **Country Comparison**: Matriks perbandingan multi-negara menggunakan diagram radar komponen risiko dan diagram batang profil infrastruktur serta ekonomi.
- **Favorites**: Daftar pantau (watchlist) pribadi untuk akses cepat dan pemantauan khusus pada simpul logistik bernilai tinggi.
- **User Profile**: Pengelolaan akun, unggah foto profil (avatar) kustom, dan pengaturan metadata profil pengguna.

### Fitur Administrator (Administrator Features)
- **Dashboard**: Telemetri sistem yang menampilkan agregat jumlah pengguna terdaftar, aset pelabuhan, artikel analisis, dan daftar pantau aktif.
- **User Management**: Kontrol operasional untuk mendaftarkan, menaikkan peran (promote), menangguhkan (suspend), atau mengelola peran anggota platform.
- **Port Dataset Management**: Pengelolaan penuh (CRUD) dataset koordinat pelabuhan global, kode pelabuhan, dan status operasionalnya.
- **Analysis Articles Management**: Panel kontrol bagi admin untuk menulis, memublikasikan, menjadwalkan, atau mengarsipkan artikel analisis ahli.
- **Administrator Profile**: Halaman khusus untuk memperbarui data profil administrator.

---

## Teknologi yang Digunakan

### Backend
- **Laravel 12** — Framework aplikasi PHP MVC modern.
- **PHP 8.3** — Bahasa pemrograman skrip performa tinggi dengan strict typing.
- **MySQL 8.0** — Sistem manajemen database relasional untuk menyimpan indeks infrastruktur, cache, dan log.

### Frontend
- **Bootstrap 5.3** — Sistem desain antarmuka responsif berbasis utility-first.
- **Blade** — Mesin template layout bawaan Laravel yang cepat dan ekspresif.
- **JavaScript (ES6)** — Reaktinitas frontend dinamis, penangan autocomplete, dan pengiriman formulir asinkron.
- **Chart.js** — Diagram radar perbandingan premium, grafik riwayat, dan analitik dashboard.
- **Leaflet.js** — Pustaka peta interaktif untuk merender titik koordinat, pelabuhan, dan rute pelayaran laut.

### Alat Pengembangan (Development Tools)
- **Composer** — Pengelola dependensi paket PHP.
- **Node.js & NPM** — Lingkungan kompilasi aset frontend.
- **Laragon / XAMPP** — Pengelolaan server web lokal.
- **phpMyAdmin** — Alat administrasi database SQL.

---

## Integrasi REST API

Platform ini mengonsumsi data real-time dari beberapa API eksternal untuk menghitung indeks risiko:

| REST API | Fungsi / Ruang Lingkup |
| :--- | :--- |
| **REST Countries API** | Mengambil metadata negara termasuk kode ISO, ibu kota, wilayah geografis, subwilayah, populasi, dan gambar bendera resmi. |
| **Open-Meteo API** | Mengambil data kondisi cuaca terkini, kecepatan angin, curah hujan, tekanan udara, dan data badai untuk koordinat aktif. |
| **ExchangeRate API** | Menyediakan nilai konversi mata uang real-time terhadap USD untuk memantau volatilitas transaksi dan risiko makro. |
| **NewsData API** | Mengalirkan berita internasional dan laporan logistik terkini untuk mengukur indeks gangguan geopolitik atau alam. |

---

## Modul Utama

- **Risk Calculation Engine**: Memproses data ekonomi, cuaca, mata uang, dan pelabuhan secara deterministik untuk mengklasifikasikan tingkat risiko negara menjadi `LOW`, `MEDIUM`, atau `HIGH`.
- **Marine Pathfinding Engine**: Menggunakan koordinat geografis dan jaringan titik laut (sea waypoints grid) untuk menghitung rute pelayaran terpendek dan teraman di antara pelabuhan global.
- **Lexicon Sentiment Analyzer**: Menganalisis judul dan deskripsi berita berdasarkan kamus indeks kata di database untuk menentukan skor sentimen positif, netral, atau negatif, yang menjadi input bagi komponen risiko berita (News Risk).

---

## Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project secara lokal:

```bash
# 1. Clone repository ini
git clone https://github.com/username-anda/global-supply-chain-risk-intelligence-platform.git
cd global-supply-chain-risk-intelligence-platform

# 2. Install dependensi backend (PHP)
composer install

# 3. Install dependensi frontend (Node.js)
npm install

# 4. Salin file konfigurasi environment
copy .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi Database (Buka .env dan atur kredensial database Anda)
# DB_DATABASE=gscrip
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi database & seed data awal
php artisan migrate --seed

# 8. Buat symbolic link untuk folder storage
php artisan storage:link

# 9. Compile aset frontend
npm run build
# Atau jalankan server dev: npm run dev

# 10. Jalankan server lokal Laravel
php artisan serve
```

---

## Struktur Project

Penjelasan folder-folder penting dalam struktur project Laravel ini:

- `app/` — Berisi logika utama backend (Controller, Service, Model, Mapper, DTO).
- `bootstrap/` — Konfigurasi awal aplikasi dan manajemen cache.
- `config/` — File konfigurasi aplikasi, database, layanan pihak ketiga, dan middleware.
- `database/` — File migrasi, seeders, dan factory untuk struktur database.
- `public/` — Folder publik yang dapat diakses langsung (CSS, JS, gambar, hasil compile Vite).
- `resources/` — Folder views (Blade template), aset CSS, dan JS sebelum dicompile.
- `routes/` — Definisi rute aplikasi (`web.php`, `auth.php`, `console.php`).
- `storage/` — Folder penyimpanan file unggahan (avatar), log, dan cache respon database.

---

## Persyaratan Sistem

- **PHP 8.3** atau versi lebih baru
- **Composer** (PHP Package Manager)
- **Node.js & NPM**
- Database **MySQL 8.0**
- Lingkungan pengembangan **Laragon**, **XAMPP**, atau Docker (Laravel Sail)

---

## Informasi Akademik

Project ini dikembangkan sebagai tugas Ujian Akhir Semester (UAS).

* **Judul Project**: Global Supply Chain Risk Intelligence Platform (GSCRIP) Berbasis Web Menggunakan Laravel 12 dengan Integrasi REST API untuk Monitoring Risiko dan Estimasi Pengiriman Rantai Pasok Global.

---

## Developer

Nama : Mhd. Ripky Alamsyah Hsb

NIM : 240180121

Kelas : A3 

---

## Lisensi

Project ini dilisensikan di bawah ketentuan **Educational Purpose** (Tujuan Pendidikan). Penggunaan kembali atau penyebaran komersial harus mengikuti pedoman akademik dan institusi terkait.
