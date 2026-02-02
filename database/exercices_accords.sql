-- ============================================
-- FRANCOPHILE.CH - Exercices d'accords
-- Sujet-verbe, groupe nominal, participe passé
-- ============================================

-- ============================================
-- ACCORDS SUJET-VERBE
-- ============================================

INSERT INTO exercices_orthographe (categorie_id, type_exercice, phrase, reponse_correcte, options_incorrectes, explication, niveau_difficulte, annee_cible, groupe_homophones) VALUES

-- Sujet simple
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Les enfants ___ dans le jardin.', 'jouent', '["joue", "joues", "jouons"]', 'Le sujet "les enfants" est à la 3e personne du pluriel → jouent', '1', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Ma sœur ___ au piano.', 'joue', '["jouent", "joues", "jouons"]', 'Le sujet "ma sœur" est à la 3e personne du singulier → joue', '1', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Nous ___ à la maison.', 'restons', '["reste", "restent", "restez"]', 'Le sujet est "nous" → 1re personne du pluriel', '1', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Tu ___ tes devoirs.', 'fais', '["fait", "faites", "font"]', 'Le sujet est "tu" → 2e personne du singulier', '1', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Les oiseaux ___ vers le sud.', 'migrent', '["migre", "migres", "migrions"]', 'Sujet pluriel → verbe au pluriel', '1', '9', 'accord_sv'),

-- Sujet inversé
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Dans la forêt ___ de nombreux animaux.', 'vivent', '["vit", "vivons", "vivez"]', 'Le sujet "de nombreux animaux" est inversé mais reste pluriel', '2', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Sur la table ___ un livre.', 'se trouve', '["se trouvent", "se trouvons", "se trouves"]', 'Le sujet "un livre" est singulier', '2', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Où ___ les clés ?', 'sont', '["est", "sommes", "êtes"]', 'Le sujet "les clés" est pluriel', '2', '9', 'accord_sv'),

-- Sujet collectif
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'La foule ___ le stade.', 'envahit', '["envahissent", "envahissons", "envahis"]', 'Sujet collectif singulier (la foule) → verbe singulier', '2', '10', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Une multitude d''étoiles ___ dans le ciel.', 'brillent', '["brille", "brillons", "brillez"]', 'Le complément "d''étoiles" donne le sens → pluriel acceptable', '3', '10', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Le groupe de musiciens ___ sur scène.', 'monte', '["montent", "montons", "montez"]', 'On accorde avec "le groupe" (singulier)', '2', '10', 'accord_sv'),

-- Sujets coordonnés
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Pierre et Marie ___ au cinéma.', 'vont', '["va", "allons", "allez"]', 'Deux sujets coordonnés par "et" → pluriel', '1', '9', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Le chien ou le chat ___ cette nuit.', 'a miaulé', '["ont miaulé", "avons miaulé", "avez miaulé"]', 'Avec "ou" exclusif, accord avec le sujet le plus proche', '3', '10', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Ni Paul ni Jean ne ___ venir.', 'peuvent', '["peut", "pouvons", "pouvez"]', '"Ni...ni" avec deux sujets → pluriel', '2', '10', 'accord_sv'),

-- Pronoms relatifs
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'C''est moi qui ___ raison.', 'ai', '["a", "as", "avons"]', '"Qui" reprend "moi" → 1re personne singulier', '2', '10', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'C''est toi qui ___ gagné.', 'as', '["a", "ai", "avez"]', '"Qui" reprend "toi" → 2e personne singulier', '2', '10', 'accord_sv'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_sv'), 'choix_multiple', 'Les livres qui ___ sur la table sont à moi.', 'sont', '["est", "sommes", "êtes"]', '"Qui" reprend "les livres" → 3e personne pluriel', '2', '10', 'accord_sv'),

-- ============================================
-- ACCORDS DANS LE GROUPE NOMINAL
-- ============================================

-- Adjectifs épithètes
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Une maison ___.', 'blanche', '["blanc", "blancs", "blanches"]', 'L''adjectif s''accorde avec "maison" (fém. sing.)', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des fleurs ___.', 'rouges', '["rouge", "rougent", "rouger"]', 'L''adjectif s''accorde avec "fleurs" (fém. plur.)', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Un garçon ___.', 'intelligent', '["intelligente", "intelligents", "intelligentes"]', 'L''adjectif s''accorde avec "garçon" (masc. sing.)', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des enfants ___.', 'heureux', '["heureuse", "heureuses", "heureus"]', 'L''adjectif s''accorde avec "enfants" (masc. plur.)', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Les filles sont ___.', 'contentes', '["content", "contents", "contente"]', 'Attribut accordé avec "les filles" (fém. plur.)', '1', '9', 'accord_gn'),

-- Adjectifs de couleur
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des chaussures ___.', 'noires', '["noir", "noirs", "noire"]', 'Adjectif de couleur simple → accord normal', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des yeux ___.', 'marron', '["marrons", "marronne", "marronnes"]', 'Adjectif de couleur issu d''un nom → invariable', '2', '10', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des robes ___.', 'orange', '["oranges", "orangée", "orangées"]', 'Adjectif de couleur issu d''un nom → invariable', '2', '10', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des chemises ___.', 'bleu clair', '["bleues claires", "bleus clairs", "bleue claire"]', 'Adjectif composé de couleur → invariable', '3', '10', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des pulls ___.', 'roses', '["rose", "rosent", "roser"]', '"Rose" comme couleur vient de la fleur mais s''accorde', '2', '10', 'accord_gn'),

