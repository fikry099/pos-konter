# W&A Cell - Custom POS & Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Version">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" alt="PWA Ready">
  <img src="https://img.shields.io/badge/Status-Production_Ready-10B981?style=for-the-badge" alt="Status">
</p>

## 📌 Deskripsi Sistem

**W&A Cell POS** adalah aplikasi kasir dan manajemen operasional konter *custom* berbasis Laravel yang dirancang khusus untuk mendukung operasional multi-cabang. Aplikasi ini dilengkapi dengan integrasi Progressive Web App (PWA) agar dapat digunakan di berbagai perangkat (Desktop, Tablet, Mobile) secara responsif dan *offline-ready*.

Sistem ini dibuat untuk menyelesaikan tantangan operasional konter fisik, seperti pencatatan transaksi barang & pulsa, transparansi stok, rekap absensi, perhitungan bonus karyawan otomatis, hingga laporan keuangan real-time untuk Owner.

---

## ✨ Fitur Unggulan

### 1. 🏪 Manajemen Multi-Cabang & Akses Peran
- **Pengaturan Cabang:** Mendukung pengolahan data terpisah untuk 2 cabang atau lebih.
- **Role & Hak Akses:** Pembagian hak akses yang jelas antara **Owner** (Akses penuh pembukuan & laporan) dan **Kasir/Karyawan** (Akses operasional harian).

### 2. 👥 Absensi & Shift Kasir (Blind Closing)
- **Manajemen Shift Kerja:** Buka shift dengan input modal awal fisik kasir.
- **Absen Susulan:** Fitur gabung shift untuk kasir pengganti/susulan.
- **Blind Closing & Validasi Selisih:** Kasir menginput jumlah uang fisik laci tanpa melihat target sistem. Pop-up validasi otomatis mendeteksi kondisi kas **Pas**, **Minus**, atau **Surplus**.

### 3. 🛒 Kasir POS & Katalog Produk
- **Katalog Produk Dinamis:** Pencarian cepat nama/kode barang, filter rentang harga, dan pemisahan kategori.
- **Pencetakan Struk Thermal:** Dukungan cetak nota transaksi ke printer thermal 58mm.
- **Manajemen Retur & Stok:** Penanganan alur retur barang dan pembaruan stok secara otomatis.

### 4. 🎁 Perhitungan Bonus Karyawan Otomatis
- Rekap otomatis bonus penjualan sebesar **Rp 1.000 / pcs** untuk kategori aksesoris yang langsung terakumulasi pada laporan shift kasir.

### 5. 📊 Dashboard Finansial Owner
- **Laporan Laba Bersih:** Grafik dan ringkasan pendapatan, pengeluaran operasional, serta keuntungan bersih terpisah per cabang.
- **Sistem Pembukuan Transparan:** Rekap harian, bulanan, dan riwayat shift yang dapat diakses kapan saja secara real-time.

---

## 🛠️ Stack Teknologi

- **Backend Framework:** Laravel 11.x
- **Frontend & Styling:** Blade Templates, Tailwind CSS, SweetAlert2, FontAwesome 6
- **Database:** MySQL / MariaDB
- **PWA Integration:** Web App Manifest & Service Workers
- **Environment:** PHP >= 8.2, Laragon / Apache

---

## 🚀 Panduan Instalasi Lokal (Development)

Untuk menjalankan proyek ini di lingkungan lokal (Laragon/XAMPP):

1. **Clone Repository:**
   ```bash
   git clone [https://github.com/fikry099/pos-konter.git](https://github.com/fikry099/pos-konter.git)
   cd pos-konter