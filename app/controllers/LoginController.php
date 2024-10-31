<?php
/**
 * LoginController
 * 
 * Deze controller beheert de authenticatie van gebruikers in de applicatie.
 * Verantwoordelijk voor inloggen, uitloggen en sessiemanagement.
 */
class LoginController {
    /** @var LoginModel Instance van het LoginModel */
    private $model;

    /**
     * Constructor
     * 
     * @param PDO $db Database connectie object
     */
    public function __construct($db) {
        $this->model = new LoginModel($db);
    }

    /**
     * Toont de login pagina
     * Wordt aangeroepen wanneer een gebruiker de login pagina bezoekt
     */
    public function index() {
        include 'app/views/login_view.php';
    }

    /**
     * Verwerkt het login formulier
     * 
     * Valideert de inloggegevens en stuurt de gebruiker door naar het juiste dashboard
     * op basis van hun rol (secretaris of penningmeester)
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Haal de inloggegevens op uit het formulier
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Verifieer gebruikersgegevens via het model
            $user = $this->model->verifyUser($username, $password);

            if ($user) {
                // Start de sessie en sla gebruikersgegevens op
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['gebruikersnaam'];
                $_SESSION['role'] = $user['rol'];

                // Redirect naar het juiste dashboard op basis van de rol
                if ($user['rol'] === 'secretaris') {
                    header('Location: /dashboard/secretaris');
                } elseif ($user['rol'] === 'penningmeester') {
                    header('Location: /dashboard/penningmeester');
                } else {
                    header('Location: /dashboard');
                }
                exit;
            } else {
                // Toon foutmelding bij ongeldige inloggegevens
                $error = "Ongeldige gebruikersnaam of wachtwoord.";
                include 'app/views/login_view.php';
            }
        } else {
            // Als het geen POST-verzoek is, toon dan gewoon de login pagina
            include 'app/views/login_view.php';
        }
    }

    /**
     * Verwerkt het uitloggen van de gebruiker
     * 
     * Vernietigt de sessie en stuurt de gebruiker terug naar de login pagina
     */
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
