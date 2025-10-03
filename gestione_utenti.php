<?php
// Includi la configurazione del database
require_once 'config.php';
session_start();

// Controlla se l'utente è autenticato e se è un admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php"); // Reindirizza alla pagina di login se non è admin
    exit;
}

// Recupera gli utenti dal database
$query = "SELECT * FROM Utente";
$stmt = $conn->prepare($query);
$stmt->execute();
$utenti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestione Utenti</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          margin: 0;
          padding: 0;
          background-color: #f4f4f4;
      }
      .sidebar {
          width: 250px;
          height: 100vh;
          background-color: #4caf50;
          color: white;
          position: fixed;
          top: 0;
          left: 0;
          padding-top: 20px;
          padding-left: 20px;
      }
      .sidebar a {
          color: white;
          text-decoration: none;
          display: block;
          padding: 10px 15px;
          margin-bottom: 10px;
          border-radius: 5px;
      }
      .sidebar a:hover {
          background-color: #45a049;
      }
      .main-content {
          margin-left: 250px;
          padding: 20px;
      }
      .header {
          background-color: #4caf50;
          color: white;
          padding: 10px 20px;
          text-align: center;
          margin-bottom: 20px;
      }
      table {
          width: 100%;
          border-collapse: collapse;
      }
      table, th, td {
          border: 1px solid #ddd;
      }
      th, td {
          padding: 10px;
          text-align: left;
      }
      .edit-btn, .delete-btn {
          background-color: #4caf50;
          color: white;
          padding: 5px 10px;
          text-decoration: none;
          border-radius: 5px;
          margin-right: 5px;
      }
      .edit-btn:hover, .delete-btn:hover {
          background-color: #45a049;
      }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
      <h3>Dashboard Admin</h3>
      <a href="gestione_utenti.php">Gestione Utenti</a>
      <a href="gestione_farmaci.php">Gestione Farmaci</a>
      <a href="report.php">Report</a>
      <a href="impostazioni.php">Impostazioni</a>
      <a href="logout.php">Esci</a>
  </div>

  <!-- Main content -->
  <div class="main-content">
      <div class="header">
          <h1>Gestione Utenti</h1>
      </div>

      <table>
          <thead>
              <tr>
                  <th>Username</th>
                  <th>Nome</th>
                  <th>Cognome</th>
                  <th>Ruolo</th>
                  <th>Azioni</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($utenti as $utente): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($utente['username']); ?></td>
                      <td><?php echo htmlspecialchars($utente['Nome']); ?></td>
                      <td><?php echo htmlspecialchars($utente['Cognome']); ?></td>
                      <td><?php echo htmlspecialchars($utente['Ruolo']); ?></td>
                      <td>
                          <!-- Utilizza ID_Utente al posto di 'id' -->
                          <a href="modifica_utente.php?id=<?php echo $utente['ID_Utente']; ?>" class="edit-btn">Modifica</a>
                          <a href="elimina_utente.php?id=<?php echo $utente['ID_Utente']; ?>" class="delete-btn" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');">Elimina</a>
                      </td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
  </div>

</body>
</html>
