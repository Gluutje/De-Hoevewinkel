<?php
require_once __DIR__ . '/../app/models/Model.php';

try {
    $db = new PDO(
        'mysql:host=localhost;dbname=hoevewinkel;charset=utf8mb4',
        'root',
        ''
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Admin gegevens
    $username = 'beheerder';
    $password = 'beheerder123'; // Dit is het wachtwoord dat je kunt gebruiken
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Controleer of de gebruiker al bestaat
    $stmt = $db->prepare("SELECT admin_id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        echo "Deze admin gebruiker bestaat al.\n";
        exit;
    }

    // Voeg de nieuwe admin toe
    $stmt = $db->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
    $stmt->execute([$username, $password_hash]);

    echo "Admin gebruiker 'beheerder' is succesvol toegevoegd.\n";
    echo "Gebruikersnaam: beheerder\n";
    echo "Wachtwoord: beheerder123\n";

} catch (PDOException $e) {
    echo "Er is een fout opgetreden: " . $e->getMessage() . "\n";
} 