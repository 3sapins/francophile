<?php
$pageTitle = 'Mes badges';
require_once __DIR__ . '/../../src/includes/init.php';
requireEleve();

$eleve = Eleve::findById(Session::getUserId());
if (!$eleve) { Session::destroy(); header('Location: /login.php'); exit; }
$db = Database::getInstance();
$annee = $eleve->getAnneeScolaire();

// Badges obtenus
$badgesObtenus = $eleve->getBadges();
$badgesObtenuIds = array_column($badgesObtenus, 'id');

// Tous les badges disponibles pour cette année, groupés par domaine
$stmt = $db->prepare('
    SELECT b.*, 
           COALESCE(pb.points_actuels, 0) as points_actuels,
           CASE WHEN be.id IS NOT NULL THEN 1 ELSE 0 END as obtenu,
           be.date_obtention
    FROM badges b
    LEFT JOIN points_badges pb ON b.id = pb.badge_id AND pb.eleve_id = ?
    LEFT JOIN badges_eleves be ON b.id = be.badge_id AND be.eleve_id = ?
    WHERE b.actif = 1 AND (b.annee_cible IS NULL OR b.annee_cible <= ?)
    ORDER BY b.domaine, b.sous_categorie, b.niveau_difficulte
');
$stmt->execute([$eleve->getId(), $eleve->getId(), $annee]);
$tousLesBadges = $stmt->fetchAll();

// Grouper par domaine
$badgesParDomaine = [];
foreach ($tousLesBadges as $badge) {
    $domaine = $badge['domaine'];
    if (!isset($badgesParDomaine[$domaine])) {
        $badgesParDomaine[$domaine] = [];
    }
    $badgesParDomaine[$domaine][] = $badge;
}

// Statistiques badges
$nbObtenus = count($badgesObtenus);
$nbTotal = count($tousLesBadges);
$progression = $nbTotal > 0 ? round(($nbObtenus / $nbTotal) * 100) : 0;

require_once __DIR__ . '/../../src/includes/header.php';
?>

<div class="container">
    <h1 class="mb-lg">Mes badges 🏆</h1>

    <!-- Résumé -->
    <div class="card mb-xl">
        <div class="card-body">
            <div class="flex flex-between" style="align-items: center;">
                <div>
                    <h2 style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                        <?= $nbObtenus ?> <span class="text-muted" style="font-size: 1.5rem;">/ <?= $nbTotal ?></span>
                    </h2>
                    <p class="text-muted">badges débloqués</p>
                </div>
                <div style="width: 200px;">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar success" style="width: <?= $progression ?>%"></div>
                    </div>
                    <p class="text-center text-small text-muted mt-sm"><?= $progression ?>% de complétion</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers badges obtenus -->
    <?php if (!empty($badgesObtenus)): ?>
    <div class="card mb-xl">
        <div class="card-header">
            <h3 class="card-title">🎉 Derniers badges obtenus</h3>
        </div>
        <div class="card-body">
            <div class="flex gap-md" style="flex-wrap: wrap;">
                <?php foreach (array_slice($badgesObtenus, 0, 8) as $badge): ?>
                    <div class="badge-card obtained" style="text-align: center; padding: 1rem; background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 12px; min-width: 120px;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            <?= $badge['niveau_difficulte'] == 3 ? '🥇' : ($badge['niveau_difficulte'] == 2 ? '🥈' : '🥉') ?>
                        </div>
                        <div style="font-weight: 600; font-size: 0.875rem;"><?= htmlspecialchars($badge['nom']) ?></div>
                        <div class="text-small text-muted"><?= date('d/m/Y', strtotime($badge['date_obtention'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Badges par domaine -->
    <?php foreach ($badgesParDomaine as $domaine => $badges): ?>
        <div class="card mb-lg">
            <div class="card-header">
                <h3 class="card-title">
                    <?= match($domaine) {
                        'conjugaison' => '✏️ Conjugaison',
                        'orthographe' => '📝 Orthographe',
                        'grammaire' => '📖 Grammaire',
                        'vocabulaire' => '📚 Vocabulaire',
                        default => ucfirst($domaine)
                    } ?>
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-4 gap-md">
                    <?php foreach ($badges as $badge): ?>
                        <?php 
                        $obtenu = $badge['obtenu'];
                        $progression = $badge['points_requis'] > 0 
                            ? min(100, ($badge['points_actuels'] / $badge['points_requis']) * 100) 
                            : 0;
                        $niveauClass = match($badge['niveau_difficulte']) {
                            '3' => 'gold',
                            '2' => 'silver',
                            default => 'bronze'
                        };
                        ?>
                        <div class="badge-item" style="
                            padding: 1rem;
                            border-radius: 12px;
                            border: 2px solid <?= $obtenu ? 'var(--success)' : 'var(--gray-200)' ?>;
                            background: <?= $obtenu ? 'var(--success-light)' : 'white' ?>;
                            opacity: <?= $obtenu ? '1' : '0.8' ?>;
                            transition: all 0.2s;
                        ">
                            <div class="flex flex-between mb-sm">
                                <span style="font-size: 1.5rem;">
                                    <?= $obtenu ? ($badge['niveau_difficulte'] == 3 ? '🥇' : ($badge['niveau_difficulte'] == 2 ? '🥈' : '🥉')) : '🔒' ?>
                                </span>
                                <span class="badge badge-<?= $niveauClass ?>" style="font-size: 0.7rem;">
                                    Niv. <?= $badge['niveau_difficulte'] ?>
                                </span>
                            </div>
                            <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <?= htmlspecialchars($badge['nom']) ?>
                            </h4>
                            <p class="text-small text-muted mb-sm">
                                <?= htmlspecialchars($badge['description'] ?? '') ?>
                            </p>
                            <?php if (!$obtenu): ?>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="width: <?= $progression ?>%"></div>
                                </div>
                                <p class="text-small text-muted mt-sm">
                                    <?= (int)$badge['points_actuels'] ?> / <?= $badge['points_requis'] ?> pts
                                </p>
                            <?php else: ?>
                                <p class="text-small" style="color: var(--success);">
                                    ✓ Obtenu le <?= date('d/m/Y', strtotime($badge['date_obtention'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($badgesParDomaine)): ?>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted">Aucun badge disponible pour le moment.</p>
                <a href="/eleve/exercices.php" class="btn btn-primary mt-md">Faire des exercices</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.badge-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
