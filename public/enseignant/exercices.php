<?php
$pageTitle = 'Gestion des exercices';
require_once __DIR__ . '/../../src/includes/init.php';
requireEnseignant();
require_once __DIR__ . '/../../src/includes/header.php';

$enseignant = Enseignant::findById(Session::getUserId());
$classes = $enseignant->getClasses();
$db = Database::getInstance();

// Classe sélectionnée (première par défaut)
$classeId = isset($_GET['classe']) ? (int) $_GET['classe'] : ($classes[0]['id'] ?? null);
$classeActive = null;
foreach ($classes as $c) {
    if ($c['id'] === $classeId) {
        $classeActive = $c;
        break;
    }
}

// Récupérer les données d'exercices
$exerciceManager = new Exercice();

// --- CONJUGAISON ---
// Tous les verbes
$verbes = $exerciceManager->getVerbes();

// Compter les phrases par verbe
$stmtPhrases = $db->prepare('SELECT verbe_id, COUNT(*) as nb FROM phrases_conjugaison GROUP BY verbe_id');
$stmtPhrases->execute();
$phrasesParVerbe = [];
foreach ($stmtPhrases->fetchAll() as $row) {
    $phrasesParVerbe[$row['verbe_id']] = $row['nb'];
}

// Sélections actuelles de la classe (verbes)
$selectionsVerbes = [];
if ($classeId) {
    $stmt = $db->prepare('SELECT verbe_id FROM selections_verbes_classe WHERE classe_id = ? AND actif = 1');
    $stmt->execute([$classeId]);
    $selectionsVerbes = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
$hasCustomVerbes = !empty($selectionsVerbes);

// Sélections actuelles de la classe (temps)
$selectionsTemps = [];
if ($classeId) {
    $stmt = $db->prepare('SELECT temps FROM selections_conj_classe WHERE classe_id = ? AND actif = 1');
    $stmt->execute([$classeId]);
    $selectionsTemps = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
$hasCustomTemps = !empty($selectionsTemps);

// --- ORTHOGRAPHE ---
$categoriesOrtho = $exerciceManager->getCategoriesOrthographe();

// Compter les exercices par catégorie
$stmtExos = $db->prepare('SELECT categorie_id, COUNT(*) as nb FROM exercices_orthographe WHERE actif = 1 GROUP BY categorie_id');
$stmtExos->execute();
$exosParCategorie = [];
foreach ($stmtExos->fetchAll() as $row) {
    $exosParCategorie[$row['categorie_id']] = $row['nb'];
}

// Sélections actuelles de la classe (catégories ortho)
$selectionsOrtho = [];
if ($classeId) {
    $stmt = $db->prepare('SELECT categorie_id FROM selections_ortho_classe WHERE classe_id = ? AND actif = 1');
    $stmt->execute([$classeId]);
    $selectionsOrtho = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
$hasCustomOrtho = !empty($selectionsOrtho);
?>

<div class="container">
    <!-- En-tête -->
    <div class="flex flex-between flex-center mb-lg">
        <div>
            <h1 class="mb-sm">📋 Gestion des exercices</h1>
            <p class="text-muted">Choisissez les exercices visibles par vos élèves pour chaque classe.</p>
        </div>
        <a href="/enseignant/dashboard.php" class="btn btn-outline">← Retour</a>
    </div>

    <?php if (empty($classes)): ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                <h3 class="mb-md">Aucune classe créée</h3>
                <p class="text-muted mb-lg">Créez d'abord une classe pour pouvoir gérer les exercices.</p>
                <a href="/enseignant/classes.php?action=new" class="btn btn-primary">Créer une classe</a>
            </div>
        </div>
    <?php else: ?>

    <!-- Sélection de la classe -->
    <div class="card mb-lg">
        <div class="card-body">
            <div class="flex flex-center gap-md">
                <label class="form-label" style="margin: 0; white-space: nowrap;"><strong>Classe :</strong></label>
                <div class="flex gap-sm" style="flex-wrap: wrap;">
                    <?php foreach ($classes as $c): ?>
                        <a href="?classe=<?= $c['id'] ?>" 
                           class="btn <?= $c['id'] === $classeId ? 'btn-primary' : 'btn-outline' ?> btn-small">
                            <?= htmlspecialchars($c['nom']) ?> (<?= $c['annee_scolaire'] ?>e)
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($classeActive): ?>
    
    <!-- Info classe -->
    <div class="alert alert-info mb-lg" style="background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: var(--radius-lg); padding: 1rem 1.5rem;">
        <strong>💡 Fonctionnement :</strong> Par défaut, tous les exercices sont accessibles. 
        Dès que vous faites une sélection personnalisée, seuls les éléments cochés seront visibles pour les élèves de 
        <strong><?= htmlspecialchars($classeActive['nom']) ?></strong>.
    </div>

    <!-- Onglets -->
    <div class="tabs mb-lg">
        <button class="tab-btn active" data-tab="conjugaison" onclick="switchTab('conjugaison')">
            ✏️ Conjugaison
        </button>
        <button class="tab-btn" data-tab="orthographe" onclick="switchTab('orthographe')">
            📝 Orthographe
        </button>
    </div>

    <!-- ========================================= -->
    <!-- TAB CONJUGAISON -->
    <!-- ========================================= -->
    <div id="tab-conjugaison" class="tab-content active">
        
        <!-- Section Temps -->
        <div class="card mb-lg">
            <div class="card-header flex flex-between flex-center">
                <h3 class="card-title">⏱️ Temps de conjugaison</h3>
                <div class="flex gap-sm">
                    <span class="badge <?= $hasCustomTemps ? 'badge-warning' : 'badge-success' ?>" id="badge-temps">
                        <?= $hasCustomTemps ? count($selectionsTemps) . '/' . count(TEMPS_CONJUGAISON) . ' sélectionnés' : 'Tous accessibles' ?>
                    </span>
                    <button class="btn btn-small btn-outline" onclick="toggleAllTemps()">
                        Tout cocher/décocher
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-3 gap-md">
                    <?php 
                    $tempsParCategorie = [
                        'Indicatif' => ['present', 'imparfait', 'passe_simple', 'futur_simple', 'passe_compose', 'plus_que_parfait', 'passe_anterieur', 'futur_anterieur'],
                        'Autres modes' => ['conditionnel_present', 'conditionnel_passe', 'subjonctif_present', 'subjonctif_passe', 'imperatif_present', 'imperatif_passe']
                    ];
                    foreach ($tempsParCategorie as $categorie => $tempsList): ?>
                        <div>
                            <p class="text-small text-muted mb-sm"><strong><?= $categorie ?></strong></p>
                            <?php foreach ($tempsList as $code): 
                                $nom = TEMPS_CONJUGAISON[$code] ?? $code;
                                $checked = !$hasCustomTemps || in_array($code, $selectionsTemps);
                            ?>
                                <label class="form-checkbox mb-xs" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0;">
                                    <input type="checkbox" name="temps_selection" value="<?= $code ?>" 
                                           <?= $checked ? 'checked' : '' ?>
                                           onchange="markDirty('temps')">
                                    <span class="text-small"><?= htmlspecialchars($nom) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex flex-between flex-center mt-lg" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
                    <div>
                        <button class="btn btn-outline btn-small" onclick="resetSelection('temps')" title="Remettre tous les temps accessibles">
                            🔄 Tout rendre accessible
                        </button>
                    </div>
                    <button class="btn btn-primary" onclick="saveSelection('temps')" id="save-temps" disabled>
                        💾 Enregistrer la sélection des temps
                    </button>
                </div>
            </div>
        </div>

        <!-- Section Verbes -->
        <div class="card mb-lg">
            <div class="card-header flex flex-between flex-center">
                <h3 class="card-title">📖 Verbes disponibles</h3>
                <div class="flex gap-sm">
                    <span class="badge <?= $hasCustomVerbes ? 'badge-warning' : 'badge-success' ?>" id="badge-verbes">
                        <?= $hasCustomVerbes ? count($selectionsVerbes) . '/' . count($verbes) . ' sélectionnés' : 'Tous accessibles' ?>
                    </span>
                    <button class="btn btn-small btn-outline" onclick="toggleAllVerbes()">
                        Tout cocher/décocher
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtres de verbes -->
                <div class="flex gap-md mb-md">
                    <button class="btn btn-small btn-outline filter-btn active" onclick="filterVerbes('all', this)">Tous</button>
                    <button class="btn btn-small btn-outline filter-btn" onclick="filterVerbes('1', this)">1er groupe</button>
                    <button class="btn btn-small btn-outline filter-btn" onclick="filterVerbes('2', this)">2e groupe</button>
                    <button class="btn btn-small btn-outline filter-btn" onclick="filterVerbes('3', this)">3e groupe</button>
                    <span class="text-muted text-small" style="margin-left: auto;">
                        <span id="verbes-count"><?= count($verbes) ?></span> verbes au total
                    </span>
                </div>
                
                <div style="max-height: 400px; overflow-y: auto; border: 1px solid var(--gray-200); border-radius: var(--radius-md); padding: 1rem;">
                    <div class="grid grid-4 gap-sm" id="verbes-grid">
                        <?php foreach ($verbes as $v): 
                            $checked = !$hasCustomVerbes || in_array($v['id'], $selectionsVerbes);
                            $nbPhrases = $phrasesParVerbe[$v['id']] ?? 0;
                        ?>
                            <label class="form-checkbox verbe-item" data-groupe="<?= $v['groupe'] ?>" data-annee="<?= $v['annee_per'] ?>"
                                   style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.5rem; border-radius: var(--radius-sm); transition: background 0.2s;"
                                   onmouseover="this.style.background='var(--gray-100)'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="verbe_selection" value="<?= $v['id'] ?>"
                                       <?= $checked ? 'checked' : '' ?>
                                       onchange="markDirty('verbes')">
                                <span class="text-small">
                                    <strong><?= htmlspecialchars($v['infinitif']) ?></strong>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        (<?= $v['groupe'] ?>e gr.)
                                        <?php if ($nbPhrases > 0): ?>
                                            • <?= $nbPhrases ?> ph.
                                        <?php endif; ?>
                                    </span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="flex flex-between flex-center mt-lg" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
                    <div>
                        <button class="btn btn-outline btn-small" onclick="resetSelection('verbes')" title="Remettre tous les verbes accessibles">
                            🔄 Tout rendre accessible
                        </button>
                    </div>
                    <button class="btn btn-primary" onclick="saveSelection('verbes')" id="save-verbes" disabled>
                        💾 Enregistrer la sélection des verbes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- TAB ORTHOGRAPHE -->
    <!-- ========================================= -->
    <div id="tab-orthographe" class="tab-content" style="display: none;">
        <div class="card mb-lg">
            <div class="card-header flex flex-between flex-center">
                <h3 class="card-title">📝 Catégories d'orthographe</h3>
                <div class="flex gap-sm">
                    <span class="badge <?= $hasCustomOrtho ? 'badge-warning' : 'badge-success' ?>" id="badge-ortho">
                        <?= $hasCustomOrtho ? count($selectionsOrtho) . '/' . count($categoriesOrtho) . ' sélectionnées' : 'Toutes accessibles' ?>
                    </span>
                    <button class="btn btn-small btn-outline" onclick="toggleAllOrtho()">
                        Tout cocher/décocher
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php
                $orthoParType = [];
                foreach ($categoriesOrtho as $cat) {
                    $orthoParType[$cat['type']][] = $cat;
                }
                $typeLabels = [
                    'homophones_gram' => '🔤 Homophones grammaticaux',
                    'homophones_lex' => '🔡 Homophones lexicaux',
                    'accords_sv' => '✅ Accords sujet-verbe',
                    'accords_gn' => '✅ Accords dans le GN',
                    'accords_pp' => '✅ Accords du participe passé',
                    'dictee' => '📜 Dictées'
                ];
                ?>
                
                <?php foreach ($orthoParType as $type => $cats): ?>
                    <div class="mb-lg">
                        <h4 class="mb-sm" style="color: var(--primary);"><?= $typeLabels[$type] ?? $type ?></h4>
                        <div class="grid grid-2 gap-sm">
                            <?php foreach ($cats as $cat):
                                $checked = !$hasCustomOrtho || in_array($cat['id'], $selectionsOrtho);
                                $nbExos = $exosParCategorie[$cat['id']] ?? 0;
                            ?>
                                <label class="form-checkbox ortho-item"
                                       style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); cursor: pointer; transition: all 0.2s;"
                                       onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(79,70,229,0.03)'" 
                                       onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='transparent'">
                                    <input type="checkbox" name="ortho_selection" value="<?= $cat['id'] ?>"
                                           <?= $checked ? 'checked' : '' ?>
                                           onchange="markDirty('orthographe')">
                                    <div>
                                        <strong class="text-small"><?= htmlspecialchars($cat['nom']) ?></strong>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars($cat['description'] ?? '') ?>
                                            <?php if ($nbExos > 0): ?>
                                                • <strong><?= $nbExos ?> exercice<?= $nbExos > 1 ? 's' : '' ?></strong>
                                            <?php else: ?>
                                                • <em>Pas encore d'exercices</em>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="flex flex-between flex-center mt-lg" style="border-top: 1px solid var(--gray-200); padding-top: 1rem;">
                    <div>
                        <button class="btn btn-outline btn-small" onclick="resetSelection('orthographe')" title="Remettre toutes les catégories accessibles">
                            🔄 Tout rendre accessible
                        </button>
                    </div>
                    <button class="btn btn-primary" onclick="saveSelection('orthographe')" id="save-orthographe" disabled>
                        💾 Enregistrer la sélection orthographe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php endif; // classeActive ?>
    <?php endif; // classes ?>
</div>

<!-- Toast notification -->
<div id="toast" style="display:none; position:fixed; bottom:2rem; right:2rem; padding:1rem 1.5rem; border-radius:var(--radius-lg); color:white; font-weight:600; z-index:1000; box-shadow:var(--shadow-lg); transition: all 0.3s;"></div>

<style>
/* Onglets */
.tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--gray-200);
}
.tab-btn {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 600;
    color: var(--gray-500);
    font-size: 1rem;
    transition: all 0.2s;
    margin-bottom: -2px;
}
.tab-btn:hover {
    color: var(--primary);
}
.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

