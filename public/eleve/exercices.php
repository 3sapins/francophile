<?php
$pageTitle = 'Exercices';
require_once __DIR__ . '/../../src/includes/init.php';
requireEleve();

$eleve = Eleve::findById(Session::getUserId());
if (!$eleve) { Session::destroy(); header('Location: /login.php'); exit; }
$annee = $eleve->getAnneeScolaire();
$classeId = $eleve->getClasseId();
$db = Database::getInstance();

$exerciceManager = new Exercice();

// ========================================
// Vérifier les sélections de l'enseignant
// ========================================

// --- Temps de conjugaison ---
$stmt = $db->prepare('SELECT temps FROM selections_conj_classe WHERE classe_id = ? AND actif = 1');
$stmt->execute([$classeId]);
$tempsSelection = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($tempsSelection)) {
    $tempsDisponibles = [];
    foreach ($tempsSelection as $code) {
        if (isset(TEMPS_CONJUGAISON[$code])) {
            $tempsDisponibles[$code] = TEMPS_CONJUGAISON[$code];
        }
    }
} else {
    $tempsDisponibles = TEMPS_CONJUGAISON;
}

// --- Verbes ---
$stmt = $db->prepare('SELECT verbe_id FROM selections_verbes_classe WHERE classe_id = ? AND actif = 1');
$stmt->execute([$classeId]);
$verbesSelection = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($verbesSelection)) {
    $placeholders = implode(',', array_fill(0, count($verbesSelection), '?'));
    $stmt = $db->prepare("SELECT * FROM verbes WHERE id IN ($placeholders) AND actif = 1 ORDER BY groupe, infinitif");
    $stmt->execute($verbesSelection);
    $verbes = $stmt->fetchAll();
} else {
    $verbes = $exerciceManager->getVerbes($annee);
}

// --- Catégories orthographe ---
$stmt = $db->prepare('SELECT categorie_id FROM selections_ortho_classe WHERE classe_id = ? AND actif = 1');
$stmt->execute([$classeId]);
$orthoSelection = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($orthoSelection)) {
    $placeholders = implode(',', array_fill(0, count($orthoSelection), '?'));
    $stmt = $db->prepare("SELECT * FROM categories_orthographe WHERE id IN ($placeholders) ORDER BY type, nom");
    $stmt->execute($orthoSelection);
    $categoriesOrtho = $stmt->fetchAll();
} else {
    $categoriesOrtho = $exerciceManager->getCategoriesOrthographe();
}

require_once __DIR__ . '/../../src/includes/header.php';
?>

