<?php
class Controller {
    public function __construct() {
        // Start de sessie als die nog niet gestart is
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function view($name, $data = []) {
        // Data variabelen extracten
        extract($data);
        
        // Template inladen
        require_once __DIR__ . '/../views/template.php';
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect($route) {
        header('Location: /?route=' . $route);
        exit;
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function getPostData() {
        return $_POST;
    }
} 