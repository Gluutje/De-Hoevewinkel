<?php
class FamilielidModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllFamilieleden() {
        $query = "SELECT f.*, fm.naam AS familie_naam, sl.omschrijving AS familie_relatie 
                  FROM Familielid f 
                  JOIN Familie fm ON f.familie_id = fm.id 
                  JOIN SoortLid sl ON f.soort_lid_id = sl.id
                  ORDER BY fm.naam, f.naam";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFamilielidById($id) {
        $query = "SELECT * FROM Familielid WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addFamilielid($naam, $geboortedatum, $familie_relatie_id, $familie_id) {
        try {
            // Controleer eerst of er een actief boekjaar is
            $query = "SELECT id FROM Boekjaar WHERE is_actief = 1 LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $boekjaar = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$boekjaar) {
                throw new Exception("De penningmeester moet eerst een boekjaar activeren, voordat je een familielid kan toevoegen.");
            }

            $this->db->beginTransaction();
            echo "Transactie gestart<br>";

            // Voeg eerst het familielid toe
            $query = "INSERT INTO Familielid (naam, geboortedatum, soort_lid_id, familie_id) 
                      VALUES (:naam, :geboortedatum, :familie_relatie_id, :familie_id)";
            echo "Query: " . $query . "<br>";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':geboortedatum', $geboortedatum);
            $stmt->bindParam(':familie_relatie_id', $familie_relatie_id, PDO::PARAM_INT);
            $stmt->bindParam(':familie_id', $familie_id, PDO::PARAM_INT);
            
            $success = $stmt->execute();
            echo "Insert familielid resultaat: " . ($success ? "success" : "failed") . "<br>";
            if (!$success) {
                $error = $stmt->errorInfo();
                echo "Database error: " . print_r($error, true) . "<br>";
                throw new Exception("Kon familielid niet toevoegen: " . $error[2]);
            }

            $familielid_id = $this->db->lastInsertId();
            echo "Nieuw familielid ID: " . $familielid_id . "<br>";

            // Bepaal het lidmaatschapstype op basis van leeftijd
            $soort_lid_id = $this->bepaalSoortLid($geboortedatum);
            
            if (!$soort_lid_id) {
                throw new Exception("Kon geen geschikt lidmaatschapstype bepalen");
            }

            // Sla het lidmaatschapstype op
            $query = "INSERT INTO LidmaatschapType (soort_lid_id, familielid_id, boekjaar_id) 
                      VALUES (:soort_lid_id, :familielid_id, :boekjaar_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
            $stmt->bindParam(':familielid_id', $familielid_id, PDO::PARAM_INT);
            $stmt->bindParam(':boekjaar_id', $boekjaar['id'], PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            echo "Transactie succesvol afgerond<br>";
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e; // Gooi de exception door naar de controller
        }
    }

    public function updateFamilielid($id, $naam, $geboortedatum, $familie_relatie_id, $familie_id) {
        try {
            $this->db->beginTransaction();

            // Update het familielid
            $query = "UPDATE Familielid 
                      SET naam = :naam, geboortedatum = :geboortedatum, 
                          soort_lid_id = :familie_relatie_id, familie_id = :familie_id 
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':geboortedatum', $geboortedatum);
            $stmt->bindParam(':familie_relatie_id', $familie_relatie_id, PDO::PARAM_INT);
            $stmt->bindParam(':familie_id', $familie_id, PDO::PARAM_INT);
            $stmt->execute();

            // Bepaal het lidmaatschapstype op basis van leeftijd
            $soort_lid_id = $this->bepaalSoortLid($geboortedatum);

            // Haal het actieve boekjaar op
            $query = "SELECT id FROM Boekjaar WHERE is_actief = 1 LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $boekjaar = $stmt->fetch(PDO::FETCH_ASSOC);
            $boekjaar_id = $boekjaar['id'];

            // Update het lidmaatschapstype
            $query = "UPDATE LidmaatschapType 
                      SET soort_lid_id = :soort_lid_id 
                      WHERE familielid_id = :familielid_id AND boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
            $stmt->bindParam(':familielid_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteFamilielid($id) {
        try {
            $this->db->beginTransaction();

            // Verwijder eerst alle lidmaatschapstypen voor dit familielid
            $query = "DELETE FROM LidmaatschapType WHERE familielid_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verwijder daarna het familielid zelf
            $query = "DELETE FROM Familielid WHERE id = :id";
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

    public function getAllFamilies() {
        $query = "SELECT id, naam FROM Familie ORDER BY naam";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFamilieRelaties() {
        $query = "SELECT id, omschrijving FROM SoortLid 
                  WHERE omschrijving IN ('vader', 'moeder', 'zoon', 'dochter', 'tante', 'oom', 'neef', 'nicht', 'partner') 
                  ORDER BY omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFamilieLedenByFamilieId($familie_id) {
        $query = "SELECT f.*, sl.omschrijving AS familie_relatie 
                  FROM Familielid f 
                  JOIN SoortLid sl ON f.soort_lid_id = sl.id
                  WHERE f.familie_id = :familie_id
                  ORDER BY f.naam";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':familie_id', $familie_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function bepaalSoortLid($geboortedatum, $peildatum = null) {
        if ($peildatum === null) {
            $peildatum = date('Y-m-d'); // Gebruik huidige datum als geen peildatum is opgegeven
        }

        // Bereken leeftijd op peildatum
        $geboorteDt = new DateTime($geboortedatum);
        $peilDt = new DateTime($peildatum);
        $leeftijd = $geboorteDt->diff($peilDt)->y;

        // Bepaal soort lid op basis van leeftijd
        if ($leeftijd < 8) {
            return $this->getSoortLidIdByOmschrijving('jeugd');
        } elseif ($leeftijd < 12) {
            return $this->getSoortLidIdByOmschrijving('aspirant');
        } elseif ($leeftijd < 17) {
            return $this->getSoortLidIdByOmschrijving('junior');
        } elseif ($leeftijd < 50) {
            return $this->getSoortLidIdByOmschrijving('senior');
        } else {
            return $this->getSoortLidIdByOmschrijving('oudere');
        }
    }

    private function getSoortLidIdByOmschrijving($omschrijving) {
        $query = "SELECT id FROM SoortLid WHERE omschrijving = :omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':omschrijving', $omschrijving);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    public function updateLidmaatschapTypes($boekjaar_id) {
        try {
            echo "Start updateLidmaatschapTypes<br>";
            
            // Haal alle familieleden op
            $query = "SELECT id, geboortedatum FROM Familielid";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $familieleden = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Aantal familieleden gevonden: " . count($familieleden) . "<br>";

            // Haal het boekjaar op
            $query = "SELECT jaar FROM Boekjaar WHERE id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
            $stmt->execute();
            $boekjaar = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Boekjaar gevonden: " . $boekjaar['jaar'] . "<br>";

            // Verwijder eerst bestaande lidmaatschapstypen voor dit boekjaar
            $query = "DELETE FROM LidmaatschapType WHERE boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
            $stmt->execute();
            echo "Oude lidmaatschapstypen verwijderd<br>";

            // Maak een peildatum van 1 januari van het boekjaar
            $peildatum = $boekjaar['jaar'] . '-01-01';
            echo "Peildatum ingesteld op: " . $peildatum . "<br>";

            foreach ($familieleden as $familielid) {
                echo "Verwerken familielid ID: " . $familielid['id'] . "<br>";
                
                // Bepaal het soort lid op basis van leeftijd op 1 januari van het boekjaar
                $soort_lid_id = $this->bepaalSoortLid($familielid['geboortedatum'], $peildatum);
                echo "Bepaald soort lid ID: " . $soort_lid_id . "<br>";

                // Voeg het lidmaatschapstype toe voor dit boekjaar
                $query = "INSERT INTO LidmaatschapType (soort_lid_id, familielid_id, boekjaar_id) 
                          VALUES (:soort_lid_id, :familielid_id, :boekjaar_id)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
                $stmt->bindParam(':familielid_id', $familielid['id'], PDO::PARAM_INT);
                $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
                $success = $stmt->execute();
                echo "Insert resultaat: " . ($success ? "success" : "failed") . "<br>";
            }

            echo "Alle lidmaatschapstypen bijgewerkt<br>";
            return true;
        } catch (Exception $e) {
            echo "Error in updateLidmaatschapTypes: " . $e->getMessage() . "<br>";
            return false;
        }
    }
}
