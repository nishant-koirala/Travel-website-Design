# Travel-website-Design
A modern travel website design featuring an intuitive booking system, user authentication, and a variety of curated travel packages. With responsive design for all devices, an easy-to-use admin dashboard, and secure payment integration, it provides a seamless experience for both users and administrators.










Database Tables
-- Create the database
CREATE DATABASE IF NOT EXISTS traveldb;
USE traveldb;

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    UserName VARCHAR(100) COLLATE latin1_swedish_ci NOT NULL,
    Password VARCHAR(100) COLLATE latin1_swedish_ci NOT NULL,
    updationDate TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    phone VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
    address VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    location VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    guests INT(11) NOT NULL,
    arrivals DATE NOT NULL,
    leaving DATE NOT NULL,
    package VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    price DECIMAL(10,2) NOT NULL,
    booking_number VARCHAR(12) COLLATE utf8mb4_general_ci NOT NULL
);

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    phone VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
    message TEXT COLLATE utf8mb4_general_ci NOT NULL,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    seen TINYINT(1) DEFAULT 0
);

-- Packages table
CREATE TABLE IF NOT EXISTS packages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT COLLATE utf8mb4_general_ci NOT NULL,
    image VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL
);

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    user_id INT(11) DEFAULT NULL,
    last_activity DATETIME DEFAULT NULL
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    site_email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL
);

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    phone VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
    password VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    address TEXT COLLATE utf8mb4_general_ci DEFAULT NULL,
    dob DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
    INDEX (email)
);
