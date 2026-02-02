-- ============================================
-- FRANCOPHILE.CH - Script d'installation complet
-- À exécuter dans Railway MySQL
-- ============================================

-- Activer le mode safe pour les imports
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- STRUCTURE DES TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS `enseignants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `etablissement` varchar(255) DEFAULT NULL,
  `est_admin` tinyint(1) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enseignant_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `annee_scolaire` enum('9','10','11') NOT NULL,
  `code_classe` varchar(10) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_classe` (`code_classe`),
  KEY `enseignant_id` (`enseignant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `eleves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classe_id` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classe_pseudo` (`classe_id`, `pseudo`),
  KEY `classe_id` (`classe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `verbes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `infinitif` varchar(50) NOT NULL,
  `groupe` enum('1','2','3') NOT NULL,
  `auxiliaire` enum('avoir','etre') DEFAULT 'avoir',
  `annee_per` enum('9','10','11') DEFAULT '9',
  `frequence` enum('haute','moyenne','basse') DEFAULT 'moyenne',
  `actif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `infinitif` (`infinitif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories_orthographe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `exercices_orthographe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie_id` int(11) NOT NULL,
  `type_exercice` varchar(50) DEFAULT 'choix_multiple',
  `phrase` text NOT NULL,
  `reponse_correcte` varchar(100) NOT NULL,
  `options_incorrectes` text DEFAULT NULL,
  `explication` text DEFAULT NULL,
  `niveau_difficulte` enum('1','2','3') DEFAULT '1',
  `annee_cible` enum('9','10','11') DEFAULT NULL,
  `groupe_homophones` varchar(50) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `categorie_id` (`categorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `phrases_conjugaison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verbe_id` int(11) NOT NULL,
  `temps` varchar(50) NOT NULL,
  `personne` varchar(10) NOT NULL,
  `phrase_avant` text NOT NULL,
  `phrase_apres` text DEFAULT NULL,
  `contexte` varchar(50) DEFAULT NULL,
  `niveau_difficulte` enum('1','2','3') DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `verbe_id` (`verbe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `progression_eleves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eleve_id` int(11) NOT NULL,
  `domaine` varchar(50) NOT NULL,
  `points_totaux` int(11) DEFAULT 0,
  `niveau_actuel` int(11) DEFAULT 1,
  `date_maj` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eleve_domaine` (`eleve_id`, `domaine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions_exercices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eleve_id` int(11) NOT NULL,
  `domaine` varchar(50) NOT NULL,
  `sous_categorie` varchar(100) DEFAULT NULL,
  `nombre_questions` int(11) DEFAULT 0,
  `nombre_correct` int(11) DEFAULT 0,
  `niveau_difficulte` enum('1','2','3') DEFAULT '1',
  `annee_cible` enum('9','10','11') DEFAULT NULL,
  `points_gagnes` int(11) DEFAULT 0,
  `points_perdus` int(11) DEFAULT 0,
  `date_debut` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_fin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eleve_id` (`eleve_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reponses_exercices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `exercice_type` varchar(50) NOT NULL,
  `exercice_id` int(11) DEFAULT NULL,
  `question_posee` text NOT NULL,
  `reponse_attendue` varchar(255) NOT NULL,
  `reponse_donnee` varchar(255) NOT NULL,
  `est_correct` tinyint(1) NOT NULL,
  `temps_reponse` int(11) DEFAULT NULL,
  `date_reponse` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `domaine` varchar(50) NOT NULL,
  `sous_categorie` varchar(50) DEFAULT NULL,
  `annee_cible` enum('9','10','11') DEFAULT NULL,
  `niveau_difficulte` enum('1','2','3') DEFAULT '1',
  `points_requis` int(11) DEFAULT 0,
  `conditions` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `badges_eleves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eleve_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `date_obtention` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eleve_badge` (`eleve_id`, `badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `points_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eleve_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `points_actuels` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `eleve_badge` (`eleve_id`, `badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `config_niveaux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domaine` varchar(50) NOT NULL,
  `niveau` int(11) NOT NULL,
  `nom_niveau` varchar(100) NOT NULL,
  `points_requis` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domaine_niveau` (`domaine`, `niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `config_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cle` varchar(50) NOT NULL,
  `valeur` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cle` (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES DE CONFIGURATION
-- ============================================

INSERT INTO `config_points` (`cle`, `valeur`, `description`) VALUES
('points_niveau_1', 10, 'Points par bonne réponse niveau 1'),
('points_niveau_2', 20, 'Points par bonne réponse niveau 2'),
('points_niveau_3', 30, 'Points par bonne réponse niveau 3'),
('malus_erreur_niveau_1', 2, 'Points perdus par erreur niveau 1'),
('malus_erreur_niveau_2', 5, 'Points perdus par erreur niveau 2'),
('malus_erreur_niveau_3', 8, 'Points perdus par erreur niveau 3'),
('bonus_sans_erreur', 15, 'Bonus session parfaite');

INSERT INTO `config_niveaux` (`domaine`, `niveau`, `nom_niveau`, `points_requis`) VALUES
('conjugaison', 1, 'Débutant', 0),
('conjugaison', 2, 'Apprenti', 100),
('conjugaison', 3, 'Confirmé', 300),
('conjugaison', 4, 'Expert', 600),
('conjugaison', 5, 'Maître', 1000),
('orthographe', 1, 'Débutant', 0),
('orthographe', 2, 'Apprenti', 100),
('orthographe', 3, 'Confirmé', 300),
('orthographe', 4, 'Expert', 600),
('orthographe', 5, 'Maître', 1000);

-- ============================================
-- CATÉGORIES ORTHOGRAPHE
-- ============================================

INSERT INTO `categories_orthographe` (`code`, `nom`, `type`) VALUES
('homo_a_a', 'a / à', 'homophones_gram'),
('homo_et_est', 'et / est / ai', 'homophones_gram'),
('homo_son_sont', 'son / sont', 'homophones_gram'),
('homo_on_ont', 'on / ont', 'homophones_gram'),
('homo_ou_ou', 'ou / où', 'homophones_gram'),
('homo_ce_se', 'ce / se', 'homophones_gram'),
('homo_ces_ses', 'ces / ses / c''est / s''est', 'homophones_gram'),
('homo_leur_leurs', 'leur / leurs', 'homophones_gram'),
('homo_la_la', 'la / là / l''a', 'homophones_gram'),
('homo_lex_ver', 'vers / vert / verre / ver', 'homophones_lex'),
('homo_lex_cou', 'cou / coup / coût', 'homophones_lex'),
('homo_lex_sot', 'sot / seau / sceau / saut', 'homophones_lex'),
('accords_sv', 'Accord sujet-verbe', 'accords_sv'),
('accords_gn', 'Accord dans le groupe nominal', 'accords_gn'),
('accords_pp', 'Accord du participe passé', 'accords_pp');

-- ============================================
-- VERBES DE BASE (échantillon)
-- ============================================

INSERT INTO `verbes` (`infinitif`, `groupe`, `auxiliaire`, `annee_per`, `frequence`) VALUES
('être', '3', 'avoir', '9', 'haute'),
('avoir', '3', 'avoir', '9', 'haute'),
('aller', '3', 'etre', '9', 'haute'),
('faire', '3', 'avoir', '9', 'haute'),
('dire', '3', 'avoir', '9', 'haute'),
('pouvoir', '3', 'avoir', '9', 'haute'),
('vouloir', '3', 'avoir', '9', 'haute'),
('venir', '3', 'etre', '9', 'haute'),
('voir', '3', 'avoir', '9', 'haute'),
('prendre', '3', 'avoir', '9', 'haute'),
('parler', '1', 'avoir', '9', 'haute'),
('manger', '1', 'avoir', '9', 'haute'),
('finir', '2', 'avoir', '9', 'haute'),
('choisir', '2', 'avoir', '9', 'haute'),
('partir', '3', 'etre', '9', 'haute'),
('mettre', '3', 'avoir', '9', 'haute'),
('savoir', '3', 'avoir', '9', 'haute'),
('devoir', '3', 'avoir', '9', 'haute'),
('croire', '3', 'avoir', '10', 'moyenne'),
('écrire', '3', 'avoir', '9', 'haute'),
('lire', '3', 'avoir', '9', 'haute'),
('vivre', '3', 'avoir', '10', 'moyenne'),
('suivre', '3', 'avoir', '10', 'moyenne'),
('mourir', '3', 'etre', '10', 'moyenne'),
('naître', '3', 'etre', '10', 'moyenne'),
('connaître', '3', 'avoir', '10', 'moyenne'),
('conduire', '3', 'avoir', '10', 'moyenne'),
('craindre', '3', 'avoir', '11', 'basse'),
('résoudre', '3', 'avoir', '11', 'basse'),
('vaincre', '3', 'avoir', '11', 'basse');

-- ============================================
-- EXERCICES HOMOPHONES (échantillon)
-- ============================================

INSERT INTO `exercices_orthographe` (`categorie_id`, `type_exercice`, `phrase`, `reponse_correcte`, `options_incorrectes`, `explication`, `niveau_difficulte`, `groupe_homophones`) VALUES
((SELECT id FROM categories_orthographe WHERE code = 'homo_a_a'), 'choix_multiple', 'Il ___ mangé une pomme.', 'a', '["à"]', '"a" = verbe avoir. On peut remplacer par "avait".', '1', 'a/à'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_a_a'), 'choix_multiple', 'Je vais ___ l''école.', 'à', '["a"]', '"à" = préposition. On ne peut pas remplacer par "avait".', '1', 'a/à'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_a_a'), 'choix_multiple', 'Elle ___ un chat.', 'a', '["à"]', '"a" = verbe avoir (possession).', '1', 'a/à'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_et_est'), 'choix_multiple', 'Pierre ___ Marie sont amis.', 'et', '["est"]', '"et" = conjonction de coordination (relie deux éléments).', '1', 'et/est'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_et_est'), 'choix_multiple', 'Il ___ grand.', 'est', '["et"]', '"est" = verbe être. On peut remplacer par "était".', '1', 'et/est'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_son_sont'), 'choix_multiple', '___ frère est gentil.', 'Son', '["Sont"]', '"Son" = déterminant possessif.', '1', 'son/sont'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_son_sont'), 'choix_multiple', 'Ils ___ partis.', 'sont', '["son"]', '"sont" = verbe être au pluriel.', '1', 'son/sont'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_on_ont'), 'choix_multiple', '___ va au cinéma.', 'On', '["Ont"]', '"On" = pronom indéfini (sujet).', '1', 'on/ont'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_on_ont'), 'choix_multiple', 'Ils ___ faim.', 'ont', '["on"]', '"ont" = verbe avoir à la 3e personne du pluriel.', '1', 'on/ont'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_ou_ou'), 'choix_multiple', 'Tu veux du thé ___ du café ?', 'ou', '["où"]', '"ou" = choix entre deux options.', '1', 'ou/où'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_ou_ou'), 'choix_multiple', '___ habites-tu ?', 'Où', '["Ou"]', '"où" = lieu (adverbe interrogatif).', '1', 'ou/où'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_ce_se'), 'choix_multiple', '___ livre est intéressant.', 'Ce', '["Se"]', '"Ce" = déterminant démonstratif.', '1', 'ce/se'),
((SELECT id FROM categories_orthographe WHERE code = 'homo_ce_se'), 'choix_multiple', 'Il ___ lave les mains.', 'se', '["ce"]', '"se" = pronom réfléchi.', '1', 'ce/se'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Les enfants ___ dans le jardin.', 'jouent', '["joue", "joues"]', 'Le sujet "les enfants" est pluriel.', '1', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Marie est ___ ce matin.', 'partie', '["parti", "partis"]', 'Avec être, accord avec le sujet (féminin singulier).', '1', 'accord_pp');

-- ============================================
-- BADGES PRINCIPAUX
-- ============================================

INSERT INTO `badges` (`code`, `nom`, `description`, `domaine`, `sous_categorie`, `niveau_difficulte`, `points_requis`, `conditions`, `icone`) VALUES
('meta_premier_pas', 'Premiers pas', 'Première session complétée', 'general', 'debutant', '1', 10, '{"sessions_min": 1}', '👶'),
('meta_assidu', 'Élève assidu', '10 sessions complétées', 'general', 'assiduite', '1', 100, '{"sessions_min": 10}', '📅'),
('meta_sans_faute', 'Sans faute', 'Première session parfaite', 'general', 'perfection', '2', 50, '{"sessions_parfaites_min": 1}', '✨'),
('conj_present_1', 'Apprenti du présent', 'Maîtrise les bases du présent', 'conjugaison', 'present', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('conj_present_2', 'Artisan du présent', 'Bonne maîtrise du présent', 'conjugaison', 'present', '2', 150, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('conj_present_3', 'Maître du présent', 'Excellente maîtrise du présent', 'conjugaison', 'present', '3', 300, '{"questions_min": 100, "taux_min": 85}', '👑'),
('ortho_homo_1', 'Apprenti homophones', 'Distingue les homophones de base', 'orthographe', 'homophones', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('ortho_homo_2', 'Artisan homophones', 'Bonne maîtrise des homophones', 'orthographe', 'homophones', '2', 150, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('ortho_accords_1', 'Apprenti accords', 'Découvre les règles d''accord', 'orthographe', 'accords', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱');

-- ============================================
-- COMPTE DE TEST
-- ============================================

INSERT INTO `enseignants` (`email`, `mot_de_passe`, `prenom`, `nom`, `etablissement`, `est_admin`) VALUES
('test@francophile.ch', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4C/pPtYgcM7UvAHa', 'Test', 'Enseignant', 'École de test', 0);

COMMIT;
