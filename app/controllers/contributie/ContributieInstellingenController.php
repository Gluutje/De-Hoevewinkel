<?php
/**
 * ContributieInstellingenController
 * 
 * Deze controller beheert de contributie instellingen
 * Verantwoordelijk voor het instellen van basisbedragen en kortingen
 */
require_once 'app/controllers/contributie/ContributieBaseController.php';
require_once 'app/services/ContributieService.php';

class ContributieInstellingenController extends ContributieBaseController {
    /** @var ContributieService Instance van de ContributieService */
    private $contributieService;

    /**
     * Constructor initialiseert de basis controller en contributie service
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        parent::__construct($db);
        $this->contributieService = new ContributieService($db);
    }

    /**
     * Hoofdmethode voor het beheren van contributie instellingen
     * Verwerkt zowel GET als POST requests
     */
    public function handle() {
        // Haal het actieve boekjaar op
        $actiefBoekjaar = $this->baseModel->getActiefBoekjaar();
        
        if (!$actiefBoekjaar) {
            $error = "Er is geen actief boekjaar. Activeer eerst een boekjaar.";
            include 'app/views/contributie/instellingen.php';
            return;
        }

        // Haal de huidige contributies op voor het actieve boekjaar
        $contributies = $this->crudModel->getContributiesByBoekjaar($actiefBoekjaar['id']);
        
        // Als er nog geen contributies zijn, gebruik dan een standaard basisbedrag
        $basisbedrag = 100.00;
        if (!empty($contributies)) {
            // Zoek het hoogste bedrag (dit is het basisbedrag zonder korting)
            foreach ($contributies as $contributie) {
                if ($contributie['bedrag'] > $basisbedrag) {
                    $basisbedrag = $contributie['bedrag'];
                }
            }
        }

        // Verwerk het formulier als er een POST request is
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $basisbedrag = $_POST['basisbedrag'] ?? 100.00;

            if ($this->contributieService->berekenContributies($actiefBoekjaar['id'], $basisbedrag)) {
                $success = "Contributies zijn succesvol ingesteld voor boekjaar " . $actiefBoekjaar['jaar'];
                // Haal de bijgewerkte contributies op
                $contributies = $this->crudModel->getContributiesByBoekjaar($actiefBoekjaar['id']);
            } else {
                $error = "Er is een fout opgetreden bij het instellen van de contributies.";
            }
        }

        include 'app/views/contributie/instellingen.php';
    }
} 