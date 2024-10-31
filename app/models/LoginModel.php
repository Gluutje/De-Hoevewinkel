<?php
/**
 * LoginModel
 * 
 * Deze klasse beheert de authenticatie van gebruikers
 * Verantwoordelijk voor het verifiëren van inloggegevens
 */
class LoginModel {
    /** @var PDO Database connectie */
    private $db;

    /**
     * Constructor
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Verifieert de inloggegevens van een gebruiker
     * 
     * @param string $username De gebruikersnaam
     * @param string $password Het wachtwoord
     * @return array|false Array met gebruikersgegevens bij succes, false bij falen
     */
    public function verifyUser($username, $password) {
        // Bereid de query voor met een prepared statement voor veiligheid
        $query = "SELECT * FROM Gebruikers WHERE gebruikersnaam = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifieer het wachtwoord met de gehashte versie in de database
        if ($user && password_verify($password, $user['wachtwoord'])) {
            return $user;
        }
        return false;
    }
}