-- Déterminants
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', '___ amie est gentille.', 'Mon', '["Ma", "Mes", "Notre"]', '"Mon" devant un nom féminin commençant par voyelle', '2', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', '___ enfants sont sages.', 'Ces', '["Ce", "Cette", "Cet"]', 'Démonstratif pluriel', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'J''ai ___ idées.', 'quelques', '["quelque", "quelqu''", "quelques-unes"]', 'Déterminant indéfini pluriel', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', '___ histoire est passionnante.', 'Cette', '["Ce", "Cet", "Ces"]', 'Démonstratif féminin singulier', '1', '9', 'accord_gn'),

-- Adjectifs à place variable
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Une ___ femme (= grande de taille).', 'grande', '["grand", "grands", "grandes"]', 'Adjectif épithète accordé avec "femme"', '1', '9', 'accord_gn'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_gn'), 'choix_multiple', 'Des ___ hommes (= courageux).', 'grands', '["grand", "grande", "grandes"]', 'Adjectif épithète accordé avec "hommes"', '1', '9', 'accord_gn'),

-- ============================================
-- ACCORDS DU PARTICIPE PASSÉ
-- ============================================

-- Avec être
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Marie est ___ ce matin.', 'partie', '["parti", "partis", "parties"]', 'Avec être, accord avec le sujet "Marie" (fém. sing.)', '1', '9', 'accord_pp_etre'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Les filles sont ___ en vacances.', 'parties', '["parti", "partis", "partie"]', 'Avec être, accord avec "les filles" (fém. plur.)', '1', '9', 'accord_pp_etre'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Nous sommes ___ hier. (locutrice)', 'arrivées', '["arrivé", "arrivés", "arrivée"]', 'Avec être, accord avec "nous" (fém. plur.)', '2', '9', 'accord_pp_etre'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Les enfants sont ___ tôt.', 'venus', '["venu", "venue", "venues"]', 'Avec être, accord avec "les enfants" (masc. plur.)', '1', '9', 'accord_pp_etre'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Elle est ___ à l''école.', 'allée', '["allé", "allés", "allées"]', 'Avec être, accord avec "elle" (fém. sing.)', '1', '9', 'accord_pp_etre'),

-- Avec avoir (COD après)
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'J''ai ___ une pomme.', 'mangé', '["mangée", "mangés", "mangées"]', 'Avec avoir, pas d''accord si COD après', '1', '9', 'accord_pp_avoir'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Elle a ___ ses devoirs.', 'terminé', '["terminée", "terminés", "terminées"]', 'Avec avoir, pas d''accord si COD après', '1', '9', 'accord_pp_avoir'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Nous avons ___ le film.', 'regardé', '["regardée", "regardés", "regardées"]', 'Avec avoir, COD "le film" est après → pas d''accord', '1', '9', 'accord_pp_avoir'),

-- Avec avoir (COD avant)
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'La pomme que j''ai ___.', 'mangée', '["mangé", "mangés", "mangées"]', 'Avec avoir, COD "que" (= la pomme) avant → accord', '2', '10', 'accord_pp_avoir'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Les lettres que tu as ___.', 'écrites', '["écrit", "écrite", "écrits"]', 'COD "que" (= les lettres) avant → accord fém. plur.', '2', '10', 'accord_pp_avoir'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Je les ai ___ hier. (mes amies)', 'vues', '["vu", "vue", "vus"]', 'COD "les" (= mes amies) avant → accord fém. plur.', '2', '10', 'accord_pp_avoir'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Quelle robe as-tu ___ ?', 'choisie', '["choisi", "choisis", "choisies"]', 'COD "quelle robe" avant → accord fém. sing.', '2', '10', 'accord_pp_avoir'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Combien de livres as-tu ___ ?', 'lus', '["lu", "lue", "lues"]', 'COD "combien de livres" avant → accord masc. plur.', '2', '10', 'accord_pp_avoir'),

-- Verbes pronominaux
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Elle s''est ___ les mains.', 'lavé', '["lavée", "lavés", "lavées"]', 'Pronominal, COD "les mains" après → pas d''accord', '3', '11', 'accord_pp_pronom'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Ils se sont ___.', 'parlé', '["parlés", "parlée", "parlées"]', '"Se parler" → COI, pas d''accord', '3', '11', 'accord_pp_pronom'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Elles se sont ___.', 'regardées', '["regardé", "regardés", "regardée"]', '"Se" est COD → accord avec le sujet', '3', '11', 'accord_pp_pronom'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Marie s''est ___ à la fenêtre.', 'mise', '["mis", "mises", "mie"]', '"Se mettre" → accord avec le sujet', '3', '11', 'accord_pp_pronom'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Les années se sont ___.', 'succédé', '["succédés", "succédée", "succédées"]', '"Se succéder" → COI, invariable', '3', '11', 'accord_pp_pronom'),

-- Cas particuliers
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'La chaleur qu''il a ___.', 'fait', '["faite", "faits", "faites"]', '"Il fait" impersonnel → invariable', '3', '11', 'accord_pp_special'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Les efforts qu''il a ___.', 'fallu', '["fallus", "fallue", "fallues"]', '"Falloir" impersonnel → invariable', '3', '11', 'accord_pp_special'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Elle s''est ___ les cheveux.', 'coupé', '["coupée", "coupés", "coupées"]', 'COD "les cheveux" après → pas d''accord', '3', '11', 'accord_pp_special'),
((SELECT id FROM categories_orthographe WHERE code = 'accords_pp'), 'choix_multiple', 'Les trois heures qu''a ___ le voyage.', 'duré', '["durée", "durés", "durées"]', '"Trois heures" = CC de temps, pas COD → invariable', '3', '11', 'accord_pp_special');
