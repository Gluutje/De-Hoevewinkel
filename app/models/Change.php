<?php
namespace App\Models;

class Change {
    private $db;
    
    public function __construct() {
        $this->db = new \PDO('mysql:host=localhost;dbname=hoevewinkel', 'root', '');
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Haal alle geldeenheden en hun status op
     */
    public function getAllUnits() {
        $query = "SELECT * FROM cash_status ORDER BY denomination DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Bereken het totale wisselgeld in de automaat
     */
    public function getTotalAmount() {
        $query = "SELECT SUM(denomination * current_stock) as total FROM cash_status";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Update de voorraad van een geldeenheid
     */
    public function updateStock($cashId, $newStock) {
        // Haal eerst de maximum_allowed waarde op
        $query = "SELECT maximum_allowed FROM cash_status WHERE cash_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$cashId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Controleer of de nieuwe voorraad niet het maximum overschrijdt
        if ($newStock > $result['maximum_allowed']) {
            return [
                'success' => false,
                'message' => 'De nieuwe voorraad overschrijdt het maximum van ' . $result['maximum_allowed']
            ];
        }

        // Update de voorraad
        $query = "UPDATE cash_status SET current_stock = ? WHERE cash_id = ?";
        $stmt = $this->db->prepare($query);
        
        try {
            $stmt->execute([$newStock, $cashId]);
            return [
                'success' => true,
                'message' => 'Voorraad succesvol bijgewerkt'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het bijwerken van de voorraad'
            ];
        }
    }
} 