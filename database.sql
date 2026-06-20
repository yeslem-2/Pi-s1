-- ============================================
-- Smart Temperature & Humidity Monitoring System
-- Database Setup Script
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS smart_monitor;
USE smart_monitor;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: sensor_data
-- ============================================
CREATE TABLE IF NOT EXISTS sensor_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature DECIMAL(5,2) NOT NULL,
    humidity DECIMAL(5,2) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Table: device_status
-- ============================================
CREATE TABLE IF NOT EXISTS device_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status ENUM('ON', 'OFF') DEFAULT 'OFF',
    ac_status ENUM('ON', 'OFF') DEFAULT 'OFF',
    auto_mode TINYINT(1) DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- Table: settings
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    max_temp DECIMAL(5,2) DEFAULT 35.00,
    min_temp DECIMAL(5,2) DEFAULT 15.00,
    auto_mode_enabled TINYINT(1) DEFAULT 0
);

-- ============================================
-- Table: notifications
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255) NOT NULL,
    type ENUM('warning', 'info', 'success') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Insert default data
-- ============================================

-- Default admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@smartmonitor.com', '$2y$12$C4AeG4NaEqhb8Bb2v7AuZ.6p0sIMik1fOQGg1TzobR.IbHE04Z.CO', 'admin');

-- Default user (password: user123)
INSERT INTO users (username, email, password, role) VALUES
('user1', 'user1@smartmonitor.com', '$2y$12$NL3hyrBeAFFBltVkSvdEQOK90qcWsDMbJyupDxOFGKYYve/zMRj0O', 'user');

-- Default device status
INSERT INTO device_status (id, status, ac_status, auto_mode) VALUES
(1, 'OFF', 'OFF', 0);

-- Default settings
INSERT INTO settings (id, max_temp, min_temp, auto_mode_enabled) VALUES
(1, 35.00, 15.00, 0);

-- Sample sensor data
INSERT INTO sensor_data (temperature, humidity) VALUES
(22.50, 45.00),
(23.10, 44.50),
(21.80, 46.00),
(24.00, 43.20),
(22.90, 45.80);

-- Sample notifications
INSERT INTO notifications (message, type, is_read) VALUES
('System initialized successfully', 'info', 1),
('Welcome to Smart Monitor', 'success', 1);
