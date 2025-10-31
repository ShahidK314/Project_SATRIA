/* 1. Membuat Database */
CREATE DATABASE IF NOT EXISTS db_satria;
USE db_satria;

/* 2. Tabel Akun (dengan id_user) - pastikan InnoDB */
CREATE TABLE IF NOT EXISTS `Akun` (
    `id_user` VARCHAR(20) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('Pengusul','Verifikator','WD2','PPK','Bendahara','Admin') NOT NULL,
    `status_akun` ENUM('Aktif','Nonaktif') DEFAULT 'Aktif',
    PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/* 3. Tabel user_profile (hindari nama `User`) */
CREATE TABLE IF NOT EXISTS `user_profile` (
    `id_user` VARCHAR(20) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `no_hp` VARCHAR(15),
    `email` VARCHAR(100),
    `jurusan_id` VARCHAR(10),
    PRIMARY KEY (`id_user`),
    CONSTRAINT `fk_userprofile_akun`
        FOREIGN KEY (`id_user`)
        REFERENCES `Akun`(`id_user`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
