<?php
/**
 * INIT PHP - Chargé AVANT tout output HTML
 * Gère config, session, et les vérifications d'accès
 */
require_once __DIR__ . '/../config/config.php';
Session::start();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isLoggedIn = Session::isLoggedIn();
$userType = Session::getUserType();
$userData = Session::getUserData();

/**
 * Vérifier que l'utilisateur est un élève connecté
 * DOIT être appelé avant include header.php
 */
function requireEleve(): void {
    if (!Session::isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
    if (!Session::isEleve()) {
        header('Location: /login.php?error=access_denied');
        exit;
    }
}

function requireEnseignant(): void {
    if (!Session::isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
    if (!Session::isEnseignant() && !Session::isAdmin()) {
        header('Location: /login.php?error=access_denied');
        exit;
    }
}

function requireAdmin(): void {
    if (!Session::isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
    if (!Session::isAdmin()) {
        header('Location: /login.php?error=access_denied');
        exit;
    }
}
