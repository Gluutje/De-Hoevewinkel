<?php
/**
 * ContributieListController
 * 
 * Deze controller beheert de weergave van contributie overzichten
 * Erft functionaliteit van ContributieBaseController
 */
class ContributieListController extends ContributieBaseController {
    /**
     * Toont het algemene contributie overzicht
     * Haalt alle contributies op voor weergave
     */
    public function handle() {
        $contributies = $this->crudModel->getAllContributies();
        include 'app/views/contributie/index.php';
    }

    /**
     * Toont het contributie overzicht voor het actieve boekjaar
     * Inclusief betaalstatus per lid
     */
    public function overzicht() {
        // Haal het actieve boekjaar op
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