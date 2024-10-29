<?php
require_once 'app/models/BoekjaarModel.php';
require_once 'app/models/FamilielidModel.php';

class BoekjaarController {
    private $model;
    private $familielidModel;

    public function __construct($db) {
        $this->model = new BoekjaarModel($db);
        $this->familielidModel = new FamilielidModel($db);
    }

    public function index() {
        $boekjaren = $this->model->getAllBoekjaren();
        include 'app/views/boekjaar/index.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jaar = $_POST['jaar'] ?? '';

            // Validatie
            $errors = [];
            if (!preg_match('/^\d{4}$/', $jaar)) {
                $errors[] = "Ongeldig jaar. Voer een geldig jaartal in (bijv. 2024).";
            }

            if (empty($errors)) {
                if ($this->model->addBoekjaar($jaar)) {
                    header('Location: /boekjaren');
                    exit;
                } else {
                    $error = "Er is een fout opgetreden bij het toevoegen van het boekjaar.";
                }
            } else {
                $error = implode("<br>", $errors);
            }
        }
        include 'app/views/boekjaar/add.php';
    }

    public function edit($id) {
        $boekjaar = $this->model->getBoekjaarById($id);
        if (!$boekjaar) {
            header('Location: /boekjaren');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jaar = $_POST['jaar'] ?? '';

            // Validatie
            $errors = [];
            if (!preg_match('/^\d{4}$/', $jaar)) {
                $errors[] = "Ongeldig jaar. Voer een geldig jaartal in (bijv. 2024).";
            }

            if (empty($errors)) {
                if ($this->model->updateBoekjaar($id, $jaar)) {
                    header('Location: /boekjaren');
                    exit;
                } else {
                    $error = "Er is een fout opgetreden bij het bijwerken van het boekjaar.";
                }
            } else {
                $error = implode("<br>", $errors);
            }
        }
        include 'app/views/boekjaar/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->deleteBoekjaar($id)) {
                header('Location: /boekjaren');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het verwijderen van het boekjaar.";
            }
        }
        $boekjaar = $this->model->getBoekjaarById($id);
        include 'app/views/boekjaar/delete.php';
    }

    public function setActief($id) {
        try {
            // Debug informatie
            echo "Start setActief methode<br>";
            echo "Boekjaar ID: " . $id . "<br>";

            // Begin een transactie
            $this->model->beginTransaction();
            echo "Transactie gestart<br>";

            // Zet het boekjaar actief
            if (!$this->model->setActiefBoekjaar($id)) {
                echo "Fout bij setActiefBoekjaar<br>";
                throw new Exception("Kon het boekjaar niet activeren.");
            }
            echo "Boekjaar succesvol op actief gezet<br>";

            // Update de lidmaatschapstypen voor alle leden
            if (!$this->familielidModel->updateLidmaatschapTypes($id)) {
                echo "Fout bij updateLidmaatschapTypes<br>";
                throw new Exception("Kon de lidmaatschapstypen niet bijwerken.");
            }
            echo "Lidmaatschapstypen succesvol bijgewerkt<br>";

            // Commit de transactie
            $this->model->commit();
            echo "Transactie succesvol afgerond<br>";

            // Voeg een kleine vertraging toe om de debug output te kunnen zien
            sleep(2);
            
            header('Location: /boekjaren');
            exit;
        } catch (Exception $e) {
            // Rollback bij een fout
            echo "Error opgevangen: " . $e->getMessage() . "<br>";
            $this->model->rollBack();
            $error = "Er is een fout opgetreden: " . $e->getMessage();
            $boekjaren = $this->model->getAllBoekjaren();
            include 'app/views/boekjaar/index.php';
        }
    }
} 