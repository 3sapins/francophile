<?php
/**
 * FRANCOPHILE.CH - Configuration pour Railway / Production
 * Lit automatiquement les variables d'environnement
 */

// Mode debug
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1');

// Configuration base de données (Railway MySQL)
$dbUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if ($dbUrl) {
    $dbParts = parse_url($dbUrl);
    define('DB_HOST', $dbParts['host'] ?? 'localhost');
    define('DB_PORT', $dbParts['port'] ?? 3306);
    define('DB_NAME', ltrim($dbParts['path'] ?? '/francophile', '/'));
    define('DB_USER', $dbParts['user'] ?? 'root');
    define('DB_PASS', $dbParts['pass'] ?? '');
} else {
    define('DB_HOST', getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);
    define('DB_NAME', getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'francophile');
    define('DB_USER', getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '');
}

define('DB_CHARSET', 'utf8mb4');
define('SITE_NAME', 'Francophile');
define('SITE_URL', getenv('RAILWAY_STATIC_URL') ?: getenv('SITE_URL') ?: 'http://localhost');
define('SITE_EMAIL', getenv('SITE_EMAIL') ?: 'contact@francophile.ch');

define('ROOT_PATH', dirname(__DIR__, 2));
define('SRC_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');

define('SESSION_LIFETIME', 3600 * 8);
define('SESSION_NAME', 'francophile_session');
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);

define('DEFAULT_POINTS', [
    'niveau_1' => 10, 'niveau_2' => 20, 'niveau_3' => 30,
    'malus_1' => 2, 'malus_2' => 5, 'malus_3' => 8,
    'bonus_serie_5' => 10, 'bonus_serie_10' => 25, 'bonus_sans_erreur' => 15
]);

define('MAX_EXERCICES_SESSION', 20);
define('TEMPS_MAX_REPONSE', 120);
define('ANNEES_VALIDES', ['9', '10', '11']);

define('DOMAINES', [
    'conjugaison' => 'Conjugaison', 'orthographe' => 'Orthographe',
    'grammaire' => 'Grammaire', 'vocabulaire' => 'Vocabulaire',
    'figures_style' => 'Figures de style', 'comprehension_texte' => 'Compréhension de texte',
    'comprehension_orale' => 'Compréhension orale', 'production_ecrite' => 'Production écrite'
]);

define('TEMPS_CONJUGAISON', [
    'present' => 'Présent de l\'indicatif', 'imparfait' => 'Imparfait',
    'passe_simple' => 'Passé simple', 'futur_simple' => 'Futur simple',
    'passe_compose' => 'Passé composé', 'plus_que_parfait' => 'Plus-que-parfait',
    'passe_anterieur' => 'Passé antérieur', 'futur_anterieur' => 'Futur antérieur',
    'conditionnel_present' => 'Conditionnel présent', 'conditionnel_passe' => 'Conditionnel passé',
    'subjonctif_present' => 'Subjonctif présent', 'subjonctif_passe' => 'Subjonctif passé',
    'imperatif_present' => 'Impératif présent', 'imperatif_passe' => 'Impératif passé'
]);

define('PERSONNES', [
    'je' => 'je/j\'', 'tu' => 'tu', 'il' => 'il/elle/on',
    'nous' => 'nous', 'vous' => 'vous', 'ils' => 'ils/elles'
]);
define('PERSONNES_IMPERATIF', ['tu', 'nous', 'vous']);

// DEBUG TEMPORAIRE - à désactiver après diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Zurich');

spl_autoload_register(function ($class) {
    $file = SRC_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});
