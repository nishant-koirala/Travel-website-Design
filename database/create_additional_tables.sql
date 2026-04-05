-- Additional Tables for Travel Website Management System
-- These tables support enhanced booking and management features

USE travel_website_db;

-- Create booking_status_history table for tracking status changes
CREATE TABLE IF NOT EXISTS booking_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    old_status VARCHAR(20),
    new_status VARCHAR(20) NOT NULL,
    changed_by VARCHAR(255), -- Admin email who made the change
    change_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_created_at (created_at)
);

-- Create payments table for tracking payment information
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    payment_method ENUM('cash', 'card', 'bank_transfer', 'online') DEFAULT 'cash',
    payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(255),
    payment_date TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_payment_date (payment_date)
);

-- Create reviews table for customer feedback
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    user_email VARCHAR(255) NOT NULL,
    package_name VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id),
    INDEX idx_user_email (user_email),
    INDEX idx_approved (approved),
    INDEX idx_rating (rating)
);

-- Create notifications table for admin alerts
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('new_booking', 'payment_received', 'review_pending', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    booking_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- Create settings table for system configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
);

-- Insert default settings
INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'Travel Agency', 'Website name'),
('contact_email', 'contact@travel.com', 'Main contact email'),
('contact_phone', '+1234567890', 'Contact phone number'),
('currency', 'USD', 'Default currency'),
('tax_rate', '0.10', 'Tax rate (decimal)'),
('auto_confirm_bookings', '0', 'Auto-confirm bookings (0/1)'),
('enable_reviews', '1', 'Enable customer reviews (0/1)'),
('admin_email', 'admin@travel.com', 'Admin notification email');

-- Create triggers for automatic notifications and status history

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS after_booking_insert;
DROP TRIGGER IF EXISTS before_booking_update;

-- Trigger to create notification when new booking is made
DELIMITER //
CREATE TRIGGER after_booking_insert
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO notifications (type, title, message, booking_id)
    VALUES (
        'new_booking',
        CONCAT('New Booking: ', NEW.booking_number),
        CONCAT('New booking received from ', NEW.name, ' for ', NEW.package),
        NEW.id
    );
END//
DELIMITER ;

-- Trigger to track booking status changes
DELIMITER //
CREATE TRIGGER before_booking_update
BEFORE UPDATE ON bookings
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO booking_status_history (booking_id, old_status, new_status, change_reason)
        VALUES (OLD.id, OLD.status, NEW.status, CONCAT('Status changed from ', OLD.status, ' to ', NEW.status));
    END IF;
END//
DELIMITER ;

-- Create view for booking reports
CREATE OR REPLACE VIEW booking_reports AS
SELECT 
    b.id,
    b.booking_number,
    b.name,
    b.email,
    b.package,
    b.price,
    b.status,
    b.arrivals,
    b.leaving,
    b.guests,
    b.created_at,
    p.payment_status,
    p.payment_method,
    r.rating,
    r.approved as review_approved
FROM bookings b
LEFT JOIN payments p ON b.id = p.booking_id
LEFT JOIN reviews r ON b.id = r.booking_id
ORDER BY b.created_at DESC;

-- Create view for dashboard statistics
CREATE OR REPLACE VIEW dashboard_stats AS
SELECT 
    COUNT(*) as total_bookings,
    SUM(price) as total_revenue,
    COUNT(DISTINCT email) as total_customers,
    COUNT(DISTINCT package) as total_packages,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_bookings
FROM bookings;
