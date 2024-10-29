<?php
require_once 'app/controllers/contributie/ContributieBaseController.php';
require_once 'app/services/ContributieService.php';

class ContributieInstellingenController extends ContributieBaseController {
    private $contributieService;

    public function __construct($db) {
        parent::__construct($db);
        $this->contributieService = new ContributieService($db);
    }

    public function handle() {
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