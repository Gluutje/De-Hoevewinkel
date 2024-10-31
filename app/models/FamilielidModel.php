<?php
/**
 * FamilielidModel
 * 
 * Deze klasse beheert alle database interacties voor familieleden
 * Inclusief CRUD operaties, lidmaatschapstype bepaling en contributie koppelingen
 */
class FamilielidModel {
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
     * Haalt alle familieleden op met hun familie- en relatiegegevens
     * 
     * @return array Array met alle familieleden en hun gegevens
     */
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

    /**
     * Haalt een specifiek familielid op basis van ID
     * 
     * @param int $id Familielid ID
     * @return array|false Familielid gegevens of false als niet gevonden
     */
    public function getFamilielidById($id) {
        $query = "SELECT * FROM Familielid WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Voegt een nieuw familielid toe en koppelt het lidmaatschapstype
     * 
     * @param string $naam Naam van het familielid
     * @param string $geboortedatum Geboortedatum
     * @param int $familie_relatie_id ID van de familierelatie
     * @param int $familie_id ID van de familie
     * @return bool True bij succes, false bij falen
     * @throws Exception Als er geen actief boekjaar is
     */
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

            // Voeg eerst het familielid toe
            $query = "INSERT INTO Familielid (naam, geboortedatum, soort_lid_id, familie_id) 
                      VALUES (:naam, :geboortedatum, :familie_relatie_id, :familie_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':geboortedatum', $geboortedatum);
            $stmt->bindParam(':familie_relatie_id', $familie_relatie_id, PDO::PARAM_INT);
            $stmt->bindParam(':familie_id', $familie_id, PDO::PARAM_INT);
            $stmt->execute();

            $familielid_id = $this->db->lastInsertId();

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
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Werkt een bestaand familielid bij
     * Inclusief het bijwerken van het lidmaatschapstype
     * 
     * @param int $id Familielid ID
     * @param string $naam Nieuwe naam
     * @param string $geboortedatum Nieuwe geboortedatum
     * @param int $familie_relatie_id Nieuwe familie relatie ID
     * @param int $familie_id Nieuwe familie ID
     * @return bool True bij succes, false bij falen
     */
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

    /**
     * Verwijdert een familielid en gerelateerde gegevens
     * 
     * @param int $id Familielid ID
     * @return bool|string True bij succes, foutmelding string bij falen
     */
    public function deleteFamilielid($id) {
        if ($this->heeftBetalingen($id)) {
            return "Dit lid kan niet worden verwijderd omdat er al contributie is betaald. Het lid heeft recht op lidmaatschap voor het hele jaar.";
        }

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
            return "Er is een fout opgetreden bij het verwijderen van het familielid.";
        }
    }

    /**
     * Haalt alle families op
     * 
     * @return array Array met alle families
     */
    public function getAllFamilies() {
        $query = "SELECT id, naam FROM Familie ORDER BY naam";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle mogelijke familierelaties op
     * 
     * @return array Array met alle familierelaties
     */
    public function getFamilieRelaties() {
        $query = "SELECT id, omschrijving FROM SoortLid 
                  WHERE omschrijving IN ('vader', 'moeder', 'zoon', 'dochter', 'tante', 'oom', 'neef', 'nicht', 'partner') 
                  ORDER BY omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alle familieleden op voor een specifieke familie
     * 
     * @param int $familie_id Familie ID
     * @return array Array met familieleden
     */
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

    /**
     * Bepaalt het soort lid op basis van leeftijd
     * 
     * @param string $geboortedatum Geboortedatum
     * @param string|null $peildatum Peildatum voor leeftijdsberekening
     * @return int|null ID van het soort lid of null bij falen
     */
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

    /**
     * Haalt het ID op van een soort lid op basis van omschrijving
     * 
     * @param string $omschrijving Omschrijving van het soort lid
     * @return int|null ID van het soort lid of null als niet gevonden
     */
    private function getSoortLidIdByOmschrijving($omschrijving) {
        $query = "SELECT id FROM SoortLid WHERE omschrijving = :omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':omschrijving', $omschrijving);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
    }

    /**
     * Werkt de lidmaatschapstypen bij voor een nieuw boekjaar
     * 
     * @param int $boekjaar_id Boekjaar ID
     * @return bool True bij succes, false bij falen
     */
    public function updateLidmaatschapTypes($boekjaar_id) {
        try {
            // Haal alle familieleden op
            $query = "SELECT id, geboortedatum FROM Familielid";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $familieleden = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Haal het boekjaar op
            $query = "SELECT jaar FROM Boekjaar WHERE id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
            $stmt->execute();
            $boekjaar = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verwijder eerst bestaande lidmaatschapstypen voor dit boekjaar
            $query = "DELETE FROM LidmaatschapType WHERE boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
            $stmt->execute();

            // Maak een peildatum van 1 januari van het boekjaar
            $peildatum = $boekjaar['jaar'] . '-01-01';

            foreach ($familieleden as $familielid) {
                // Bepaal het soort lid op basis van leeftijd op 1 januari van het boekjaar
                $soort_lid_id = $this->bepaalSoortLid($familielid['geboortedatum'], $peildatum);

                // Voeg het lidmaatschapstype toe voor dit boekjaar
                $query = "INSERT INTO LidmaatschapType (soort_lid_id, familielid_id, boekjaar_id) 
                          VALUES (:soort_lid_id, :familielid_id, :boekjaar_id)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
                $stmt->bindParam(':familielid_id', $familielid['id'], PDO::PARAM_INT);
                $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in updateLidmaatschapTypes: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Controleert of een lid betalingen heeft gedaan
     * 
     * @param int $id Familielid ID
     * @return bool True als het lid betalingen heeft, anders false
     */
    public function heeftBetalingen($id) {
        $query = "SELECT COUNT(*) as aantal FROM contributielid WHERE familielid_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['aantal'] > 0;
    }
}
