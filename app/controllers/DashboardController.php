<?php
require_once 'app/models/FamilieModel.php';
require_once 'app/models/FamilielidModel.php';
require_once 'app/models/contributie/ContributieBaseModel.php';
require_once 'app/models/contributie/ContributieCrudModel.php';

class DashboardController {
    private $familieModel;
    private $familielidModel;
    private $contributieBaseModel;
    private $contributieCrudModel;

    public function __construct($db) {
        $this->familieModel = new FamilieModel($db);
        $this->familielidModel = new FamilielidModel($db);
        $this->contributieBaseModel = new ContributieBaseModel($db);
        $this->contributieCrudModel = new ContributieCrudModel($db);
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        
        if ($role === 'secretaris') {
            $this->secretaris();
        } elseif ($role === 'penningmeester') {
            $this->penningmeester();
        } else {
            include 'app/views/dashboard_view.php';
        }
    }

    public function secretaris() {
        $this->checkRole('secretaris');
        $username = $_SESSION['username'];
        $families = $this->familieModel->getAllFamilies();
        $familieleden = $this->familielidModel->getAllFamilieleden();
        include 'app/views/dashboard_secretaris_view.php';
    }

    public function penningmeester() {
        $this->checkRole('penningmeester');
        $username = $_SESSION['username'];
        
        // Haal het actieve boekjaar op
        $actiefBoekjaar = $this->contributieBaseModel->getActiefBoekjaar();
        
        if ($actiefBoekjaar) {
            // Haal de contributie-instellingen op
            $contributies = $this->contributieCrudModel->getContributiesByBoekjaar($actiefBoekjaar['id']);
            
            // Bepaal het basisbedrag (hoogste bedrag)
            $basisbedrag = 0;
            foreach ($contributies as $contributie) {
                if ($contributie['bedrag'] > $basisbedrag) {
                    $basisbedrag = $contributie['bedrag'];
                }
            }
            
            // Haal het contributie-overzicht op
            $ledenContributies = $this->contributieCrudModel->getContributieOverzicht($actiefBoekjaar['id']);
        }
        
        include 'app/views/dashboard_penningmeester_view.php';
    }

    private function checkRole($requiredRole) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $requiredRole) {
            header('Location: /login');
            exit;
        }
    }
}
