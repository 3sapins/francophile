<?php
$pageTitle = 'Mes classes';
require_once __DIR__ . '/../../src/includes/init.php';
requireEnseignant();
require_once __DIR__ . '/../../src/includes/header.php';

$enseignant = Enseignant::findById(Session::getUserId());
$db = Database::getInstance();

$action = $_GET['action'] ?? '';
$classeId = (int) ($_GET['id'] ?? 0);
$error = '';
$success = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    
    if ($postAction === 'create_class') {
        $nom = trim($_POST['nom'] ?? '');
        $annee = $_POST['annee'] ?? '9';
        
        if (empty($nom)) {
            $error = 'Le nom de la classe est obligatoire.';
        } elseif (!in_array($annee, ['9', '10', '11'])) {
            $error = 'Année scolaire invalide.';
        } else {
            $classe = $enseignant->creerClasse($nom, $annee);
            if ($classe) {
                Session::setFlash('success', 'Classe créée avec succès ! Code : ' . $classe['code_classe']);
                header('Location: /enseignant/classes.php?id=' . $classe['id']);
                exit;
            } else {
                $error = 'Erreur lors de la création de la classe.';
            }
        }
    }
    elseif ($postAction === 'add_student') {
        $classeId = (int) ($_POST['classe_id'] ?? 0);
        $pseudo = trim($_POST['pseudo'] ?? '');
        $password = $_POST['password'] ?? '';
        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        
        if (empty($pseudo) || empty($password)) {
            $error = 'Le pseudo et le mot de passe sont obligatoires.';
        } elseif (strlen($password) < 4) {
            $error = 'Le mot de passe doit contenir au moins 4 caractères.';
        } else {
            $eleve = Eleve::create($classeId, $pseudo, $password, $prenom, $nom);
            if ($eleve) {
                Session::setFlash('success', 'Élève ajouté avec succès !');
                header('Location: /enseignant/classes.php?id=' . $classeId);
                exit;
            } else {
                $error = 'Ce pseudo existe déjà dans cette classe.';
            }
        }
    }
    elseif ($postAction === 'rename_class') {
        $classeId = (int) ($_POST['classe_id'] ?? 0);
        $newNom = trim($_POST['nom'] ?? '');
        $newAnnee = $_POST['annee'] ?? '';
        
        if (!empty($newNom)) {
            $stmt = $db->prepare('UPDATE classes SET nom = ?, annee_scolaire = ? WHERE id = ? AND enseignant_id = ?');
            $stmt->execute([$newNom, $newAnnee, $classeId, $enseignant->getId()]);
            Session::setFlash('success', 'Classe mise à jour.');
            header('Location: /enseignant/classes.php?id=' . $classeId);
            exit;
        }
    }
}

// Récupérer les classes
$classes = $enseignant->getClasses();

