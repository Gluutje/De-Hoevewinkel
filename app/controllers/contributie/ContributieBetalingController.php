<?php
require_once 'app/controllers/contributie/ContributieBaseController.php';

class ContributieBetalingController extends ContributieBaseController {
    public function verwerkBetaling() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contributies/overzicht');
            exit;
        }

        $familielid_id = $_POST['familielid_id'] ?? null;
        $boekjaar_id = $_POST['boekjaar_id'] ?? null;

        if ($this->crudModel->verwerkBetaling($familielid_id, $boekjaar_id)) {
            header('Location: /contributies/overzicht?success=Betaling succesvol verwerkt');
        } else {
            header('Location: /contributies/overzicht?error=Er is een fout opgetreden bij het verwerken van de betaling');
        }
        exit;
    }
} 