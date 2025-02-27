<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';

use App\Models\Product;

class HomeController extends Controller {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
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

        // Haal slot informatie op
        $slots = $this->productModel->getAllSlots();
        
        // Render de home view met de producten en slots
        $this->view('home', [
            'categories' => $categories,
            'slots' => $slots
        ]);
    }
} 