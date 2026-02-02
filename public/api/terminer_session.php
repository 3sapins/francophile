<?php
/**
 * API - Terminer une session et calculer les points
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/config/config.php';
Session::start();

if (!Session::isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$sessionId = (int) ($input['session_id'] ?? 0);

if (!$sessionId) {
    echo json_encode(['success' => false, 'error' => 'Session invalide']);
    exit;
}

try {
    $exerciceManager = new Exercice();
    $result = $exerciceManager->terminerSession($sessionId);
    
    echo json_encode(array_merge(['success' => true], $result));
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
