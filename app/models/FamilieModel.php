<?php
class FamilieModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllFamilies() {
        $query = "SELECT * FROM Familie ORDER BY naam";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFamilyById($id) {
        $query = "SELECT * FROM Familie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addFamily($naam, $straatnaam, $huisnummer, $postcode, $plaats) {
        $query = "INSERT INTO Familie (naam, straatnaam, huisnummer, postcode, plaats) VALUES (:naam, :straatnaam, :huisnummer, :postcode, :plaats)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':straatnaam', $straatnaam);
        $stmt->bindParam(':huisnummer', $huisnummer);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindParam(':plaats', $plaats);
        return $stmt->execute();
    }

    public function updateFamily($id, $naam, $straatnaam, $huisnummer, $postcode, $plaats) {
        $query = "UPDATE Familie SET naam = :naam, straatnaam = :straatnaam, huisnummer = :huisnummer, postcode = :postcode, plaats = :plaats WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':straatnaam', $straatnaam);
        $stmt->bindParam(':huisnummer', $huisnummer);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindParam(':plaats', $plaats);
        return $stmt->execute();
    }

    public function deleteFamily($id) {
        try {
            $this->db->beginTransaction();

            // Verwijder eerst alle lidmaatschapstypen van familieleden in deze familie
            $query = "DELETE lt FROM LidmaatschapType lt 
                      JOIN Familielid f ON lt.familielid_id = f.id 
                      WHERE f.familie_id = :familie_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familie_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder alle contributiebetalingen van familieleden in deze familie
            $query = "DELETE cl FROM ContributieLid cl 
                      JOIN Familielid f ON cl.familielid_id = f.id 
                      WHERE f.familie_id = :familie_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familie_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder alle familieleden van deze familie
            $query = "DELETE FROM Familielid WHERE familie_id = :familie_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familie_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder tenslotte de familie zelf
            $query = "DELETE FROM Familie WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in deleteFamily: " . $e->getMessage());
            return false;
        }
    }
}
