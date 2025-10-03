<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inserimento di un nuovo farmaco
    if (isset($_POST['nome']) && !isset($_POST['id_farmaco'])) {
        $nome = $_POST['nome'];
        $categoria = $_POST['categoria']; // Modificato per ricevere la categoria da select
        $pericolosita = isset($_POST['pericolosita']) ? 1 : 0;
        $prezzo_unitario = $_POST['prezzo_unitario'];

        $stmt = $pdo->prepare("INSERT INTO Farmaco (Nome, Categoria, Pericolosità, Prezzo_Unitario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $categoria, $pericolosita, $prezzo_unitario]);
        header("Location: farmaco.php");
    }

    // Modifica di un farmaco esistente
    if (isset($_POST['id_farmaco'])) {
        $id_farmaco = $_POST['id_farmaco'];
        $nome = $_POST['nome'];
        $categoria = $_POST['categoria']; // Modificato per ricevere la categoria da select
        $pericolosita = isset($_POST['pericolosita']) ? 1 : 0;
        $prezzo_unitario = $_POST['prezzo_unitario'];

        $stmt = $pdo->prepare("UPDATE Farmaco SET Nome = ?, Categoria = ?, Pericolosità = ?, Prezzo_Unitario = ? WHERE ID_Farmaco = ?");
        $stmt->execute([$nome, $categoria, $pericolosita, $prezzo_unitario, $id_farmaco]);
        header("Location: farmaco.php");
    }
}

// Elimina un farmaco
if (isset($_GET['elimina_id'])) {
    $id_farmaco = $_GET['elimina_id'];

    // Prepara la query per eliminare il farmaco
    $stmt = $pdo->prepare("DELETE FROM Farmaco WHERE ID_Farmaco = ?");
    $stmt->execute([$id_farmaco]);

    // Redirect alla pagina di gestione farmaci dopo l'eliminazione
    header("Location: farmaco.php");
    exit;
}

// Recupera tutti i farmaci
$farmaci = $pdo->query("SELECT * FROM Farmaco")->fetchAll(PDO::FETCH_ASSOC);

// Se un ID farmaco è passato via GET, carica i dettagli del farmaco da modificare
$farmacoDaModificare = null;
if (isset($_GET['id_farmaco'])) {
    $id_farmaco = $_GET['id_farmaco'];
    $farmacoDaModificare = $pdo->prepare("SELECT * FROM Farmaco WHERE ID_Farmaco = ?");
    $farmacoDaModificare->execute([$id_farmaco]);
    $farmacoDaModificare = $farmacoDaModificare->fetch(PDO::FETCH_ASSOC);
}

// Elenco delle categorie di farmaci
$categorias = [
    'Antibiotico',
    'Analgesico',
    'Antipiretico',
    'Antidolorifico',
    'Antinfiammatorio',
    'Antidepressivo',
    'Anticoagulante',
    'Beta-Bloccante',
    'Diuretico',
    'Farmaco da banco',
    'Farmaco da prescrizione',
    'Altro'
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Farmaci</title>
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
            width: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .menu-container h1 {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .menu-container form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .menu-container input, .menu-container button, .menu-container select {
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        .menu-container button {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        .menu-container button:hover {
            background-color: #45a049;
        }
        .menu-container table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
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
        .scrollable-table-container {
            max-height: 300px;
            overflow-y: auto;
            width: 100%;
            margin-top: 20px;
        }
        /* Stile per il pulsante Indietro */
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
        <h1>Gestione Farmaci</h1>
        
        <!-- Se un farmaco è selezionato per la modifica, mostra il modulo di modifica -->
        <?php if ($farmacoDaModificare): ?>
        <h2>Modifica Farmaco</h2>
        <form method="post">
            <input type="hidden" name="id_farmaco" value="<?= $farmacoDaModificare['ID_Farmaco'] ?>">
            <input type="text" name="nome" value="<?= $farmacoDaModificare['Nome'] ?>" placeholder="Nome Farmaco" required>
            
            <!-- Selettore per la categoria -->
            <select name="categoria" required>
                <option value="">Seleziona Categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria ?>" <?= $farmacoDaModificare['Categoria'] === $categoria ? 'selected' : '' ?>>
                        <?= $categoria ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="checkbox-container">
                <input type="checkbox" name="pericolosita" id="pericolosita" <?= $farmacoDaModificare['Pericolosità'] ? 'checked' : '' ?>>
                <label for="pericolosita">Pericoloso</label>
            </div>
            <input type="number" step="0.01" name="prezzo_unitario" value="<?= $farmacoDaModificare['Prezzo_Unitario'] ?>" placeholder="Prezzo Unitario" required>
            <button type="submit">Modifica Farmaco</button>
        </form>
        <?php else: ?>
        <form method="post">
            <input type="text" name="nome" placeholder="Nome Farmaco" required>

            <!-- Selettore per la categoria -->
            <select name="categoria" required>
                <option value="">Seleziona Categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria ?>"><?= $categoria ?></option>
                <?php endforeach; ?>
            </select>

            <div class="checkbox-container">
                <input type="checkbox" name="pericolosita" id="pericolosita">
                <label for="pericolosita">Pericoloso</label>
            </div>
            <input type="number" step="0.01" name="prezzo_unitario" placeholder="Prezzo Unitario" required>
            <button type="submit">Aggiungi Farmaco</button>
        </form>
        <?php endif; ?>

        <h2>Elenco Farmaci</h2>
        <div class="scrollable-table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Pericolosità</th>
                    <th>Prezzo</th>
                    <th>Azioni</th>
                </tr>
                <?php foreach ($farmaci as $farmaco): ?>
                <tr>
                    <td><?= $farmaco['ID_Farmaco'] ?></td>
                    <td><?= $farmaco['Nome'] ?></td>
                    <td><?= $farmaco['Categoria'] ?></td>
                    <td><?= $farmaco['Pericolosità'] ? 'Sì' : 'No' ?></td>
                    <td><?= $farmaco['Prezzo_Unitario'] ?></td>
                    <td>
                        <a href="farmaco.php?id_farmaco=<?= $farmaco['ID_Farmaco'] ?>">Modifica</a>
                        <a href="farmaco.php?elimina_id=<?= $farmaco['ID_Farmaco'] ?>" onclick="return confirm('Sei sicuro di voler eliminare questo farmaco?')">Elimina</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- Pulsante Indietro -->
    <button class="menu-button" onclick="goBack()">Indietro</button>

    <script>
        function goBack() {
            window.history.back(); // Torna alla pagina precedente
        }
    </script>
</body>
</html>
