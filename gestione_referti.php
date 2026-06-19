<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth(['Admin', 'Dottore', 'Dottoressa']);

csrfToken();

$flash = '';
if (isset($_GET['msg'])) {
    $msgs = ['aggiunto' => 'Referto aggiunto.', 'modificato' => 'Referto modificato.', 'eliminato' => 'Referto eliminato.'];
    $flash = $msgs[$_GET['msg']] ?? '';
}

$stmt = $pdo->query("SELECT RT.ID_Referto,
                            CONCAT(P.Nome,' ',P.Cognome) AS Paziente_Nome,
                            F.Nome AS Nome_Farmaco,
                            CONCAT(U.Nome,' ',U.Cognome) AS Dottore_Nome,
                            RT.Referto,
                            RT.Terapia,
                            RT.Data_Assegnazione
                     FROM RefertiTerapie RT
                     JOIN Paziente P ON RT.ID_Paziente = P.ID_Paziente
                     JOIN Farmaco F ON RT.ID_Farmaco = F.ID_Farmaco
                     JOIN Utente U ON RT.ID_Dottore = U.ID_Utente
                     ORDER BY RT.Data_Assegnazione DESC");
$referti = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referti e Terapie — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php if ($_SESSION['role'] === 'Admin') include 'sidebar_admin.php'; ?>

    <div class="main-content" style="<?= $_SESSION['role'] !== 'Admin' ? 'margin-left:0' : '' ?>">
        <div class="page-header flex justify-between items-center">
            <h1>📋 Gestione Referti e Terapie</h1>
            <a href="aggiungi_referto_terapia.php" class="btn btn-primary">➕ Nuovo Referto</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Referti (<?= count($referti) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paziente</th>
                            <th>Farmaco</th>
                            <th>Dottore</th>
                            <th>Referto</th>
                            <th>Terapia</th>
                            <th>Data</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($referti as $r): ?>
                        <tr>
                            <td><?= (int)$r['ID_Referto'] ?></td>
                            <td><?= htmlspecialchars($r['Paziente_Nome']) ?></td>
                            <td><?= htmlspecialchars($r['Nome_Farmaco']) ?></td>
                            <td><?= htmlspecialchars($r['Dottore_Nome']) ?></td>
                            <td><?= htmlspecialchars($r['Referto']) ?></td>
                            <td><?= htmlspecialchars($r['Terapia']) ?></td>
                            <td><?= htmlspecialchars($r['Data_Assegnazione']) ?></td>
                            <td class="actions-cell">
                                <a href="modifica_referto_terapia.php?id_referto=<?= (int)$r['ID_Referto'] ?>" class="btn btn-sm btn-secondary">✏️ Modifica</a>
                                <form method="post" action="elimina_referto_terapia.php" class="inline"
                                      onsubmit="return confirm('Eliminare il referto #<?= (int)$r['ID_Referto'] ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id_referto" value="<?= (int)$r['ID_Referto'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($referti)): ?>
                        <tr><td colspan="8" class="text-center" style="color:var(--gray-700);padding:20px">Nessun referto presente.</td></tr>
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
