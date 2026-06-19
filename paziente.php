<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

// Recupera i pazienti dal database
$pazienti = $pdo->query("SELECT P.ID_Paziente, P.Nome, P.Cognome, P.Data_Nascita, P.Sesso, R.Nome_Reparto
                         FROM Paziente P
                         JOIN Reparto R ON P.ID_Reparto = R.ID_Reparto
                         ORDER BY P.Cognome, P.Nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pazienti — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header flex justify-between items-center">
            <h1>🧑‍⚕️ Gestione Pazienti</h1>
            <a href="aggiungi_paziente.php" class="btn btn-primary">➕ Aggiungi Paziente</a>
        </div>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Pazienti (<?= count($pazienti) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Data Nascita</th>
                            <th>Sesso</th>
                            <th>Reparto</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pazienti as $paziente): ?>
                        <tr>
                            <td><?= (int)$paziente['ID_Paziente'] ?></td>
                            <td><?= htmlspecialchars($paziente['Nome']) ?></td>
                            <td><?= htmlspecialchars($paziente['Cognome']) ?></td>
                            <td><?= htmlspecialchars($paziente['Data_Nascita']) ?></td>
                            <td><?= htmlspecialchars($paziente['Sesso']) ?></td>
                            <td><?= htmlspecialchars($paziente['Nome_Reparto']) ?></td>
                            <td class="actions-cell">
                                <a href="gestione_prescrizione.php?id_paziente=<?= (int)$paziente['ID_Paziente'] ?>" class="btn btn-sm btn-secondary">📋 Prescrizioni</a>
                                <a href="modifica_paziente.php?id=<?= (int)$paziente['ID_Paziente'] ?>" class="btn btn-sm btn-secondary">✏️ Modifica</a>
                                <form method="post" action="elimina_paziente.php" class="inline"
                                      onsubmit="return confirm('Eliminare il paziente «<?= htmlspecialchars(addslashes($paziente['Nome'] . ' ' . $paziente['Cognome'])) ?>»?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$paziente['ID_Paziente'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pazienti)): ?>
                        <tr><td colspan="7" class="text-center" style="color:var(--gray-700);padding:20px">Nessun paziente presente.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if ($_SESSION['role'] !== 'Admin'): ?>
    <a href="menu.php" class="btn-back">← Indietro</a>
<?php endif; ?>
</body>
</html>
