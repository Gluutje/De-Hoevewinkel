<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Slot.php';

use App\Models\Product;
use App\Models\Slot;

class HomeController extends Controller {
    private $productModel;
    private $slotModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->slotModel = new Slot();
    }

    public function index() {
        // Haal alle beschikbare producten op
        $products = $this->productModel->getAvailableProducts();
        
        // Groepeer producten per categorie
        $categories = [];
        foreach ($products as $product) {
            if (!isset($categories[$product['category']])) {
                $categories[$product['category']] = [];
            }
            $categories[$product['category']][] = $product;
        }

        // Haal slot informatie op via het Slot model
        $slots = $this->slotModel->getAllSlots();
        
        // Render de home view met de producten en slots
        $this->view('home', [
            'categories' => $categories,
            'slots' => $slots
        ]);
    }
} 