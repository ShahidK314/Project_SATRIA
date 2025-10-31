/* 1. Membuat Database */
CREATE DATABASE db_satria;

/* 2. Menggunakan Database */
USE db_satria;

/* 3. Membuat Tabel Akun (sesuai ERD) */
/* Tabel ini menyimpan data untuk login */
CREATE TABLE Akun (
    username VARCHAR(50) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Pengusul', 'Verifikator', 'WD2', 'PPK', 'Bendahara', 'Admin') NOT NULL,
    status_akun ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif'
);

/* 4. Membuat Tabel User (sesuai ERD) */
/* Tabel ini menyimpan data profil/identitas pengguna */
CREATE TABLE User (
    id_user VARCHAR(20) PRIMARY KEY, 
    /* id_user bisa berupa NIP/NIM/ID Pegawai */
    
    username VARCHAR(50) NOT NULL,
    /* 'username' di sini adalah Foreign Key yang merujuk ke Tabel Akun */
    
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15),
    Email VARCHAR(100),
    jurusan_id VARCHAR(10), 
    [span_0](start_span)/* Ini didasarkan pada atribut 'Jurusan (0)' di ERD Anda[span_0](end_span) */
    
    UNIQUE(username),
    FOREIGN KEY (username) REFERENCES Akun(username)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);