// Si on affiche une classe spécifique
$classeDetail = null;
$eleves = [];
if ($classeId > 0) {
    $stmt = $db->prepare('SELECT * FROM classes WHERE id = ? AND enseignant_id = ?');
    $stmt->execute([$classeId, $enseignant->getId()]);
    $classeDetail = $stmt->fetch();
    
    if ($classeDetail) {
        $stmt = $db->prepare('
            SELECT e.*, 
                   (SELECT SUM(points_totaux) FROM progression_eleves WHERE eleve_id = e.id) as points_totaux,
                   (SELECT COUNT(*) FROM sessions_exercices WHERE eleve_id = e.id) as nb_sessions
            FROM eleves e 
            WHERE e.classe_id = ? AND e.actif = 1 
            ORDER BY e.pseudo
        ');
        $stmt->execute([$classeId]);
        $eleves = $stmt->fetchAll();
    }
}
?>

<div class="container">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="flex flex-between mb-xl">
        <h1>Mes classes</h1>
        <a href="/enseignant/classes.php?action=new" class="btn btn-primary">+ Nouvelle classe</a>
    </div>

    <?php if ($action === 'new'): ?>
        <!-- Formulaire création classe -->
        <div class="card" style="max-width: 500px;">
            <div class="card-header">
                <h3 class="card-title">Créer une nouvelle classe</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="create_class">
                    
                    <div class="form-group">
                        <label class="form-label">Nom de la classe *</label>
                        <input type="text" name="nom" class="form-input" placeholder="Ex: 10VP1" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Année scolaire *</label>
                        <select name="annee" class="form-select" required>
                            <option value="9">9e année</option>
                            <option value="10">10e année</option>
                            <option value="11">11e année</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-md">
                        <button type="submit" class="btn btn-primary">Créer la classe</button>
                        <a href="/enseignant/classes.php" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

    <?php elseif ($classeDetail): ?>
        <!-- Détail d'une classe -->
        <div class="mb-lg">
            <a href="/enseignant/classes.php" class="text-small">← Retour aux classes</a>
        </div>

        <div class="card mb-lg">
            <div class="card-header flex flex-between">
                <div>
                    <h2 class="mb-sm"><?= htmlspecialchars($classeDetail['nom']) ?></h2>
                    <p class="text-muted"><?= $classeDetail['annee_scolaire'] ?>e année</p>
                </div>
                <div class="text-right">
                    <p class="text-small text-muted">Code de classe</p>
                    <span class="badge badge-primary" style="font-size: 1.25rem; cursor: pointer;" 
                          onclick="ClasseManager.copyCode('<?= $classeDetail['code_classe'] ?>')"
                          title="Cliquer pour copier">
                        <?= $classeDetail['code_classe'] ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-2 gap-lg">
                    <!-- Liste des élèves -->
                    <div>
                        <h4 class="mb-md">Élèves (<?= count($eleves) ?>)</h4>
                        <?php if (!empty($eleves)): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Pseudo</th>
                                        <th>Points</th>
                                        <th>Sessions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($eleves as $eleve): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($eleve['pseudo']) ?></strong>
                                                <?php if ($eleve['prenom'] || $eleve['nom']): ?>
                                                    <br><span class="text-small text-muted">
                                                        <?= htmlspecialchars(trim($eleve['prenom'] . ' ' . $eleve['nom'])) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($eleve['points_totaux'] ?? 0, 0, ',', ' ') ?></td>
                                            <td><?= $eleve['nb_sessions'] ?? 0 ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">Aucun élève dans cette classe.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Ajouter un élève -->
                    <div>
                        <h4 class="mb-md">Ajouter un élève</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_student">
                            <input type="hidden" name="classe_id" value="<?= $classeDetail['id'] ?>">
                            
                            <div class="form-group">
                                <label class="form-label">Pseudo *</label>
                                <input type="text" name="pseudo" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Mot de passe *</label>
                                <input type="text" name="password" class="form-input" 
                                       placeholder="Min. 4 caractères" required>
                                <span class="form-hint">Visible pour que vous puissiez le communiquer</span>
                            </div>
                            
                            <div class="grid grid-2 gap-sm">
                                <div class="form-group">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" name="prenom" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nom" class="form-input">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Ajouter l'élève</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Liste des classes -->
        <?php if (!empty($classes)): ?>
            <div class="grid grid-3 gap-lg">
                <?php foreach ($classes as $classe): ?>
                    <?php
                    $stmt = $db->prepare('SELECT COUNT(*) FROM eleves WHERE classe_id = ? AND actif = 1');
                    $stmt->execute([$classe['id']]);
                    $nbEleves = $stmt->fetchColumn();
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <h3 class="mb-sm"><?= htmlspecialchars($classe['nom']) ?></h3>
                            <p class="text-muted mb-md">
                                <?= $classe['annee_scolaire'] ?>e année<br>
                                <?= $nbEleves ?> élève<?= $nbEleves > 1 ? 's' : '' ?>
                            </p>
                            <div class="flex flex-between">
                                <span class="badge badge-primary" style="cursor: pointer;"
                                      onclick="ClasseManager.copyCode('<?= $classe['code_classe'] ?>')">
                                    <?= $classe['code_classe'] ?>
                                </span>
                                <a href="/enseignant/classes.php?id=<?= $classe['id'] ?>" class="btn btn-outline btn-small">
                                    Gérer
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted mb-md">Vous n'avez pas encore créé de classe.</p>
                    <a href="/enseignant/classes.php?action=new" class="btn btn-primary btn-large">
                        Créer ma première classe
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
