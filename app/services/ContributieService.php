<?php
class ContributieService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function berekenContributies($boekjaar_id, $basisbedrag) {
        try {
            $this->db->beginTransaction();

            // Verwijder bestaande contributies
            $this->verwijderBestaandeContributies($boekjaar_id);

            // Maak nieuwe contributies aan
            $this->maakNieuweContributies($boekjaar_id, $basisbedrag);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function verwijderBestaandeContributies($boekjaar_id) {
        $query = "DELETE FROM Contributie WHERE boekjaar_id = :boekjaar_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id);
        $stmt->execute();
    }

    private function maakNieuweContributies($boekjaar_id, $basisbedrag) {
        $categories = [
            ['leeftijd' => 7, 'korting' => 0.50, 'soort' => 'jeugd'],
            ['leeftijd' => 12, 'korting' => 0.40, 'soort' => 'aspirant'],
            ['leeftijd' => 17, 'korting' => 0.25, 'soort' => 'junior'],
            ['leeftijd' => 50, 'korting' => 0.00, 'soort' => 'senior'],
            ['leeftijd' => 150, 'korting' => 0.45, 'soort' => 'oudere']
        ];

        foreach ($categories as $category) {
            $this->maakContributie($boekjaar_id, $basisbedrag, $category);
        }
    }

    private function maakContributie($boekjaar_id, $basisbedrag, $category) {
        $soort_lid_id = $this->getSoortLidId($category['soort']);
        $bedrag = $basisbedrag * (1 - $category['korting']);

        $query = "INSERT INTO Contributie (leeftijd, soort_lid_id, bedrag, boekjaar_id) 
                  VALUES (:leeftijd, :soort_lid_id, :bedrag, :boekjaar_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':leeftijd', $category['leeftijd']);
        $stmt->bindParam(':soort_lid_id', $soort_lid_id);
        $stmt->bindParam(':bedrag', $bedrag);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id);
        $stmt->execute();
    }

    private function getSoortLidId($omschrijving) {
        $query = "SELECT id FROM SoortLid WHERE omschrijving = :omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':omschrijving', $omschrijving);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
} 