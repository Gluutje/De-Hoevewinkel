<?php
require_once 'app/models/FamilieModel.php';
require_once 'app/models/FamilielidModel.php';

class FamilieController {
    private $model;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new FamilieModel($db);
    }

    public function index() {
        $families = $this->model->getAllFamilies();
        include 'app/views/familie/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naam = $_POST['naam'] ?? '';
            $straatnaam = $_POST['straatnaam'] ?? '';
            $huisnummer = $_POST['huisnummer'] ?? '';
            $postcode = $_POST['postcode'] ?? '';
            $plaats = $_POST['plaats'] ?? '';

            // Server-side validatie
            $errors = [];
            if (!preg_match('/^[A-Za-z\s-]+$/', $naam)) {
                $errors[] = "Ongeldige familienaam. Alleen letters, spaties en koppeltekens zijn toegestaan.";
            }
            if (!preg_match('/^[A-Za-z\s-]+$/', $straatnaam)) {
                $errors[] = "Ongeldige straatnaam. Alleen letters, spaties en koppeltekens zijn toegestaan.";
            }
            if (!preg_match('/^[0-9]+[A-Za-z]?$/', $huisnummer)) {
                $errors[] = "Ongeldig huisnummer. Voer een geldig huisnummer in (bijv. 42 of 42A).";
            }
            if (!preg_match('/^[1-9][0-9]{3}[A-Za-z]{2}$/', str_replace(' ', '', $postcode))) {
                $errors[] = "Ongeldige postcode. Voer een geldige Nederlandse postcode in (bijv. 1234 AB of 1234AB).";
            }
            if (!preg_match('/^[A-Za-z\s-]+$/', $plaats)) {
                $errors[] = "Ongeldige plaatsnaam. Alleen letters, spaties en koppeltekens zijn toegestaan.";
            }

            if (empty($errors)) {
                $postcode = strtoupper(str_replace(' ', '', $postcode)); // Verwijder spaties en zet om naar hoofdletters
                if ($this->model->addFamily($naam, $straatnaam, $huisnummer, $postcode, $plaats)) {
                    header('Location: /families');
                    exit;
                } else {
                    $error = "Er is een fout opgetreden bij het toevoegen van de familie.";
                }
            } else {
                $error = implode("<br>", $errors);
            }
        }
        include 'app/views/familie/add.php';
    }

    public function edit($id) {
        $family = $this->model->getFamilyById($id);
        if (!$family) {
            header('Location: /families');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naam = $_POST['naam'] ?? '';
            $straatnaam = $_POST['straatnaam'] ?? '';
            $huisnummer = $_POST['huisnummer'] ?? '';
            $postcode = $_POST['postcode'] ?? '';
            $plaats = $_POST['plaats'] ?? '';

            // Server-side validatie
            $errors = [];
            if (!preg_match('/^[A-Za-z\s-]+$/', $naam)) {
                $errors[] = "Ongeldige familienaam. Alleen letters, spaties en koppeltekens zijn toegestaan.";
            }
            if (!preg_match('/^[A-Za-z\s-]+$/', $straatnaam)) {
                $errors[] = "Ongeldige straatnaam. Alleen letters, spaties en koppeltekens zijn toegestaan.";
            }
            if (!preg_match('/^[0-9]+[A-Za-z]?$/', $huisnummer)) {
                $errors[] = "Ongeldig huisnummer. Voer een geldig huisnummer in (bijv. 42 of 42A).";
            }
            if (!preg_match('/^[1-9][0-9]{3}[A-Za-z]{2}$/', str_replace(' ', '', $postcode))) {
                $errors[] = "Ongeldige postcode. Voer een geldige Nederlandse postcode in (bijv. 1234 AB of 1234AB).";
            }
            if (!preg_match('/^[A-Za-z\s-]+$/', $plaats)) {
                $errors[] = "Ongeldige plaatsnaam. Alleen letters, spaties en koppeltekens zijn toegestaan.";
            }

            if (empty($errors)) {
                $postcode = strtoupper(str_replace(' ', '', $postcode)); // Verwijder spaties en zet om naar hoofdletters
                if ($this->model->updateFamily($id, $naam, $straatnaam, $huisnummer, $postcode, $plaats)) {
                    header('Location: /families');
                    exit;
                } else {
                    $error = "Er is een fout opgetreden bij het bijwerken van de familie.";
                }
            } else {
                $error = implode("<br>", $errors);
            }
        }
        include 'app/views/familie/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->deleteFamily($id)) {
                header('Location: /families');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het verwijderen van de familie.";
            }
        }
        $family = $this->model->getFamilyById($id);
        include 'app/views/familie/delete.php';
    }

    public function viewMembers($id) {
        $family = $this->model->getFamilyById($id);
        if (!$family) {
            header('Location: /families');
            exit;
        }
        
        $familielidModel = new FamilielidModel($this->db);
        $familieleden = $familielidModel->getFamilieLedenByFamilieId($id);
        
        include 'app/views/familie/view_members.php';
    }
}
