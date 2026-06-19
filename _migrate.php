<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM Utente");
    $colonne = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Colonne della tabella Utente:</h3><ul>";
    foreach ($colonne as $col) {
        $ok = in_array($col, ['domanda_segreta', 'risposta_segreta']) ? ' ✅' : '';
        echo "<li><strong>{$col}</strong>{$ok}</li>";
    }
    echo "</ul>";

    $hasDomanda  = in_array('domanda_segreta',  $colonne);
    $hasRisposta = in_array('risposta_segreta', $colonne);

    if ($hasDomanda && $hasRisposta) {
        echo "<p style='color:green;font-size:1.2rem;'><strong>✅ Tutto OK! Le colonne sono presenti.</strong></p>";
    } else {
        echo "<p style='color:red;'><strong>❌ Colonne mancanti:</strong></p><ul>";
        if (!$hasDomanda)  echo "<li>domanda_segreta</li>";
        if (!$hasRisposta) echo "<li>risposta_segreta</li>";
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "<strong style='color:red'>❌ Errore: " . htmlspecialchars($e->getMessage()) . "</strong>";
}
?>
