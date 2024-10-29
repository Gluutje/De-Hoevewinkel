<?php
require_once 'app/models/contributie/ContributieBaseModel.php';
require_once 'app/models/contributie/ContributieCrudModel.php';
require_once 'app/models/contributie/ContributieRekenModel.php';

class ContributieController {
    private $baseModel;
    private $crudModel;
    private $rekenModel;

    public function __construct($db) {
        $this->baseModel = new ContributieBaseModel($db);
        $this->crudModel = new ContributieCrudModel($db);
        $this->rekenModel = new ContributieRekenModel($db);
    }

    public function index() {
        $contributies = $this->crudModel->getContributiesByBoekjaar($this->baseModel->getActiefBoekjaar()['id']);
        include 'app/views/contributie/index.php';
    }

    public function add() {
        $soortLeden = $this->baseModel->getAllSoortLeden();
        $boekjaren = $this->baseModel->getAllBoekjaren();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $leeftijd = $_POST['leeftijd'] ?? '';
            $soort_lid_id = $_POST['soort_lid_id'] ?? '';
            $bedrag = $_POST['bedrag'] ?? '';
            $boekjaar_id = $_POST['boekjaar_id'] ?? '';

            if ($this->crudModel->addContributie($leeftijd, $soort_lid_id, $bedrag, $boekjaar_id)) {
                header('Location: /contributies');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het toevoegen van de contributie.";
            }
        }
        include 'app/views/contributie/add.php';
    }

    public function edit($id) {
        $contributie = $this->crudModel->getContributieById($id);
        $soortLeden = $this->baseModel->getAllSoortLeden();
        $boekjaren = $this->baseModel->getAllBoekjaren();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $leeftijd = $_POST['leeftijd'] ?? '';
            $soort_lid_id = $_POST['soort_lid_id'] ?? '';
            $bedrag = $_POST['bedrag'] ?? '';
            $boekjaar_id = $_POST['boekjaar_id'] ?? '';

            if ($this->crudModel->updateContributie($id, $leeftijd, $soort_lid_id, $bedrag, $boekjaar_id)) {
                header('Location: /contributies');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het bijwerken van de contributie.";
            }
        }
        include 'app/views/contributie/edit.php';
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->crudModel->deleteContributie($id)) {
                header('Location: /contributies');
                exit;
            } else {
                $error = "Er is een fout opgetreden bij het verwijderen van de contributie.";
            }
        }
        $contributie = $this->crudModel->getContributieById($id);
        include 'app/views/contributie/delete.php';
    }
}
