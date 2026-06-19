<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

// Solo POST con CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestione_referti.php');
    exit;
}
verifyCsrf();

$id_referto = (int)($_POST['id_referto'] ?? 0);
if ($id_referto <= 0) {
    header('Location: gestione_referti.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM RefertiTerapie WHERE ID_Referto = ?");
$stmt->execute([$id_referto]);

header('Location: gestione_referti.php?msg=eliminato');
exit;
