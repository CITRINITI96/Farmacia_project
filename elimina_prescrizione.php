<?php
include 'db.php';  // Connessione al database

// Controlla se i parametri sono stati passati tramite GET
if (isset($_GET['id_paziente'], $_GET['id_farmaco'], $_GET['data_prescrizione'])) {
    $id_paziente = $_GET['id_paziente'];
    $id_farmaco = $_GET['id_farmaco'];
    $data_prescrizione = $_GET['data_prescrizione'];

    try {
        // Preparazione per la query di eliminazione
        $stmt = $pdo->prepare("DELETE FROM Prescrizione WHERE ID_Paziente = ? AND ID_Farmaco = ? AND Data_Prescrizione = ?");
        $stmt->execute([$id_paziente, $id_farmaco, $data_prescrizione]);

        // Verifica se la prescrizione è stata eliminata correttamente
        if ($stmt->rowCount() > 0) {
            // Redirige alla pagina di gestione con un messaggio di successo
            header("Location: gestione_prescrizione.php?id_paziente=$id_paziente&message=Prescrizione eliminata con successo");
            exit;
        } else {
            // Se la prescrizione non è stata trovata
            echo "Errore: Prescrizione non trovata.";
        }
    } catch (PDOException $e) {
        // Gestione degli errori
        echo "Errore nella connessione o nella query: " . $e->getMessage();
    }
} else {
    // Se i parametri non sono stati passati, mostra un errore
    echo "Errore: ID prescrizione non specificato.";
}
?>
