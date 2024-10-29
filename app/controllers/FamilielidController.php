<?php
require_once 'app/models/FamilielidModel.php';

class FamilielidController {
    private $model;

    public function __construct($db) {
        $this->model = new FamilielidModel($db);
    }

    public function index() {
        $familieleden = $this->model->getAllFamilieleden();
        include 'app/views/familielid/index.php';
    }

    public function add() {
        $families = $this->model->getAllFamilies();
        $familieRelaties = $this->model->getFamilieRelaties();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naam = $_POST['naam'] ?? '';
            $geboortedatum = $_POST['geboortedatum'] ?? '';
            $familie_relatie_id = $_POST['familie_relatie_id'] ?? '';
            $familie_id = $_POST['familie_id'] ?? '';

            try {
                if ($this->model->addFamilielid($naam, $geboortedatum, $familie_relatie_id, $familie_id)) {
                    header('Location: /familieleden');
                    exit;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        include 'app/views/familielid/add.php';
    }

    public function edit($id) {
        $familielid = $this->model->getFamilielidById($id);
        $families = $this->model->getAllFamilies();
        $familieRelaties = $this->model->getFamilieRelaties();

        if (!$familielid) {
            header('Location: /familieleden');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naam = $_POST['naam'] ?? '';
            $geboortedatum = $_POST['geboortedatum'] ?? '';
            $familie_relatie_id = $_POST['familie_relatie_id'] ?? '';
            $familie_id = $_POST['familie_id'] ?? '';

            if ($this->model->updateFamilielid($id, $naam, $geboortedatum, $familie_relatie_id, $familie_id)) {
                header('Location: /familieleden');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het bijwerken van het familielid.";
            }
        }
        include 'app/views/familielid/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->deleteFamilielid($id)) {
                header('Location: /familieleden');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het verwijderen van het familielid.";
            }
        }
        $familielid = $this->model->getFamilielidById($id);
        include 'app/views/familielid/delete.php';
    }
}
