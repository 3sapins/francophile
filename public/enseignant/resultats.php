<?php
$pageTitle = 'Résultats';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEnseignant();

$enseignant = Enseignant::findById(Session::getUserId());
$db = Database::getInstance();

$classeId = (int)($_GET['classe'] ?? 0);
$eleveId = (int)($_GET['eleve'] ?? 0);
$periode = $_GET['periode'] ?? '30';

// Récupérer les classes de l'enseignant
$classes = $enseignant->getClasses();

// Si une classe est sélectionnée, récupérer ses élèves
$eleves = [];
$classeSelectionnee = null;
if ($classeId > 0) {
    $stmt = $db->prepare('SELECT * FROM classes WHERE id = ? AND enseignant_id = ?');
    $stmt->execute([$classeId, $enseignant->getId()]);
    $classeSelectionnee = $stmt->fetch();
    
    if ($classeSelectionnee) {
        $stmt = $db->prepare('
            SELECT e.*, 
                   (SELECT SUM(points_totaux) FROM progression_eleves WHERE eleve_id = e.id) as points_totaux,
                   (SELECT COUNT(*) FROM sessions_exercices WHERE eleve_id = e.id) as nb_sessions,
                   (SELECT SUM(nombre_correct) FROM sessions_exercices WHERE eleve_id = e.id) as total_correct,
                   (SELECT SUM(nombre_questions) FROM sessions_exercices WHERE eleve_id = e.id) as total_questions
            FROM eleves e 
            WHERE e.classe_id = ? AND e.actif = 1 
            ORDER BY points_totaux DESC
        ');
        $stmt->execute([$classeId]);
        $eleves = $stmt->fetchAll();
    }
}

// Si un élève est sélectionné, récupérer ses détails
$eleveDetail = null;
$sessionsEleve = [];
$progressionsEleve = [];
$badgesEleve = [];
if ($eleveId > 0) {
    $eleveObj = Eleve::findById($eleveId);
    if ($eleveObj && $eleveObj->getClasseId() == $classeId) {
        $eleveDetail = $eleveObj;
        $progressionsEleve = $eleveObj->getProgressions();
        $badgesEleve = $eleveObj->getBadges();
        
        // Sessions avec filtre période
        $dateLimit = match($periode) {
            '7' => 'DATE_SUB(NOW(), INTERVAL 7 DAY)',
            '30' => 'DATE_SUB(NOW(), INTERVAL 30 DAY)',
            '90' => 'DATE_SUB(NOW(), INTERVAL 90 DAY)',
            default => 'DATE_SUB(NOW(), INTERVAL 1 YEAR)'
        };
        
        $stmt = $db->prepare("
            SELECT se.*, 
                   GROUP_CONCAT(DISTINCT re.question_posee SEPARATOR '|||') as questions
            FROM sessions_exercices se
            LEFT JOIN reponses_exercices re ON se.id = re.session_id
            WHERE se.eleve_id = ? AND se.date_debut >= $dateLimit
            GROUP BY se.id
            ORDER BY se.date_debut DESC
            LIMIT 50
        ");
        $stmt->execute([$eleveId]);
        $sessionsEleve = $stmt->fetchAll();
    }
}

// Statistiques globales de la classe si sélectionnée
$statsClasse = [];
if ($classeSelectionnee && !empty($eleves)) {
    $eleveIds = array_column($eleves, 'id');
    $placeholders = implode(',', array_fill(0, count($eleveIds), '?'));
    
    // Moyenne de réussite
    $stmt = $db->prepare("
        SELECT AVG(nombre_correct * 100.0 / NULLIF(nombre_questions, 0)) as taux_moyen,
               SUM(nombre_questions) as total_questions,
               SUM(nombre_correct) as total_correct,
               COUNT(*) as nb_sessions
        FROM sessions_exercices 
        WHERE eleve_id IN ($placeholders)
    ");
    $stmt->execute($eleveIds);
    $statsClasse = $stmt->fetch();
    
    // Répartition par domaine
    $stmt = $db->prepare("
        SELECT domaine, COUNT(*) as nb, SUM(nombre_correct) as correct, SUM(nombre_questions) as total
        FROM sessions_exercices 
        WHERE eleve_id IN ($placeholders)
        GROUP BY domaine
    ");
    $stmt->execute($eleveIds);
    $statsClasse['par_domaine'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <h1 class="mb-lg">Résultats des élèves</h1>

    <!-- Filtres -->
    <div class="card mb-lg">
        <div class="card-body">
            <form method="GET" class="flex gap-md" style="flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                    <label class="form-label">Classe</label>
                    <select name="classe" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $classeId == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nom']) ?> (<?= $c['annee_scolaire'] ?>e)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if ($classeId && !empty($eleves)): ?>
                <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                    <label class="form-label">Élève</label>
                    <select name="eleve" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tous les élèves --</option>
                        <?php foreach ($eleves as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $eleveId == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['pseudo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                    <label class="form-label">Période</label>
                    <select name="periode" class="form-select" onchange="this.form.submit()">
                        <option value="7" <?= $periode == '7' ? 'selected' : '' ?>>7 derniers jours</option>
                        <option value="30" <?= $periode == '30' ? 'selected' : '' ?>>30 derniers jours</option>
                        <option value="90" <?= $periode == '90' ? 'selected' : '' ?>>3 derniers mois</option>
                        <option value="365" <?= $periode == '365' ? 'selected' : '' ?>>Cette année</option>
                    </select>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (!$classeId): ?>
        <!-- Pas de classe sélectionnée -->
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted">Sélectionnez une classe pour voir les résultats.</p>
            </div>
        </div>

    <?php elseif ($eleveDetail): ?>
        <!-- Détail d'un élève -->
        <div class="mb-lg">
            <a href="?classe=<?= $classeId ?>" class="text-small">← Retour à la classe</a>
        </div>

        <div class="card mb-lg">
            <div class="card-header">
                <h2><?= htmlspecialchars($eleveDetail->getPseudo()) ?></h2>
                <p class="text-muted">
                    <?= htmlspecialchars($classeSelectionnee['nom']) ?> • 
                    Dernière connexion : <?= $eleveDetail->getDerniereConnexion() ? date('d/m/Y H:i', strtotime($eleveDetail->getDerniereConnexion())) : 'Jamais' ?>
                </p>
            </div>
            <div class="card-body">
                <!-- Statistiques de l'élève -->
                <div class="stats-grid mb-lg">
                    <?php 
                    $statsEleve = $eleveDetail->getStatistiques();
                    ?>
                    <div class="stat-card">
                        <div class="stat-value"><?= number_format($statsEleve['points_totaux'], 0, ',', ' ') ?></div>
                        <div class="stat-label">Points totaux</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $statsEleve['total_exercices'] ?></div>
                        <div class="stat-label">Questions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $statsEleve['taux_reussite'] ?>%</div>
                        <div class="stat-label">Réussite</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $statsEleve['nb_badges'] ?></div>
                        <div class="stat-label">Badges</div>
                    </div>
                </div>

                <!-- Progression par domaine -->
                <h4 class="mb-md">Progression par domaine</h4>
                <div class="grid grid-2 gap-md mb-lg">
                    <?php foreach ($progressionsEleve as $prog): ?>
                        <div style="padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                            <div class="flex flex-between mb-sm">
                                <strong><?= htmlspecialchars(DOMAINES[$prog['domaine']] ?? $prog['domaine']) ?></strong>
                                <span class="badge badge-<?= $prog['niveau_actuel'] >= 4 ? 'gold' : ($prog['niveau_actuel'] >= 2 ? 'silver' : 'bronze') ?>">
                                    Niveau <?= $prog['niveau_actuel'] ?>
                                </span>
                            </div>
                            <p class="text-small text-muted"><?= number_format($prog['points_totaux'], 0, ',', ' ') ?> points</p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sessions récentes -->
                <h4 class="mb-md">Sessions récentes</h4>
                <?php if (!empty($sessionsEleve)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Domaine</th>
                                <th class="text-center">Niveau</th>
                                <th class="text-center">Résultat</th>
                                <th class="text-center">Taux</th>
                                <th class="text-right">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessionsEleve as $s): ?>
                                <?php 
                                $taux = $s['nombre_questions'] > 0 ? round(($s['nombre_correct'] / $s['nombre_questions']) * 100) : 0;
                                $points = ($s['points_gagnes'] ?? 0) - ($s['points_perdus'] ?? 0);
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($s['date_debut'])) ?></td>
                                    <td><?= htmlspecialchars(DOMAINES[$s['domaine']] ?? $s['domaine']) ?></td>
                                    <td class="text-center"><?= $s['niveau_difficulte'] ?></td>
                                    <td class="text-center"><?= $s['nombre_correct'] ?>/<?= $s['nombre_questions'] ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>">
                                            <?= $taux ?>%
                                        </span>
                                    </td>
                                    <td class="text-right" style="color: <?= $points >= 0 ? 'var(--success)' : 'var(--error)' ?>">
                                        <?= $points >= 0 ? '+' : '' ?><?= $points ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Aucune session sur cette période.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- Vue classe -->
        
        <!-- Statistiques de la classe -->
        <?php if (!empty($statsClasse) && $statsClasse['nb_sessions'] > 0): ?>
        <div class="card mb-lg">
            <div class="card-header">
                <h3 class="card-title">Statistiques de la classe : <?= htmlspecialchars($classeSelectionnee['nom']) ?></h3>
            </div>
            <div class="card-body">
                <div class="stats-grid mb-lg">
                    <div class="stat-card">
                        <div class="stat-value"><?= count($eleves) ?></div>
                        <div class="stat-label">Élèves</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= number_format($statsClasse['nb_sessions'], 0, ',', ' ') ?></div>
                        <div class="stat-label">Sessions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= number_format($statsClasse['total_questions'], 0, ',', ' ') ?></div>
                        <div class="stat-label">Questions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= round($statsClasse['taux_moyen'] ?? 0) ?>%</div>
                        <div class="stat-label">Réussite moyenne</div>
                    </div>
                </div>

                <?php if (!empty($statsClasse['par_domaine'])): ?>
                <h4 class="mb-md">Répartition par domaine</h4>
                <div class="grid grid-3 gap-md">
                    <?php foreach ($statsClasse['par_domaine'] as $sd): ?>
                        <?php $taux = $sd['total'] > 0 ? round(($sd['correct'] / $sd['total']) * 100) : 0; ?>
                        <div style="padding: 1rem; background: var(--gray-50); border-radius: 8px;">
                            <strong><?= htmlspecialchars(DOMAINES[$sd['domaine']] ?? $sd['domaine']) ?></strong>
                            <p class="text-small text-muted"><?= $sd['nb'] ?> sessions • <?= $sd['total'] ?> questions</p>
                            <span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>">
                                <?= $taux ?>% réussite
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Classement des élèves -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Classement des élèves</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($eleves)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Élève</th>
                                <th class="text-center">Sessions</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Réussite</th>
                                <th class="text-right">Points</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rang = 0; foreach ($eleves as $e): $rang++; ?>
                                <?php 
                                $taux = $e['total_questions'] > 0 
                                    ? round(($e['total_correct'] / $e['total_questions']) * 100) 
                                    : 0;
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($rang <= 3): ?>
                                            <span style="font-size: 1.25rem;"><?= $rang == 1 ? '🥇' : ($rang == 2 ? '🥈' : '🥉') ?></span>
                                        <?php else: ?>
                                            <?= $rang ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($e['pseudo']) ?></strong>
                                        <?php if ($e['prenom'] || $e['nom']): ?>
                                            <br><span class="text-small text-muted"><?= htmlspecialchars(trim($e['prenom'] . ' ' . $e['nom'])) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $e['nb_sessions'] ?? 0 ?></td>
                                    <td class="text-center"><?= $e['total_questions'] ?? 0 ?></td>
                                    <td class="text-center">
                                        <?php if ($e['total_questions'] > 0): ?>
                                            <span class="badge badge-<?= $taux >= 70 ? 'success' : ($taux >= 50 ? 'warning' : 'error') ?>">
                                                <?= $taux ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <strong><?= number_format($e['points_totaux'] ?? 0, 0, ',', ' ') ?></strong>
                                    </td>
                                    <td>
                                        <a href="?classe=<?= $classeId ?>&eleve=<?= $e['id'] ?>&periode=<?= $periode ?>" 
                                           class="btn btn-small btn-outline">Détails</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-center">Aucun élève dans cette classe.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
