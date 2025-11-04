CREATE DATABASE IF NOT EXISTS realestate_db;
USE realestate_db;
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  role ENUM('landlord','tenant') NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE listings (
  listing_id INT AUTO_INCREMENT PRIMARY KEY,
  landlord_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  location VARCHAR(150),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (landlord_id) REFERENCES users(user_id) ON DELETE CASCADE
);
CREATE TABLE bookings (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id INT NOT NULL,
  listing_id INT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tenant_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (listing_id) REFERENCES listings(listing_id) ON DELETE CASCADE
);
CREATE TABLE payments (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  provider VARCHAR(50),
  provider_ref VARCHAR(255),
  last4 CHAR(4),
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('succeeded','failed') DEFAULT 'failed',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);
INSERT INTO users (full_name, email, role, password_hash)
VALUES
('Alice Trench', 'alice@gmail.com', 'landlord', '2y10322wwwty!'),
('Bob Lipser',    'bob@aol.com',   'tenant',   'bobtherobber22');

INSERT INTO listings (landlord_id, title, description, price, location)
VALUES
(1, 'Cozy 1BR', 'Cozy one-bedroom near downtown. Perfect for single tenants.', 1200.00, 'Downtown');
(2, 'Modern Studio Apartment', 'A bright studio apartment with modern kitchen and walk-in closet. Perfect for single tenants.', 950.00, 'Uptown');
(3, 'Spacious 3-Bedroom Family Home', 'Large family home with backyard, garage, and updated kitchen. Near schools and parks.', 2500.00, 'Suburban Area');
