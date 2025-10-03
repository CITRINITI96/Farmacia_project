<?php
// Impostazioni di connessione al database
$host = 'localhost';   // O l'host appropriato
$dbname = 'farmaciaospedaliera';  // Nome del tuo database
$username = 'root';    // Il tuo username del database
$password = 'root12345';        // La tua password (lascia vuoto per 'root' su XAMPP di default)

// Connessione al database con PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Impostare l'errore in modalità eccezione
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se c'è un errore, mostralo
    echo "Errore di connessione: " . $e->getMessage();
}
?>
