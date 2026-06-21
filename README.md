# 🎮 WB : WarnetBray

![PHP](https://img.shields.io/badge/PHP-Native-blue)
![MySQL](https://img.shields.io/badge/Database-MySQL-orange)
![Bootstrap](https://img.shields.io/badge/UI-Bootstrap-purple)
![License](https://img.shields.io/badge/Project-Web%20Application-green)

## 📌 About Project

**WB : WarnetBray** adalah aplikasi web manajemen rental warnet berbasis **PHP Native dan MySQL**.

Aplikasi ini dibuat untuk membantu pengelolaan warnet secara digital, mulai dari melihat ketersediaan komputer, melakukan booking, pembayaran, hingga monitoring penggunaan komputer.

Sistem menggunakan metode **Prepaid Rental**, dimana pelanggan wajib melakukan pembayaran terlebih dahulu sebelum komputer dapat digunakan.


---

# 🎯 Tujuan Project

Membangun sistem informasi rental warnet yang:

- Mempermudah pelanggan melakukan booking komputer
- Mengurangi proses pencatatan manual
- Membantu operator mengelola komputer
- Mengelola transaksi secara terstruktur
- Menyediakan laporan pendapatan warnet


---

# 👥 User Role

## Customer

Customer dapat:

- Membuat akun
- Login
- Melihat daftar komputer
- Melihat status komputer
- Melakukan booking
- Memilih durasi bermain
- Melakukan pembayaran
- Upload bukti pembayaran
- Melihat timer bermain
- Melihat riwayat transaksi


## Admin / Operator

Admin dapat:

- Login admin
- Mengelola komputer
- Mengelola user
- Mengelola booking
- Verifikasi pembayaran
- Mengaktifkan komputer
- Mengelola transaksi
- Melihat laporan


---

# 🔄 System Flow



Customer Login

  ↓

Melihat PC tersedia

  ↓

Pilih PC

  ↓

Pilih Durasi

  ↓

Sistem menghitung harga

  ↓

Pembayaran

  ↓

Admin verifikasi

  ↓

PC aktif

  ↓

Timer berjalan

  ↓

Selesai

  ↓

Transaksi tersimpan



---

# 💻 Features


## Customer Features

### Authentication

- Register
- Login
- Logout
- Session Management


### Computer Rental

- List komputer
- Status komputer
- Booking komputer
- Pemilihan durasi


### Payment

- Pembayaran awal
- Upload bukti pembayaran
- Status pembayaran


### Rental Timer

- Countdown waktu bermain
- Status penggunaan komputer


### History

- Riwayat booking
- Riwayat transaksi


---

# 🖥️ Computer Status


| Status | Keterangan |
|---|---|
| AVAILABLE | Komputer tersedia |
| WAITING PAYMENT | Menunggu pembayaran |
| PAID | Sudah bayar |
| PLAYING | Sedang digunakan |
| MAINTENANCE | Perbaikan |


---

# 🛠️ Technology Stack


## Frontend

- HTML
- CSS
- JavaScript
- Bootstrap 5


## Backend

- PHP Native
- PHP OOP
- MVC Pattern


## Database

- MySQL


## Server

- Apache
- XAMPP


---

# 📂 Project Structure



WB-WarnetBray/

│
├── index.php
├── login.php
├── register.php
├── logout.php
│
├── config/
│ └── database.php
│
├── controllers/
│ ├── AuthController.php
│ ├── BookingController.php
│ ├── PaymentController.php
│ └── ComputerController.php
│
├── models/
│ ├── User.php
│ ├── Computer.php
│ ├── Booking.php
│ └── Payment.php
│
├── views/
│
│ ├── customer/
│ │ ├── dashboard.php
│ │ ├── computers.php
│ │ ├── booking.php
│ │ ├── payment.php
│ │ ├── timer.php
│ │ └── history.php
│
│ └── admin/
│ ├── dashboard.php
│ ├── computers.php
│ ├── users.php
│ ├── booking.php
│ ├── payment.php
│ └── report.php
│
├── assets/
│ ├── css/
│ ├── js/
│ └── images/
│
└── uploads/
└── payment/



---

# 🗄️ Database Design


Database:


warnetbray_db



Tables:


users

computers

bookings

payments

transactions



---

# ⚙️ Installation


## 1. Clone Repository

```bash
git clone https://github.com/Xion-Enfys/WarnetBray.git
2. Masuk Folder Project
cd WB-WarnetBray
3. Pindahkan ke XAMPP

Copy folder:

WB-WarnetBray

ke:

htdocs/
4. Jalankan XAMPP

Aktifkan:

Apache
MySQL
5. Buat Database

Buka:

phpMyAdmin

Buat database:

warnetbray_db

Import file SQL

6. Jalankan Project

Browser:

http://localhost/WB-WarnetBray
🔐 Security

Implementasi:

Password hashing
Session authentication
Role access
SQL Injection prevention
Form validation
🚀 Future Development

Rencana pengembangan:

QR Payment
Notifikasi WhatsApp
Member system
Voucher warnet
Monitoring PC realtime
Dashboard statistik
👨‍💻 Developer

WB : WarnetBray

Web Programming Project

⭐ Jika project ini membantu, jangan lupa memberikan star!
