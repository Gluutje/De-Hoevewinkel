<?php
require_once 'app/models/FamilielidModel.php';

// FamilielidController class om familieleden te beheren
class FamilielidController {
    private $model;

    // Constructor om het model te initialiseren
    public function __construct($db) {
        $this->model = new FamilielidModel($db);
    }

    // Methode om alle familieleden weer te geven
    public function index() {
        // Haal alle familieleden op uit het model
        $familieleden = $this->model->getAllFamilieleden();
        include 'app/views/familielid/index.php'; // Laadt de view om familieleden weer te geven
    }

    // Methode om een nieuw familielid toe te voegen
    public function add() {
        // Haal alle families en familie relaties op voor het formulier
        $families = $this->model->getAllFamilies();
        $familieRelaties = $this->model->getFamilieRelaties();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verkrijg de ingevoerde gegevens van het formulier
            $naam = $_POST['naam'] ?? '';
            $geboortedatum = $_POST['geboortedatum'] ?? '';
            $familie_relatie_id = $_POST['familie_relatie_id'] ?? '';
            $familie_id = $_POST['familie_id'] ?? '';

            try {
                // Voeg het familielid toe aan de database
                if ($this->model->addFamilielid($naam, $geboortedatum, $familie_relatie_id, $familie_id)) {
                    header('Location: /familieleden'); // Redirect na succesvolle toevoeging
                    exit;
                }
            } catch (Exception $e) {
                $error = $e->getMessage(); // Foutmelding bij een exception
            }
        }
        include 'app/views/familielid/add.php'; // Laadt de view om een nieuw familielid toe te voegen
    }

    // Methode om een bestaand familielid te bewerken
    public function edit($id) {
        // Haal het familielid en de nodige dropdown opties op
        $familielid = $this->model->getFamilielidById($id);
        $families = $this->model->getAllFamilies();
        $familieRelaties = $this->model->getFamilieRelaties();

        if (!$familielid) {
            header('Location: /familieleden'); // Redirect als het familielid niet bestaat
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verkrijg de bijgewerkte gegevens van het formulier
            $naam = $_POST['naam'] ?? '';
            $geboortedatum = $_POST['geboortedatum'] ?? '';
            $familie_relatie_id = $_POST['familie_relatie_id'] ?? '';
            $familie_id = $_POST['familie_id'] ?? '';

            // Werk het familielid bij in de database
            if ($this->model->updateFamilielid($id, $naam, $geboortedatum, $familie_relatie_id, $familie_id)) {
                header('Location: /familieleden'); // Redirect na succesvolle bewerking
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het bijwerken van het familielid.";
            }
        }
        include 'app/views/familielid/edit.php'; // Laadt de view om een familielid te bewerken
    }

    // Methode om een familielid te verwijderen
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->model->deleteFamilielid($id);
            
            if ($result === true) {
                header('Location: /familieleden');
                exit;
            } else {
                $error = is_string($result) ? $result : "Er is een fout opgetreden bij het verwijderen van het familielid.";
            }
        }
        
        $familielid = $this->model->getFamilielidById($id);
        include 'app/views/familielid/delete.php';
    }
}

