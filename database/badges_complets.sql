-- ============================================
-- FRANCOPHILE.CH - Système de badges complet
-- ============================================

-- Supprimer les anciens badges s'ils existent
DELETE FROM badges WHERE id > 0;

-- ============================================
-- BADGES CONJUGAISON
-- ============================================

-- Présent de l'indicatif
INSERT INTO badges (code, nom, description, domaine, sous_categorie, annee_cible, niveau_difficulte, points_requis, conditions, icone) VALUES
('conj_present_9_1', 'Apprenti du présent', 'Maîtrise les bases du présent de l''indicatif', 'conjugaison', 'present', '9', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('conj_present_9_2', 'Artisan du présent', 'Bonne maîtrise du présent', 'conjugaison', 'present', '9', '2', 150, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('conj_present_9_3', 'Maître du présent', 'Excellente maîtrise du présent', 'conjugaison', 'present', '9', '3', 300, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- Imparfait
('conj_imparfait_9_1', 'Apprenti de l''imparfait', 'Découvre l''imparfait', 'conjugaison', 'imparfait', '9', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('conj_imparfait_9_2', 'Artisan de l''imparfait', 'Progresse à l''imparfait', 'conjugaison', 'imparfait', '9', '2', 150, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('conj_imparfait_9_3', 'Maître de l''imparfait', 'Maîtrise l''imparfait', 'conjugaison', 'imparfait', '9', '3', 300, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- Futur simple
('conj_futur_9_1', 'Apprenti du futur', 'Premiers pas au futur simple', 'conjugaison', 'futur_simple', '9', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('conj_futur_9_2', 'Artisan du futur', 'Bonne maîtrise du futur', 'conjugaison', 'futur_simple', '9', '2', 150, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('conj_futur_9_3', 'Maître du futur', 'Excellente maîtrise du futur', 'conjugaison', 'futur_simple', '9', '3', 300, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- Passé simple (10e)
('conj_passe_simple_10_1', 'Explorateur du passé simple', 'Découvre le passé simple', 'conjugaison', 'passe_simple', '10', '1', 75, '{"questions_min": 25, "taux_min": 55}', '🔍'),
('conj_passe_simple_10_2', 'Navigateur du passé simple', 'Progresse au passé simple', 'conjugaison', 'passe_simple', '10', '2', 200, '{"questions_min": 60, "taux_min": 70}', '🧭'),
('conj_passe_simple_10_3', 'Maître du passé simple', 'Maîtrise le passé simple', 'conjugaison', 'passe_simple', '10', '3', 400, '{"questions_min": 120, "taux_min": 80}', '👑'),

-- Conditionnel (10e)
('conj_conditionnel_10_1', 'Apprenti du conditionnel', 'Premiers pas au conditionnel', 'conjugaison', 'conditionnel_present', '10', '1', 60, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('conj_conditionnel_10_2', 'Artisan du conditionnel', 'Bonne maîtrise du conditionnel', 'conjugaison', 'conditionnel_present', '10', '2', 175, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('conj_conditionnel_10_3', 'Maître du conditionnel', 'Excellente maîtrise', 'conjugaison', 'conditionnel_present', '10', '3', 350, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- Subjonctif (11e)
('conj_subjonctif_11_1', 'Explorateur du subjonctif', 'Découvre le subjonctif', 'conjugaison', 'subjonctif_present', '11', '1', 100, '{"questions_min": 30, "taux_min": 50}', '🔍'),
('conj_subjonctif_11_2', 'Navigateur du subjonctif', 'Progresse au subjonctif', 'conjugaison', 'subjonctif_present', '11', '2', 250, '{"questions_min": 75, "taux_min": 65}', '🧭'),
('conj_subjonctif_11_3', 'Maître du subjonctif', 'Maîtrise le subjonctif', 'conjugaison', 'subjonctif_present', '11', '3', 500, '{"questions_min": 150, "taux_min": 75}', '👑'),

-- Badges transversaux conjugaison
('conj_polyvalent_9', 'Conjugueur polyvalent', 'Pratique tous les temps de 9e', 'conjugaison', 'general', '9', '2', 300, '{"sessions_min": 20}', '🎭'),
('conj_expert_10', 'Expert en conjugaison', 'Niveau expert atteint', 'conjugaison', 'general', '10', '3', 750, '{"points_min": 750, "taux_min": 75}', '🎓'),
('conj_perfectionniste', 'Perfectionniste', '5 sessions parfaites en conjugaison', 'conjugaison', 'special', NULL, '3', 200, '{"sessions_parfaites_min": 5}', '💎'),

-- ============================================
-- BADGES ORTHOGRAPHE - HOMOPHONES
-- ============================================

-- a / à
('ortho_a_a_9_1', 'Apprenti a/à', 'Distingue a et à', 'orthographe', 'homo_a_a', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_a_a_9_2', 'Artisan a/à', 'Bonne maîtrise de a/à', 'orthographe', 'homo_a_a', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),
('ortho_a_a_9_3', 'Maître a/à', 'Excellente maîtrise', 'orthographe', 'homo_a_a', '9', '3', 200, '{"questions_min": 80, "taux_min": 90}', '👑'),

-- et / est
('ortho_et_est_9_1', 'Apprenti et/est', 'Distingue et et est', 'orthographe', 'homo_et_est', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_et_est_9_2', 'Artisan et/est', 'Bonne maîtrise', 'orthographe', 'homo_et_est', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),
('ortho_et_est_9_3', 'Maître et/est', 'Excellente maîtrise', 'orthographe', 'homo_et_est', '9', '3', 200, '{"questions_min": 80, "taux_min": 90}', '👑'),

-- son / sont
('ortho_son_sont_9_1', 'Apprenti son/sont', 'Distingue son et sont', 'orthographe', 'homo_son_sont', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_son_sont_9_2', 'Artisan son/sont', 'Bonne maîtrise', 'orthographe', 'homo_son_sont', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),

-- on / ont
('ortho_on_ont_9_1', 'Apprenti on/ont', 'Distingue on et ont', 'orthographe', 'homo_on_ont', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_on_ont_9_2', 'Artisan on/ont', 'Bonne maîtrise', 'orthographe', 'homo_on_ont', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),

-- ou / où
('ortho_ou_ou_9_1', 'Apprenti ou/où', 'Distingue ou et où', 'orthographe', 'homo_ou_ou', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_ou_ou_9_2', 'Artisan ou/où', 'Bonne maîtrise', 'orthographe', 'homo_ou_ou', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),

-- ce / se
('ortho_ce_se_9_1', 'Apprenti ce/se', 'Distingue ce et se', 'orthographe', 'homo_ce_se', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_ce_se_9_2', 'Artisan ce/se', 'Bonne maîtrise', 'orthographe', 'homo_ce_se', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),

-- ces / ses / c'est / s'est (10e)
('ortho_ces_ses_10_1', 'Apprenti ces/ses', 'Distingue ces, ses, c''est, s''est', 'orthographe', 'homo_ces_ses', '10', '1', 50, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('ortho_ces_ses_10_2', 'Artisan ces/ses', 'Bonne maîtrise', 'orthographe', 'homo_ces_ses', '10', '2', 150, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('ortho_ces_ses_10_3', 'Maître ces/ses', 'Excellente maîtrise', 'orthographe', 'homo_ces_ses', '10', '3', 300, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- leur / leurs
('ortho_leur_leurs_9_1', 'Apprenti leur/leurs', 'Distingue leur et leurs', 'orthographe', 'homo_leur_leurs', '9', '1', 30, '{"questions_min": 15, "taux_min": 65}', '🌱'),
('ortho_leur_leurs_9_2', 'Artisan leur/leurs', 'Bonne maîtrise', 'orthographe', 'homo_leur_leurs', '9', '2', 100, '{"questions_min": 40, "taux_min": 80}', '⚒️'),

-- Champion des homophones
('ortho_champion_homo_9', 'Champion des homophones', 'Maîtrise tous les homophones de 9e', 'orthographe', 'homophones', '9', '3', 500, '{"points_min": 400, "taux_min": 80}', '🏆'),
('ortho_expert_homo_10', 'Expert des homophones', 'Maîtrise avancée des homophones', 'orthographe', 'homophones', '10', '3', 800, '{"points_min": 700, "taux_min": 85}', '🎓'),

-- ============================================
-- BADGES ORTHOGRAPHE - ACCORDS
-- ============================================

-- Accord sujet-verbe
('ortho_accord_sv_9_1', 'Apprenti accords S-V', 'Découvre les accords sujet-verbe', 'orthographe', 'accords_sv', '9', '1', 40, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('ortho_accord_sv_9_2', 'Artisan accords S-V', 'Bonne maîtrise des accords S-V', 'orthographe', 'accords_sv', '9', '2', 120, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('ortho_accord_sv_9_3', 'Maître accords S-V', 'Excellente maîtrise', 'orthographe', 'accords_sv', '9', '3', 250, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- Accord dans le GN
('ortho_accord_gn_9_1', 'Apprenti accords GN', 'Découvre les accords dans le GN', 'orthographe', 'accords_gn', '9', '1', 40, '{"questions_min": 20, "taux_min": 60}', '🌱'),
('ortho_accord_gn_9_2', 'Artisan accords GN', 'Bonne maîtrise des accords GN', 'orthographe', 'accords_gn', '9', '2', 120, '{"questions_min": 50, "taux_min": 75}', '⚒️'),
('ortho_accord_gn_9_3', 'Maître accords GN', 'Excellente maîtrise', 'orthographe', 'accords_gn', '9', '3', 250, '{"questions_min": 100, "taux_min": 85}', '👑'),

-- Accord du participe passé
('ortho_accord_pp_9_1', 'Apprenti PP avec être', 'Accorde le PP avec être', 'orthographe', 'accords_pp', '9', '1', 40, '{"questions_min": 20, "taux_min": 55}', '🌱'),
('ortho_accord_pp_10_1', 'Apprenti PP avec avoir', 'Accorde le PP avec avoir', 'orthographe', 'accords_pp', '10', '1', 60, '{"questions_min": 25, "taux_min": 50}', '🌱'),
('ortho_accord_pp_10_2', 'Artisan des PP', 'Bonne maîtrise des PP', 'orthographe', 'accords_pp', '10', '2', 180, '{"questions_min": 60, "taux_min": 70}', '⚒️'),
('ortho_accord_pp_11_3', 'Maître des PP', 'Maîtrise complète des PP', 'orthographe', 'accords_pp', '11', '3', 400, '{"questions_min": 120, "taux_min": 80}', '👑'),

-- Champion des accords
('ortho_champion_accords', 'Champion des accords', 'Maîtrise tous les types d''accords', 'orthographe', 'accords', NULL, '3', 600, '{"points_min": 500, "taux_min": 80}', '🏆'),

-- ============================================
-- BADGES GÉNÉRAUX / MÉTA
-- ============================================

-- Premiers pas
('meta_premier_pas', 'Premiers pas', 'Première session complétée', 'general', 'debutant', NULL, '1', 10, '{"sessions_min": 1}', '👶'),
('meta_assidu', 'Élève assidu', '10 sessions complétées', 'general', 'assiduite', NULL, '1', 100, '{"sessions_min": 10}', '📅'),
('meta_regulier', 'Travailleur régulier', '25 sessions complétées', 'general', 'assiduite', NULL, '2', 250, '{"sessions_min": 25}', '⏰'),
('meta_marathonien', 'Marathonien', '50 sessions complétées', 'general', 'assiduite', NULL, '3', 500, '{"sessions_min": 50}', '🏃'),
('meta_centurion', 'Centurion', '100 sessions complétées', 'general', 'assiduite', NULL, '3', 1000, '{"sessions_min": 100}', '💯'),

-- Réussite
('meta_sans_faute', 'Sans faute', 'Première session parfaite', 'general', 'perfection', NULL, '2', 50, '{"sessions_parfaites_min": 1}', '✨'),
('meta_precision', 'Précision chirurgicale', '10 sessions parfaites', 'general', 'perfection', NULL, '3', 300, '{"sessions_parfaites_min": 10}', '🎯'),

-- Points
('meta_100_pts', 'Première centaine', '100 points atteints', 'general', 'points', NULL, '1', 100, '{"points_min": 100}', '💰'),
('meta_500_pts', 'Demi-millier', '500 points atteints', 'general', 'points', NULL, '2', 500, '{"points_min": 500}', '💎'),
('meta_1000_pts', 'Millionnaire', '1000 points atteints', 'general', 'points', NULL, '3', 1000, '{"points_min": 1000}', '🌟'),
('meta_5000_pts', 'Légende', '5000 points atteints', 'general', 'points', NULL, '3', 5000, '{"points_min": 5000}', '🏛️');
