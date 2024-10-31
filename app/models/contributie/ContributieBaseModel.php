<?php
/**
 * ContributieBaseModel
 * 
 * Basis model voor contributie functionaliteit
 * Bevat gedeelde functionaliteit voor alle contributie models
 */
class ContributieBaseModel {
    /** @var PDO Database connectie */
    protected $db;

    /**
     * Constructor
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Haalt alle soorten leden op die relevant zijn voor contributies
     * 
     * @return array Array met alle soorten leden
     */
    public function getAllSoortLeden() {
        $query = "SELECT id, omschrijving FROM SoortLid 
                  WHERE omschrijving IN ('jeugd', 'aspirant', 'junior', 'senior', 'oudere') 
                  ORDER BY omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle boekjaren op, gesorteerd op jaar (aflopend)
     * 
     * @return array Array met alle boekjaren
     */
    public function getAllBoekjaren() {
        $query = "SELECT id, jaar FROM Boekjaar ORDER BY jaar DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt het actieve boekjaar op
     * 
     * @return array|false Boekjaar gegevens of false als er geen actief boekjaar is
     */
    public function getActiefBoekjaar() {
        $query = "SELECT * FROM Boekjaar WHERE is_actief = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt het ID op van een soort lid op basis van omschrijving
     * 
     * @param string $omschrijving Omschrijving van het soort lid
     * @return int|null ID van het soort lid of null als niet gevonden
     */
    protected function getSoortLidIdByOmschrijving($omschrijving) {
        $query = "SELECT id FROM SoortLid WHERE omschrijving = :omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':omschrijving', $omschrijving);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }
} 