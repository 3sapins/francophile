<?php
$pageTitle = 'Accueil';

// Rediriger si déjà connecté AVANT tout output HTML
require_once __DIR__ . '/../src/config/config.php';
Session::start();

if (Session::isLoggedIn()) {
    $redirect = Session::isEleve() ? '/eleve/dashboard.php' : '/enseignant/dashboard.php';
    header('Location: ' . $redirect);
    exit;
}

require_once __DIR__ . '/../src/includes/header.php';
?>

<div class="container">
    <section class="hero text-center" style="padding: 4rem 0;">
        <h1 style="font-size: 3rem; margin-bottom: 1rem;">
            <span style="font-size: 4rem;">📚</span><br>
            Bienvenue sur Francophile
        </h1>
        <p class="text-large text-muted" style="max-width: 600px; margin: 0 auto 2rem;">
            La plateforme d'exercices de français pour les élèves du cycle 3.<br>
            Conjugaison, orthographe, grammaire... progressez en vous amusant !
        </p>
        <div class="flex flex-center gap-md">
            <a href="/login.php" class="btn btn-primary btn-large">Se connecter</a>
            <a href="/login.php?tab=register" class="btn btn-outline btn-large">Créer un compte enseignant</a>
        </div>
    </section>

    <section class="features" style="padding: 3rem 0;">
        <h2 class="text-center mb-xl">Pourquoi Francophile ?</h2>
        <div class="grid grid-3">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                    <h3>Aligné sur le PER</h3>
                    <p class="text-muted">Exercices conformes au Plan d'études romand pour les 9e, 10e et 11e années.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                    <h3>Gamification</h3>
                    <p class="text-muted">Gagnez des points, débloquez des badges et montez de niveau pour rester motivé.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                    <h3>Suivi des progrès</h3>
                    <p class="text-muted">Enseignants : suivez les résultats de vos élèves en temps réel.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="domains" style="padding: 3rem 0;">
        <h2 class="text-center mb-xl">Domaines d'exercices</h2>
        <div class="grid grid-4">
            <div class="card" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <h4>✏️ Conjugaison</h4>
                    <p class="text-small text-muted">Tous les temps, tous les modes, avec des phrases contextuelles.</p>
                </div>
            </div>
            <div class="card" style="border-left: 4px solid var(--secondary);">
                <div class="card-body">
                    <h4>📝 Orthographe</h4>
                    <p class="text-small text-muted">Homophones, accords, dictées audio.</p>
                </div>
            </div>
            <div class="card" style="border-left: 4px solid var(--success);">
                <div class="card-body">
                    <h4>📖 Grammaire</h4>
                    <p class="text-small text-muted">Analyse, fonctions, transformations.</p>
                    <span class="badge badge-gray">Bientôt</span>
                </div>
            </div>
            <div class="card" style="border-left: 4px solid var(--warning);">
                <div class="card-body">
                    <h4>📚 Vocabulaire</h4>
                    <p class="text-small text-muted">Synonymes, antonymes, champs lexicaux.</p>
                    <span class="badge badge-gray">Bientôt</span>
                </div>
            </div>
        </div>
    </section>

    <section class="cta text-center" style="padding: 3rem 0; background: var(--gray-100); border-radius: var(--radius-xl); margin: 2rem 0;">
        <h2>Prêt à commencer ?</h2>
        <p class="text-muted mb-lg">Rejoignez Francophile et améliorez votre français !</p>
        <a href="/login.php" class="btn btn-primary btn-large">Commencer maintenant</a>
    </section>
</div>

<?php require_once __DIR__ . '/../src/includes/footer.php'; ?>
