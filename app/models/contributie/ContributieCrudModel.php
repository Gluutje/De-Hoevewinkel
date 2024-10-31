<?php
/**
 * ContributieCrudModel
 * 
 * Deze klasse beheert de CRUD operaties voor contributies
 * Erft basis functionaliteit van ContributieBaseModel
 */
require_once 'app/models/contributie/ContributieBaseModel.php';

class ContributieCrudModel extends ContributieBaseModel {
    /**
     * Haalt alle contributies op voor een specifiek boekjaar
     * 
     * @param int $boekjaar_id Boekjaar ID
     * @return array Array met contributies en gerelateerde gegevens
     */
    public function getContributiesByBoekjaar($boekjaar_id) {
        $query = "SELECT c.*, sl.omschrijving AS soort_lid 
                  FROM Contributie c
                  JOIN SoortLid sl ON c.soort_lid_id = sl.id
                  WHERE c.boekjaar_id = :boekjaar_id
                  ORDER BY c.leeftijd ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt een overzicht op van alle contributies voor het actieve boekjaar
     * Inclusief lid- en betaalgegevens
     * 
     * @param int $boekjaar_id Boekjaar ID
     * @return array Array met contributie overzicht
     */
    public function getContributieOverzicht($boekjaar_id) {
        $query = "SELECT f.id AS familielid_id, f.naam AS lid_naam, f.geboortedatum, 
                         fm.naam AS familie_naam,
                         sl.omschrijving AS soort_lid,
                         c.bedrag,
                         cl.betaald,
                         cl.betaaldatum
                  FROM Familielid f
                  JOIN Familie fm ON f.familie_id = fm.id
                  JOIN LidmaatschapType lt ON f.id = lt.familielid_id
                  JOIN SoortLid sl ON lt.soort_lid_id = sl.id
                  JOIN Contributie c ON lt.soort_lid_id = c.soort_lid_id
                  LEFT JOIN ContributieLid cl ON f.id = cl.familielid_id AND cl.boekjaar_id = :boekjaar_id
                  WHERE lt.boekjaar_id = :boekjaar_id
                  AND c.boekjaar_id = :boekjaar_id
                  ORDER BY fm.naam, f.naam";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verwerkt een contributiebetaling
     * Gebruikt transacties voor data-integriteit
     * 
     * @param int $familielid_id Familielid ID
     * @param int $boekjaar_id Boekjaar ID
     * @return bool True bij succes, false bij falen
     */
    public function verwerkBetaling($familielid_id, $boekjaar_id) {
        try {
            $this->db->beginTransaction();

            // Controleer of er al een betaling bestaat
            $query = "SELECT id FROM ContributieLid 
                     WHERE familielid_id = :familielid_id 
                     AND boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familielid_id', $familielid_id);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id);
            $stmt->execute();
            $bestaandeBetaling = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($bestaandeBetaling) {
                // Update bestaande betaling
                $query = "UPDATE ContributieLid 
                         SET betaald = 1, betaaldatum = CURRENT_DATE 
                         WHERE familielid_id = :familielid_id 
                         AND boekjaar_id = :boekjaar_id";
            } else {
                // Haal eerst het juiste bedrag op
                $query = "SELECT c.bedrag
                         FROM Familielid f
                         JOIN LidmaatschapType lt ON f.id = lt.familielid_id
                         JOIN Contributie c ON lt.soort_lid_id = c.soort_lid_id
                         WHERE f.id = :familielid_id
                         AND lt.boekjaar_id = :boekjaar_id
                         AND c.boekjaar_id = :boekjaar_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':familielid_id', $familielid_id);
                $stmt->bindParam(':boekjaar_id', $boekjaar_id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $bedrag = $result['bedrag'];

                // Voeg nieuwe betaling toe
                $query = "INSERT INTO ContributieLid (familielid_id, boekjaar_id, bedrag, betaald, betaaldatum) 
                         VALUES (:familielid_id, :boekjaar_id, :bedrag, 1, CURRENT_DATE)";
            }

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':familielid_id', $familielid_id);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id);
            if (!$bestaandeBetaling) {
                $stmt->bindParam(':bedrag', $bedrag);
            }
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in verwerkBetaling: " . $e->getMessage());
            return false;
        }
    }
} 