/* Filtres actifs */
.filter-btn.active {
    background: var(--primary) !important;
    color: white !important;
    border-color: var(--primary) !important;
}

/* Badge styles */
.badge-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}
.badge-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

/* Info alert */
.alert-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Checkbox amélioré pour les items */
.ortho-item input[type="checkbox"]:checked + div strong {
    color: var(--primary);
}

/* Animation save button */
@keyframes pulse-save {
    0%, 100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(79, 70, 229, 0); }
}
.btn-primary:not(:disabled) {
    animation: pulse-save 2s infinite;
}
.btn-primary:disabled {
    animation: none;
}
</style>

<script>
const CLASSE_ID = <?= $classeId ?: 'null' ?>;
const dirtyState = { temps: false, verbes: false, orthographe: false };

// ========================================
// ONGLETS
// ========================================
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).style.display = 'block';
    document.querySelector('[data-tab="' + tab + '"]').classList.add('active');
}

// ========================================
// MARQUAGE MODIFICATIONS
// ========================================
function markDirty(type) {
    dirtyState[type] = true;
    const btn = document.getElementById('save-' + type);
    if (btn) btn.disabled = false;
    updateBadge(type);
}

function updateBadge(type) {
    let checked, total, badgeEl;
    
    if (type === 'temps') {
        checked = document.querySelectorAll('input[name="temps_selection"]:checked').length;
        total = document.querySelectorAll('input[name="temps_selection"]').length;
        badgeEl = document.getElementById('badge-temps');
    } else if (type === 'verbes') {
        checked = document.querySelectorAll('input[name="verbe_selection"]:checked').length;
        total = document.querySelectorAll('input[name="verbe_selection"]').length;
        badgeEl = document.getElementById('badge-verbes');
    } else if (type === 'orthographe') {
        checked = document.querySelectorAll('input[name="ortho_selection"]:checked').length;
        total = document.querySelectorAll('input[name="ortho_selection"]').length;
        badgeEl = document.getElementById('badge-ortho');
    }
    
    if (badgeEl) {
        if (checked === total) {
            badgeEl.textContent = 'Tous accessibles';
            badgeEl.className = 'badge badge-success';
        } else {
            badgeEl.textContent = checked + '/' + total + ' sélectionné' + (type === 'orthographe' ? 'es' : 's');
            badgeEl.className = 'badge badge-warning';
        }
    }
}

