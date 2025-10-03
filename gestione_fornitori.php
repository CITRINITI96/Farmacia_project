<?php
require_once 'config.php'; // Assicurati che il file di configurazione includa la connessione al database $conn

$error = '';
$success = '';

// Aggiunta di un nuovo fornitore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    $nome = $_POST['nome'];
    $indirizzo = $_POST['indirizzo'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if ($nome && $indirizzo && $telefono && $email) {
        $query = "INSERT INTO Fornitori (Nome, Indirizzo, Telefono, Email) VALUES (:nome, :indirizzo, :telefono, :email)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':indirizzo', $indirizzo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            $success = 'Fornitore aggiunto con successo!';
        } else {
            $error = 'Errore nell\'aggiunta del fornitore.';
        }
    } else {
        $error = 'Tutti i campi sono obbligatori.';
    }
}

// Eliminazione di un fornitore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_supplier'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM Fornitori WHERE ID_Fornitore = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        $success = 'Fornitore eliminato con successo!';
    } else {
        $error = 'Errore nell\'eliminazione del fornitore.';
    }
}

// Modifica di un fornitore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_supplier'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $indirizzo = $_POST['indirizzo'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    if ($nome && $indirizzo && $telefono && $email) {
        $query = "UPDATE Fornitori SET Nome = :nome, Indirizzo = :indirizzo, Telefono = :telefono, Email = :email WHERE ID_Fornitore = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':indirizzo', $indirizzo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        if ($stmt->execute()) {
            $success = 'Fornitore aggiornato con successo!';
        } else {
            $error = 'Errore nell\'aggiornamento del fornitore.';
        }
    } else {
        $error = 'Tutti i campi sono obbligatori per la modifica.';
    }
}

// Recupero dei fornitori
$query = "SELECT * FROM Fornitori";
$stmt = $conn->prepare($query);
$stmt->execute();
$fornitori = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Fornitori</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            padding: 20px;
            margin: auto;
            width: 80%;
            max-width: 1200px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #4caf50;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form {
            margin-bottom: 20px;
        }
        form input, form button {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        .scrollable-table {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .back-button {
            position: absolute;
            bottom: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestione Fornitori</h1>

        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <h3>Aggiungi Fornitore</h3>
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="text" name="indirizzo" placeholder="Indirizzo" required>
            <input type="text" name="telefono" placeholder="Telefono" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit" name="add_supplier">Aggiungi</button>
        </form>

        <div class="scrollable-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Indirizzo</th>
                        <th>Telefono</th>
                        <th>Email</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fornitori as $fornitore): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fornitore['ID_Fornitore']); ?></td>
                            <td><?php echo htmlspecialchars($fornitore['Nome']); ?></td>
                            <td><?php echo htmlspecialchars($fornitore['Indirizzo']); ?></td>
                            <td><?php echo htmlspecialchars($fornitore['Telefono']); ?></td>
                            <td><?php echo htmlspecialchars($fornitore['Email']); ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $fornitore['ID_Fornitore']; ?>">
                                    <input type="text" name="nome" placeholder="Nome" value="<?php echo htmlspecialchars($fornitore['Nome']); ?>" required>
                                    <input type="text" name="indirizzo" placeholder="Indirizzo" value="<?php echo htmlspecialchars($fornitore['Indirizzo']); ?>" required>
                                    <input type="text" name="telefono" placeholder="Telefono" value="<?php echo htmlspecialchars($fornitore['Telefono']); ?>" required>
                                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($fornitore['Email']); ?>" required>
                                    <button type="submit" name="edit_supplier">Modifica</button>
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $fornitore['ID_Fornitore']; ?>">
                                    <button type="submit" name="delete_supplier">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="menu.php" class="back-button">Indietro</a>
</body>
</html>