<div class="container">
    <h1 class="mb-lg">Choisir des exercices</h1>

    <div class="grid grid-2 gap-lg">
        <!-- Conjugaison -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white;">
                <h3 class="card-title" style="color: white;">✏️ Conjugaison</h3>
            </div>
            <div class="card-body">
                <form action="/eleve/session.php" method="POST" id="form-conjugaison">
                    <input type="hidden" name="domaine" value="conjugaison">
                    
                    <!-- Mode -->
                    <div class="form-group">
                        <label class="form-label">Mode d'exercice</label>
                        <div class="flex gap-md">
                            <label class="form-checkbox">
                                <input type="radio" name="mode" value="pronoms" checked>
                                <span>Pronoms seuls</span>
                            </label>
                            <label class="form-checkbox">
                                <input type="radio" name="mode" value="phrases">
                                <span>Phrases contextuelles</span>
                            </label>
                        </div>
                    </div>

                    <!-- Temps -->
                    <div class="form-group">
                        <label class="form-label">Temps <span class="text-muted">(sélectionne au moins 1)</span></label>
                        <div class="grid grid-2 gap-sm">
                            <?php foreach ($tempsDisponibles as $code => $nom): ?>
                                <label class="form-checkbox">
                                    <input type="checkbox" name="temps[]" value="<?= $code ?>" class="exercice-checkbox">
                                    <span class="text-small"><?= htmlspecialchars($nom) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Verbes (liste simplifiée) -->
                    <div class="form-group">
                        <label class="form-label">
                            Verbes 
                            <button type="button" class="btn btn-small btn-outline select-all" data-group="verbes">Tout sélectionner</button>
                        </label>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: var(--spacing-sm);">
                            <div class="grid grid-3 gap-sm">
                                <?php foreach (array_slice($verbes, 0, 30) as $verbe): ?>
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="verbes[]" value="<?= $verbe['id'] ?>" class="exercice-checkbox">
                                        <span class="text-small"><?= htmlspecialchars($verbe['infinitif']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Niveau et nombre -->
                    <div class="grid grid-2 gap-md">
                        <div class="form-group">
                            <label class="form-label">Niveau</label>
                            <select name="niveau" class="form-select">
                                <option value="1">Niveau 1 (facile)</option>
                                <option value="2">Niveau 2 (moyen)</option>
                                <option value="3">Niveau 3 (difficile)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre de questions</label>
                            <select name="nombre" class="form-select">
                                <option value="5">5 questions</option>
                                <option value="10" selected>10 questions</option>
                                <option value="15">15 questions</option>
                                <option value="20">20 questions</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Commencer la conjugaison</button>
                </form>
            </div>
        </div>

        <!-- Orthographe -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, var(--secondary), var(--secondary-dark)); color: white;">
                <h3 class="card-title" style="color: white;">📝 Orthographe</h3>
            </div>
            <div class="card-body">
                <form action="/eleve/session.php" method="POST" id="form-orthographe">
                    <input type="hidden" name="domaine" value="orthographe">

                    <!-- Catégories -->
                    <div class="form-group">
                        <label class="form-label">Catégories <span class="text-muted">(sélectionne au moins 1)</span></label>
                        
                        <p class="text-small text-muted mb-sm"><strong>Homophones grammaticaux</strong></p>
                        <div class="grid grid-2 gap-sm mb-md">
                            <?php foreach ($categoriesOrtho as $cat): ?>
                                <?php if ($cat['type'] === 'homophones_gram'): ?>
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" class="exercice-checkbox">
                                        <span class="text-small"><?= htmlspecialchars($cat['nom']) ?></span>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <p class="text-small text-muted mb-sm"><strong>Homophones lexicaux</strong></p>
                        <div class="grid grid-2 gap-sm mb-md">
                            <?php foreach ($categoriesOrtho as $cat): ?>
                                <?php if ($cat['type'] === 'homophones_lex'): ?>
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" class="exercice-checkbox">
                                        <span class="text-small"><?= htmlspecialchars($cat['nom']) ?></span>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <p class="text-small text-muted mb-sm"><strong>Accords</strong></p>
                        <div class="grid grid-2 gap-sm">
                            <?php foreach ($categoriesOrtho as $cat): ?>
                                <?php if (strpos($cat['type'], 'accords') === 0): ?>
                                    <label class="form-checkbox">
                                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" class="exercice-checkbox">
                                        <span class="text-small"><?= htmlspecialchars($cat['nom']) ?></span>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Niveau et nombre -->
                    <div class="grid grid-2 gap-md">
                        <div class="form-group">
                            <label class="form-label">Niveau</label>
                            <select name="niveau" class="form-select">
                                <option value="1">Niveau 1 (facile)</option>
                                <option value="2">Niveau 2 (moyen)</option>
                                <option value="3">Niveau 3 (difficile)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre de questions</label>
                            <select name="nombre" class="form-select">
                                <option value="5">5 questions</option>
                                <option value="10" selected>10 questions</option>
                                <option value="15">15 questions</option>
                                <option value="20">20 questions</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-secondary btn-block">Commencer l'orthographe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validation des formulaires
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const domaine = this.querySelector('input[name="domaine"]').value;
        
        if (domaine === 'conjugaison') {
            const temps = this.querySelectorAll('input[name="temps[]"]:checked');
            const verbes = this.querySelectorAll('input[name="verbes[]"]:checked');
            
            if (temps.length === 0) {
                e.preventDefault();
                alert('Sélectionne au moins un temps.');
                return;
            }
            if (verbes.length === 0) {
                e.preventDefault();
                alert('Sélectionne au moins un verbe.');
                return;
            }
        } else if (domaine === 'orthographe') {
            const categories = this.querySelectorAll('input[name="categories[]"]:checked');
            if (categories.length === 0) {
                e.preventDefault();
                alert('Sélectionne au moins une catégorie.');
                return;
            }
        }
    });
});

// Tout sélectionner
document.querySelectorAll('.select-all').forEach(btn => {
    btn.addEventListener('click', function() {
        const group = this.dataset.group;
        const checkboxes = document.querySelectorAll(`input[name="${group}[]"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        this.textContent = allChecked ? 'Tout sélectionner' : 'Tout désélectionner';
    });
});
</script>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
