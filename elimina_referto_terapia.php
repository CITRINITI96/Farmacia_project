<?php
include 'db.php';  // Connessione al database

// Verifica se l'ID del referto è stato passato tramite GET
if (isset($_GET['id_referto'])) {
    $id_referto = $_GET['id_referto'];

    // Esegui la query per eliminare il referto
    $stmt = $pdo->prepare("DELETE FROM RefertiTerapie WHERE ID_Referto = ?");
    $stmt->execute([$id_referto]);

    // Dopo l'eliminazione, reindirizza alla pagina di gestione referti
    header('Location: gestione_referti.php');
    exit;
} else {
    echo "Errore: ID Referto non specificato.";
    exit;
}
?>
