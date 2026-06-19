<?php
// =============================================
// RECUPERO PASSWORD — sistema domanda segreta
// Flusso: Step 1 (email) → Step 2 (domanda) → Step 3 (nuova password)
// =============================================
require_once 'db.php';

session_start();

$step    = 1;
$error   = '';
$success = '';
$domanda = '';

// ── STEP 1: l'utente inserisce l'email ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '1') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Inserisci un indirizzo email valido.';
        $step  = 1;
    } else {
        $stmt = $pdo->prepare("SELECT ID_Utente, domanda_segreta FROM Utente WHERE Email = ?");
        $stmt->execute([$email]);
        $utente = $stmt->fetch();

        if (!$utente || empty($utente['domanda_segreta'])) {
            // Non rivelare se l'email esiste o no (sicurezza)
            $error = 'Email non trovata o nessuna domanda segreta impostata.';
            $step  = 1;
        } else {
            // Salva in sessione e vai allo step 2
            $_SESSION['reset_email']      = $email;
            $_SESSION['reset_id_utente']  = $utente['ID_Utente'];
            $domanda = $utente['domanda_segreta'];
            $step    = 2;
        }
    }

// ── STEP 2: l'utente risponde alla domanda segreta ──────────────────────────
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '2') {
    if (empty($_SESSION['reset_email'])) {
        // Sessione scaduta, ricomincia
        $step  = 1;
        $error = 'Sessione scaduta, ricomincia dall\'inizio.';
    } else {
        $risposta = trim($_POST['risposta'] ?? '');

        $stmt = $pdo->prepare("SELECT risposta_segreta, domanda_segreta FROM Utente WHERE ID_Utente = ?");
        $stmt->execute([$_SESSION['reset_id_utente']]);
        $utente = $stmt->fetch();

        if (!$utente) {
            $step  = 1;
            $error = 'Utente non trovato. Ricomincia.';
        } elseif (strtolower(trim($risposta)) !== strtolower(trim($utente['risposta_segreta']))) {
            $domanda = $utente['domanda_segreta'];
            $step    = 2;
            $error   = 'Risposta non corretta. Riprova.';
        } else {
            // Risposta corretta → step 3
            $_SESSION['reset_verificato'] = true;
            $domanda = $utente['domanda_segreta'];
            $step    = 3;
        }
    }

// ── STEP 3: l'utente imposta la nuova password ──────────────────────────────
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '3') {
    if (empty($_SESSION['reset_email']) || empty($_SESSION['reset_verificato'])) {
        $step  = 1;
        $error = 'Sessione non valida. Ricomincia dall\'inizio.';
    } else {
        $nuova_password  = $_POST['nuova_password']  ?? '';
        $conferma_password = $_POST['conferma_password'] ?? '';

        if (strlen($nuova_password) < 8) {
            $step  = 3;
            $error = 'La password deve essere di almeno 8 caratteri.';
        } elseif ($nuova_password !== $conferma_password) {
            $step  = 3;
            $error = 'Le due password non coincidono.';
        } else {
            $hashed = password_hash($nuova_password, PASSWORD_DEFAULT);
            $stmt   = $pdo->prepare("UPDATE Utente SET Password = ? WHERE ID_Utente = ?");
            $stmt->execute([$hashed, $_SESSION['reset_id_utente']]);

            // Pulizia sessione
            unset($_SESSION['reset_email'], $_SESSION['reset_id_utente'], $_SESSION['reset_verificato']);

            $success = 'Password aggiornata con successo! Ora puoi accedere.';
            $step    = 0; // Step finale
        }
    }

// ── Rientro da step 2 se si ricarica la pagina ──────────────────────────────
} elseif (!empty($_SESSION['reset_email']) && empty($_SESSION['reset_verificato'])) {
    $stmt = $pdo->prepare("SELECT domanda_segreta FROM Utente WHERE ID_Utente = ?");
    $stmt->execute([$_SESSION['reset_id_utente']]);
    $utente = $stmt->fetch();
    $domanda = $utente['domanda_segreta'] ?? '';
    $step    = 2;
} elseif (!empty($_SESSION['reset_email']) && !empty($_SESSION['reset_verificato'])) {
    $step = 3;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera Password — PharmaCare</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .steps {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-bottom: 28px;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--gray-300);
            z-index: 0;
        }
        .step-item.active:not(:last-child)::after,
        .step-item.done:not(:last-child)::after {
            background: var(--green-500);
        }
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gray-300);
            color: var(--gray-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .85rem;
            z-index: 1;
            position: relative;
            transition: background .3s, color .3s;
        }
        .step-item.active .step-circle {
            background: var(--green-500);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(76,175,80,.2);
        }
        .step-item.done .step-circle {
            background: var(--green-700);
            color: #fff;
        }
        .step-label {
            font-size: .72rem;
            color: var(--gray-700);
            margin-top: 4px;
            text-align: center;
        }
        .step-item.active .step-label { color: var(--green-600); font-weight: 600; }

        .success-box {
            text-align: center;
            padding: 24px 0 8px;
        }
        .success-icon {
            font-size: 3.5rem;
            display: block;
            margin-bottom: 12px;
        }
        .hint-text {
            font-size: .8rem;
            color: var(--gray-700);
            margin-top: 6px;
        }
    </style>
