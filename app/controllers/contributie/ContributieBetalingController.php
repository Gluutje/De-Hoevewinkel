<?php
/**
 * ContributieBetalingController
 * 
 * Deze controller beheert de verwerking van contributiebetalingen
 * Erft functionaliteit van ContributieBaseController
 */
require_once 'app/controllers/contributie/ContributieBaseController.php';

class ContributieBetalingController extends ContributieBaseController {
    /**
     * Verwerkt een nieuwe contributiebetaling
     * Controleert of het een POST request is en verwerkt de betaling
     */
    public function verwerkBetaling() {
        // Controleer of het een POST request is
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contributies/overzicht');
            exit;
        }

        // Haal de benodigde parameters op
        $familielid_id = $_POST['familielid_id'] ?? null;
        $boekjaar_id = $_POST['boekjaar_id'] ?? null;

        // Verwerk de betaling via het model
        if ($this->crudModel->verwerkBetaling($familielid_id, $boekjaar_id)) {
            header('Location: /contributies/overzicht?success=Betaling succesvol verwerkt');
        } else {
            header('Location: /contributies/overzicht?error=Er is een fout opgetreden bij het verwerken van de betaling');
        }
        exit;
    }
} 