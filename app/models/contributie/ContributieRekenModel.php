<?php
require_once 'app/models/contributie/ContributieBaseModel.php';

class ContributieRekenModel extends ContributieBaseModel {
    public function setupContributiesVoorBoekjaar($boekjaar_id, $basisbedrag) {
        try {
            $this->db->beginTransaction();

            // Verwijder eerst bestaande contributies voor dit boekjaar
            $query = "DELETE FROM Contributie WHERE boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id);
            $stmt->execute();

            // Definieer de leeftijdscategorieën en kortingen
            $categories = [
                ['leeftijd' => 7, 'korting' => 0.50, 'soort' => 'jeugd'],    // 50% korting
                ['leeftijd' => 12, 'korting' => 0.40, 'soort' => 'aspirant'], // 40% korting
                ['leeftijd' => 17, 'korting' => 0.25, 'soort' => 'junior'],   // 25% korting
                ['leeftijd' => 50, 'korting' => 0.00, 'soort' => 'senior'],   // geen korting
                ['leeftijd' => 150, 'korting' => 0.45, 'soort' => 'oudere']   // 45% korting
            ];

            // Voeg contributie toe voor elke categorie
            foreach ($categories as $category) {
                $soort_lid_id = $this->getSoortLidIdByOmschrijving($category['soort']);
                if (!$soort_lid_id) {
                    throw new Exception("Soort lid '{$category['soort']}' niet gevonden");
                }

                $bedrag = $basisbedrag * (1 - $category['korting']);
                
                $query = "INSERT INTO Contributie (leeftijd, soort_lid_id, bedrag, boekjaar_id) 
                         VALUES (:leeftijd, :soort_lid_id, :bedrag, :boekjaar_id)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':leeftijd', $category['leeftijd'], PDO::PARAM_INT);
                $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
                $stmt->bindParam(':bedrag', $bedrag);
                $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
                
                if (!$stmt->execute()) {
                    throw new Exception("Kon contributie niet toevoegen voor {$category['soort']}");
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in setupContributiesVoorBoekjaar: " . $e->getMessage());
            return false;
        }
    }

    public function berekenContributiesVoorAlleLeden($boekjaar_id) {
        try {
            $this->db->beginTransaction();

            // Haal alle familieleden op
            $query = "SELECT f.id, f.geboortedatum, lt.soort_lid_id 
                     FROM Familielid f
                     JOIN LidmaatschapType lt ON f.id = lt.familielid_id
                     WHERE lt.boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id);
            $stmt->execute();
            $leden = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Haal de contributietarieven op voor dit boekjaar
            $query = "SELECT soort_lid_id, bedrag FROM Contributie WHERE boekjaar_id = :boekjaar_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':boekjaar_id', $boekjaar_id);
            $stmt->execute();
            $tarieven = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            // Bereken en sla de contributie op voor elk lid
            foreach ($leden as $lid) {
                $bedrag = $tarieven[$lid['soort_lid_id']] ?? 0;
                
                // Sla het contributiebedrag op voor dit lid
                $query = "INSERT INTO ContributieLid (familielid_id, boekjaar_id, bedrag, betaald) 
                         VALUES (:familielid_id, :boekjaar_id, :bedrag, 0)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':familielid_id', $lid['id']);
                $stmt->bindParam(':boekjaar_id', $boekjaar_id);
                $stmt->bindParam(':bedrag', $bedrag);
                $stmt->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
} 