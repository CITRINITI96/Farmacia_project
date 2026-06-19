<?php
// =============================================
// CONNESSIONE DATABASE — FILE UNICO
// Usare SOLO questo file in tutto il progetto.
// Variabile di connessione: $pdo
// =============================================

// TODO produzione: spostare queste credenziali in variabili d'ambiente (.env)
define('DB_HOST', 'localhost');
define('DB_NAME', 'farmaciaospedaliera');
define('DB_USER', 'root');
define('DB_PASS', 'root12345');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Logga l'errore reale su file, non esporre dettagli all'utente
    error_log('[DB ERROR] ' . $e->getMessage(), 3, __DIR__ . '/logs/db_errors.log');
    die("Errore di connessione al database. Contatta l'amministratore.");
}
