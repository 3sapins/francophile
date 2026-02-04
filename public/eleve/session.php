<?php
$pageTitle = 'Session d\'exercices';
require_once __DIR__ . '/../../src/includes/init.php';
requireEleve();

$eleve = Eleve::findById(Session::getUserId());
if (!$eleve) { Session::destroy(); header('Location: /login.php'); exit; }
$annee = $eleve->getAnneeScolaire();
$exerciceManager = new Exercice();

$exercices = [];
$sessionId = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domaine = $_POST['domaine'] ?? '';
    $niveau = $_POST['niveau'] ?? '1';
    $nombre = (int) ($_POST['nombre'] ?? 10);
    $nombre = min(20, max(5, $nombre));

    if ($domaine === 'conjugaison') {
        $verbes = $_POST['verbes'] ?? [];
        $temps = $_POST['temps'] ?? [];
        $mode = $_POST['mode'] ?? 'pronoms';

        if (empty($verbes) || empty($temps)) {
            $error = 'Sélectionne au moins un verbe et un temps.';
        } else {
            // Utiliser les formats enrichis si niveau >= 2
            if ((int)$niveau >= 2) {
                $exercices = $exerciceManager->genererExercicesConjugaisonEnrichis(
                    array_map('intval', $verbes),
                    $temps,
                    $mode,
                    $nombre,
                    $niveau
                );
            } else {
                $exercices = $exerciceManager->genererExercicesConjugaison(
                    array_map('intval', $verbes),
                    $temps,
                    $mode,
                    $nombre,
                    $niveau
                );
                // Ajouter le format par défaut
                foreach ($exercices as &$ex) {
                    if (!isset($ex['format'])) {
                        $ex['format'] = empty($ex['options']) ? 'input' : 'qcm';
                    }
                }
                unset($ex);
            }
            
            if (!empty($exercices)) {
                $sessionId = $exerciceManager->creerSession(
                    $eleve->getId(),
                    'conjugaison',
                    count($exercices),
                    $niveau,
                    implode(',', $temps),
                    $annee
                );
            }
        }
    } elseif ($domaine === 'orthographe') {
        $categories = $_POST['categories'] ?? [];

        if (empty($categories)) {
            $error = 'Sélectionne au moins une catégorie.';
        } else {
            $exercices = $exerciceManager->genererExercicesOrthographe(
                array_map('intval', $categories),
                $nombre,
                $niveau,
                $annee
            );
            // S'assurer que chaque exercice a un format
            foreach ($exercices as &$ex) {
                if (!isset($ex['format'])) {
                    $ex['format'] = !empty($ex['options']) ? 'qcm' : 'input';
                }
            }
            unset($ex);

            if (!empty($exercices)) {
                $sessionId = $exerciceManager->creerSession(
                    $eleve->getId(),
                    'orthographe',
                    count($exercices),
                    $niveau,
                    null,
                    $annee
                );
            }
        }
    }

    if (empty($exercices) && !$error) {
        $error = 'Aucun exercice trouvé avec ces critères. Essaie d\'autres options.';
    }
}

// Si pas de POST ou erreur, rediriger
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $error) {
    if ($error) {
        Session::setFlash('error', $error);
    }
    header('Location: /eleve/exercices.php');
    exit;
}

require_once __DIR__ . '/../../src/includes/header.php';
?>

<div class="container">
    <div class="exercice-container">
        <div class="exercice-header">
            <a href="/eleve/exercices.php" class="btn btn-outline btn-small">← Quitter</a>
            <div class="exercice-progress">
                <span class="exercice-counter">1 / <?= count($exercices) ?></span>
                <div class="progress" style="width: 200px;">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div class="exercice-card">
            <!-- Le contenu sera généré par JavaScript -->
            <div class="text-center">
                <p>Chargement...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Données des exercices (injectées par PHP)
const exercices = <?= json_encode($exercices, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const sessionId = <?= $sessionId ?>;

// Initialiser le gestionnaire d'exercices
document.addEventListener('DOMContentLoaded', () => {
    if (exercices.length > 0) {
        ExerciceManager.init(exercices, sessionId);
    } else {
        document.querySelector('.exercice-card').innerHTML = `
            <div class="text-center">
                <p class="text-muted">Aucun exercice disponible.</p>
                <a href="/eleve/exercices.php" class="btn btn-primary">Retour</a>
            </div>
        `;
    }
});
</script>

<?php require_once __DIR__ . '/../../src/includes/footer.php'; ?>
