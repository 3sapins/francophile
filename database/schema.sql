-- ============================================
-- FRANCOPHILE.CH - Schéma de base de données
-- Plateforme d'exercices de français 9-11H
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ============================================
-- TABLES UTILISATEURS ET CLASSES
-- ============================================

CREATE TABLE enseignants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    etablissement VARCHAR(255),
    est_admin BOOLEAN DEFAULT FALSE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enseignant_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    annee_scolaire ENUM('9', '10', '11') NOT NULL,
    code_classe VARCHAR(10) UNIQUE NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eleves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    classe_id INT NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_pseudo_classe (classe_id, pseudo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLES DE GAMIFICATION
-- ============================================

-- Points globaux par domaine pour chaque élève
CREATE TABLE progression_eleves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    domaine ENUM('conjugaison', 'orthographe', 'grammaire', 'vocabulaire', 'figures_style', 'comprehension_texte', 'comprehension_orale', 'production_ecrite') NOT NULL,
    points_totaux INT DEFAULT 0,
    niveau_actuel INT DEFAULT 1,
    date_maj DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    UNIQUE KEY unique_eleve_domaine (eleve_id, domaine)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuration des paliers de niveaux (paramétrable par admin)
CREATE TABLE config_niveaux (
    id INT PRIMARY KEY AUTO_INCREMENT,
    domaine ENUM('conjugaison', 'orthographe', 'grammaire', 'vocabulaire', 'figures_style', 'comprehension_texte', 'comprehension_orale', 'production_ecrite') NOT NULL,
    niveau INT NOT NULL,
    points_requis INT NOT NULL,
    nom_niveau VARCHAR(100),
    UNIQUE KEY unique_domaine_niveau (domaine, niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Badges disponibles
CREATE TABLE badges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    domaine ENUM('conjugaison', 'orthographe', 'grammaire', 'vocabulaire', 'figures_style', 'comprehension_texte', 'comprehension_orale', 'production_ecrite', 'general') NOT NULL,
    sous_categorie VARCHAR(100), -- ex: 'homophones', 'passe_simple', 'accords_gn'
    icone VARCHAR(255),
    points_requis INT NOT NULL,
    annee_per ENUM('9', '10', '11'),
    niveau_difficulte ENUM('1', '2', '3'),
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Badges obtenus par les élèves
CREATE TABLE badges_eleves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    badge_id INT NOT NULL,
    date_obtention DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_eleve_badge (eleve_id, badge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Points spécifiques pour badges (tracking détaillé)
CREATE TABLE points_badges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    badge_id INT NOT NULL,
    points_actuels INT DEFAULT 0,
    date_maj DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_eleve_badge_points (eleve_id, badge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLES CONJUGAISON
-- ============================================

-- Liste des verbes du PER
CREATE TABLE verbes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    infinitif VARCHAR(50) NOT NULL,
    groupe ENUM('1', '2', '3') NOT NULL,
    auxiliaire ENUM('avoir', 'être') NOT NULL,
    annee_per ENUM('9', '10', '11') NOT NULL,
    particularite VARCHAR(100), -- ex: 'pronominal', 'défectif'
    actif BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_infinitif (infinitif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conjugaisons de tous les verbes à tous les temps
CREATE TABLE conjugaisons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    verbe_id INT NOT NULL,
    temps ENUM('present', 'imparfait', 'passe_simple', 'futur_simple', 'passe_compose', 'plus_que_parfait', 'passe_anterieur', 'futur_anterieur', 'conditionnel_present', 'conditionnel_passe', 'subjonctif_present', 'subjonctif_passe', 'imperatif_present', 'imperatif_passe', 'participe_present', 'participe_passe') NOT NULL,
    personne ENUM('je', 'tu', 'il', 'nous', 'vous', 'ils') NOT NULL,
    forme VARCHAR(100) NOT NULL,
    FOREIGN KEY (verbe_id) REFERENCES verbes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_conjugaison (verbe_id, temps, personne)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Banque de phrases contextuelles pour conjugaison
CREATE TABLE phrases_conjugaison (
    id INT PRIMARY KEY AUTO_INCREMENT,
    verbe_id INT NOT NULL,
    temps ENUM('present', 'imparfait', 'passe_simple', 'futur_simple', 'passe_compose', 'plus_que_parfait', 'passe_anterieur', 'futur_anterieur', 'conditionnel_present', 'conditionnel_passe', 'subjonctif_present', 'subjonctif_passe', 'imperatif_present', 'imperatif_passe') NOT NULL,
    personne ENUM('je', 'tu', 'il', 'nous', 'vous', 'ils') NOT NULL,
    phrase_avant TEXT NOT NULL, -- partie avant le trou
    phrase_apres TEXT, -- partie après le trou
    contexte VARCHAR(100), -- ex: 'quotidien', 'historique', 'fantastique'
    niveau_difficulte ENUM('1', '2', '3') DEFAULT '1',
    annee_cible ENUM('9', '10', '11'),
    FOREIGN KEY (verbe_id) REFERENCES verbes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLES ORTHOGRAPHE
-- ============================================

-- Catégories d'orthographe
CREATE TABLE categories_orthographe (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    type ENUM('homophones_gram', 'homophones_lex', 'accords_sv', 'accords_gn', 'accords_pp', 'dictee') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Homophones (grammaticaux et lexicaux)
CREATE TABLE homophones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categorie_id INT NOT NULL,
    groupe_homophones VARCHAR(100) NOT NULL, -- ex: 'a/à', 'et/est/ai'
    mot VARCHAR(50) NOT NULL,
    nature_grammaticale VARCHAR(100), -- ex: 'verbe avoir', 'préposition'
    regle TEXT, -- astuce/règle pour distinguer
    exemple TEXT,
    FOREIGN KEY (categorie_id) REFERENCES categories_orthographe(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exercices d'orthographe (phrases à trous)
CREATE TABLE exercices_orthographe (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categorie_id INT NOT NULL,
    type_exercice ENUM('choix_multiple', 'texte_trou', 'correction', 'dictee') NOT NULL,
    phrase TEXT NOT NULL,
    reponse_correcte VARCHAR(255) NOT NULL,
    options_incorrectes TEXT, -- JSON array pour QCM
    explication TEXT,
    niveau_difficulte ENUM('1', '2', '3') DEFAULT '1',
    annee_cible ENUM('9', '10', '11'),
    groupe_homophones VARCHAR(100), -- pour lier aux homophones spécifiques
    actif BOOLEAN DEFAULT TRUE,
    cree_par INT, -- NULL = système, sinon enseignant_id
    FOREIGN KEY (categorie_id) REFERENCES categories_orthographe(id) ON DELETE CASCADE,
    FOREIGN KEY (cree_par) REFERENCES enseignants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLES EXERCICES ET RÉSULTATS
-- ============================================

-- Sessions d'exercices (une session = un ensemble d'exercices faits d'un coup)
CREATE TABLE sessions_exercices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    domaine ENUM('conjugaison', 'orthographe', 'grammaire', 'vocabulaire', 'figures_style', 'comprehension_texte', 'comprehension_orale', 'production_ecrite') NOT NULL,
    sous_categorie VARCHAR(100), -- ex: pour conjugaison: 'present_1er_groupe'
    date_debut DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_fin DATETIME,
    nombre_questions INT NOT NULL,
    nombre_correct INT DEFAULT 0,
    points_gagnes INT DEFAULT 0,
    points_perdus INT DEFAULT 0,
    niveau_difficulte ENUM('1', '2', '3'),
    annee_cible ENUM('9', '10', '11'),
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Détail des réponses par exercice
CREATE TABLE reponses_exercices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    exercice_type ENUM('conjugaison', 'orthographe') NOT NULL,
    exercice_id INT NOT NULL, -- ID dans la table source (phrases_conjugaison ou exercices_orthographe)
    question_posee TEXT NOT NULL,
    reponse_attendue VARCHAR(255) NOT NULL,
    reponse_donnee VARCHAR(255),
    est_correct BOOLEAN NOT NULL,
    temps_reponse INT, -- en secondes
    date_reponse DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions_exercices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLES CONFIGURATION
-- ============================================

-- Configuration des points (paramétrable par admin)
CREATE TABLE config_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(50) UNIQUE NOT NULL,
    valeur INT NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paramètres généraux
CREATE TABLE config_general (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(50) UNIQUE NOT NULL,
    valeur TEXT NOT NULL,
    type ENUM('int', 'string', 'boolean', 'json') DEFAULT 'string',
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES INITIALES
-- ============================================

-- Configuration des points par défaut
INSERT INTO config_points (cle, valeur, description) VALUES
('points_niveau_1', 10, 'Points gagnés pour un exercice niveau 1 réussi'),
('points_niveau_2', 20, 'Points gagnés pour un exercice niveau 2 réussi'),
('points_niveau_3', 30, 'Points gagnés pour un exercice niveau 3 réussi'),
('malus_erreur_niveau_1', 2, 'Points perdus pour une erreur niveau 1'),
('malus_erreur_niveau_2', 5, 'Points perdus pour une erreur niveau 2'),
('malus_erreur_niveau_3', 8, 'Points perdus pour une erreur niveau 3'),
('bonus_serie_5', 10, 'Bonus pour 5 bonnes réponses consécutives'),
('bonus_serie_10', 25, 'Bonus pour 10 bonnes réponses consécutives'),
('bonus_sans_erreur', 15, 'Bonus pour une session sans erreur');

-- Configuration des niveaux par défaut (exemple pour conjugaison)
INSERT INTO config_niveaux (domaine, niveau, points_requis, nom_niveau) VALUES
('conjugaison', 1, 0, 'Débutant'),
('conjugaison', 2, 100, 'Apprenti'),
('conjugaison', 3, 300, 'Intermédiaire'),
('conjugaison', 4, 600, 'Avancé'),
('conjugaison', 5, 1000, 'Expert'),
('conjugaison', 6, 1500, 'Maître'),
('orthographe', 1, 0, 'Débutant'),
('orthographe', 2, 100, 'Apprenti'),
('orthographe', 3, 300, 'Intermédiaire'),
('orthographe', 4, 600, 'Avancé'),
('orthographe', 5, 1000, 'Expert'),
('orthographe', 6, 1500, 'Maître');

-- Catégories d'orthographe
INSERT INTO categories_orthographe (code, nom, description, type) VALUES
('homo_a_a', 'a / à', 'Distinguer le verbe avoir de la préposition', 'homophones_gram'),
('homo_et_est', 'et / est / ai', 'Distinguer la conjonction, le verbe être et le verbe avoir', 'homophones_gram'),
('homo_son_sont', 'son / sont', 'Distinguer le déterminant possessif du verbe être', 'homophones_gram'),
('homo_on_ont', 'on / ont', 'Distinguer le pronom indéfini du verbe avoir', 'homophones_gram'),
('homo_ou_ou', 'ou / où', 'Distinguer la conjonction de l''adverbe de lieu', 'homophones_gram'),
('homo_ce_se', 'ce / se', 'Distinguer le déterminant démonstratif du pronom réfléchi', 'homophones_gram'),
('homo_ces_ses', 'ces / ses / c''est / s''est', 'Distinguer les démonstratifs, possessifs et formes verbales', 'homophones_gram'),
('homo_leur_leurs', 'leur / leurs', 'Distinguer le pronom du déterminant possessif', 'homophones_gram'),
('homo_la_la_la', 'la / là / l''a', 'Distinguer l''article, l''adverbe et le pronom + verbe', 'homophones_gram'),
('homo_ma_ma', 'm''a / ma', 'Distinguer le pronom + verbe du déterminant possessif', 'homophones_gram'),
('homo_lex_ver', 'vers / vert / verre / ver', 'Homophones lexicaux autour de [vɛʁ]', 'homophones_lex'),
('homo_lex_cou', 'cou / coup / coût', 'Homophones lexicaux autour de [ku]', 'homophones_lex'),
('homo_lex_sot', 'sot / seau / sceau / saut', 'Homophones lexicaux autour de [so]', 'homophones_lex'),
('homo_lex_fin', 'fin / faim', 'Homophones lexicaux autour de [fɛ̃]', 'homophones_lex'),
('homo_lex_mer', 'mer / mère / maire', 'Homophones lexicaux autour de [mɛʁ]', 'homophones_lex'),
('accord_sv', 'Accord sujet-verbe', 'Accorder correctement le verbe avec son sujet', 'accords_sv'),
('accord_gn', 'Accord dans le GN', 'Accorder les éléments du groupe nominal', 'accords_gn'),
('accord_pp', 'Accord du participe passé', 'Accorder le participe passé selon les règles', 'accords_pp');

-- Badges initiaux
INSERT INTO badges (code, nom, description, domaine, sous_categorie, points_requis, annee_per, niveau_difficulte) VALUES
-- Badges conjugaison
('conj_present_9_1', 'Apprenti du présent (9e)', 'Maîtrise les bases du présent de l''indicatif', 'conjugaison', 'present', 100, '9', '1'),
('conj_present_9_2', 'Artisan du présent (9e)', 'Bonne maîtrise du présent de l''indicatif', 'conjugaison', 'present', 250, '9', '2'),
('conj_present_9_3', 'Maître du présent (9e)', 'Excellente maîtrise du présent de l''indicatif', 'conjugaison', 'present', 500, '9', '3'),
('conj_imparfait_9_1', 'Apprenti de l''imparfait (9e)', 'Maîtrise les bases de l''imparfait', 'conjugaison', 'imparfait', 100, '9', '1'),
('conj_passe_simple_10_1', 'Apprenti du passé simple (10e)', 'Maîtrise les bases du passé simple', 'conjugaison', 'passe_simple', 150, '10', '1'),
('conj_passe_simple_10_2', 'Artisan du passé simple (10e)', 'Bonne maîtrise du passé simple', 'conjugaison', 'passe_simple', 350, '10', '2'),
('conj_passe_simple_10_3', 'Maître du passé simple (10e)', 'Excellente maîtrise du passé simple', 'conjugaison', 'passe_simple', 700, '10', '3'),
('conj_subjonctif_11_1', 'Apprenti du subjonctif (11e)', 'Maîtrise les bases du subjonctif', 'conjugaison', 'subjonctif', 150, '11', '1'),
('conj_subjonctif_11_2', 'Artisan du subjonctif (11e)', 'Bonne maîtrise du subjonctif', 'conjugaison', 'subjonctif', 400, '11', '2'),
('conj_subjonctif_11_3', 'Maître du subjonctif (11e)', 'Excellente maîtrise du subjonctif', 'conjugaison', 'subjonctif', 800, '11', '3'),
-- Badges orthographe homophones
('ortho_homo_a_9_1', 'Apprenti a/à (9e)', 'Distingue a et à correctement', 'orthographe', 'homophones_a', 80, '9', '1'),
('ortho_homo_a_9_2', 'Artisan a/à (9e)', 'Bonne maîtrise de a/à', 'orthographe', 'homophones_a', 200, '9', '2'),
('ortho_homo_et_9_1', 'Apprenti et/est (9e)', 'Distingue et et est correctement', 'orthographe', 'homophones_et', 80, '9', '1'),
('ortho_homo_son_9_1', 'Apprenti son/sont (9e)', 'Distingue son et sont correctement', 'orthographe', 'homophones_son', 80, '9', '1'),
('ortho_homo_ces_10_1', 'Apprenti ces/ses/c''est/s''est (10e)', 'Distingue ces formes correctement', 'orthographe', 'homophones_ces', 120, '10', '1'),
('ortho_homo_ces_10_2', 'Artisan ces/ses/c''est/s''est (10e)', 'Bonne maîtrise de ces formes', 'orthographe', 'homophones_ces', 300, '10', '2'),
('ortho_champion_homo', 'Champion des homophones', 'A obtenu tous les badges homophones niveau 1', 'orthographe', 'homophones', 1000, NULL, NULL),
-- Badges accords
('ortho_accord_sv_9_1', 'Apprenti accord S-V (9e)', 'Maîtrise les bases de l''accord sujet-verbe', 'orthographe', 'accords_sv', 100, '9', '1'),
('ortho_accord_gn_9_1', 'Apprenti accord GN (9e)', 'Maîtrise les bases de l''accord dans le GN', 'orthographe', 'accords_gn', 100, '9', '1'),
('ortho_accord_pp_10_1', 'Apprenti participe passé (10e)', 'Maîtrise les bases de l''accord du PP', 'orthographe', 'accords_pp', 150, '10', '1'),
('ortho_accord_pp_10_2', 'Artisan participe passé (10e)', 'Bonne maîtrise de l''accord du PP', 'orthographe', 'accords_pp', 400, '10', '2'),
('ortho_accord_pp_11_3', 'Maître du participe passé (11e)', 'Excellente maîtrise de l''accord du PP', 'orthographe', 'accords_pp', 800, '11', '3');

-- Index pour performances
CREATE INDEX idx_eleves_classe ON eleves(classe_id);
CREATE INDEX idx_sessions_eleve ON sessions_exercices(eleve_id);
CREATE INDEX idx_sessions_domaine ON sessions_exercices(domaine);
CREATE INDEX idx_reponses_session ON reponses_exercices(session_id);
CREATE INDEX idx_progression_eleve ON progression_eleves(eleve_id);
CREATE INDEX idx_conjugaisons_verbe ON conjugaisons(verbe_id);
CREATE INDEX idx_phrases_verbe_temps ON phrases_conjugaison(verbe_id, temps);
CREATE INDEX idx_exercices_categorie ON exercices_orthographe(categorie_id);
