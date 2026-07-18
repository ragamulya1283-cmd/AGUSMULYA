-- Mobil Showroom database schema + sample data
-- Import this file ke MySQL untuk membuat database lengkap dengan data awal.
DROP DATABASE IF EXISTS mobil_showroom;
CREATE DATABASE mobil_showroom CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE mobil_showroom;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS wishlist,inquiry,transaksi,mobil,kontak,kategori,merek,users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users(
id INT AUTO_INCREMENT PRIMARY KEY,
nama VARCHAR(150),email VARCHAR(150) UNIQUE,password VARCHAR(255),
role ENUM('user','admin') DEFAULT 'user',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

CREATE TABLE merek(id INT AUTO_INCREMENT PRIMARY KEY,nama VARCHAR(100));
CREATE TABLE kategori(id INT AUTO_INCREMENT PRIMARY KEY,nama VARCHAR(100));

CREATE TABLE mobil(
id INT AUTO_INCREMENT PRIMARY KEY,
nama VARCHAR(200),
merek_id INT,
kategori_id INT,
tahun INT,
kilometer INT DEFAULT 0,
harga DECIMAL(15,2),
stok INT DEFAULT 10,
deskripsi TEXT,
gambar VARCHAR(255),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY(merek_id) REFERENCES merek(id) ON DELETE SET NULL,
FOREIGN KEY(kategori_id) REFERENCES kategori(id) ON DELETE SET NULL);

CREATE TABLE transaksi(
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,mobil_id INT,total DECIMAL(15,2),
status VARCHAR(30) DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY(mobil_id) REFERENCES mobil(id) ON DELETE CASCADE);

CREATE TABLE wishlist(
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,mobil_id INT,
UNIQUE(user_id,mobil_id),
FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY(mobil_id) REFERENCES mobil(id) ON DELETE CASCADE);

CREATE TABLE inquiry(
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,mobil_id INT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY(mobil_id) REFERENCES mobil(id) ON DELETE CASCADE);

CREATE TABLE kontak(
id INT AUTO_INCREMENT PRIMARY KEY,
nama VARCHAR(150),email VARCHAR(150),pesan TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);

INSERT INTO merek(nama) VALUES
('Toyota'),('Honda'),('Mitsubishi'),('Suzuki'),('Daihatsu'),
('Hyundai'),('Wuling'),('Nissan'),('Mazda'),('BMW');

INSERT INTO kategori(nama) VALUES
('SUV'),('Sedan'),('MPV'),('Hatchback'),('Pick-up');

INSERT INTO users(nama,email,password,role) VALUES
('Administrator','admin@showroom.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Agus','user@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user');

INSERT INTO mobil(nama,merek_id,kategori_id,tahun,kilometer,harga,stok,deskripsi,gambar) VALUES
('Toyota Rush',1,1,2022,18000,280000000,5,'SUV keluarga','toyota-rush.jpg'),
('Honda Civic',2,2,2021,32000,450000000,3,'Sedan sport','honda-civic.jpg'),
('Xpander',3,3,2023,12000,310000000,6,'MPV','xpander.jpg'),
('Ertiga',4,3,2022,25000,250000000,4,'MPV','ertiga.jpg'),
('Terios',5,1,2021,40000,240000000,5,'SUV','terios.jpg'),
('Creta',6,1,2024,8000,330000000,5,'SUV','creta.jpg'),
('Almaz',7,1,2023,15000,360000000,2,'SUV','almaz.jpg'),
('Livina',8,3,2022,28000,295000000,4,'MPV','livina.jpg'),
('Mazda 3',9,4,2023,22000,520000000,2,'Hatchback','mazda3.jpg'),
('BMW 320i',10,2,2024,6000,950000000,1,'Sedan premium','320i.jpg');

INSERT INTO transaksi(user_id,mobil_id,total,status) VALUES
(2,1,280000000,'selesai'),
(2,4,250000000,'pending'),
(2,2,450000000,'dibatalkan'),
(2,7,360000000,'selesai');

INSERT INTO wishlist(user_id,mobil_id) VALUES (2,2),(2,5);
INSERT INTO inquiry(user_id,mobil_id) VALUES (2,3),(2,6);
