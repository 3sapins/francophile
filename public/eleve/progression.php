<?php
$pageTitle = 'Ma progression';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEleve();

$eleve = Eleve::findById(Session::getUserId());
$db = Database::getInstance();

// Récupérer toutes les progressions
$progressions = $eleve->getProgressions();

// Statistiques globales
$stats = $eleve->getStatistiques();

// Historique complet avec pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$stmt = $db->prepare('SELECT COUNT(*) FROM sessions_exercices WHERE eleve_id = ?');
$stmt->execute([$eleve->getId()]);
$totalSessions = (int) $stmt->fetchColumn();
$totalPages = ceil($totalSessions / $perPage);

$stmt = $db->prepare('
    SELECT * FROM sessions_exercices 
    WHERE eleve_id = ? 
    ORDER BY date_debut DESC 
    LIMIT ? OFFSET ?
');
$stmt->execute([$eleve->getId(), $perPage, $offset]);
$sessions = $stmt->fetchAll();

// Évolution sur les 30 derniers jours
$stmt = $db->prepare('
    SELECT DATE(date_debut) as jour, 
           SUM(nombre_correct) as correct, 
           SUM(nombre_questions) as total,
           SUM(points_gagnes - points_perdus) as points
    FROM sessions_exercices 
    WHERE eleve_id = ? AND date_debut >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(date_debut)
    ORDER BY jour ASC
');
$stmt->execute([$eleve->getId()]);
$evolution = $stmt->fetchAll();

// Statistiques par domaine
$stmt = $db->prepare('
    SELECT domaine, 
           COUNT(*) as nb_sessions,
           SUM(nombre_questions) as total_questions,
           SUM(nombre_correct) as total_correct,
           AVG(nombre_correct * 100.0 / NULLIF(nombre_questions, 0)) as taux_moyen
    FROM sessions_exercices 
    WHERE eleve_id = ?
    GROUP BY domaine
');
$stmt->execute([$eleve->getId()]);
$statsDomaines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="mb-lg">Ma progression</h1>

    <!-- Statistiques générales -->
    <div class="stats-grid mb-xl">
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-value"><?= number_format($stats['points_totaux'], 0, ',', ' ') ?></div>
            <div class="stat-label">Points totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value"><?= number_format($stats['total_correct'], 0, ',', ' ') ?></div>
            <div class="stat-label">Bonnes réponses</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-value"><?= number_format($stats['total_exercices'], 0, ',', ' ') ?></div>
            <div class="stat-label">Questions traitées</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-value"><?= $stats['taux_reussite'] ?>%</div>
            <div class="stat-label">Taux de réussite</div>
        </div>
    </div>

    <div class="grid grid-2 gap-lg mb-xl">
        <!-- Niveaux par domaine -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mes niveaux par domaine</h3>
            </div>
            <div class="card-body">
                <?php foreach ($progressions as $prog): ?>
                    <?php 
                    $domaine = DOMAINES[$prog['domaine']] ?? ucfirst($prog['domaine']);
                    $niveauNom = $prog['nom_niveau'] ?? 'Niveau ' . $prog['niveau_actuel'];
                    $pointsProchain = $prog['points_prochain_niveau'] ?? null;
                    $pointsActuel = $prog['points_niveau_actuel'] ?? 0;
                    
                    if ($pointsProchain && $pointsProchain > $pointsActuel) {
                        $progression = (($prog['points_totaux'] - $pointsActuel) / ($pointsProchain - $pointsActuel)) * 100;
                    } else {
                        $progression = 100;
                    }
                    $progression = min(100, max(0, $progression));
                    ?>
                    <div class="mb-lg">
                        <div class="flex flex-between mb-sm">
                            <div>
                                <strong><?= htmlspecialchars($domaine) ?></strong>
                                <span class="badge badge-<?= $prog['niveau_actuel'] >= 5 ? 'gold' : ($prog['niveau_actuel'] >= 3 ? 'silver' : 'bronze') ?> ml-sm">
                                    <?= htmlspecialchars($niveauNom) ?>
                                </span>
                            </div>
                            <span class="text-muted text-small">
                                <?= number_format($prog['points_totaux'], 0, ',', ' ') ?> pts
                                <?php if ($pointsProchain): ?>
                                    / <?= number_format($pointsProchain, 0, ',', ' ') ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar <?= $progression >= 100 ? 'success' : '' ?>" 
                                 style="width: <?= $progression ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Statistiques par domaine -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Performances par domaine</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($statsDomaines)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Domaine</th>
                                <th class="text-center">Sessions</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Réussite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statsDomaines as $sd): ?>
                                <?php $taux = round($sd['taux_moyen'] ?? 0); ?>
                                <tr>
                                    <td><?= htmlspecialchars(DOMAINES[$sd['domaine']] ?? $sd['domaine']) ?></td>
                                    <td class="text-center"><?= $sd['nb_sessions'] ?></td>
                                    <td class="text-center"><?= $sd['total_questions'] ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>">
                                            <?= $taux ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-center">Pas encore de données. Fais tes premiers exercices !</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Évolution graphique -->
    <?php if (!empty($evolution)): ?>
    <div class="card mb-xl">
        <div class="card-header">
            <h3 class="card-title">Mon évolution (30 derniers jours)</h3>
        </div>
        <div class="card-body">
            <div id="evolution-chart" style="height: 250px; display: flex; align-items: flex-end; gap: 4px; padding: 20px 0;">
                <?php 
                $maxQuestions = max(array_column($evolution, 'total')) ?: 1;
                foreach ($evolution as $jour): 
                    $height = ($jour['total'] / $maxQuestions) * 200;
                    $taux = $jour['total'] > 0 ? round(($jour['correct'] / $jour['total']) * 100) : 0;
                    $color = $taux >= 70 ? 'var(--success)' : ($taux >= 50 ? 'var(--warning)' : 'var(--error)');
                ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 100%; max-width: 30px; height: <?= max(10, $height) ?>px; background: <?= $color ?>; border-radius: 4px 4px 0 0;" 
                             title="<?= date('d/m', strtotime($jour['jour'])) ?> : <?= $jour['correct'] ?>/<?= $jour['total'] ?> (<?= $taux ?>%)"></div>
                        <span class="text-small text-muted" style="font-size: 10px; margin-top: 4px;">
                            <?= date('d', strtotime($jour['jour'])) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center text-small text-muted">
                <span style="display: inline-block; width: 12px; height: 12px; background: var(--success); border-radius: 2px;"></span> ≥70%
                <span style="display: inline-block; width: 12px; height: 12px; background: var(--warning); border-radius: 2px; margin-left: 10px;"></span> 50-69%
                <span style="display: inline-block; width: 12px; height: 12px; background: var(--error); border-radius: 2px; margin-left: 10px;"></span> <50%
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historique des sessions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Historique des sessions</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($sessions)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Domaine</th>
                            <th>Sous-catégorie</th>
                            <th class="text-center">Niveau</th>
                            <th class="text-center">Résultat</th>
                            <th class="text-center">Taux</th>
                            <th class="text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <?php 
                            $taux = $session['nombre_questions'] > 0 
                                ? round(($session['nombre_correct'] / $session['nombre_questions']) * 100) 
                                : 0;
                            $points = ($session['points_gagnes'] ?? 0) - ($session['points_perdus'] ?? 0);
                            ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($session['date_debut'])) ?></td>
                                <td><?= htmlspecialchars(DOMAINES[$session['domaine']] ?? $session['domaine']) ?></td>
                                <td class="text-small text-muted"><?= htmlspecialchars($session['sous_categorie'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge badge-outline"><?= $session['niveau_difficulte'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?= $session['nombre_correct'] ?> / <?= $session['nombre_questions'] ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>">
                                        <?= $taux ?>%
                                    </span>
                                </td>
                                <td class="text-right" style="color: <?= $points >= 0 ? 'var(--success)' : 'var(--error)' ?>; font-weight: 600;">
                                    <?= $points >= 0 ? '+' : '' ?><?= $points ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex flex-center gap-sm mt-lg">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="btn btn-small btn-outline">← Précédent</a>
                        <?php endif; ?>
                        
                        <span class="text-muted">Page <?= $page ?> / <?= $totalPages ?></span>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="btn btn-small btn-outline">Suivant →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted text-center">Aucune session pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
