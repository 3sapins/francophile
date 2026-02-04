<?php
$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEleve();

$eleve = Eleve::findById(Session::getUserId());
$stats = $eleve->getStatistiques();
$progressions = $eleve->getProgressions();
$badges = $eleve->getBadges();
$historique = $eleve->getHistorique(5);
$classe = $eleve->getClasse();
$hasData = $stats['total_exercices'] > 0;
?>

<div class="container">
    <!-- Welcome banner -->
    <div class="welcome-banner fade-in">
        <h1>Salut <?= htmlspecialchars($eleve->getPseudo()) ?> ! 👋</h1>
        <p>
            <?php if ($hasData): ?>
                Tu as déjà <?= number_format($stats['points_totaux'], 0, ',', ' ') ?> points. Continue comme ça !
            <?php else: ?>
                Bienvenue sur Francophile ! Lance-toi avec ton premier exercice 🚀
            <?php endif; ?>
        </p>
    </div>

    <!-- Quick actions -->
    <div class="quick-actions fade-in">
        <a href="/eleve/parcours.php" class="quick-action parcours-qa">
            <div class="quick-action-icon">🗺️</div>
            <div class="quick-action-title">Parcours</div>
            <div class="quick-action-desc">Progression guidée</div>
        </a>
        <a href="/eleve/exercices.php" class="quick-action conjugaison">
            <div class="quick-action-icon">✏️</div>
            <div class="quick-action-title">Exercices libres</div>
            <div class="quick-action-desc">Entraînement au choix</div>
        </a>
        <a href="/eleve/progression.php" class="quick-action progression">
            <div class="quick-action-icon">📊</div>
            <div class="quick-action-title">Progression</div>
            <div class="quick-action-desc">Suis tes résultats</div>
        </a>
        <a href="/eleve/badges.php" class="quick-action badges-card">
            <div class="quick-action-icon">🏆</div>
            <div class="quick-action-title">Badges</div>
            <div class="quick-action-desc"><?= count($badges) ?> badge<?= count($badges) > 1 ? 's' : '' ?> obtenu<?= count($badges) > 1 ? 's' : '' ?></div>
        </a>
    </div>

    <!-- Statistiques -->
    <?php if ($hasData): ?>
    <div class="stats-grid mb-xl fade-in">
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-value"><?= number_format($stats['points_totaux'], 0, ',', ' ') ?></div>
            <div class="stat-label">Points totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value"><?= $stats['total_correct'] ?></div>
            <div class="stat-label">Bonnes réponses</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-value"><?= $stats['taux_reussite'] ?>%</div>
            <div class="stat-label">Taux de réussite</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-value"><?= $stats['nb_badges'] ?></div>
            <div class="stat-label">Badges obtenus</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-2 gap-lg">
        <!-- Progression par domaine -->
        <div class="card fade-in">
            <div class="card-header">
                <h3 class="card-title">📈 Ma progression</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($progressions)): ?>
                    <?php foreach ($progressions as $prog): ?>
                        <?php 
                        $domaine = DOMAINES[$prog['domaine']] ?? $prog['domaine'];
                        $pointsProchain = $prog['points_prochain_niveau'] ?? ($prog['points_totaux'] + 100);
                        $pointsActuel = $prog['points_niveau_actuel'] ?? 0;
                        $progression = $pointsProchain > $pointsActuel 
                            ? (($prog['points_totaux'] - $pointsActuel) / ($pointsProchain - $pointsActuel)) * 100 
                            : 100;
                        $progression = min(100, max(0, $progression));
                        ?>
                        <div class="mb-md">
                            <div class="flex flex-between mb-sm">
                                <span class="text-small"><strong><?= htmlspecialchars($domaine) ?></strong></span>
                                <span class="text-small text-muted">
                                    Niveau <?= $prog['niveau_actuel'] ?> 
                                    (<?= number_format($prog['points_totaux'], 0, ',', ' ') ?> pts)
                                </span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $progression ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center" style="padding: 2rem 1rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🎮</div>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">Prêt à commencer ?</p>
                        <p class="text-muted text-small mb-lg">Fais ton premier exercice et ta progression apparaîtra ici !</p>
                        <a href="/eleve/exercices.php" class="btn btn-primary">Commencer</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Derniers badges -->
        <div class="card fade-in">
            <div class="card-header flex flex-between">
                <h3 class="card-title">🏆 Mes badges</h3>
                <a href="/eleve/badges.php" class="text-small" style="font-weight: 600;">Voir tout →</a>
            </div>
            <div class="card-body">
                <?php if (!empty($badges)): ?>
                    <div class="flex gap-sm" style="flex-wrap: wrap;">
                        <?php foreach (array_slice($badges, 0, 6) as $badge): ?>
                            <div class="badge badge-<?= $badge['niveau_difficulte'] == 3 ? 'gold' : ($badge['niveau_difficulte'] == 2 ? 'silver' : 'bronze') ?>" 
                                 title="<?= htmlspecialchars($badge['description'] ?? '') ?>">
                                <?= htmlspecialchars($badge['nom']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center" style="padding: 2rem 1rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">Des badges t'attendent !</p>
                        <p class="text-muted text-small">Fais des exercices pour débloquer tes premiers badges.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <?php if (!empty($historique)): ?>
    <div class="card mt-lg fade-in">
        <div class="card-header">
            <h3 class="card-title">📋 Mes dernières sessions</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Domaine</th>
                        <th>Questions</th>
                        <th>Réussite</th>
                        <th class="text-right">Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $session): ?>
                        <?php 
                        $taux = $session['nombre_questions'] > 0 
                            ? round(($session['nombre_correct'] / $session['nombre_questions']) * 100) 
                            : 0;
                        $points = ($session['points_gagnes'] ?? 0) - ($session['points_perdus'] ?? 0);
                        ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($session['date_debut'])) ?></td>
                            <td><?= htmlspecialchars(DOMAINES[$session['domaine']] ?? $session['domaine']) ?></td>
                            <td><?= $session['nombre_correct'] ?> / <?= $session['nombre_questions'] ?></td>
                            <td>
                                <span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>">
                                    <?= $taux ?>%
                                </span>
                            </td>
                            <td class="text-right" style="color: <?= $points >= 0 ? 'var(--success)' : 'var(--error)' ?>; font-weight: 700;">
                                <?= $points >= 0 ? '+' : '' ?><?= $points ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
