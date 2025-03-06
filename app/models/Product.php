<?php
namespace App\Models;

require_once __DIR__ . '/Model.php';

/**
 * Product Model
 * Beheert de product data voor de automaat
 */
class Product extends Model {
    private $id;
    private $naam;
    private $prijs;
    private $beschrijving;
    private $voorraad;
    private $category;
    private $requires_cooling;

    public function __construct() {
        parent::__construct();
    }

    public function getAllSlots() {
        $sql = "SELECT s.*, p.name as product_name, p.requires_cooling, s.current_stock as product_stock 
                FROM slots s 
                LEFT JOIN products p ON s.product_id = p.product_id 
                ORDER BY s.slot_number";
        return $this->fetchAll($sql);
    }

    public function getAvailableProducts() {
        $sql = "SELECT * FROM products ORDER BY category, name";
        return $this->fetchAll($sql);
    }

    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE product_id = ?";
        return $this->fetch($sql, [$id]);
    }

    public function getAvailableSlots($productId) {
        $sql = "SELECT * FROM slots 
                WHERE product_id = ? 
                AND status = 'FILLED' 
                AND current_stock > 0";
        return $this->fetchAll($sql, [$productId]);
    }

    // Voorraad beheer
    public function updateStock($productId, $amount, $increase = true) {
        $sql = "UPDATE slots 
                SET current_stock = current_stock " . ($increase ? '+' : '-') . " ?
                WHERE product_id = ? 
                AND status = 'FILLED'
                AND current_stock " . ($increase ? '<' : '>=') . " ?";
        
        return $this->execute($sql, [$amount, $productId, $amount]);
    }

    /**
     * Haal alle producten op
     * @return array
     */
    public function getAllProducts() {
        $sql = "SELECT * FROM products ORDER BY category, name";
        return $this->fetchAll($sql);
    }

    /**
     * Verwijder een product uit alle automaatvakken
     * @param int $productId
     * @return bool
     */
    private function removeFromSlots($productId) {
        $sql = "UPDATE slots 
                SET product_id = NULL, 
                    current_stock = 0, 
                    status = 'EMPTY' 
                WHERE product_id = ?";
        return $this->execute($sql, [$productId]);
    }

    /**
     * Verwijder een product
     * @param int $productId
     * @return bool
     */
    public function deleteProduct($productId) {
        try {
            // Start een transactie
            $this->db->beginTransaction();
            
            // Verwijder het product eerst uit alle vakken
            $this->removeFromSlots($productId);
            
            // Verwijder het product zelf
            $sql = "DELETE FROM products WHERE product_id = ?";
            $result = $this->execute($sql, [$productId]);
            
            // Commit de transactie
            $this->db->commit();
            
            return $result;
        } catch (\Exception $e) {
            // Rollback bij een fout
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Maak een nieuw product aan
     * @param array $data Product gegevens
     * @return array Success status en bericht of product ID
     */
    public function createProduct($data) {
        try {
            // Valideer verplichte velden
            if (empty($data['name']) || !isset($data['price']) || empty($data['unit']) || empty($data['category'])) {
                return ['success' => false, 'message' => 'Niet alle verplichte velden zijn ingevuld'];
            }

            // Controleer of product naam al bestaat
            $existingProduct = $this->fetch(
                "SELECT product_id FROM products WHERE name = ?", 
                [$data['name']]
            );
            
            if ($existingProduct) {
                return ['success' => false, 'message' => 'Er bestaat al een product met deze naam'];
            }

            // Voeg product toe
            $sql = "INSERT INTO products (name, description, unit, price, category, requires_cooling) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $params = [
                $data['name'],
                $data['description'] ?? '',
                $data['unit'],
                (float) $data['price'],
                $data['category'],
                $data['requires_cooling'] ? 1 : 0
            ];

            $this->execute($sql, $params);
            $productId = $this->db->lastInsertId();

            return [
                'success' => true,
                'product_id' => $productId
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het aanmaken van het product'
            ];
        }
    }

    /**
     * Update een bestaand product
     * @param array $data Product gegevens
     * @return array Success status en bericht
     */
    public function updateProduct($data) {
        try {
            // Valideer verplichte velden
            if (empty($data['name']) || !isset($data['price']) || empty($data['unit']) || empty($data['category'])) {
                return ['success' => false, 'message' => 'Niet alle verplichte velden zijn ingevuld'];
            }

            // Controleer of product bestaat
            $existingProduct = $this->fetch(
                "SELECT product_id FROM products WHERE product_id = ?", 
                [$data['product_id']]
            );
            
            if (!$existingProduct) {
                return ['success' => false, 'message' => 'Product niet gevonden'];
            }

            // Controleer of nieuwe naam niet al bestaat bij ander product
            $duplicateName = $this->fetch(
                "SELECT product_id FROM products WHERE name = ? AND product_id != ?", 
                [$data['name'], $data['product_id']]
            );
            
            if ($duplicateName) {
                return ['success' => false, 'message' => 'Er bestaat al een product met deze naam'];
            }

            // Update product
            $sql = "UPDATE products 
                    SET name = ?, 
                        description = ?, 
                        unit = ?, 
                        price = ?, 
                        category = ?, 
                        requires_cooling = ?
                    WHERE product_id = ?";
            
            $params = [
                $data['name'],
                $data['description'],
                $data['unit'],
                (float) $data['price'],
                $data['category'],
                $data['requires_cooling'] ? 1 : 0,
                $data['product_id']
            ];

            $this->execute($sql, $params);

            return [
                'success' => true,
                'message' => 'Product succesvol bijgewerkt'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het bijwerken van het product'
            ];
        }
    }
} 