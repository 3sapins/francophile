<?php
$pageTitle = 'Parcours';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEleve();

$eleve = Eleve::findById(Session::getUserId());
$db = Database::getInstance();
$parcoursId = (int)($_GET['id'] ?? 0);

if (!$parcoursId) { header('Location: /eleve/parcours.php'); exit; }

// Récupérer le parcours
$stmt = $db->prepare('SELECT * FROM parcours WHERE id = ? AND actif = 1');
$stmt->execute([$parcoursId]);
$parcours = $stmt->fetch();
if (!$parcours) { header('Location: /eleve/parcours.php'); exit; }

// Récupérer les niveaux
$stmt = $db->prepare('SELECT * FROM parcours_niveaux WHERE parcours_id = ? ORDER BY numero');
$stmt->execute([$parcoursId]);
$niveaux = $stmt->fetchAll();

// Récupérer la progression
$stmt = $db->prepare('SELECT * FROM parcours_progression WHERE eleve_id = ? AND parcours_id = ?');
$stmt->execute([$eleve->getId(), $parcoursId]);
$progression = $stmt->fetch();
$niveauMaxAtteint = $progression['niveau_max_atteint'] ?? 0;

// Récupérer les tentatives par niveau
$tentativesParNiveau = [];
$stmt = $db->prepare('
    SELECT pt.niveau_id, COUNT(*) as nb_tentatives, MAX(pt.reussi) as meilleur,
           MAX(pt.score) as meilleur_score, MAX(pt.total) as total
    FROM parcours_tentatives pt
    JOIN parcours_niveaux pn ON pt.niveau_id = pn.id
    WHERE pt.eleve_id = ? AND pn.parcours_id = ?
    GROUP BY pt.niveau_id
');
$stmt->execute([$eleve->getId(), $parcoursId]);
foreach ($stmt->fetchAll() as $t) {
    $tentativesParNiveau[$t['niveau_id']] = $t;
}

$pageTitle = $parcours['nom'];
?>

<div class="container">
    <div class="mb-lg">
        <a href="/eleve/parcours.php" class="btn btn-outline btn-small">← Tous les parcours</a>
    </div>

    <div class="parcours-hero fade-in" style="--parcours-color: <?= htmlspecialchars($parcours['couleur']) ?>;">
        <div class="parcours-hero-icon"><?= $parcours['icone'] ?></div>
        <div>
            <h1><?= htmlspecialchars($parcours['nom']) ?></h1>
            <p><?= htmlspecialchars($parcours['description']) ?></p>
            <div class="parcours-hero-progress">
                <span><?= $niveauMaxAtteint ?>/<?= count($niveaux) ?> niveaux complétés</span>
                <div class="progress" style="width: 200px; height: 8px;">
                    <div class="progress-bar" style="width: <?= count($niveaux) > 0 ? round(($niveauMaxAtteint / count($niveaux)) * 100) : 0 ?>%; background: white;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="niveaux-timeline">
        <?php foreach ($niveaux as $i => $niveau):
            $numero = $niveau['numero'];
            $estDebloque = ($numero <= $niveauMaxAtteint + 1);
            $estComplete = ($numero <= $niveauMaxAtteint);
            $tentative = $tentativesParNiveau[$niveau['id']] ?? null;
            $nbTentatives = $tentative['nb_tentatives'] ?? 0;
            $statusClass = $estComplete ? 'complete' : ($estDebloque ? 'unlocked' : 'locked');
        ?>
            <div class="niveau-node <?= $statusClass ?>">
                <div class="niveau-connector <?= $estComplete ? 'complete' : '' ?>"></div>
                <div class="niveau-circle">
                    <?php if ($estComplete): ?>
                        <span>✓</span>
                    <?php elseif ($estDebloque): ?>
                        <span><?= $numero ?></span>
                    <?php else: ?>
                        <span>🔒</span>
                    <?php endif; ?>
                </div>
                <div class="niveau-card">
                    <div class="niveau-card-header">
                        <h3><?= htmlspecialchars($niveau['titre']) ?></h3>
                        <?php if ($estComplete): ?>
                            <span class="badge badge-success">✓ Réussi</span>
                        <?php elseif (!$estDebloque): ?>
                            <span class="badge badge-outline">🔒 Verrouillé</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-small text-muted"><?= htmlspecialchars($niveau['description']) ?></p>
                    <div class="niveau-info">
                        <span class="text-small"><?= $niveau['nb_questions'] ?> questions</span>
                        <span class="text-small">🎯 100% requis</span>
                        <span class="text-small">⭐ <?= $niveau['points_recompense'] ?> pts</span>
                    </div>
                    <?php if ($nbTentatives > 0): ?>
                        <div class="text-small text-muted mt-sm">
                            <?= $nbTentatives ?> tentative<?= $nbTentatives > 1 ? 's' : '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($estDebloque): ?>
                        <a href="/eleve/parcours_jouer.php?niveau=<?= $niveau['id'] ?>" 
                           class="btn btn-small <?= $estComplete ? 'btn-outline' : 'btn-primary' ?> mt-sm" style="<?= !$estComplete ? 'background:' . htmlspecialchars($parcours['couleur']) : '' ?>">
                            <?= $estComplete ? 'Rejouer' : 'Commencer' ?> →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($niveauMaxAtteint >= count($niveaux) && count($niveaux) > 0): ?>
            <div class="niveau-node complete">
                <div class="niveau-connector complete"></div>
                <div class="niveau-circle" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); font-size: 1.5rem;">
                    <span>🏆</span>
                </div>
                <div class="niveau-card" style="border-color: #fbbf24; background: linear-gradient(135deg, #fef3c7, #fde68a);">
                    <h3 style="color: #92400e;">Parcours terminé !</h3>
                    <p class="text-small" style="color: #92400e;">Bravo, tu as complété tous les niveaux de ce parcours !</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.parcours-hero { display: flex; align-items: center; gap: 1.5rem; padding: 2rem; border-radius: var(--radius-2xl); background: linear-gradient(135deg, var(--parcours-color), color-mix(in srgb, var(--parcours-color) 70%, #000)); color: white; margin-bottom: 2rem; }
.parcours-hero-icon { font-size: 3rem; background: rgba(255,255,255,0.15); padding: 1rem; border-radius: var(--radius-xl); }
.parcours-hero h1 { color: white; font-size: 1.5rem; margin-bottom: 0.375rem; }
.parcours-hero p { color: rgba(255,255,255,0.85); margin-bottom: 0.75rem; }
.parcours-hero-progress { display: flex; align-items: center; gap: 1rem; font-size: 0.875rem; }
.parcours-hero-progress .progress { background: rgba(255,255,255,0.25); }

.niveaux-timeline { position: relative; padding-left: 3rem; max-width: 700px; }
.niveau-node { position: relative; padding-bottom: 1.5rem; }
.niveau-node:last-child { padding-bottom: 0; }
.niveau-connector { position: absolute; left: -2.05rem; top: 2.5rem; bottom: 0; width: 3px; background: var(--gray-200); }
.niveau-connector.complete { background: var(--success); }
.niveau-node:last-child .niveau-connector { display: none; }
.niveau-circle { position: absolute; left: -3rem; top: 0.5rem; width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; z-index: 1; border: 3px solid var(--gray-300); background: white; color: var(--gray-400); }
.niveau-node.complete .niveau-circle { background: var(--success); border-color: var(--success); color: white; }
.niveau-node.unlocked .niveau-circle { border-color: var(--primary); color: var(--primary); animation: pulse-node 2s infinite; }
.niveau-node.locked .niveau-circle { background: var(--gray-100); }
@keyframes pulse-node { 0%,100% { box-shadow: 0 0 0 0 rgba(79,70,229,0.3); } 50% { box-shadow: 0 0 0 8px rgba(79,70,229,0); } }

.niveau-card { background: white; border: 2px solid var(--gray-200); border-radius: var(--radius-lg); padding: 1rem 1.25rem; transition: all 0.2s; }
.niveau-node.complete .niveau-card { border-color: var(--success); background: #f0fdf4; }
.niveau-node.unlocked .niveau-card { border-color: var(--primary); }
.niveau-node.locked .niveau-card { opacity: 0.6; }
.niveau-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem; }
.niveau-card h3 { font-size: 1rem; margin: 0; }
.niveau-info { display: flex; gap: 1rem; margin-top: 0.5rem; color: var(--gray-500); }

@media (max-width: 640px) {
    .parcours-hero { flex-direction: column; text-align: center; }
    .niveaux-timeline { padding-left: 2.5rem; }
    .niveau-circle { left: -2.5rem; width: 2rem; height: 2rem; font-size: 0.75rem; }
    .niveau-info { flex-wrap: wrap; gap: 0.5rem; }
}
</style>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
