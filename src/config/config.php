<?php
/**
 * FRANCOPHILE.CH - Configuration principale
 * À adapter selon votre hébergement Infomaniak
 */

// Mode debug (désactiver en production)
define('DEBUG_MODE', true);

// Configuration base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'francophile');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_CHARSET', 'utf8mb4');

// Configuration du site
define('SITE_NAME', 'Francophile');
define('SITE_URL', 'https://francophile.ch');
define('SITE_EMAIL', 'contact@francophile.ch');

// Chemins
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');

// Session
define('SESSION_LIFETIME', 3600 * 8); // 8 heures
define('SESSION_NAME', 'francophile_session');

// Sécurité
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);

// Points par défaut (peuvent être surchargés en BDD)
define('DEFAULT_POINTS', [
    'niveau_1' => 10,
    'niveau_2' => 20,
    'niveau_3' => 30,
    'malus_1' => 2,
    'malus_2' => 5,
    'malus_3' => 8,
    'bonus_serie_5' => 10,
    'bonus_serie_10' => 25,
    'bonus_sans_erreur' => 15
]);

// Temps de réponse et autres limites
define('MAX_EXERCICES_SESSION', 20);
define('TEMPS_MAX_REPONSE', 120); // secondes

// Années scolaires valides
define('ANNEES_VALIDES', ['9', '10', '11']);

// Domaines disponibles
define('DOMAINES', [
    'conjugaison' => 'Conjugaison',
    'orthographe' => 'Orthographe',
    'grammaire' => 'Grammaire',
    'vocabulaire' => 'Vocabulaire',
    'figures_style' => 'Figures de style',
    'comprehension_texte' => 'Compréhension de texte',
    'comprehension_orale' => 'Compréhension orale',
    'production_ecrite' => 'Production écrite'
]);

// Temps de conjugaison
define('TEMPS_CONJUGAISON', [
    'present' => 'Présent de l\'indicatif',
    'imparfait' => 'Imparfait',
    'passe_simple' => 'Passé simple',
    'futur_simple' => 'Futur simple',
    'passe_compose' => 'Passé composé',
    'plus_que_parfait' => 'Plus-que-parfait',
    'passe_anterieur' => 'Passé antérieur',
    'futur_anterieur' => 'Futur antérieur',
    'conditionnel_present' => 'Conditionnel présent',
    'conditionnel_passe' => 'Conditionnel passé',
    'subjonctif_present' => 'Subjonctif présent',
    'subjonctif_passe' => 'Subjonctif passé',
    'imperatif_present' => 'Impératif présent',
    'imperatif_passe' => 'Impératif passé'
]);

// Personnes de conjugaison
define('PERSONNES', [
    'je' => 'je/j\'',
    'tu' => 'tu',
    'il' => 'il/elle/on',
    'nous' => 'nous',
    'vous' => 'vous',
    'ils' => 'ils/elles'
]);

// Personnes de l'impératif
define('PERSONNES_IMPERATIF', ['tu', 'nous', 'vous']);

// Erreurs
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Zurich');

// Autoloader simple
spl_autoload_register(function ($class) {
    $file = SRC_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
