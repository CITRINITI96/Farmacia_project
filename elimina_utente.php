<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth('Admin');

// Solo POST è accettato (form con CSRF)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestione_utenti.php');
    exit;
}

verifyCsrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: gestione_utenti.php');
    exit;
}

// Proteggi l'admin loggato: non può eliminare se stesso
if ($id === (int)$_SESSION['user_id']) {
    header('Location: gestione_utenti.php?err=selfdelete');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM Utente WHERE ID_Utente = ?");
$stmt->execute([$id]);

header('Location: gestione_utenti.php?msg=eliminato');
exit;
