<?php
require_once __DIR__ . '/../src/config/config.php';

// Générer un nouveau hash
$nouveauHash = password_hash('password123', PASSWORD_BCRYPT);
echo "Nouveau hash: " . $nouveauHash . "<br><br>";

// Mettre à jour en base
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("UPDATE enseignants SET mot_de_passe = ? WHERE email = 'test@francophile.ch'");
    $stmt->execute([$nouveauHash]);
    echo "✅ Mot de passe mis à jour !<br><br>";
    echo "Connecte-toi avec :<br>";
    echo "Email: test@francophile.ch<br>";
    echo "Mot de passe: password123";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
