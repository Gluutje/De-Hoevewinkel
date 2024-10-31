<?php
require_once 'app/models/BoekjaarModel.php';
require_once 'app/models/FamilielidModel.php';

// BoekjaarController class om boekjaren te beheren
class BoekjaarController {
    private $model;
    private $familielidModel;

    // Constructor om de modellen te initialiseren
    public function __construct($db) {
        $this->model = new BoekjaarModel($db);
        $this->familielidModel = new FamilielidModel($db);
    }

    // Methode om alle boekjaren weer te geven
    public function index() {
        $boekjaren = $this->model->getAllBoekjaren();
        include 'app/views/boekjaar/index.php'; // Laadt de view om de boekjaren weer te geven
    }

    // Methode om een nieuw boekjaar toe te voegen
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jaar = $_POST['jaar'] ?? '';

            // Validatie van het jaar
            $errors = [];
            if (!preg_match('/^\d{4}$/', $jaar)) {
                $errors[] = "Ongeldig jaar. Voer een geldig jaartal in (bijv. 2024).";
            }

            if (empty($errors)) {
                // Voeg het boekjaar toe als er geen fouten zijn
                if ($this->model->addBoekjaar($jaar)) {
                    header('Location: /boekjaren'); // Redirect naar de boekjarenpagina
                    exit;
                } else {
                    $error = "Er is een fout opgetreden bij het toevoegen van het boekjaar.";
                }
            } else {
                $error = implode("<br>", $errors); // Toon validatiefouten
            }
        }
        include 'app/views/boekjaar/add.php'; // Laadt de view om een boekjaar toe te voegen
    }

    // Methode om een bestaand boekjaar te bewerken
    public function edit($id) {
        $boekjaar = $this->model->getBoekjaarById($id);
        if (!$boekjaar) {
            header('Location: /boekjaren'); // Redirect als het boekjaar niet bestaat
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jaar = $_POST['jaar'] ?? '';

            // Validatie van het jaar
            $errors = [];
            if (!preg_match('/^\d{4}$/', $jaar)) {
                $errors[] = "Ongeldig jaar. Voer een geldig jaartal in (bijv. 2024).";
            }

            if (empty($errors)) {
                // Werk het boekjaar bij als er geen fouten zijn
                if ($this->model->updateBoekjaar($id, $jaar)) {
                    header('Location: /boekjaren'); // Redirect na succesvolle update
                    exit;
                } else {
                    $error = "Er is een fout opgetreden bij het bijwerken van het boekjaar.";
                }
            } else {
                $error = implode("<br>", $errors); // Toon validatiefouten
            }
        }
        include 'app/views/boekjaar/edit.php'; // Laadt de view om een boekjaar te bewerken
    }

    // Methode om een boekjaar te verwijderen
    public function delete($id) {
        $boekjaar = $this->model->getBoekjaarById($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->model->deleteBoekjaar($id);
            
            if ($result === true) {
                header('Location: /boekjaren');
                exit;
            } else {
                $error = is_string($result) ? $result : "Er is een fout opgetreden bij het verwijderen van het boekjaar.";
            }
        }
        
        include 'app/views/boekjaar/delete.php';
    }

    // Methode om een boekjaar actief te maken en lidmaatschapstypen bij te werken
    public function setActief($id) {
        try {
            $this->model->beginTransaction();

            if (!$this->model->setActiefBoekjaar($id)) {
                throw new Exception("Kon het boekjaar niet activeren.");
            }

            if (!$this->familielidModel->updateLidmaatschapTypes($id)) {
                throw new Exception("Kon de lidmaatschapstypen niet bijwerken.");
            }

            $this->model->commit();
            header('Location: /boekjaren');
            exit;
        } catch (Exception $e) {
            $this->model->rollBack();
            $error = "Er is een fout opgetreden bij het activeren van het boekjaar.";
            $boekjaren = $this->model->getAllBoekjaren();
            include 'app/views/boekjaar/index.php';
        }
    }
}
