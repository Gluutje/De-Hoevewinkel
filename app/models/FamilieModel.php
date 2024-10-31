<?php
/**
 * FamilieModel
 * 
 * Deze klasse beheert alle database interacties voor families
 * Inclusief CRUD operaties en gerelateerde functionaliteit
 */
class FamilieModel {
    /** @var PDO Database connectie */
    private $db;

    /**
     * Constructor
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Haalt alle families op uit de database
     * Gesorteerd op familienaam
     * 
     * @return array Array met alle families
     */
    public function getAllFamilies() {
        $query = "SELECT * FROM Familie ORDER BY naam";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt een specifieke familie op basis van ID
     * 
     * @param int $id Familie ID
     * @return array|false Familie gegevens of false als niet gevonden
     */
    public function getFamilyById($id) {
        $query = "SELECT * FROM Familie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Voegt een nieuwe familie toe aan de database
     * 
     * @param string $naam Familienaam
     * @param string $straatnaam Straatnaam
     * @param string $huisnummer Huisnummer
     * @param string $postcode Postcode
     * @param string $plaats Plaatsnaam
     * @return bool True bij succes, false bij falen
     */
    public function addFamily($naam, $straatnaam, $huisnummer, $postcode, $plaats) {
        $query = "INSERT INTO Familie (naam, straatnaam, huisnummer, postcode, plaats) 
                  VALUES (:naam, :straatnaam, :huisnummer, :postcode, :plaats)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':straatnaam', $straatnaam);
        $stmt->bindParam(':huisnummer', $huisnummer);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindParam(':plaats', $plaats);
        return $stmt->execute();
    }

    /**
     * Werkt een bestaande familie bij in de database
     * 
     * @param int $id Familie ID
     * @param string $naam Nieuwe familienaam
     * @param string $straatnaam Nieuwe straatnaam
     * @param string $huisnummer Nieuw huisnummer
     * @param string $postcode Nieuwe postcode
     * @param string $plaats Nieuwe plaatsnaam
     * @return bool True bij succes, false bij falen
     */
    public function updateFamily($id, $naam, $straatnaam, $huisnummer, $postcode, $plaats) {
        $query = "UPDATE Familie 
                  SET naam = :naam, straatnaam = :straatnaam, 
                      huisnummer = :huisnummer, postcode = :postcode, 
                      plaats = :plaats 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':naam', $naam);
        $stmt->bindParam(':straatnaam', $straatnaam);
        $stmt->bindParam(':huisnummer', $huisnummer);
        $stmt->bindParam(':postcode', $postcode);
        $stmt->bindParam(':plaats', $plaats);
        return $stmt->execute();
    }

    /**
     * Verwijdert een familie en alle gerelateerde gegevens
     * Gebruikt een transactie om data-integriteit te waarborgen
     * Verwijdert ook alle lidmaatschapstypen en contributiebetalingen van familieleden
     * 
     * @param int $id Familie ID
     * @return bool True bij succes, false bij falen
     */
    public function deleteFamily($id) {
        try {
            $this->db->beginTransaction();

            // Verwijder eerst alle lidmaatschapstypen van familieleden
            $query = "DELETE lt FROM LidmaatschapType lt 
                      JOIN Familielid f ON lt.familielid_id = f.id 
                      WHERE f.familie_id = :familie_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familie_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder alle contributiebetalingen
            $query = "DELETE cl FROM ContributieLid cl 
                      JOIN Familielid f ON cl.familielid_id = f.id 
                      WHERE f.familie_id = :familie_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familie_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder alle familieleden
            $query = "DELETE FROM Familielid WHERE familie_id = :familie_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familie_id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder de familie zelf
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
