<?php
$pageTitle = 'Connexion';
$bodyClass = 'login-page';

// Charger la config et démarrer la session AVANT tout output HTML
require_once __DIR__ . '/../src/config/config.php';
Session::start();

// Rediriger si déjà connecté
if (Session::isLoggedIn()) {
    $redirect = Session::isEleve() ? '/eleve/dashboard.php' : '/enseignant/dashboard.php';
    header('Location: ' . $redirect);
    exit;
}

$error = '';
$success = '';
$activeTab = $_GET['tab'] ?? 'eleve';

// Traitement du formulaire AVANT tout output HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login_eleve') {
        $pseudo = trim($_POST['pseudo'] ?? '');
        $codeClasse = trim($_POST['code_classe'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($pseudo) || empty($codeClasse) || empty($password)) {
            $error = 'Veuillez remplir tous les champs.';
            $activeTab = 'eleve';
        } else {
            $eleve = Eleve::authenticate($pseudo, $codeClasse, $password);
            if ($eleve) {
                Session::login($eleve->getId(), 'eleve', $eleve->toArray());
                header('Location: /eleve/dashboard.php');
                exit;
            } else {
                $error = 'Identifiants incorrects.';
                $activeTab = 'eleve';
            }
        }
    }
    elseif ($action === 'login_enseignant') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Veuillez remplir tous les champs.';
            $activeTab = 'enseignant';
        } else {
            $enseignant = Enseignant::authenticate($email, $password);
            if ($enseignant) {
                $userType = $enseignant->isAdmin() ? 'admin' : 'enseignant';
                Session::login($enseignant->getId(), $userType, $enseignant->toArray());
                header('Location: /enseignant/dashboard.php');
                exit;
            } else {
                $error = 'Identifiants incorrects.';
                $activeTab = 'enseignant';
            }
        }
    }
    elseif ($action === 'register') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $prenom = trim($_POST['prenom'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $etablissement = trim($_POST['etablissement'] ?? '');
        
        if (empty($email) || empty($password) || empty($prenom) || empty($nom)) {
            $error = 'Veuillez remplir tous les champs obligatoires.';
            $activeTab = 'register';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Les mots de passe ne correspondent pas.';
            $activeTab = 'register';
        } elseif (strlen($password) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
            $activeTab = 'register';
        } else {
            $enseignant = Enseignant::create($email, $password, $prenom, $nom, $etablissement);
            if ($enseignant) {
                $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
                $activeTab = 'enseignant';
            } else {
                $error = 'Cette adresse email est déjà utilisée.';
                $activeTab = 'register';
            }
        }
    }
}

// MAINTENANT on peut inclure le header (qui envoie du HTML)
require_once __DIR__ . '/../src/includes/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <span class="logo-icon">📚</span>
            <h1 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Francophile</h1>
            <p class="text-muted">Connectez-vous pour continuer</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="login-tabs">
            <div class="login-tab <?= $activeTab === 'eleve' ? 'active' : '' ?>" data-tab="eleve">Élève</div>
            <div class="login-tab <?= $activeTab === 'enseignant' ? 'active' : '' ?>" data-tab="enseignant">Enseignant</div>
            <div class="login-tab <?= $activeTab === 'register' ? 'active' : '' ?>" data-tab="register">Inscription</div>
        </div>

        <!-- Formulaire Élève -->
        <form id="form-eleve" class="login-form <?= $activeTab === 'eleve' ? 'active' : '' ?>" method="POST">
            <input type="hidden" name="action" value="login_eleve">
            
            <div class="form-group">
                <label class="form-label" for="pseudo">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" class="form-input" 
                       placeholder="Ton pseudo" required autofocus>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="code_classe">Code de classe</label>
                <input type="text" id="code_classe" name="code_classe" class="form-input" 
                       placeholder="Ex: ABC123" maxlength="6" style="text-transform: uppercase;" required>
                <span class="form-hint">Le code donné par ton enseignant</span>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password_eleve">Mot de passe</label>
                <input type="password" id="password_eleve" name="password" class="form-input" 
                       placeholder="Ton mot de passe" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">Se connecter</button>
        </form>

        <!-- Formulaire Enseignant -->
        <form id="form-enseignant" class="login-form <?= $activeTab === 'enseignant' ? 'active' : '' ?>" method="POST">
            <input type="hidden" name="action" value="login_enseignant">
            
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" 
                       placeholder="votre@email.ch" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password_ens">Mot de passe</label>
                <input type="password" id="password_ens" name="password" class="form-input" 
                       placeholder="Votre mot de passe" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">Se connecter</button>
        </form>

        <!-- Formulaire Inscription -->
        <form id="form-register" class="login-form <?= $activeTab === 'register' ? 'active' : '' ?>" method="POST">
            <input type="hidden" name="action" value="register">
            
            <p class="text-small text-muted mb-lg">Créez un compte enseignant pour gérer vos classes.</p>
            
            <div class="grid grid-2 gap-md">
                <div class="form-group">
                    <label class="form-label" for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email_reg">Email *</label>
                <input type="email" id="email_reg" name="email" class="form-input" 
                       placeholder="votre@email.ch" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="etablissement">Établissement</label>
                <input type="text" id="etablissement" name="etablissement" class="form-input" 
                       placeholder="Nom de votre école">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password_reg">Mot de passe *</label>
                <input type="password" id="password_reg" name="password" class="form-input" 
                       placeholder="Minimum 8 caractères" minlength="8" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password_confirm">Confirmer le mot de passe *</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-input" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-large">Créer mon compte</button>
        </form>
    </div>
</div>

<script>
// Focus automatique sur le premier champ vide du formulaire actif
document.addEventListener('DOMContentLoaded', function() {
    const activeForm = document.querySelector('.login-form.active');
    if (activeForm) {
        const firstEmpty = activeForm.querySelector('input:not([type=hidden]):not([value])');
        if (firstEmpty) firstEmpty.focus();
    }
});
</script>

<?php require_once __DIR__ . '/../src/includes/footer.php'; ?>
