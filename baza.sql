-- Tworzenie bazy danych
DROP DATABASE IF EXISTS KLINIKA;
CREATE DATABASE IF NOT EXISTS KLINIKA;
USE KLINIKA;

-- Tworzenie tabeli Pacjenci
CREATE TABLE IF NOT EXISTS Patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    pesel VARCHAR(11) UNIQUE,
    phone VARCHAR(15),
    login VARCHAR(50) UNIQUE,
    password VARCHAR(100),
    email VARCHAR(100) UNIQUE
);

-- Wypełnienie tabeli Pacjenci przykładowymi danymi
INSERT INTO Patients (first_name, last_name, pesel, phone, login, password, email) VALUES
('Jan', 'Kowalski', '990805897511', '12578945', 'jankowalski', 'test123', 'jankowalski@wp.pl'),
('Anna', 'Nowak', '98060587611', '12231221', 'annanowak', 'test1234', 'annanowak@wp.pl');

-- Tworzenie tabeli Specjalizacja
CREATE TABLE IF NOT EXISTS Specializations (
    specialization_id INT AUTO_INCREMENT PRIMARY KEY,
    specialization_name VARCHAR(100) NOT NULL
);

-- Wypełnienie tabeli Specjalizacja przykładowymi danymi
INSERT INTO Specializations (specialization_name) VALUES
('Pediatra'),
('Kardiolog'),
('Dermatolog'),
('Internista'),
('Ginekolog');

-- Tworzenie tabeli Lekarze
CREATE TABLE IF NOT EXISTS Doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    specialization_id INT,
    login VARCHAR(50) UNIQUE,
    password VARCHAR(100),
    email VARCHAR(100),
    FOREIGN KEY (specialization_id) REFERENCES Specializations(specialization_id)
);

-- Wypełnienie tabeli Lekarze przykładowymi danymi
INSERT INTO Doctors (first_name, last_name, specialization_id, login, password, email) VALUES
('Michał', 'Lis', 1, 'michal.lis', 'haslolekarz4', 'michal.lis@example.com'),
('Alicja', 'Duda', 4, 'alicja.duda', 'haslolekarz5', 'alicja.duda@example.com');
