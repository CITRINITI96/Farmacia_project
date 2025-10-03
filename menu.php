<?php
session_start();

// Verifica se l'utente è autenticato
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Titolo della pagina
$title = "Menu Farmacia Ospedaliera";

// Ruolo dell'utente
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            background: linear-gradient(to bottom, #4caf50 50%, #ffffff 50%);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .menu-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }
        .menu-container h1 {
            font-size: 28px;
            font-weight: bold;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .menu-container a {
            display: block;
            font-size: 18px;
            color: #4caf50;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #4caf50;
            border-radius: 5px;
        }
        .menu-container a:hover {
            background-color: #4caf50;
            color: white;
        }
    </style>
</head>
<body>

    <div class="menu-container">
        <h1>Menu Farmacia Ospedaliera</h1>

        <!-- Menu personalizzato in base al ruolo -->
        <?php if ($role === 'Dottore' || $role === 'Dottoressa'): ?>
            <a href="paziente.php">Anagrafica Paziente</a>
            <a href="gestione_reparti.php">Gestione Reparti</a>
            <a href="gestione_referti.php">Gestione Referti</a>
        <?php endif; ?>

        <?php if ($role === 'Farmacista'): ?>
            <a href="farmaco.php">Gestione Farmaci</a>
            <a href="ordini_fornitore.php">Gestione Ordini</a>
            <a href="gestione_fornitori.php">Gestione Fornitori</a>
            <a href="notifiche.php">Notifiche</a>
        <?php endif; ?>

        <?php if ($role === 'Magazziniere'): ?>
            <a href="ordini_fornitore.php">Gestione Ordini</a>
            <a href="magazzino.php">Gestione Magazzini</a>
        <?php endif; ?>

        <!-- Link comune per tutti gli utenti -->
        <a href="logout.php">Logout</a>
    </div>
<?php include 'footer.php'; ?>
</body>
</html>
