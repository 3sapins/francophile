<?php
$pageTitle = 'Parcours thématiques';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEleve();

$eleve = Eleve::findById(Session::getUserId());
$db = Database::getInstance();

// Récupérer tous les parcours actifs
$stmt = $db->query('SELECT * FROM parcours WHERE actif = 1 ORDER BY ordre_affichage');
$parcours = $stmt->fetchAll();

// Récupérer la progression de l'élève pour chaque parcours
$progressions = [];
$stmt = $db->prepare('SELECT * FROM parcours_progression WHERE eleve_id = ?');
$stmt->execute([$eleve->getId()]);
foreach ($stmt->fetchAll() as $p) {
    $progressions[$p['parcours_id']] = $p;
}

// Compter les niveaux par parcours
$stmt = $db->query('SELECT parcours_id, COUNT(*) as nb_niveaux FROM parcours_niveaux GROUP BY parcours_id');
$nbNiveaux = [];
foreach ($stmt->fetchAll() as $row) {
    $nbNiveaux[$row['parcours_id']] = (int)$row['nb_niveaux'];
}

// Grouper par domaine
$parDomaine = [];
foreach ($parcours as $p) {
    $parDomaine[$p['domaine']][] = $p;
}
?>

<div class="container">
    <div class="welcome-banner fade-in" style="background: linear-gradient(135deg, #4f46e5, #7c3aed, #06b6d4);">
        <h1>Parcours thématiques 🗺️</h1>
        <p>Progresse étape par étape. Chaque niveau doit être réussi à 100% pour débloquer le suivant !</p>
    </div>

    <?php foreach ($parDomaine as $domaine => $listeParcours): ?>
        <h2 class="mb-md mt-lg" style="font-size: 1.25rem; color: var(--gray-700);">
            <?= htmlspecialchars(DOMAINES[$domaine] ?? ucfirst($domaine)) ?>
        </h2>
        
        <div class="parcours-grid mb-xl">
            <?php foreach ($listeParcours as $p): 
                $prog = $progressions[$p['id']] ?? null;
                $total = $nbNiveaux[$p['id']] ?? 0;
                $niveauMax = $prog['niveau_max_atteint'] ?? 0;
                $termine = $prog['termine'] ?? false;
                $pourcent = $total > 0 ? round(($niveauMax / $total) * 100) : 0;
            ?>
                <a href="/eleve/parcours_detail.php?id=<?= $p['id'] ?>" class="parcours-card <?= $termine ? 'termine' : '' ?>">
                    <div class="parcours-card-header" style="background: <?= htmlspecialchars($p['couleur']) ?>;">
                        <span class="parcours-icone"><?= $p['icone'] ?></span>
                        <?php if ($termine): ?>
                            <span class="parcours-badge-complete">✓ Terminé</span>
                        <?php endif; ?>
                    </div>
                    <div class="parcours-card-body">
                        <h3><?= htmlspecialchars($p['nom']) ?></h3>
                        <p class="text-small text-muted"><?= htmlspecialchars($p['description']) ?></p>
                        <div class="parcours-meta">
                            <span class="text-small"><?= $total ?> niveau<?= $total > 1 ? 'x' : '' ?></span>
                            <span class="text-small font-bold" style="color: <?= htmlspecialchars($p['couleur']) ?>;">
                                <?= $niveauMax ?>/<?= $total ?>
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" style="width: <?= $pourcent ?>%; background: <?= htmlspecialchars($p['couleur']) ?>;"></div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<style>
.parcours-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: var(--spacing-md); }
.parcours-card { display: block; border-radius: var(--radius-xl); overflow: hidden; background: white; box-shadow: var(--shadow); transition: all 0.2s; text-decoration: none; color: inherit; border: 2px solid transparent; }
.parcours-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.parcours-card.termine { border-color: var(--success); }
.parcours-card-header { padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; }
.parcours-icone { font-size: 2rem; }
.parcours-badge-complete { background: rgba(255,255,255,0.9); color: var(--success); font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); }
.parcours-card-body { padding: 1rem 1.25rem 1.25rem; }
.parcours-card-body h3 { font-size: 1rem; font-weight: 700; margin-bottom: 0.375rem; color: var(--gray-900); }
.parcours-card-body p { margin-bottom: 0.75rem; line-height: 1.4; }
.parcours-meta { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
@media (max-width: 640px) { .parcours-grid { grid-template-columns: 1fr; } }
</style>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
