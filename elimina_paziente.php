<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

// Solo POST con CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: paziente.php');
    exit;
}
verifyCsrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: paziente.php');
    exit;
}

// Elimina prima le prescrizioni associate
$stmt = $pdo->prepare("DELETE FROM Prescrizione WHERE ID_Paziente = ?");
$stmt->execute([$id]);

// Poi elimina il paziente
$stmt = $pdo->prepare("DELETE FROM Paziente WHERE ID_Paziente = ?");
$stmt->execute([$id]);

header('Location: paziente.php?msg=eliminato');
exit;
