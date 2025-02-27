<?php
namespace App\Models;

class Model {
    protected $db;
    
    public function __construct() {
        $this->connect();
    }
    
    protected function connect() {
        try {
            $this->db = new \PDO(
                'mysql:host=localhost;dbname=hoevewinkel;charset=utf8mb4',
                'root',
                ''
            );
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die('Database connectie mislukt: ' . $e->getMessage());
        }
    }
    
    protected function fetch($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    protected function fetchAll($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function execute($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
} 