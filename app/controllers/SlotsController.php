<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Slot.php';
require_once __DIR__ . '/../models/Product.php';

use App\Models\Slot;
use App\Models\Product;

class SlotsController extends Controller {
    private $slotModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->slotModel = new Slot();
        $this->productModel = new Product();
    }

    /**
     * Toon vak beheer overzicht
     */
    public function index() {
        // Check admin rechten
        if (!isset($_SESSION['admin'])) {
            $this->redirect('admin/login');
            return;
        }

        // Haal alle slots en beschikbare producten op
        $slots = $this->slotModel->getAllSlots();
        $products = $this->productModel->getAllProducts();
        
        // Render de view
        $this->view('admin/slots/index', [
            'slots' => $slots,
            'products' => $products
        ]);
    }

    /**
     * Update vak configuratie
     */
    public function update() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Niet geautoriseerd']);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Ongeldige aanvraag']);
            return;
        }

        // Valideer input
        $slotId = filter_input(INPUT_POST, 'slotId', FILTER_VALIDATE_INT);
        $productId = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT);
        $currentStock = filter_input(INPUT_POST, 'currentStock', FILTER_VALIDATE_INT);
        $maxCapacity = filter_input(INPUT_POST, 'maxCapacity', FILTER_VALIDATE_INT);

        if (!$slotId || $maxCapacity < 1) {
            $this->json(['success' => false, 'message' => 'Ongeldige invoer']);
            return;
        }

        // Update vak
        $result = $this->slotModel->updateSlot($slotId, [
            'product_id' => $productId,
            'current_stock' => $currentStock,
            'max_capacity' => $maxCapacity
        ]);

        $this->json($result);
    }

    /**
     * Zet vak in/uit onderhoud
     */
    public function maintenance() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Niet geautoriseerd']);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Ongeldige aanvraag']);
            return;
        }

        $slotId = filter_input(INPUT_POST, 'slotId', FILTER_VALIDATE_INT);
        $maintenance = filter_input(INPUT_POST, 'maintenance', FILTER_VALIDATE_BOOLEAN);

        if (!$slotId) {
            $this->json(['success' => false, 'message' => 'Ongeldig vak ID']);
            return;
        }

        if ($this->slotModel->setMaintenance($slotId, $maintenance)) {
            $this->json([
                'success' => true,
                'message' => $maintenance ? 'Vak in onderhoud gezet' : 'Vak uit onderhoud gehaald'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => 'Kon de onderhoudsstatus niet wijzigen'
            ]);
        }
    }

    /**
     * Vul een vak met een product
     */
    public function fill() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Niet geautoriseerd']);
            return;
        }

        // Haal JSON data op
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data['slot_id']) || !isset($data['product_id'])) {
            $this->json(['success' => false, 'message' => 'Ongeldige invoer']);
            return;
        }

        // Valideer slot en product
        $slot = $this->slotModel->getSlotById($data['slot_id']);
        $product = $this->productModel->getProductById($data['product_id']);

        if (!$slot || !$product) {
            $this->json(['success' => false, 'message' => 'Vak of product niet gevonden']);
            return;
        }

        // Controleer koeling vereisten
        if ($product['requires_cooling'] && $slot['slot_type'] !== 'COOLED') {
            $this->json(['success' => false, 'message' => 'Dit product vereist een gekoeld vak']);
            return;
        }

        if (!$product['requires_cooling'] && $slot['slot_type'] === 'COOLED') {
            $this->json(['success' => false, 'message' => 'Dit product hoeft niet gekoeld te worden']);
            return;
        }

        // Vul het vak
        $result = $this->slotModel->fillSlot($data['slot_id'], $data['product_id']);
        
        if ($result['success']) {
            $this->json([
                'success' => true,
                'message' => 'Vak succesvol gevuld'
            ]);
        } else {
            $this->json([
                'success' => false,
                'message' => $result['message'] ?? 'Kon het vak niet vullen'
            ]);
        }
    }

    public function remove() {
        // Check admin toegang
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Geen toegang']);
            return;
        }

        // Haal POST data op
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['slot_id'])) {
            $this->json(['success' => false, 'message' => 'Slot ID ontbreekt']);
            return;
        }

        $slotModel = new Slot();
        $result = $slotModel->removeProduct($data['slot_id']);

        $this->json($result);
    }

    /**
     * Haal alle slots op voor real-time updates
     */
    public function getAll() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Geen toegang']);
            return;
        }

        $slots = $this->slotModel->getAllSlots();
        
        // Splits slots in gekoeld en ongekoeld
        $cooledSlots = array_filter($slots, function($slot) {
            return $slot['slot_type'] === 'COOLED';
        });
        
        $uncooledSlots = array_filter($slots, function($slot) {
            return $slot['slot_type'] === 'UNCOOLED';
        });

        $this->json([
            'success' => true,
            'cooled' => array_values($cooledSlots),
            'uncooled' => array_values($uncooledSlots)
        ]);
    }
} 