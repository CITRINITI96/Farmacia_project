<?php
// Configurazione dei dettagli di connessione al database
$servername = "localhost";    // Indirizzo del server MySQL (di solito "localhost" per XAMPP)
$username = "root";           // Nome utente di MySQL (di default "root" in XAMPP)
$password = "root12345";               // Password di MySQL (lascia vuoto per XAMPP)
$dbname = "FarmaciaOspedaliera"; // Nome del database

try {
    // Creazione della connessione PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}
?>
