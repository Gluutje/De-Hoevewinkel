<?php
$host = 'localhost';
$dbname = 'ledenadministratie';
$username = 'root';
$password = ''; // Laat dit leeg als er geen wachtwoord is ingesteld voor de root-gebruiker

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}
