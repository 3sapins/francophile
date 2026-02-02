<?php
$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/../../src/includes/header.php';
Session::requireEnseignant();

$enseignant = Enseignant::findById(Session::getUserId());
$stats = $enseignant->getStatistiques();
$classes = $enseignant->getClasses();
?>

<div class="container">
    <div class="flex flex-between mb-xl">
        <div>
            <h1 class="mb-sm">Bonjour <?= htmlspecialchars($enseignant->getPrenom()) ?> !</h1>
            <p class="text-muted"><?= htmlspecialchars($enseignant->getEtablissement() ?? 'Enseignant') ?></p>
        </div>
        <a href="/enseignant/classes.php?action=new" class="btn btn-primary">+ Créer une classe</a>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid mb-xl">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?= $stats['nb_classes'] ?></div>
            <div class="stat-label">Classes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎓</div>
            <div class="stat-value"><?= $stats['nb_eleves'] ?></div>
            <div class="stat-label">Élèves</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-value"><?= number_format($stats['nb_exercices'], 0, ',', ' ') ?></div>
            <div class="stat-label">Exercices réalisés</div>
        </div>
    </div>

    <!-- Liste des classes -->
    <div class="card">
        <div class="card-header flex flex-between">
            <h3 class="card-title">Mes classes</h3>
            <a href="/enseignant/classes.php" class="text-small">Gérer les classes →</a>
        </div>
        <div class="card-body">
            <?php if (!empty($classes)): ?>
                <div class="grid grid-3 gap-md">
                    <?php foreach ($classes as $classe): ?>
                        <?php
                        $db = Database::getInstance();
                        $stmt = $db->prepare('SELECT COUNT(*) FROM eleves WHERE classe_id = ? AND actif = 1');
                        $stmt->execute([$classe['id']]);
                        $nbEleves = $stmt->fetchColumn();
                        ?>
                        <div class="card" style="border-left: 4px solid var(--primary);">
                            <div class="card-body">
                                <h4 class="mb-sm"><?= htmlspecialchars($classe['nom']) ?></h4>
                                <p class="text-muted text-small mb-md">
                                    <?= $classe['annee_scolaire'] ?>e année • <?= $nbEleves ?> élève<?= $nbEleves > 1 ? 's' : '' ?>
                                </p>
                                <div class="flex flex-between">
                                    <span class="badge badge-primary" style="cursor: pointer;" 
                                          onclick="ClasseManager.copyCode('<?= $classe['code_classe'] ?>')"
                                          title="Cliquer pour copier">
                                        <?= $classe['code_classe'] ?>
                                    </span>
                                    <a href="/enseignant/classes.php?id=<?= $classe['id'] ?>" class="text-small">Voir →</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p class="text-muted mb-md">Vous n'avez pas encore de classe.</p>
                    <a href="/enseignant/classes.php?action=new" class="btn btn-primary">Créer ma première classe</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-2 gap-lg mt-lg">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-md">📊 Résultats récents</h4>
                <p class="text-muted text-small mb-md">Consultez les performances de vos élèves.</p>
                <a href="/enseignant/resultats.php" class="btn btn-outline btn-block">Voir les résultats</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="mb-md">✏️ Exercices personnalisés</h4>
                <p class="text-muted text-small mb-md">Créez des exercices adaptés à vos élèves.</p>
                <a href="/enseignant/exercices.php" class="btn btn-outline btn-block">Gérer les exercices</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
