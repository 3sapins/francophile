<?php
/**
 * API - Terminer un niveau de parcours
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/config/config.php';
Session::start();

if (!Session::isLoggedIn() || !Session::isEleve()) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$niveauId = (int)($input['niveau_id'] ?? 0);
$parcoursId = (int)($input['parcours_id'] ?? 0);
$score = (int)($input['score'] ?? 0);
$total = (int)($input['total'] ?? 0);
$reussi = (bool)($input['reussi'] ?? false);
$erreurs = $input['erreurs'] ?? [];
$eleveId = Session::getUserId();

if (!$niveauId || !$parcoursId || !$total) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    // Récupérer les infos du niveau
    $stmt = $db->prepare('SELECT * FROM parcours_niveaux WHERE id = ? AND parcours_id = ?');
    $stmt->execute([$niveauId, $parcoursId]);
    $niveau = $stmt->fetch();

    if (!$niveau) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => 'Niveau introuvable']);
        exit;
    }

    // Vérifier que le niveau est débloqué
    $stmt = $db->prepare('SELECT * FROM parcours_progression WHERE eleve_id = ? AND parcours_id = ?');
    $stmt->execute([$eleveId, $parcoursId]);
    $progression = $stmt->fetch();
    $niveauMaxAtteint = $progression['niveau_max_atteint'] ?? 0;

    if ($niveau['numero'] > $niveauMaxAtteint + 1) {
        $db->rollBack();
        echo json_encode(['success' => false, 'error' => 'Niveau verrouillé']);
        exit;
    }

    // Enregistrer la tentative
    $stmt = $db->prepare('
        INSERT INTO parcours_tentatives (eleve_id, niveau_id, score, total, reussi, points_gagnes, reponses_detail)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $pointsGagnes = $reussi ? $niveau['points_recompense'] : 0;
    $stmt->execute([
        $eleveId, $niveauId, $score, $total,
        $reussi ? 1 : 0, $pointsGagnes,
        json_encode($erreurs, JSON_UNESCAPED_UNICODE)
    ]);

    $parcoursTermine = false;

    // Si réussi, mettre à jour la progression
    if ($reussi && $niveau['numero'] > $niveauMaxAtteint) {
        $nouveauMax = $niveau['numero'];

        // Compter le nombre total de niveaux du parcours
        $stmt = $db->prepare('SELECT COUNT(*) FROM parcours_niveaux WHERE parcours_id = ?');
        $stmt->execute([$parcoursId]);
        $totalNiveaux = (int)$stmt->fetchColumn();
        $parcoursTermine = ($nouveauMax >= $totalNiveaux);

        if ($progression) {
            $stmt = $db->prepare('
                UPDATE parcours_progression 
                SET niveau_max_atteint = ?, niveau_actuel = ?, tentatives = tentatives + 1, termine = ?
                WHERE eleve_id = ? AND parcours_id = ?
            ');
            $stmt->execute([$nouveauMax, min($nouveauMax + 1, $totalNiveaux), $parcoursTermine ? 1 : 0, $eleveId, $parcoursId]);
        } else {
            $stmt = $db->prepare('
                INSERT INTO parcours_progression (eleve_id, parcours_id, niveau_actuel, niveau_max_atteint, tentatives, termine)
                VALUES (?, ?, ?, ?, 1, ?)
            ');
            $stmt->execute([$eleveId, $parcoursId, min($nouveauMax + 1, $totalNiveaux), $nouveauMax, $parcoursTermine ? 1 : 0]);
        }

        // Ajouter les points au domaine
        $stmt = $db->prepare('SELECT domaine FROM parcours WHERE id = ?');
        $stmt->execute([$parcoursId]);
        $domaine = $stmt->fetchColumn();

        if ($domaine && $pointsGagnes > 0) {
            $eleve = Eleve::findById($eleveId);
            if ($eleve) {
                $eleve->ajouterPoints($domaine, $pointsGagnes);
            }
        }
    } else {
        // Juste incrémenter les tentatives
        if ($progression) {
            $stmt = $db->prepare('UPDATE parcours_progression SET tentatives = tentatives + 1 WHERE eleve_id = ? AND parcours_id = ?');
            $stmt->execute([$eleveId, $parcoursId]);
        } else {
            $stmt = $db->prepare('INSERT INTO parcours_progression (eleve_id, parcours_id, niveau_actuel, niveau_max_atteint, tentatives) VALUES (?, ?, 1, 0, 1)');
            $stmt->execute([$eleveId, $parcoursId]);
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'reussi' => $reussi,
        'points_gagnes' => $pointsGagnes,
        'parcours_termine' => $parcoursTermine
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
