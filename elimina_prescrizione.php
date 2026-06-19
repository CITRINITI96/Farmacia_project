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

$id_paziente       = (int)($_POST['id_paziente'] ?? 0);
$id_farmaco        = (int)($_POST['id_farmaco'] ?? 0);
$data_prescrizione = $_POST['data_prescrizione'] ?? '';

if ($id_paziente <= 0 || $id_farmaco <= 0 || empty($data_prescrizione)) {
    header('Location: paziente.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM Prescrizione WHERE ID_Paziente=? AND ID_Farmaco=? AND Data_Prescrizione=?");
$stmt->execute([$id_paziente, $id_farmaco, $data_prescrizione]);

header("Location: gestione_prescrizione.php?id_paziente=$id_paziente&msg=eliminato");
exit;
