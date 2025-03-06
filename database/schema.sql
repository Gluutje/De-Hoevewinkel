-- Database aanmaken
CREATE DATABASE IF NOT EXISTS hoevewinkel;
USE hoevewinkel;

-- Admin gebruikers tabel
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Producten tabel
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    unit VARCHAR(20) NOT NULL,                    -- Bijvoorbeeld: stuk, kg, liter
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,                -- Bijvoorbeeld: zuivel, groenten, etc.
    requires_cooling BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Vakken tabel
CREATE TABLE slots (
    slot_id INT PRIMARY KEY AUTO_INCREMENT,
    slot_number INT NOT NULL UNIQUE,              -- Fysieke locatie in automaat
    slot_type ENUM('COOLED', 'UNCOOLED') NOT NULL,
    product_id INT,
    current_stock INT NOT NULL DEFAULT 0,
    max_capacity INT NOT NULL,
    status ENUM('EMPTY', 'FILLED', 'RESERVED', 'MAINTENANCE') DEFAULT 'EMPTY',
    is_visible BOOLEAN DEFAULT TRUE,              -- Voor tijdelijk uitschakelen van vakken
    last_refill TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Kassa/wisselgeld status
CREATE TABLE cash_status (
    cash_id INT PRIMARY KEY AUTO_INCREMENT,
    denomination DECIMAL(10,2) NOT NULL,          -- Waarde van munt/biljet
    current_stock INT DEFAULT 0,
    minimum_required INT DEFAULT 10,
    maximum_allowed INT DEFAULT 200,              -- Voorkom overvulling
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_denomination (denomination)  -- Voorkom dubbele waardes
) ENGINE=InnoDB;

-- Transacties tabel
CREATE TABLE transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    slot_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('CASH', 'CARD', 'OTHER') NOT NULL,
    payment_status ENUM('PENDING', 'COMPLETED', 'FAILED', 'REFUNDED') DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (slot_id) REFERENCES slots(slot_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
) ENGINE=InnoDB;

-- Trigger voor koeling validatie bij invoegen
DELIMITER //
CREATE TRIGGER before_slot_insert 
BEFORE INSERT ON slots
FOR EACH ROW
BEGIN
    DECLARE product_needs_cooling BOOLEAN;
    
    IF NEW.product_id IS NOT NULL THEN
        SELECT requires_cooling INTO product_needs_cooling
        FROM products 
        WHERE product_id = NEW.product_id;
        
        IF (NEW.slot_type = 'UNCOOLED' AND product_needs_cooling) OR
           (NEW.slot_type = 'COOLED' AND NOT product_needs_cooling) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Product cooling requirements do not match slot type';
        END IF;
    END IF;
END //

-- Trigger voor koeling validatie bij updaten
CREATE TRIGGER before_slot_update
BEFORE UPDATE ON slots
FOR EACH ROW
BEGIN
    DECLARE product_needs_cooling BOOLEAN;
    
    IF NEW.product_id IS NOT NULL THEN
        SELECT requires_cooling INTO product_needs_cooling
        FROM products 
        WHERE product_id = NEW.product_id;
        
        IF (NEW.slot_type = 'UNCOOLED' AND product_needs_cooling) OR
           (NEW.slot_type = 'COOLED' AND NOT product_needs_cooling) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Product cooling requirements do not match slot type';
        END IF;
    END IF;
END //
DELIMITER ;

-- Indexen voor optimale performance
CREATE INDEX idx_product_cooling ON products(requires_cooling);
CREATE INDEX idx_product_category ON products(category);
CREATE INDEX idx_slot_status ON slots(status);
CREATE INDEX idx_slot_type ON slots(slot_type);
CREATE INDEX idx_transaction_date ON transactions(created_at);
CREATE INDEX idx_transaction_status ON transactions(payment_status);

-- Basis admin gebruiker (wachtwoord: admin123)
INSERT INTO admins (username, password_hash) VALUES 
('admin', '$2y$10$8K1p/a4SqD.37nqGQn1NB.4XOVS6E.j6BiNxk.Sd45bPh8KXbhKhy');

-- Basis wisselgeld configuratie
INSERT INTO cash_status (denomination, current_stock, minimum_required, maximum_allowed) VALUES 
(0.05, 100, 50, 200),   -- 5 cent
(0.10, 100, 50, 200),   -- 10 cent
(0.20, 100, 50, 200),   -- 20 cent
(0.50, 100, 50, 200),   -- 50 cent
(1.00, 100, 50, 200),   -- 1 euro
(2.00, 100, 50, 200),   -- 2 euro
(5.00, 50, 20, 100),    -- 5 euro
(10.00, 50, 20, 100),   -- 10 euro
(20.00, 30, 15, 50),    -- 20 euro
(50.00, 20, 10, 30);    -- 50 euro

-- Voorbeeld producten
INSERT INTO products (name, description, unit, price, category, requires_cooling) VALUES
('Verse Melk', 'Verse boerderijmelk, direct van de koe', 'liter', 1.50, 'zuivel', TRUE),
('Scharreleieren', 'Verse eieren van eigen kippen', 'doos', 2.50, 'eieren', FALSE),
('Boerenkaas', 'Jonge boerenkaas', 'stuk', 4.75, 'zuivel', TRUE),
('Aardappelen', 'Verse aardappelen van het land', 'kg', 3.00, 'groenten', FALSE),
('Appels', 'Zoete appels uit eigen boomgaard', 'kg', 2.00, 'fruit', FALSE);

-- Voorbeeld vakken
INSERT INTO slots (slot_number, slot_type, max_capacity, status) VALUES
(1, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(2, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(3, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(4, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(5, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(6, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(7, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(8, 'COOLED', 10, 'EMPTY'),    -- Gekoeld vak
(9, 'UNCOOLED', 15, 'EMPTY'),  -- Ongekoeld vak
(10, 'UNCOOLED', 15, 'EMPTY'), -- Ongekoeld vak
(11, 'UNCOOLED', 15, 'EMPTY'), -- Ongekoeld vak
(12, 'UNCOOLED', 15, 'EMPTY'), -- Ongekoeld vak
(13, 'UNCOOLED', 15, 'EMPTY'), -- Ongekoeld vak
(14, 'UNCOOLED', 15, 'EMPTY'), -- Ongekoeld vak
(15, 'UNCOOLED', 15, 'EMPTY'), -- Ongekoeld vak
(16, 'UNCOOLED', 15, 'EMPTY'); -- Ongekoeld vak 