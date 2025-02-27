<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../utils/Logger.php';

use App\Models\Product;
use App\Models\Admin;
use App\Utils\Logger;

class AdminController extends Controller {
    private $productModel;
    private $adminModel;
    private $logger;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->adminModel = new Admin();
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
                'low_stock' => count(array_filter($slots, fn($s) => $s['status'] === 'FILLED' && $s['current_stock'] < 3))
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
} 