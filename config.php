<?php
// config.php — mantenuto per compatibilità, ora include semplicemente db.php
// Tutti i file che includevano config.php troveranno $pdo disponibile.
require_once __DIR__ . '/db.php';

// Alias per retrocompatibilità: alcuni file usavano $conn
$conn = $pdo;