// ========================================
// SAUVEGARDE
// ========================================
async function saveSelection(type) {
    if (!CLASSE_ID) return;
    
    let selections = [];
    let apiType = type;
    
    if (type === 'temps') {
        selections = Array.from(document.querySelectorAll('input[name="temps_selection"]:checked'))
            .map(cb => cb.value);
        const total = document.querySelectorAll('input[name="temps_selection"]').length;
        var mode = selections.length === total ? 'all' : 'custom';
    } else if (type === 'verbes') {
        selections = Array.from(document.querySelectorAll('input[name="verbe_selection"]:checked'))
            .map(cb => parseInt(cb.value));
        const total = document.querySelectorAll('input[name="verbe_selection"]').length;
        var mode = selections.length === total ? 'all' : 'custom';
    } else if (type === 'orthographe') {
        selections = Array.from(document.querySelectorAll('input[name="ortho_selection"]:checked'))
            .map(cb => parseInt(cb.value));
        const total = document.querySelectorAll('input[name="ortho_selection"]').length;
        var mode = selections.length === total ? 'all' : 'custom';
    }
    
    const btn = document.getElementById('save-' + type);
    const originalText = btn.textContent;
    btn.textContent = '⏳ Enregistrement...';
    btn.disabled = true;
    
    try {
        const response = await fetch('/api/save_selections.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                classe_id: CLASSE_ID,
                type: apiType,
                selections: selections,
                mode: mode
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Sélection enregistrée !', 'success');
            dirtyState[type] = false;
            btn.textContent = '✅ Enregistré';
            setTimeout(() => { btn.textContent = originalText; }, 2000);
        } else {
            throw new Error(data.error || 'Erreur inconnue');
        }
    } catch (err) {
        showToast('❌ Erreur : ' + err.message, 'error');
        btn.textContent = originalText;
        btn.disabled = false;
    }
}

