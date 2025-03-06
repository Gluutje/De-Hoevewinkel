<?php
namespace App\Models;

require_once __DIR__ . '/Model.php';

/**
 * Slot Model
 * Beheert de automaatvakken
 */
class Slot extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Haal alle vakken op met product informatie
     */
    public function getAllSlots() {
        $sql = "SELECT s.*, p.name as product_name, p.requires_cooling, s.current_stock as product_stock 
                FROM slots s 
                LEFT JOIN products p ON s.product_id = p.product_id 
                ORDER BY s.slot_number";
        return $this->fetchAll($sql);
    }

    /**
     * Haal specifiek vak op
     */
    public function getSlotById($slotId) {
        $sql = "SELECT s.*, p.name as product_name, p.requires_cooling, s.current_stock as product_stock 
                FROM slots s 
                LEFT JOIN products p ON s.product_id = p.product_id 
                WHERE s.slot_id = ?";
        return $this->fetch($sql, [$slotId]);
    }

    /**
     * Update vak configuratie
     */
    public function updateSlot($slotId, $data) {
        try {
            // Start transactie
            $this->db->beginTransaction();

            // Valideer product koeling vereisten
            if (!empty($data['product_id'])) {
                $product = $this->fetch(
                    "SELECT requires_cooling FROM products WHERE product_id = ?", 
                    [$data['product_id']]
                );
                
                $slot = $this->getSlotById($slotId);
                
                if ($product['requires_cooling'] && $slot['slot_type'] !== 'COOLED') {
                    return [
                        'success' => false,
                        'message' => 'Dit product vereist een gekoeld vak'
                    ];
                }
                
                if (!$product['requires_cooling'] && $slot['slot_type'] === 'COOLED') {
                    return [
                        'success' => false,
                        'message' => 'Dit product hoeft niet gekoeld te worden'
                    ];
                }
            }

            // Update vak
            $sql = "UPDATE slots 
                    SET product_id = ?, 
                        current_stock = ?, 
                        max_capacity = ?,
                        status = ?,
                        last_refill = CURRENT_TIMESTAMP
                    WHERE slot_id = ?";

            $status = empty($data['product_id']) ? 'EMPTY' : 'FILLED';
            
            $params = [
                $data['product_id'] ?: null,
                $data['current_stock'] ?? 0,
                $data['max_capacity'],
                $status,
                $slotId
            ];

            $this->execute($sql, $params);
            
            // Commit transactie
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Vak succesvol bijgewerkt'
            ];

        } catch (\Exception $e) {
            // Rollback bij fouten
            $this->db->rollBack();
            
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het bijwerken van het vak'
            ];
        }
    }

    /**
     * Zet vak in onderhoudsmodus
     */
    public function setMaintenance($slotId, $maintenance = true) {
        $sql = "UPDATE slots 
                SET status = ?, 
                    product_id = IF(? = 1, NULL, product_id),
                    current_stock = IF(? = 1, 0, current_stock)
                WHERE slot_id = ?";
        
        $status = $maintenance ? 'MAINTENANCE' : 'EMPTY';
        
        return $this->execute($sql, [$status, $maintenance, $maintenance, $slotId]);
    }

    /**
     * Vul een vak met een product
     * @param int $slotId
     * @param int $productId
     * @return array
     */
    public function fillSlot($slotId, $productId) {
        try {
            // Start transactie
            $this->db->beginTransaction();

            // Haal vak informatie op
            $slot = $this->getSlotById($slotId);
            if (!$slot) {
                throw new \Exception('Vak niet gevonden');
            }

            // Controleer of vak niet in onderhoud is
            if ($slot['status'] === 'MAINTENANCE') {
                throw new \Exception('Vak is in onderhoud');
            }

            // Update vak met nieuw product
            $sql = "UPDATE slots 
                    SET product_id = ?,
                        current_stock = max_capacity,
                        status = 'FILLED',
                        last_refill = CURRENT_TIMESTAMP
                    WHERE slot_id = ?";

            $this->execute($sql, [$productId, $slotId]);
            
            // Commit transactie
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Vak succesvol gevuld'
            ];

        } catch (\Exception $e) {
            // Rollback bij fouten
            $this->db->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verwijder een product uit een vak en verlaag de voorraad
     * @param int $slotId
     * @return array
     */
    public function removeProduct($slotId) {
        try {
            // Start een transactie
            $this->db->beginTransaction();

            // Haal eerst de huidige slot informatie op
            $slot = $this->getSlotById($slotId);
            if (!$slot) {
                throw new \Exception('Vak niet gevonden');
            }

            if ($slot['status'] !== 'FILLED') {
                throw new \Exception('Dit vak bevat geen product');
            }

            // Update het vak
            $sql = "UPDATE slots 
                    SET product_id = NULL,
                        current_stock = 0,
                        status = 'EMPTY',
                        updated_at = CURRENT_TIMESTAMP
                    WHERE slot_id = ?";
            
            $this->execute($sql, [$slotId]);

            // Commit de transactie
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Product succesvol verwijderd uit het vak'
            ];

        } catch (\Exception $e) {
            // Rollback bij een fout
            $this->db->rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
} 