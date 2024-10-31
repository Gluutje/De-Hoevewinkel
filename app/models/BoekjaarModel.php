<?php
/**
 * BoekjaarModel
 * 
 * Deze klasse beheert alle database interacties voor boekjaren
 * Inclusief CRUD operaties en activering van boekjaren
 */
class BoekjaarModel {
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
     * Haalt alle boekjaren op uit de database
     * 
     * @return array Array met alle boekjaren
     */
    public function getAllBoekjaren() {
        $query = "SELECT * FROM Boekjaar ORDER BY jaar DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt een specifiek boekjaar op basis van ID
     * 
     * @param int $id Boekjaar ID
     * @return array|false Boekjaar gegevens of false als niet gevonden
     */
    public function getBoekjaarById($id) {
        $query = "SELECT * FROM Boekjaar WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Voegt een nieuw boekjaar toe
     * 
     * @param string $jaar Het jaar voor het nieuwe boekjaar
     * @return bool True bij succes, false bij falen
     */
    public function addBoekjaar($jaar) {
        $query = "INSERT INTO Boekjaar (jaar) VALUES (:jaar)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jaar', $jaar);
        return $stmt->execute();
    }

    /**
     * Werkt een bestaand boekjaar bij
     * 
     * @param int $id Boekjaar ID
     * @param string $jaar Nieuw jaar voor het boekjaar
     * @return bool True bij succes, false bij falen
     */
    public function updateBoekjaar($id, $jaar) {
        $query = "UPDATE Boekjaar SET jaar = :jaar WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':jaar', $jaar);
        return $stmt->execute();
    }

    /**
     * Controleert of een boekjaar actief is
     * 
     * @param int $id Boekjaar ID
     * @return bool True als het boekjaar actief is, anders false
     */
    public function isActief($id) {
        $query = "SELECT is_actief FROM Boekjaar WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['is_actief'] == 1;
    }

    /**
     * Verwijdert een boekjaar en alle gerelateerde gegevens
     * 
     * @param int $id Boekjaar ID
     * @return bool|string True bij succes, foutmelding string bij falen
     */
    public function deleteBoekjaar($id) {
        if ($this->isActief($id)) {
            return "Kan een actief boekjaar niet verwijderen. Maak eerst een ander boekjaar actief.";
        }

        try {
            $this->db->beginTransaction();

            // Eerst contributielid records verwijderen
            $query = "DELETE FROM contributielid WHERE boekjaar_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Dan contributies verwijderen
            $query = "DELETE FROM Contributie WHERE boekjaar_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder lidmaatschapstypen
            $query = "DELETE FROM LidmaatschapType WHERE boekjaar_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Als laatste het boekjaar zelf verwijderen
            $query = "DELETE FROM Boekjaar WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return "Er is een fout opgetreden bij het verwijderen van het boekjaar.";
        }
    }

    /**
     * Activeert een specifiek boekjaar
     * Deactiveert eerst alle andere boekjaren
     * 
     * @param int $id Boekjaar ID
     * @return bool True bij succes, false bij falen
     */
    public function setActiefBoekjaar($id) {
        try {
            // Controleer of het boekjaar bestaat
            $query = "SELECT * FROM Boekjaar WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $boekjaar = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$boekjaar) {
                throw new Exception("Boekjaar met ID $id niet gevonden");
            }

            // Eerst alle boekjaren op niet-actief zetten
            $query1 = "UPDATE Boekjaar SET is_actief = 0";
            $stmt1 = $this->db->prepare($query1);
            $success1 = $stmt1->execute();

            // Dan het geselecteerde boekjaar op actief zetten
            $query2 = "UPDATE Boekjaar SET is_actief = 1 WHERE id = :id";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
            $success2 = $stmt2->execute();

            // Controleer of het boekjaar daadwerkelijk is geactiveerd
            $query = "SELECT is_actief FROM Boekjaar WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$success1 || !$success2 || !$result['is_actief']) {
                throw new Exception("Database update failed");
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Start een nieuwe database transactie
     * 
     * @return bool True bij succes
     */
    public function beginTransaction() {
        if (!$this->db->inTransaction()) {
            return $this->db->beginTransaction();
        }
        return true;
    }

    /**
     * Commit de huidige database transactie
     * 
     * @return bool True bij succes
     */
    public function commit() {
        if ($this->db->inTransaction()) {
            return $this->db->commit();
        }
        return true;
    }

    /**
     * Rollback de huidige database transactie
     * 
     * @return bool True bij succes
     */
    public function rollBack() {
        if ($this->db->inTransaction()) {
            return $this->db->rollBack();
        }
        return true;
    }
} 