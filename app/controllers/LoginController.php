<?php
class LoginController {
    private $model;

    public function __construct($db) {
        $this->model = new LoginModel($db);
    }

    public function index() {
        // Toon de login pagina
        include 'app/views/login_view.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

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
                $error = "Ongeldige gebruikersnaam of wachtwoord.";
                include 'app/views/login_view.php';
            }
        } else {
            // Als het geen POST-verzoek is, toon dan gewoon de login pagina
            include 'app/views/login_view.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
