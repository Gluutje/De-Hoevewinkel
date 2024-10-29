<?php
class BoekjaarModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBoekjaren() {
        $query = "SELECT * FROM Boekjaar ORDER BY jaar DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBoekjaarById($id) {
        $query = "SELECT * FROM Boekjaar WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addBoekjaar($jaar) {
        $query = "INSERT INTO Boekjaar (jaar) VALUES (:jaar)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jaar', $jaar);
        return $stmt->execute();
    }

    public function updateBoekjaar($id, $jaar) {
        $query = "UPDATE Boekjaar SET jaar = :jaar WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':jaar', $jaar);
        return $stmt->execute();
    }

    public function deleteBoekjaar($id) {
        try {
            $this->db->beginTransaction();

            // Eerst alle contributies voor dit boekjaar verwijderen
            $query = "DELETE FROM Contributie WHERE boekjaar_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder alle lidmaatschapstypen voor dit boekjaar
            $query = "DELETE FROM LidmaatschapType WHERE boekjaar_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Dan pas het boekjaar zelf verwijderen
            $query = "DELETE FROM Boekjaar WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function setActiefBoekjaar($id) {
        try {
            // Debug informatie
            echo "Begin setActiefBoekjaar<br>";
            echo "Probeer boekjaar $id te activeren<br>";

            // Controleer of het boekjaar bestaat
            $query = "SELECT * FROM Boekjaar WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $boekjaar = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$boekjaar) {
                throw new Exception("Boekjaar met ID $id niet gevonden");
            }
            echo "Boekjaar gevonden: " . $boekjaar['jaar'] . "<br>";

            // Eerst alle boekjaren op niet-actief zetten
            $query1 = "UPDATE Boekjaar SET is_actief = 0";
            $stmt1 = $this->db->prepare($query1);
            $success1 = $stmt1->execute();
            echo "Reset alle boekjaren: " . ($success1 ? "success" : "failed") . "<br>";

            // Dan het geselecteerde boekjaar op actief zetten
            $query2 = "UPDATE Boekjaar SET is_actief = 1 WHERE id = :id";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
            $success2 = $stmt2->execute();
            echo "Activeer boekjaar $id: " . ($success2 ? "success" : "failed") . "<br>";

            // Controleer of het boekjaar daadwerkelijk is geactiveerd
            $query = "SELECT is_actief FROM Boekjaar WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Controle activering: is_actief = " . ($result['is_actief'] ? "true" : "false") . "<br>";

            if (!$success1 || !$success2 || !$result['is_actief']) {
                throw new Exception("Database update failed");
            }

            echo "Boekjaar succesvol geactiveerd<br>";
            return true;
        } catch (Exception $e) {
            echo "Error in setActiefBoekjaar: " . $e->getMessage() . "<br>";
            return false;
        }
    }

    // Transactiemethoden
    public function beginTransaction() {
        if (!$this->db->inTransaction()) {
            return $this->db->beginTransaction();
        }
        return true;
    }

    public function commit() {
        if ($this->db->inTransaction()) {
            return $this->db->commit();
        }
        return true;
    }

    public function rollBack() {
        if ($this->db->inTransaction()) {
            return $this->db->rollBack();
        }
        return true;
    }
} 