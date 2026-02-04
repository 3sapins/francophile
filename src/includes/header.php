<?php
/**
 * HEADER HTML - Produit le HTML du header
 * IMPORTANT: init.php DOIT être inclus avant ce fichier
 */

// Si init.php n'a pas été chargé, le charger maintenant (compatibilité)
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/init.php';
}

// Anti-cache pour empêcher le bouton retour de montrer des pages périmées
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Francophile' ?> - Francophile.ch</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Crimson+Pro:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <header class="main-header">
        <div class="container">
            <?php 
            $logoTarget = '/';
            if ($isLoggedIn) {
                $logoTarget = ($userType === 'eleve') ? '/eleve/dashboard.php' : '/enseignant/dashboard.php';
            }
            ?>
            <a href="<?= $logoTarget ?>" class="logo">
                <span class="logo-icon">📚</span>
                <span class="logo-text">Francophile</span>
            </a>
            
            <nav class="main-nav">
                <?php if ($isLoggedIn): ?>
                    <?php if ($userType === 'eleve'): ?>
                        <a href="/eleve/dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">Tableau de bord</a>
                        <a href="/eleve/parcours.php" class="<?= $currentPage === 'parcours' || $currentPage === 'parcours_detail' || $currentPage === 'parcours_jouer' ? 'active' : '' ?>">Parcours</a>
                        <a href="/eleve/exercices.php" class="<?= $currentPage === 'exercices' ? 'active' : '' ?>">Exercices libres</a>
                        <a href="/eleve/progression.php" class="<?= $currentPage === 'progression' ? 'active' : '' ?>">Ma progression</a>
                        <a href="/eleve/badges.php" class="<?= $currentPage === 'badges' ? 'active' : '' ?>">Mes badges</a>
                    <?php elseif ($userType === 'enseignant' || $userType === 'admin'): ?>
                        <a href="/enseignant/dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">Tableau de bord</a>
                        <a href="/enseignant/classes.php" class="<?= $currentPage === 'classes' ? 'active' : '' ?>">Mes classes</a>
                        <a href="/enseignant/exercices.php" class="<?= $currentPage === 'exercices' ? 'active' : '' ?>">Exercices</a>
                        <a href="/enseignant/resultats.php" class="<?= $currentPage === 'resultats' ? 'active' : '' ?>">Résultats</a>
                        <?php if ($userType === 'admin'): ?>
                            <a href="/admin/dashboard.php" class="<?= $currentPage === 'admin' ? 'active' : '' ?>">Administration</a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Accueil</a>
                    <a href="/login.php" class="<?= $currentPage === 'login' ? 'active' : '' ?>">Connexion</a>
                <?php endif; ?>
            </nav>
            
            <?php if ($isLoggedIn): ?>
                <div class="user-menu">
                    <span class="user-name">
                        <?php if ($userType === 'eleve'): ?>
                            <?= htmlspecialchars($userData['pseudo'] ?? '') ?>
                            <span class="user-badge"><?= htmlspecialchars($userData['classe_nom'] ?? '') ?></span>
                        <?php else: ?>
                            <?= htmlspecialchars(($userData['prenom'] ?? '') . ' ' . ($userData['nom'] ?? '')) ?>
                        <?php endif; ?>
                    </span>
                    <a href="/logout.php" class="btn btn-small btn-outline">Déconnexion</a>
                </div>
            <?php endif; ?>
            
            <button class="mobile-menu-toggle" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars(Session::getFlash('success')) ?>
        </div>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars(Session::getFlash('error')) ?>
        </div>
    <?php endif; ?>
    
    <main class="main-content">
