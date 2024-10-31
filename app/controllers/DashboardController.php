<?php
// Voeg alle benodigde model imports toe
require_once 'app/models/FamilieModel.php';
require_once 'app/models/FamilielidModel.php';
require_once 'app/models/contributie/ContributieBaseModel.php';
require_once 'app/models/contributie/ContributieCrudModel.php';

/**
 * DashboardController
 * 
 * Deze controller beheert de dashboard functionaliteit voor verschillende gebruikersrollen
 * Verantwoordelijk voor het tonen van rol-specifieke dashboards en bijbehorende data
 */
class DashboardController {
    /** @var FamilieModel Instance van het FamilieModel */
    private $familieModel;
    /** @var FamilielidModel Instance van het FamilielidModel */
    private $familielidModel;
    /** @var ContributieBaseModel Instance van het ContributieBaseModel */
    private $contributieBaseModel;
    /** @var ContributieCrudModel Instance van het ContributieCrudModel */
    private $contributieCrudModel;

    /**
     * Constructor initialiseert alle benodigde models
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        $this->familieModel = new FamilieModel($db);
        $this->familielidModel = new FamilielidModel($db);
        $this->contributieBaseModel = new ContributieBaseModel($db);
        $this->contributieCrudModel = new ContributieCrudModel($db);
    }

    /**
     * Hoofdmethode voor het dashboard
     * Controleert sessie en routeert naar het juiste rol-dashboard
     */
    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        
        // Route naar het juiste dashboard op basis van rol
        if ($role === 'secretaris') {
            $this->secretaris();
        } elseif ($role === 'penningmeester') {
            $this->penningmeester();
        } else {
            include 'app/views/dashboard_view.php';
        }
    }

    /**
     * Toont het secretaris dashboard
     * Laadt familie- en ledeninformatie voor weergave
     */
    public function secretaris() {
        $this->checkRole('secretaris');
        $username = $_SESSION['username'];
        $families = $this->familieModel->getAllFamilies();
        $familieleden = $this->familielidModel->getAllFamilieleden();
        include 'app/views/dashboard_secretaris_view.php';
    }

    /**
     * Toont het penningmeester dashboard
     * Laadt contributie-informatie en boekjaargegevens
     */
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

    /**
     * Controleert of de gebruiker de juiste rol heeft
     * 
     * @param string $requiredRole De vereiste rol voor toegang
     */
    private function checkRole($requiredRole) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $requiredRole) {
            header('Location: /login');
            exit;
        }
    }
}
