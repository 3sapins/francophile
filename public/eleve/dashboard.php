<?php
$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/../../src/includes/init.php';
requireEleve();

$eleve = Eleve::findById(Session::getUserId());
if (!$eleve) { Session::destroy(); header('Location: /login.php'); exit; }

$stats = $eleve->getStatistiques();
$progressions = $eleve->getProgressions();
$badges = $eleve->getBadges();
$historique = $eleve->getHistorique(5);
$hasData = $stats['total_exercices'] > 0;

// Parcours progression
$db = Database::getInstance();
$parcoursRecents = [];
try {
    $stmt = $db->prepare('
        SELECT p.nom, p.icone, p.couleur, pp.niveau_max_atteint, pp.termine,
               (SELECT COUNT(*) FROM parcours_niveaux WHERE parcours_id = p.id) as total_niveaux
        FROM parcours_progression pp
        JOIN parcours p ON pp.parcours_id = p.id
        WHERE pp.eleve_id = ? AND pp.niveau_max_atteint > 0
        ORDER BY pp.date_derniere_activite DESC LIMIT 3
    ');
    $stmt->execute([$eleve->getId()]);
    $parcoursRecents = $stmt->fetchAll();
} catch (Exception $e) { /* table might not exist yet */ }

require_once __DIR__ . '/../../src/includes/header.php';
?>

<div class="container">
    <div class="welcome-banner fade-in">
        <h1>Salut <?= htmlspecialchars($eleve->getPseudo()) ?> ! 👋</h1>
        <p><?php if ($hasData): ?>Tu as déjà <?= number_format($stats['points_totaux'], 0, ',', ' ') ?> points. Continue comme ça !<?php else: ?>Bienvenue sur Francophile ! Lance-toi avec ton premier exercice 🚀<?php endif; ?></p>
    </div>

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
            <div class="quick-action-desc"><?= count($badges) ?> badge<?= count($badges) > 1 ? 's' : '' ?></div>
        </a>
    </div>

    <?php if ($hasData): ?>
    <div class="stats-grid mb-xl fade-in">
        <div class="stat-card"><div class="stat-icon">⭐</div><div class="stat-value"><?= number_format($stats['points_totaux'], 0, ',', ' ') ?></div><div class="stat-label">Points totaux</div></div>
        <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-value"><?= $stats['total_correct'] ?></div><div class="stat-label">Bonnes réponses</div></div>
        <div class="stat-card"><div class="stat-icon">🎯</div><div class="stat-value"><?= $stats['taux_reussite'] ?>%</div><div class="stat-label">Taux de réussite</div></div>
        <div class="stat-card"><div class="stat-icon">🏆</div><div class="stat-value"><?= $stats['nb_badges'] ?></div><div class="stat-label">Badges obtenus</div></div>
    </div>
    <?php endif; ?>

    <div class="grid grid-2 gap-lg">
        <div class="card fade-in">
            <div class="card-header flex flex-between">
                <h3 class="card-title">🗺️ Mes parcours</h3>
                <a href="/eleve/parcours.php" class="text-small" style="font-weight: 600;">Voir tout →</a>
            </div>
            <div class="card-body">
                <?php if (!empty($parcoursRecents)): ?>
                    <?php foreach ($parcoursRecents as $pc): ?>
                        <?php $pourcent = $pc['total_niveaux'] > 0 ? round(($pc['niveau_max_atteint'] / $pc['total_niveaux']) * 100) : 0; ?>
                        <div class="mb-md">
                            <div class="flex flex-between mb-sm">
                                <span class="text-small"><strong><?= $pc['icone'] ?> <?= htmlspecialchars($pc['nom']) ?></strong></span>
                                <span class="text-small" style="color: <?= htmlspecialchars($pc['couleur']) ?>; font-weight: 600;"><?= $pc['termine'] ? '✓ Terminé' : $pc['niveau_max_atteint'] . '/' . $pc['total_niveaux'] ?></span>
                            </div>
                            <div class="progress" style="height: 6px;"><div class="progress-bar" style="width: <?= $pourcent ?>%; background: <?= htmlspecialchars($pc['couleur']) ?>;"></div></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center" style="padding: 2rem 1rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🗺️</div>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">Découvre les parcours !</p>
                        <p class="text-muted text-small mb-lg">Des exercices thématiques avec progression guidée.</p>
                        <a href="/eleve/parcours.php" class="btn btn-primary">Explorer</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card fade-in">
            <div class="card-header flex flex-between">
                <h3 class="card-title">📈 Ma progression</h3>
                <a href="/eleve/progression.php" class="text-small" style="font-weight: 600;">Détails →</a>
            </div>
            <div class="card-body">
                <?php 
                $hasProgress = false;
                foreach ($progressions as $prog):
                    if ($prog['points_totaux'] == 0) continue;
                    $hasProgress = true;
                    $domaine = DOMAINES[$prog['domaine']] ?? $prog['domaine'];
                    $pointsProchain = $prog['points_prochain_niveau'] ?? ($prog['points_totaux'] + 100);
                    $pointsActuel = $prog['points_niveau_actuel'] ?? 0;
                    $pct = $pointsProchain > $pointsActuel ? (($prog['points_totaux'] - $pointsActuel) / ($pointsProchain - $pointsActuel)) * 100 : 100;
                    $pct = min(100, max(0, $pct));
                ?>
                    <div class="mb-md">
                        <div class="flex flex-between mb-sm">
                            <span class="text-small"><strong><?= htmlspecialchars($domaine) ?></strong></span>
                            <span class="text-small text-muted">Niv. <?= $prog['niveau_actuel'] ?> (<?= number_format($prog['points_totaux'], 0, ',', ' ') ?> pts)</span>
                        </div>
                        <div class="progress"><div class="progress-bar" style="width: <?= $pct ?>%"></div></div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$hasProgress): ?>
                    <div class="text-center" style="padding: 2rem 1rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🎮</div>
                        <p style="font-weight: 600; margin-bottom: 0.5rem;">Prêt à commencer ?</p>
                        <p class="text-muted text-small mb-lg">Fais ton premier exercice !</p>
                        <a href="/eleve/parcours.php" class="btn btn-primary">Commencer</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($historique)): ?>
    <div class="card mt-lg fade-in">
        <div class="card-header"><h3 class="card-title">📋 Dernières sessions</h3></div>
        <div class="card-body">
            <table class="table">
                <thead><tr><th>Date</th><th>Domaine</th><th>Questions</th><th>Réussite</th><th class="text-right">Points</th></tr></thead>
                <tbody>
                    <?php foreach ($historique as $s): ?>
                        <?php $taux = $s['nombre_questions'] > 0 ? round(($s['nombre_correct'] / $s['nombre_questions']) * 100) : 0; $pts = ($s['points_gagnes'] ?? 0) - ($s['points_perdus'] ?? 0); ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($s['date_debut'])) ?></td>
                            <td><?= htmlspecialchars(DOMAINES[$s['domaine']] ?? $s['domaine']) ?></td>
                            <td><?= $s['nombre_correct'] ?>/<?= $s['nombre_questions'] ?></td>
                            <td><span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>"><?= $taux ?>%</span></td>
                            <td class="text-right" style="color: <?= $pts >= 0 ? 'var(--success)' : 'var(--error)' ?>; font-weight: 700;"><?= $pts >= 0 ? '+' : '' ?><?= $pts ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
