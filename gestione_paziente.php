<?php
// gestione_paziente.php — redirect to paziente.php (same functionality)
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);
header('Location: paziente.php');
exit;
