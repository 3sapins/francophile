<?php
/**
 * API - Sauvegarder une réponse d'exercice
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/config/config.php';
Session::start();

if (!Session::isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

$sessionId = (int) ($input['session_id'] ?? 0);
$exerciceType = $input['exercice_type'] ?? '';
$exerciceId = (int) ($input['exercice_id'] ?? 0);
$question = $input['question'] ?? '';
$reponseAttendue = $input['reponse_attendue'] ?? '';
$reponseDonnee = $input['reponse_donnee'] ?? '';
$correct = (bool) ($input['correct'] ?? false);
$tempsReponse = (int) ($input['temps_reponse'] ?? 0);

if (!$sessionId) {
    echo json_encode(['success' => false, 'error' => 'Session invalide']);
    exit;
}

try {
    $exerciceManager = new Exercice();
    $exerciceManager->enregistrerReponse(
        $sessionId,
        $exerciceType,
        $exerciceId,
        $question,
        $reponseAttendue,
        $reponseDonnee,
        $correct,
        $tempsReponse
    );
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
