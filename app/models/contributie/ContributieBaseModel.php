<?php
class ContributieBaseModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllSoortLeden() {
        $query = "SELECT id, omschrijving FROM SoortLid 
                  WHERE omschrijving IN ('jeugd', 'aspirant', 'junior', 'senior', 'oudere') 
                  ORDER BY omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBoekjaren() {
        $query = "SELECT id, jaar FROM Boekjaar ORDER BY jaar DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiefBoekjaar() {
        $query = "SELECT * FROM Boekjaar WHERE is_actief = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getSoortLidIdByOmschrijving($omschrijving) {
        $query = "SELECT id FROM SoortLid WHERE omschrijving = :omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':omschrijving', $omschrijving);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }
} 