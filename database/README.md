# Database Setup Instructions

This directory contains the database setup files for the Travel Website Management System.

## Files Included

1. **create_database.sql** - SQL script to create database and tables
2. **setup_database.php** - PHP script to execute the SQL setup
3. **db_connect.php** - Database connection file (already exists)

## Database Schema

The system requires 4 main tables:

### Users Table
- Stores user information (customers and admins)
- Fields: id, name, email, password, address, phone, dob, role, timestamps

### Packages Table  
- Stores travel package information
- Fields: id, title, description, price, image, timestamps

### Bookings Table
- Stores booking information
- Fields: id, booking_number, customer details, travel dates, package info, price, timestamps

### Messages Table
- Stores contact form messages
- Fields: id, name, email, phone, message, seen status, timestamps

## Quick Setup

1. Make sure your XAMPP/WAMP server is running
2. Access: `http://localhost/12/Travel-website-Design-main/database/setup_database.php`
3. Follow the on-screen instructions

## Default Credentials

- **Admin Email**: admin@travel.com
- **Admin Password**: admin123

## Manual Setup (Alternative)

If you prefer manual setup:

1. Open phpMyAdmin
2. Import the `create_database.sql` file
3. Update database credentials in `db_connect.php` if needed (database name: travel_website_db)

## Security Notes

- Delete `setup_database.php` after successful setup
- Change default admin password immediately
- Ensure proper file permissions on database files

## Sample Data

The setup includes 5 sample travel packages to demonstrate functionality.
