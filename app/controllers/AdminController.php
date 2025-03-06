<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Change.php';
require_once __DIR__ . '/../utils/Logger.php';

use App\Models\Product;
use App\Models\Admin;
use App\Models\Change;
use App\Utils\Logger;

class AdminController extends Controller {
    private $productModel;
    private $adminModel;
    private $changeModel;
    private $logger;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->adminModel = new Admin();
        $this->changeModel = new Change();
        $this->logger = new Logger('admin_activity.log');
    }

    /**
     * Controleer of gebruiker is ingelogd en sessie nog geldig is
     * Implementeert beveiliging en betrouwbaarheid (ISO25010)
     */
    private function checkAuth() {
        if (!$this->adminModel->isValidSession()) {
            // Log uitloggen door timeout
            @$this->logger->logActivity(
                'session_timeout',
                isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'unknown'
            );
            
            // Verwijder sessie
            session_destroy();
            $this->redirect('admin/login');
        }
        
        // Update laatste activiteit
        $this->adminModel->updateLastActivity();
    }

    /**
     * Toon het admin login scherm
     * Implementeert gebruiksgemak (ISO25010)
     */
    public function login() {
        // Haal slot informatie op voor de fysieke automaat
        $slots = $this->productModel->getAllSlots();
        
        // Als er al een POST request is, valideer de login
        if ($this->isPost()) {
            try {
                // Sanitize input
                $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                $password = $_POST['password'] ?? '';
                
                // Valideer login via Admin model
                $result = $this->adminModel->validateLogin($username, $password);
                
                if ($result['success']) {
                    $_SESSION['admin'] = true;
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['last_activity'] = time();
                    
                    // Log succesvolle login
                    @$this->logger->logActivity('login_success', $username);
                    
                    $this->redirect('admin/dashboard');
                } else {
                    $error = $result['message'];
                    
                    // Log mislukte poging (stil)
                    @$this->logger->logActivity('login_failed', $username, $error);
                }
            } catch (Exception $e) {
                // Log error (stil)
                @$this->logger->logError($e->getMessage(), 'system');
                $error = 'Er is een systeemfout opgetreden. Probeer het later opnieuw.';
            }
        }

        // Toon login form met eventuele error feedback
        $this->view('admin/login', [
            'error' => $error ?? null,
            'slots' => $slots
        ]);
    }

    /**
     * Toon het admin dashboard
     */
    public function dashboard() {
        $this->checkAuth();
        
        try {
            // Haal statistieken op
            $slots = $this->productModel->getAllSlots();
            $stats = [
                'total_products' => count($this->productModel->getAvailableProducts()),
                'empty_slots' => count(array_filter($slots, fn($s) => $s['status'] === 'EMPTY')),
                'filled_slots' => count(array_filter($slots, fn($s) => $s['status'] === 'FILLED'))
            ];
            
            $this->view('admin/dashboard', [
                'slots' => $slots,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            // Log error (stil)
            @$this->logger->logError($e->getMessage(), $_SESSION['admin_username'] ?? 'unknown');
            $this->view('admin/dashboard', ['error' => 'Er is een fout opgetreden bij het laden van het dashboard.']);
        }
    }

    /**
     * Uitloggen
     * Implementeert beveiliging (ISO25010)
     */
    public function logout() {
        // Log uitloggen (stil)
        if (isset($_SESSION['admin_username'])) {
            @$this->logger->logActivity('logout', $_SESSION['admin_username']);
        }
        
        // Verwijder alle sessie data
        session_destroy();
        
        // Redirect naar home met feedback
        $_SESSION['message'] = 'U bent succesvol uitgelogd.';
        $this->redirect('home');
    }

    /**
     * Wisselgeld beheer pagina
     */
    public function change() {
        $this->checkAuth();
        
        try {
            // Haal slot informatie op voor de fysieke automaat display
            $slots = $this->productModel->getAllSlots();
            
            // Haal geldeenheden op
            $moneyUnits = $this->changeModel->getAllUnits();
            
            // Bereken totaal wisselgeld
            $totalAmount = $this->changeModel->getTotalAmount();
            
            $this->view('admin/change', [
                'slots' => $slots,
                'moneyUnits' => $moneyUnits,
                'totalAmount' => $totalAmount
            ]);
        } catch (Exception $e) {
            @$this->logger->logError($e->getMessage(), $_SESSION['admin_username'] ?? 'unknown');
            $this->view('admin/change', ['error' => 'Er is een fout opgetreden bij het laden van wisselgeld beheer.']);
        }
    }

    /**
     * Update wisselgeld voorraad
     */
    public function updateCashStock() {
        $this->checkAuth();
        
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Ongeldige aanvraag']);
            return;
        }

        // Haal JSON data op
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['cash_id']) || !isset($data['new_stock'])) {
            $this->json(['success' => false, 'message' => 'Ongeldige invoer']);
            return;
        }

        // Valideer invoer
        $cashId = filter_var($data['cash_id'], FILTER_VALIDATE_INT);
        $newStock = filter_var($data['new_stock'], FILTER_VALIDATE_INT);

        if ($cashId === false || $newStock === false || $newStock < 0) {
            $this->json(['success' => false, 'message' => 'Ongeldige voorraad waarde']);
            return;
        }

        // Update de voorraad
        $result = $this->changeModel->updateStock($cashId, $newStock);
        
        if ($result['success']) {
            // Log de wijziging
            @$this->logger->logActivity(
                'update_cash_stock',
                $_SESSION['admin_username'],
                "Updated cash_id {$cashId} to stock {$newStock}"
            );
        }
        
        $this->json($result);
    }
} 