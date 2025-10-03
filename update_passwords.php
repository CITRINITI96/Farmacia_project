<?php
// Includi la configurazione del database
require_once 'config.php'; // Assicurati che config.php definisca $conn per la connessione al database

// Prepara la query per selezionare tutte le righe dalla tabella Utente
$query = "SELECT username, password FROM Utente"; // Selezioniamo username e password
$stmt = $conn->prepare($query);
$stmt->execute();

// Recupera tutti gli utenti
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Itera su ogni utente e cripta la sua password
foreach ($users as $user) {
    // Cripta la password
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

    // Prepara la query per aggiornare la password nel database
    // Utilizza la colonna 'username' per identificare univocamente l'utente
    $updateQuery = "UPDATE Utente SET password = :password WHERE username = :username";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':password', $hashedPassword);
    $updateStmt->bindParam(':username', $user['username']);
    $updateStmt->execute();
}

echo "Le password sono state aggiornate con successo!";
?>
