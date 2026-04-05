-- Travel Website Database Schema
-- Created for Travel Agency Management System

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS travel_website_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE travel_website_db;

-- Drop tables if they exist (for clean re-creation)
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS packages;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    dob DATE,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Create packages table
CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_price (price)
);

-- Create bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_number VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    location VARCHAR(255) NOT NULL,
    guests INT NOT NULL DEFAULT 1,
    arrivals DATE NOT NULL,
    leaving DATE NOT NULL,
    package VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_booking_number (booking_number),
    INDEX idx_email (email),
    INDEX idx_arrivals (arrivals),
    INDEX idx_package (package),
    INDEX idx_status (status)
);

-- Create messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    seen BOOLEAN DEFAULT FALSE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_seen (seen),
    INDEX idx_submitted_at (submitted_at)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@travel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert super admin user (password: superadmin)
INSERT INTO users (name, email, password, role) VALUES 
('Super Admin', 'superadmin@travel.com', '$2y$10$K5mZ6Q8L9X2W7Y4T1R0P8O3N5M8J2K4H6G9F1D2S5A8C1V3B6N9Q2W5E8R1T', 'admin');

-- Insert sample packages
INSERT INTO packages (title, description, price, image) VALUES 
('Beach Paradise', 'Enjoy a relaxing beach vacation with pristine white sand beaches and crystal clear waters. Perfect for couples and families.', 599.99, 'beach1.jpg'),
('Mountain Adventure', 'Experience the thrill of mountain climbing and hiking with breathtaking views. Includes guided tours and equipment.', 799.99, 'mountain1.jpg'),
('City Explorer', 'Discover the vibrant city life with guided tours to famous landmarks, museums, and local attractions.', 449.99, 'city1.jpg'),
('Safari Experience', 'Get up close with wildlife in their natural habitat. Includes accommodation and guided safari tours.', 1299.99, 'safari1.jpg'),
('Island Getaway', 'Escape to a tropical paradise with beautiful beaches, water sports, and island hopping adventures.', 899.99, 'island1.jpg');
