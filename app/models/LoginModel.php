<?php
class LoginModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function verifyUser($username, $password) {
        $query = "SELECT * FROM Gebruikers WHERE gebruikersnaam = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['wachtwoord'])) {
            return $user;
        }
        return false;
    }
}
