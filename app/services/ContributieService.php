<?php
/**
 * ContributieService
 * 
 * Deze service klasse beheert de contributie berekeningen
 * Verantwoordelijk voor het instellen en berekenen van contributiebedragen
 */
class ContributieService {
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
     * Berekent contributies voor een boekjaar
     * 
     * @param int $boekjaar_id Boekjaar ID
     * @param float $basisbedrag Het basisbedrag voor contributies
     * @return bool True bij succes, false bij falen
     */
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

    /**
     * Verwijdert bestaande contributies voor een boekjaar
     * 
     * @param int $boekjaar_id Boekjaar ID
     */
    private function verwijderBestaandeContributies($boekjaar_id) {
        $query = "DELETE FROM Contributie WHERE boekjaar_id = :boekjaar_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':boekjaar_id', $boekjaar_id);
        $stmt->execute();
    }

    /**
     * Maakt nieuwe contributies aan voor een boekjaar
     * 
     * @param int $boekjaar_id Boekjaar ID
     * @param float $basisbedrag Het basisbedrag voor contributies
     */
    private function maakNieuweContributies($boekjaar_id, $basisbedrag) {
        // Definieer de leeftijdscategorieën en kortingen
        $categories = [
            ['leeftijd' => 7, 'korting' => 0.50, 'soort' => 'jeugd'],
            ['leeftijd' => 12, 'korting' => 0.40, 'soort' => 'aspirant'],
            ['leeftijd' => 17, 'korting' => 0.25, 'soort' => 'junior'],
            ['leeftijd' => 50, 'korting' => 0.00, 'soort' => 'senior'],
            ['leeftijd' => 150, 'korting' => 0.45, 'soort' => 'oudere']
        ];

        // Maak contributie aan voor elke categorie
        foreach ($categories as $category) {
            $this->maakContributie($boekjaar_id, $basisbedrag, $category);
        }
    }

    /**
     * Maakt een contributie aan voor een specifieke categorie
     * 
     * @param int $boekjaar_id Boekjaar ID
     * @param float $basisbedrag Het basisbedrag
     * @param array $category De categorie informatie
     */
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

    /**
     * Haalt het ID op van een soort lid
     * 
     * @param string $omschrijving De omschrijving van het soort lid
     * @return int Het ID van het soort lid
     */
    private function getSoortLidId($omschrijving) {
        $query = "SELECT id FROM SoortLid WHERE omschrijving = :omschrijving";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':omschrijving', $omschrijving);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
} 