// ========================================
// RESET (tout rendre accessible)
// ========================================
function resetSelection(type) {
    if (!confirm('Remettre tout accessible pour cette classe ? Les élèves verront tous les exercices.')) return;
    
    let checkboxes;
    if (type === 'temps') {
        checkboxes = document.querySelectorAll('input[name="temps_selection"]');
    } else if (type === 'verbes') {
        checkboxes = document.querySelectorAll('input[name="verbe_selection"]');
    } else if (type === 'orthographe') {
        checkboxes = document.querySelectorAll('input[name="ortho_selection"]');
    }
    
    checkboxes.forEach(cb => cb.checked = true);
    markDirty(type);
    
    // Sauvegarder immédiatement en mode "all"
    saveSelectionDirect(type, 'all', []);
}

async function saveSelectionDirect(type, mode, selections) {
    try {
        const response = await fetch('/api/save_selections.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                classe_id: CLASSE_ID,
                type: type,
                selections: selections,
                mode: mode
            })
        });
        const data = await response.json();
        if (data.success) {
            showToast('✅ Tout rendu accessible !', 'success');
            updateBadge(type);
            const btn = document.getElementById('save-' + type);
            if (btn) btn.disabled = true;
        }
    } catch (err) {
        showToast('❌ Erreur', 'error');
    }
}

