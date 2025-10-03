<?php
include 'db.php';
session_start();

// Token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestione eliminazione reparto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reparto_elimina'], $_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $id_reparto = $_POST['id_reparto_elimina'];

    // Chiama la funzione per eliminare il reparto
    if (eliminaReparto($id_reparto, $pdo)) {
        $_SESSION['message'] = "Reparto eliminato con successo.";
    } else {
        $_SESSION['error'] = "Errore nell'eliminazione del reparto o reparto non trovato.";
    }

    // Redirige alla pagina corrente per ricaricare i dati
    header("Location: gestione_reparti.php");
    exit;
}

// Gestione modifica reparto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reparto_modifica'], $_POST['nome_reparto_modifica'], $_POST['responsabile_modifica'], $_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $id_reparto = $_POST['id_reparto_modifica'];
    $nome_reparto = trim($_POST['nome_reparto_modifica']);
    $responsabile = trim($_POST['responsabile_modifica']);

    if (!is_numeric($id_reparto) || empty($nome_reparto) || empty($responsabile)) {
        $_SESSION['error'] = "Dati mancanti o errati.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE reparto SET Nome_Reparto = ?, Responsabile = ? WHERE ID_Reparto = ?");
            $stmt->execute([$nome_reparto, $responsabile, $id_reparto]);
            $_SESSION['message'] = "Reparto modificato con successo.";
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Errore nella modifica del reparto. Contatta l'amministratore.";
        }
    }

    header("Location: gestione_reparti.php");
    exit;
}

// Gestione aggiunta nuovo reparto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_reparto'], $_POST['responsabile'], $_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $nome_reparto = trim($_POST['nome_reparto']);
    $responsabile = trim($_POST['responsabile']);

    if (empty($nome_reparto) || empty($responsabile)) {
        $_SESSION['error'] = "Dati mancanti.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO reparto (Nome_Reparto, Responsabile) VALUES (?, ?)");
            $stmt->execute([$nome_reparto, $responsabile]);
            $_SESSION['message'] = "Nuovo reparto aggiunto con successo.";
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Errore nell'aggiunta del reparto. Contatta l'amministratore.";
        }
    }

    header("Location: gestione_reparti.php");
    exit;
}

// Recupero reparti
$reparti = $pdo->query("SELECT * FROM reparto")->fetchAll(PDO::FETCH_ASSOC);

// Funzione elimina reparto
function eliminaReparto($id_reparto, $pdo) {
    // Verifica se l'ID è valido
    if (!is_numeric($id_reparto)) {
        return false; // ID non valido
    }

    try {
        // Prepara la query SQL per eliminare il reparto
        $stmt = $pdo->prepare("DELETE FROM reparto WHERE ID_Reparto = ?");
        $stmt->execute([$id_reparto]);

        // Verifica se il reparto è stato effettivamente eliminato
        if ($stmt->rowCount() > 0) {
            return true; // Reparto eliminato con successo
        } else {
            return false; // Nessuna riga eliminata, il reparto potrebbe non esistere
        }
    } catch (PDOException $e) {
        // Gestione degli errori
        error_log($e->getMessage());
        return false; // In caso di errore
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Reparti</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
        }
        .menu-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 1200px; /* Limite la larghezza massima */
        }
        .menu-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .menu-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            max-height: 300px;
            overflow-y: auto;
            display: block;
        }
        .menu-container table th,
        .menu-container table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .menu-container table th {
            background-color: #4caf50;
            color: white;
        }
        .menu-container form {
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .menu-container input[type="text"] {
            padding: 8px;
            margin: 5px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .menu-container button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .menu-container button:hover {
            background-color: #45a049;
        }
        .menu-button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }
        .menu-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <h1>Gestione Reparti</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php elseif (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <h2>Elenco Reparti</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome Reparto</th>
                <th>Responsabile</th>
                <th>Azioni</th>
            </tr>
            <?php foreach ($reparti as $reparto): ?>
            <tr>
                <td><?= htmlspecialchars($reparto['ID_Reparto']) ?></td>
                <td><?= htmlspecialchars($reparto['Nome_Reparto']) ?></td>
                <td><?= htmlspecialchars($reparto['Responsabile']) ?></td>
                <td>
                    <!-- Form per eliminare il reparto -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="id_reparto_elimina" value="<?= htmlspecialchars($reparto['ID_Reparto']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" onclick="return confirm('Sei sicuro di voler eliminare questo reparto?')">Elimina</button>
                    </form>
                    
                    <!-- Form per modificare il reparto -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="id_reparto_modifica" value="<?= htmlspecialchars($reparto['ID_Reparto']) ?>">
                        <input type="text" name="nome_reparto_modifica" placeholder="Nuovo nome" required>
                        <input type="text" name="responsabile_modifica" placeholder="Nuovo responsabile" required>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit">Modifica</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Inserisci Nuovo Reparto</h2>
        <form method="post">
            <label for="nome_reparto">Nome Reparto:</label>
            <input type="text" id="nome_reparto" name="nome_reparto" required>
            <label for="responsabile">Responsabile:</label>
            <input type="text" id="responsabile" name="responsabile" required>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit">Aggiungi Reparto</button>
        </form>
    </div>

    <button class="menu-button" onclick="window.location.href='menu.php'">Indietro</button>
</body>
</html>
