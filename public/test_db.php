<?php
require_once __DIR__ . '/../src/config/config.php';

echo "<h2>Test connexion</h2>";

try {
    $db = Database::getInstance();
    echo "✅ Connexion OK<br><br>";
    
    $stmt = $db->query("SELECT * FROM enseignants");
    $user = $stmt->fetch();
    
    echo "Email: " . $user['email'] . "<br>";
    echo "Hash: " . $user['mot_de_passe'] . "<br><br>";
    
    // Test password
    $test = password_verify('password', $user['mot_de_passe']);
    echo "Test 'password': " . ($test ? "✅ OK" : "❌ FAIL") . "<br>";
    
    $test2 = password_verify('password123', $user['mot_de_passe']);
    echo "Test 'password123': " . ($test2 ? "✅ OK" : "❌ FAIL") . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
