<?php
require_once 'app/models/contributie/ContributieBaseModel.php';
require_once 'app/models/contributie/ContributieCrudModel.php';
require_once 'app/models/contributie/ContributieRekenModel.php';

// ContributieController class om contributies te beheren
class ContributieController {
    private $baseModel;
    private $crudModel;
    private $rekenModel;

    // Constructor om de modellen te initialiseren
    public function __construct($db) {
        $this->baseModel = new ContributieBaseModel($db);
        $this->crudModel = new ContributieCrudModel($db);
        $this->rekenModel = new ContributieRekenModel($db);
    }

    // Methode om alle contributies van het actieve boekjaar weer te geven
    public function index() {
        // Haal contributies op voor het actieve boekjaar
        $contributies = $this->crudModel->getContributiesByBoekjaar($this->baseModel->getActiefBoekjaar()['id']);
        include 'app/views/contributie/index.php'; // Laadt de view om contributies weer te geven
    }

    // Methode om een nieuwe contributie toe te voegen
    public function add() {
        // Haal alle soorten leden en boekjaren op voor de formulieren
        $soortLeden = $this->baseModel->getAllSoortLeden();
        $boekjaren = $this->baseModel->getAllBoekjaren();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verkrijg de ingevoerde gegevens van het formulier
            $leeftijd = $_POST['leeftijd'] ?? '';
            $soort_lid_id = $_POST['soort_lid_id'] ?? '';
            $bedrag = $_POST['bedrag'] ?? '';
            $boekjaar_id = $_POST['boekjaar_id'] ?? '';

            // Voeg de contributie toe aan de database
            if ($this->crudModel->addContributie($leeftijd, $soort_lid_id, $bedrag, $boekjaar_id)) {
                header('Location: /contributies'); // Redirect na succesvolle toevoeging
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het toevoegen van de contributie.";
            }
        }
        include 'app/views/contributie/add.php'; // Laadt de view om een nieuwe contributie toe te voegen
    }

    // Methode om een bestaande contributie te bewerken
    public function edit($id) {
        // Haal de contributie op voor bewerking en de nodige dropdown opties
        $contributie = $this->crudModel->getContributieById($id);
        $soortLeden = $this->baseModel->getAllSoortLeden();
        $boekjaren = $this->baseModel->getAllBoekjaren();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verkrijg de bijgewerkte gegevens van het formulier
            $leeftijd = $_POST['leeftijd'] ?? '';
            $soort_lid_id = $_POST['soort_lid_id'] ?? '';
            $bedrag = $_POST['bedrag'] ?? '';
            $boekjaar_id = $_POST['boekjaar_id'] ?? '';

            // Werk de contributie bij in de database
            if ($this->crudModel->updateContributie($id, $leeftijd, $soort_lid_id, $bedrag, $boekjaar_id)) {
                header('Location: /contributies'); // Redirect na succesvolle bewerking
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het bijwerken van de contributie.";
            }
        }
        include 'app/views/contributie/edit.php'; // Laadt de view om een contributie te bewerken
    }

    // Methode om een contributie te verwijderen
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verwijder de contributie uit de database
            if ($this->crudModel->deleteContributie($id)) {
                header('Location: /contributies'); // Redirect na succesvolle verwijdering
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het verwijderen van de contributie.";
            }
        }
        // Haal de contributie op om te bevestigen voordat deze verwijderd wordt
        $contributie = $this->crudModel->getContributieById($id);
        include 'app/views/contributie/delete.php'; // Laadt de view om een contributie te verwijderen
    }
}

