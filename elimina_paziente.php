<?php
include 'db.php'; // Connessione al database

// Verifica se l'ID del paziente è passato tramite GET
if (isset($_GET['id'])) {
    $id_paziente = $_GET['id'];

    // Elimina prima tutte le prescrizioni del paziente
    $stmt = $pdo->prepare("DELETE FROM Prescrizione WHERE ID_Paziente = ?");
    $stmt->execute([$id_paziente]);

    // Ora elimina il paziente
    $stmt = $pdo->prepare("DELETE FROM Paziente WHERE ID_Paziente = ?");
    $stmt->execute([$id_paziente]);

    // Reindirizza alla pagina di gestione pazienti dopo aver eliminato il paziente e le prescrizioni
    header('Location: paziente.php');
    exit;
} else {
    // Se l'ID non è stato passato, mostriamo un errore
    echo "Errore: ID paziente non specificato.";
    exit;
}
?>