// ========================================
// TOGGLE ALL
// ========================================
function toggleAllTemps() {
    const checkboxes = document.querySelectorAll('input[name="temps_selection"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    markDirty('temps');
}

function toggleAllVerbes() {
    // Seulement les verbes visibles (filtrés)
    const items = document.querySelectorAll('.verbe-item:not([style*="display: none"]) input[name="verbe_selection"]');
    const allChecked = Array.from(items).every(cb => cb.checked);
    items.forEach(cb => cb.checked = !allChecked);
    markDirty('verbes');
}

function toggleAllOrtho() {
    const checkboxes = document.querySelectorAll('input[name="ortho_selection"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    markDirty('orthographe');
}

// ========================================
// FILTRE VERBES PAR GROUPE
// ========================================
function filterVerbes(groupe, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const items = document.querySelectorAll('.verbe-item');
    let visible = 0;
    items.forEach(item => {
        if (groupe === 'all' || item.dataset.groupe === groupe) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });
    document.getElementById('verbes-count').textContent = visible;
}

// ========================================
// TOAST NOTIFICATION
// ========================================
function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.background = type === 'success' 
        ? 'linear-gradient(135deg, #10b981, #059669)' 
        : 'linear-gradient(135deg, #ef4444, #dc2626)';
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}

// ========================================
// ALERTE AVANT QUITTER
// ========================================
window.addEventListener('beforeunload', function(e) {
    if (dirtyState.temps || dirtyState.verbes || dirtyState.orthographe) {
        e.preventDefault();
        e.returnValue = 'Des modifications non enregistrées seront perdues.';
    }
});
</script>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
