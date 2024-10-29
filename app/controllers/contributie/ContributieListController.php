<?php
require_once 'app/controllers/contributie/ContributieBaseController.php';

class ContributieListController extends ContributieBaseController {
    public function handle() {
        $contributies = $this->crudModel->getAllContributies();
        include 'app/views/contributie/index.php';
    }

    public function overzicht() {
        $actiefBoekjaar = $this->baseModel->getActiefBoekjaar();
        
        if (!$actiefBoekjaar) {
            $error = "Er is geen actief boekjaar. Activeer eerst een boekjaar.";
            include 'app/views/contributie/overzicht.php';
            return;
        }

        // Haal alle contributies op voor het actieve boekjaar
        $contributies = $this->crudModel->getContributieOverzicht($actiefBoekjaar['id']);
        include 'app/views/contributie/overzicht.php';
    }
} 