<?php
namespace App\Models;

require_once __DIR__ . '/Model.php';

/**
 * Admin Model
 * Beheert admin authenticatie en validatie
 */
class Admin extends Model {
    /**
     * Valideer login gegevens
     * @param string $username
     * @param string $password
     * @return array
     */
    public function validateLogin($username, $password) {
        // Debug logging
        error_log("Login poging voor gebruiker: " . $username);
        
        // Input validatie
        if (empty($username) || empty($password)) {
            error_log("Lege username of password");
            return ['success' => false, 'message' => 'Vul alle velden in'];
        }

        // Haal admin op uit database
        $sql = "SELECT admin_id, username, password_hash FROM admins WHERE username = ?";
        $admin = $this->fetch($sql, [$username]);
        
        // Debug logging
        if (!$admin) {
            error_log("Geen admin gevonden voor username: " . $username);
            return ['success' => false, 'message' => "Ongeldige inloggegevens"];
        }
        
        error_log("Admin gevonden, controleer wachtwoord");
        error_log("Stored hash: " . $admin['password_hash']);
        
        // Test password_verify
        $verify_result = password_verify($password, $admin['password_hash']);
        error_log("Password verify result: " . ($verify_result ? "true" : "false"));

        if ($verify_result) {
            error_log("Login succesvol");
            return ['success' => true, 'admin' => [
                'username' => $admin['username']
            ]];
        }

        error_log("Wachtwoord incorrect");
        return ['success' => false, 'message' => "Ongeldige inloggegevens"];
    }

    /**
     * Check of een sessie nog geldig is
     * @return bool
     */
    public function isValidSession() {
        return isset($_SESSION['admin']) && 
               isset($_SESSION['last_activity']) && 
               (time() - $_SESSION['last_activity'] <= 1800); // 30 minuten timeout
    }

    /**
     * Update laatste activiteit timestamp
     */
    public function updateLastActivity() {
        $_SESSION['last_activity'] = time();
    }
} 