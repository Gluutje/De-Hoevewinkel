<?php
/**
 * Database configuratie bestand
 * 
 * Dit bestand bevat de configuratie voor de database connectie
 * en initialiseert de PDO connectie met de juiste instellingen
 */

// Database configuratie parameters
$host = 'localhost';        // Database host
$dbname = 'ledenadministratie';  // Database naam
$username = 'root';        // Database gebruikersnaam
$password = '';           // Database wachtwoord (leeg voor lokale ontwikkeling)

try {
    // Maak een nieuwe PDO connectie met UTF-8 karakterset
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Zet de error mode op exceptions voor betere foutafhandeling
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Stop de applicatie bij database connectie problemen
    die("Databaseverbinding mislukt: " . $e->getMessage());
}
