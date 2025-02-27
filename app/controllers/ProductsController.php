<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';

use App\Models\Product;

class ProductsController extends Controller {
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }

    /**
     * Toon product beheer overzicht
     */
    public function index() {
        // Haal alle producten op
        $products = $this->productModel->getAllProducts();
        
        // Haal slot informatie op voor de fysieke automaat display
        $slots = $this->productModel->getAllSlots();
        
        // Render de view
        $this->view('admin/products/index', [
            'products' => $products,
            'slots' => $slots
        ]);
    }

    /**
     * Verwijder een product
     */
    public function delete() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Niet geautoriseerd']);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Ongeldige aanvraag']);
            return;
        }

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Ongeldig product ID']);
            return;
        }

        if ($this->productModel->deleteProduct($productId)) {
            $this->json(['success' => true]);
        } else {
            $this->json([
                'success' => false, 
                'message' => 'Product kon niet worden verwijderd. Mogelijk is het nog in gebruik in een automaatvak.'
            ]);
        }
    }

    /**
     * Maak een nieuw product aan
     */
    public function create() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Niet geautoriseerd']);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Ongeldige aanvraag']);
            return;
        }

        // Sanitize input
        $productData = [
            'name' => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'price' => filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT),
            'unit' => htmlspecialchars(trim($_POST['unit'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars(trim($_POST['category'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'requires_cooling' => $_POST['category'] === 'Gekoeld'
        ];

        // Valideer prijs
        if ($productData['price'] === false || $productData['price'] <= 0) {
            $this->json(['success' => false, 'message' => 'Ongeldige prijs']);
            return;
        }

        // Maak product aan
        $result = $this->productModel->createProduct($productData);
        $this->json($result);
    }

    /**
     * Update een bestaand product
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

        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Ongeldig product ID']);
            return;
        }

        // Sanitize input
        $productData = [
            'product_id' => $productId,
            'name' => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'price' => filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT),
            'unit' => htmlspecialchars(trim($_POST['unit'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars(trim($_POST['category'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'requires_cooling' => $_POST['category'] === 'Gekoeld'
        ];

        // Valideer prijs
        if ($productData['price'] === false || $productData['price'] <= 0) {
            $this->json(['success' => false, 'message' => 'Ongeldige prijs']);
            return;
        }

        // Update product
        $result = $this->productModel->updateProduct($productData);
        $this->json($result);
    }

    /**
     * Haal product details op
     */
    public function get() {
        if (!isset($_SESSION['admin'])) {
            $this->json(['success' => false, 'message' => 'Niet geautoriseerd']);
            return;
        }

        $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Ongeldig product ID']);
            return;
        }

        $product = $this->productModel->getProductById($productId);
        if ($product) {
            $this->json(['success' => true, 'product' => $product]);
        } else {
            $this->json(['success' => false, 'message' => 'Product niet gevonden']);
        }
    }
} 