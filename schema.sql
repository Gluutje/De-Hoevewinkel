-- Geldeenheden tabel
CREATE TABLE IF NOT EXISTS money_units (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    value INT NOT NULL COMMENT 'Waarde in centen',
    quantity INT NOT NULL DEFAULT 0 COMMENT 'Aantal beschikbare munten/biljetten',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Standaard geldeenheden invoegen
INSERT INTO money_units (value, quantity) VALUES
(5000, 0),  -- €50
(2000, 0),  -- €20
(1000, 0),  -- €10
(500, 0),   -- €5
(200, 0),   -- €2
(100, 0),   -- €1
(50, 0),    -- 50c
(20, 0),    -- 20c
(10, 0),    -- 10c
(5, 0);     -- 5c 