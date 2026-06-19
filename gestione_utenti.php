<?php
require_once 'db.php';
require_once 'auth.php';
requireAuth('Admin');

csrfToken();

$message = '';
$msgType = 'success';

// Recupera gli utenti dal database
$utenti = $pdo->query("SELECT * FROM Utente ORDER BY Cognome, Nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar_admin.php'; ?>

    <div class="main-content">
        <div class="page-header flex justify-between items-center">
            <h1>👥 Gestione Utenti</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;box-shadow:var(--shadow-sm);">
            <h2>📋 Elenco Utenti (<?= count($utenti) ?>)</h2>
            <div class="table-wrapper table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Email</th>
                            <th>Ruolo</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($utenti as $utente): ?>
                        <tr>
                            <td><?= (int)$utente['ID_Utente'] ?></td>
                            <td><?= htmlspecialchars($utente['username']) ?></td>
                            <td><?= htmlspecialchars($utente['Nome']) ?></td>
                            <td><?= htmlspecialchars($utente['Cognome']) ?></td>
                            <td><?= htmlspecialchars($utente['Email'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $utente['Ruolo'] === 'Admin' ? 'badge-danger' : 'badge-success' ?>">
                                    <?= htmlspecialchars($utente['Ruolo']) ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="modifica_utente.php?id=<?= (int)$utente['ID_Utente'] ?>" class="btn btn-sm btn-secondary">✏️ Modifica</a>
                                <form method="post" action="elimina_utente.php" class="inline"
                                      onsubmit="return confirm('Eliminare l\'utente «<?= htmlspecialchars(addslashes($utente['username'])) ?>»?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$utente['ID_Utente'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
