CREATE DATABASE attendance_db;
USE attendance_db;

CREATE TABLE users(
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) UNIQUE,
email VARCHAR(100),
password VARCHAR(255)
);

INSERT INTO users(username,email,password)
VALUES('admin','admin@test.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

CREATE TABLE students(
id INT AUTO_INCREMENT PRIMARY KEY,
reg_no VARCHAR(50) UNIQUE,
full_name VARCHAR(100),
class VARCHAR(50)
);

CREATE TABLE attendance(
id INT AUTO_INCREMENT PRIMARY KEY,
student_id INT,
date DATE,
status ENUM('Present','Absent'),
FOREIGN KEY(student_id) REFERENCES students(id)
);