</head>
<body>
<div class="page-center" style="flex-direction:column;">
    <div class="card" style="max-width:440px;">
        <div class="card-header">
            <div class="brand">💊 PharmaCare</div>
            <h1>Recupera Password</h1>
        </div>

        <?php if ($step > 0 && $step <= 3): ?>
        <!-- Indicatore passi -->
        <div class="steps">
            <div class="step-item <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">
                <div class="step-circle"><?= $step > 1 ? '✓' : '1' ?></div>
                <span class="step-label">Email</span>
            </div>
            <div class="step-item <?= $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' ?>">
                <div class="step-circle"><?= $step > 2 ? '✓' : '2' ?></div>
                <span class="step-label">Domanda</span>
            </div>
            <div class="step-item <?= $step >= 3 ? 'active' : '' ?>">
                <div class="step-circle">3</div>
                <span class="step-label">Nuova Password</span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <!-- Step finale: successo -->
        <div class="success-box">
            <span class="success-icon">✅</span>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <a href="login.php" class="btn btn-primary btn-block" style="margin-top:16px;">
                Vai al Login
            </a>
        </div>

        <?php elseif ($step === 1): ?>
        <!-- STEP 1: Inserisci email -->
        <p style="font-size:.9rem;color:var(--gray-700);margin-bottom:16px;">
            Inserisci la tua email per recuperare l'accesso al tuo account.
        </p>
        <form method="POST" action="recupera_password.php" novalidate>
            <input type="hidden" name="step" value="1">
            <label for="email">Indirizzo Email *</label>
            <input type="email" id="email" name="email" required
                   placeholder="es. mario.rossi@ospedale.it"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">
                Continua →
            </button>
        </form>

        <?php elseif ($step === 2): ?>
        <!-- STEP 2: Domanda segreta -->
        <p style="font-size:.9rem;color:var(--gray-700);margin-bottom:16px;">
            Rispondi alla tua domanda segreta per verificare la tua identità.
        </p>
        <form method="POST" action="recupera_password.php" novalidate>
            <input type="hidden" name="step" value="2">
            <label>Domanda segreta</label>
            <div style="padding:10px 14px;background:var(--gray-100);border-radius:var(--radius-sm);
                        font-size:.95rem;color:var(--gray-900);border:1.5px solid var(--gray-300);margin-bottom:4px;">
                🔒 <?= htmlspecialchars($domanda) ?>
            </div>
            <label for="risposta">La tua risposta *</label>
            <input type="text" id="risposta" name="risposta" required
                   placeholder="Scrivi la tua risposta"
                   autocomplete="off">
            <p class="hint-text">💡 La risposta non è sensibile alle maiuscole/minuscole.</p>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">
                Verifica →
            </button>
        </form>

        <?php elseif ($step === 3): ?>
        <!-- STEP 3: Nuova password -->
        <p style="font-size:.9rem;color:var(--gray-700);margin-bottom:16px;">
            Identità verificata! Scegli una nuova password sicura.
        </p>
        <form method="POST" action="recupera_password.php" novalidate>
            <input type="hidden" name="step" value="3">
            <label for="nuova_password">Nuova Password * <small style="color:var(--gray-700)">(min. 8 caratteri)</small></label>
            <input type="password" id="nuova_password" name="nuova_password" required minlength="8"
                   placeholder="Nuova password">
            <label for="conferma_password">Conferma Password *</label>
            <input type="password" id="conferma_password" name="conferma_password" required minlength="8"
                   placeholder="Ripeti la password">
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">
                🔐 Salva Nuova Password
            </button>
        </form>
        <?php endif; ?>

        <?php if ($step > 0): ?>
        <p class="text-center mt-2" style="font-size:.85rem;color:var(--gray-700);">
            Ricordi la password? <a href="login.php">Accedi</a>
        </p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
