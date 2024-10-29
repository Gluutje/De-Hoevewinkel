<?php
class ContributieModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllContributies() {
        $query = "SELECT c.*, sl.omschrijving AS soort_lid, b.jaar AS boekjaar 
                  FROM Contributie c
                  JOIN SoortLid sl ON c.soort_lid_id = sl.id
                  JOIN Boekjaar b ON c.boekjaar_id = b.id
                  ORDER BY b.jaar DESC, c.leeftijd ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContributieById($id) {
        $query = "SELECT c.*, sl.omschrijving AS soort_lid, b.jaar AS boekjaar 
                  FROM Contributie c
                  JOIN SoortLid sl ON c.soort_lid_id = sl.id
                  JOIN Boekjaar b ON c.boekjaar_id = b.id
                  WHERE c.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addContributie($leeftijd, $soort_lid_id, $bedrag, $boekjaar_id) {
        $query = "INSERT INTO Contributie (leeftijd, soort_lid_id, bedrag, boekjaar_id) 
                  VALUES (:leeftijd, :soort_lid_id, :bedrag, :boekjaar_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':leeftijd', $leeftijd, PDO::PARAM_INT);
        $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
        $stmt->bindParam(':bedrag', $bedrag);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateContributie($id, $leeftijd, $soort_lid_id, $bedrag, $boekjaar_id) {
        $query = "UPDATE Contributie 
                  SET leeftijd = :leeftijd, soort_lid_id = :soort_lid_id, 
                      bedrag = :bedrag, boekjaar_id = :boekjaar_id 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':leeftijd', $leeftijd, PDO::PARAM_INT);
        $stmt->bindParam(':soort_lid_id', $soort_lid_id, PDO::PARAM_INT);
        $stmt->bindParam(':bedrag', $bedrag);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteContributie($id) {
        $query = "DELETE FROM Contributie WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
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

    public function setupContributiesVoorBoekjaar($boekjaar_id, $basisbedrag) {
        // Begin een transactie
        $this->db->beginTransaction();

        try {
            // Verwijder bestaande contributies voor dit boekjaar
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
                $bedrag = $basisbedrag * (1 - $category['korting']);
                
                $this->addContributie(
                    $category['leeftijd'],
                    $soort_lid_id,
                    $bedrag,
                    $boekjaar_id
                );
            }

            // Commit de transactie
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Als er iets misgaat, rol de transactie terug
            $this->db->rollBack();
            return false;
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

    public function verwijderContributiesVoorBoekjaar($boekjaar_id) {
        $query = "DELETE FROM Contributie WHERE boekjaar_id = :boekjaar_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
