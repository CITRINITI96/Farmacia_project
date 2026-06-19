<?php
// =============================================
// AUTH HELPER — Controllo autenticazione
// Da includere in CIMA a ogni pagina protetta.
// =============================================

/**
 * Verifica che l'utente sia autenticato e abbia il ruolo richiesto.
 *
 * @param string|string[]|null $ruoli  Ruolo/i ammessi. Null = qualsiasi ruolo loggato.
 */
function requireAuth($ruoli = null): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Rigenerare l'ID sessione ogni 30 minuti per prevenire session fixation
    if (!isset($_SESSION['last_regen'])) {
        $_SESSION['last_regen'] = time();
    } elseif (time() - $_SESSION['last_regen'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    if ($ruoli !== null) {
        $ruoli = (array) $ruoli;
        if (!in_array($_SESSION['role'], $ruoli, true)) {
            // Utente loggato ma ruolo non autorizzato
            header('Location: menu.php');
            exit;
        }
    }
}

/**
 * Genera (se non esiste) e restituisce il token CSRF di sessione.
 */
function csrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica il token CSRF da POST, termina con 403 se non valido.
 */
function verifyCsrf(): void
{
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die('Richiesta non valida (CSRF token mancante o errato).');
    }
}

/**
 * Stampa un campo hidden con il token CSRF pronto per i form.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}
