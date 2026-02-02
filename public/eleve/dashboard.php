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
?>

<div class="container">
    <div class="flex flex-between mb-xl">
        <div>
            <h1 class="mb-sm">Bonjour <?= htmlspecialchars($eleve->getPseudo()) ?> ! 👋</h1>
            <p class="text-muted">
                <?= htmlspecialchars($classe['nom'] ?? '') ?> - <?= $classe['annee_scolaire'] ?? '' ?>e année
            </p>
        </div>
        <a href="/eleve/exercices.php" class="btn btn-primary btn-large">Faire des exercices</a>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid mb-xl">
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-value"><?= number_format($stats['points_totaux'], 0, ',', ' ') ?></div>
            <div class="stat-label">Points totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value"><?= $stats['total_exercices'] ?></div>
            <div class="stat-label">Exercices réalisés</div>
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

    <div class="grid grid-2 gap-lg">
        <!-- Progression par domaine -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ma progression</h3>
            </div>
            <div class="card-body">
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
                
                <?php if (empty($progressions)): ?>
                    <p class="text-muted text-center">Fais tes premiers exercices pour voir ta progression !</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Derniers badges -->
        <div class="card">
            <div class="card-header flex flex-between">
                <h3 class="card-title">Mes badges</h3>
                <a href="/eleve/badges.php" class="text-small">Voir tout →</a>
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
                    <p class="text-muted text-center">Pas encore de badges. Continue tes exercices !</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="card mt-lg">
        <div class="card-header">
            <h3 class="card-title">Mes dernières sessions</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($historique)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Domaine</th>
                            <th>Questions</th>
                            <th>Réussite</th>
                            <th>Points</th>
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
                                <td style="color: <?= $points >= 0 ? 'var(--success)' : 'var(--error)' ?>">
                                    <?= $points >= 0 ? '+' : '' ?><?= $points ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted text-center">Aucune session pour le moment. Lance-toi !</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
