<?php
/**
 * API : Sauvegarder la sélection d'exercices pour une classe
 * POST /api/save_selections.php
 */
require_once __DIR__ . '/../../src/config/config.php';
Session::start();

header('Content-Type: application/json');

// Vérifier authentification enseignant
if (!Session::isLoggedIn() || Session::getUserType() !== 'enseignant' && Session::getUserType() !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit;
}

// Lire le body JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['classe_id']) || !isset($input['type'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$classeId = (int) $input['classe_id'];
$type = $input['type']; // 'orthographe', 'temps', 'verbes'
$selections = $input['selections'] ?? []; // Array d'IDs ou de codes
$mode = $input['mode'] ?? 'custom'; // 'all' = tout activer (supprimer les restrictions), 'custom' = sélection

$db = Database::getInstance();

// Vérifier que l'enseignant possède cette classe
$enseignantId = Session::getUserId();
$stmt = $db->prepare('SELECT id FROM classes WHERE id = ? AND enseignant_id = ?');
$stmt->execute([$classeId, $enseignantId]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Classe non trouvée ou accès refusé']);
    exit;
}

try {
    $db->beginTransaction();
    
    switch ($type) {
        case 'orthographe':
            // Supprimer les anciennes sélections
            $stmt = $db->prepare('DELETE FROM selections_ortho_classe WHERE classe_id = ?');
            $stmt->execute([$classeId]);
            
            // Insérer les nouvelles (seulement en mode custom avec des sélections)
            if ($mode === 'custom' && !empty($selections)) {
                $stmt = $db->prepare('INSERT INTO selections_ortho_classe (classe_id, categorie_id) VALUES (?, ?)');
                foreach ($selections as $catId) {
                    $stmt->execute([$classeId, (int) $catId]);
                }
            }
            break;
            
        case 'temps':
            // Supprimer les anciennes sélections
            $stmt = $db->prepare('DELETE FROM selections_conj_classe WHERE classe_id = ?');
            $stmt->execute([$classeId]);
            
            if ($mode === 'custom' && !empty($selections)) {
                $stmt = $db->prepare('INSERT INTO selections_conj_classe (classe_id, temps) VALUES (?, ?)');
                foreach ($selections as $tempsCode) {
                    $stmt->execute([$classeId, $tempsCode]);
                }
            }
            break;
            
        case 'verbes':
            // Supprimer les anciennes sélections
            $stmt = $db->prepare('DELETE FROM selections_verbes_classe WHERE classe_id = ?');
            $stmt->execute([$classeId]);
            
            if ($mode === 'custom' && !empty($selections)) {
                $stmt = $db->prepare('INSERT INTO selections_verbes_classe (classe_id, verbe_id) VALUES (?, ?)');
                foreach ($selections as $verbeId) {
                    $stmt->execute([$classeId, (int) $verbeId]);
                }
            }
            break;
            
        default:
            throw new Exception('Type de sélection invalide');
    }
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Sélection enregistrée',
        'type' => $type,
        'mode' => $mode,
        'count' => count($selections)
    ]);
    
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
}
