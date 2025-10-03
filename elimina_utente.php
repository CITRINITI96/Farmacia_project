<?php
// Includi la configurazione del database
require_once 'config.php';
session_start();

// Controlla se l'utente è autenticato e se è un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Verifica se è stato passato un ID
if (!isset($_GET['id'])) {
    header("Location: gestione_utenti.php");
    exit;
}

// Recupera l'ID dell'utente da eliminare
$userId = $_GET['id'];

// Prepara la query per eliminare l'utente (assumendo che la colonna sia ID_Utente)
$query = "DELETE FROM Utente WHERE ID_Utente = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: gestione_utenti.php");
    exit;
} else {
    echo "Errore durante l'eliminazione dell'utente.";
}
?>
