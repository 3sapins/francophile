-- ============================================
-- FRANCOPHILE.CH - Banque de phrases contextuelles
-- Phrases pour exercices de conjugaison
-- ============================================

-- Format: verbe_id sera à remplacer par l'ID réel après insertion des verbes
-- Le symbole ___ indique l'emplacement du verbe conjugué

-- ============================================
-- PHRASES GÉNÉRIQUES (utilisables pour plusieurs verbes)
-- ============================================

-- Ces phrases peuvent être adaptées à différents verbes
-- {sujet} sera remplacé par je/tu/il/nous/vous/ils
-- {infinitif} sera remplacé par le verbe à conjuguer

-- ============================================
-- PRÉSENT DE L'INDICATIF
-- ============================================

-- Contexte quotidien
INSERT INTO phrases_conjugaison (verbe_id, temps, personne, phrase_avant, phrase_apres, contexte, niveau_difficulte, annee_cible) VALUES
-- ÊTRE
((SELECT id FROM verbes WHERE infinitif = 'être'), 'present', 'je', 'En ce moment, je', 'très fatigué après cette longue journée.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'present', 'tu', 'Tu', 'vraiment doué en mathématiques !', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'present', 'il', 'Mon frère', 'le plus grand de la famille.', 'famille', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'present', 'nous', 'Nous', 'toujours à l''heure pour le cours.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'present', 'vous', 'Vous', 'les bienvenus dans notre maison.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'present', 'ils', 'Mes amis', 'partis en vacances.', 'quotidien', '1', '9'),

-- AVOIR
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'present', 'je', 'J''', 'quinze ans depuis hier.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'present', 'tu', 'Tu', 'de la chance d''avoir réussi cet examen.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'present', 'il', 'Ce livre', 'plus de cinq cents pages.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'present', 'nous', 'Nous', 'un chien et deux chats à la maison.', 'famille', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'present', 'vous', 'Vous', 'raison de vous méfier.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'present', 'ils', 'Les élèves', 'beaucoup de devoirs ce soir.', 'scolaire', '1', '9'),

-- ALLER
((SELECT id FROM verbes WHERE infinitif = 'aller'), 'present', 'je', 'Je', 'au cinéma tous les samedis.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'aller'), 'present', 'tu', 'Tu', 'trop vite en voiture !', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'aller'), 'present', 'il', 'Pierre', 'à l''école en vélo.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'aller'), 'present', 'nous', 'Nous', 'souvent nous promener en forêt.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'aller'), 'present', 'vous', 'Où', '-vous pendant les vacances ?', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'aller'), 'present', 'ils', 'Les enfants', 'jouer dans le jardin.', 'quotidien', '1', '9'),

-- FAIRE
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'present', 'je', 'Je', 'mes devoirs avant le dîner.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'present', 'tu', 'Que', '-tu ce week-end ?', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'present', 'il', 'Il', 'beau aujourd''hui.', 'météo', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'present', 'nous', 'Nous', 'du sport trois fois par semaine.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'present', 'vous', 'Vous', 'un excellent travail !', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'present', 'ils', 'Mes parents', 'les courses le samedi.', 'quotidien', '1', '9'),

-- FINIR (2e groupe)
((SELECT id FROM verbes WHERE infinitif = 'finir'), 'present', 'je', 'Je', 'toujours mes exercices en premier.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'finir'), 'present', 'tu', 'Tu', 'tes légumes avant le dessert.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'finir'), 'present', 'il', 'Le cours', 'à seize heures.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'finir'), 'present', 'nous', 'Nous', 'notre travail avec application.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'finir'), 'present', 'vous', 'Vous', 'toujours par comprendre.', 'scolaire', '2', '9'),
((SELECT id FROM verbes WHERE infinitif = 'finir'), 'present', 'ils', 'Les ouvriers', 'les travaux demain.', 'quotidien', '1', '9'),

-- CHOISIR (2e groupe)
((SELECT id FROM verbes WHERE infinitif = 'choisir'), 'present', 'je', 'Je', 'toujours le livre le plus épais.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'choisir'), 'present', 'tu', 'Tu', 'bien tes amis.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'choisir'), 'present', 'il', 'Le client', 'son plat avec soin.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'choisir'), 'present', 'nous', 'Nous', 'notre destination de vacances.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'choisir'), 'present', 'vous', 'Vous', 'le moment idéal pour partir.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'choisir'), 'present', 'ils', 'Les électeurs', 'leurs représentants.', 'société', '2', '10'),

-- PARLER (1er groupe)
((SELECT id FROM verbes WHERE infinitif = 'parler'), 'present', 'je', 'Je', 'trois langues couramment.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'parler'), 'present', 'tu', 'Tu', 'trop vite, je ne comprends pas.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'parler'), 'present', 'il', 'Le professeur', 'de la Révolution française.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'parler'), 'present', 'nous', 'Nous', 'de nos projets d''avenir.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'parler'), 'present', 'vous', 'Vous', 'très bien le français !', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'parler'), 'present', 'ils', 'Les témoins', 'de ce qu''ils ont vu.', 'société', '2', '10'),

-- ============================================
-- IMPARFAIT
-- ============================================

-- ÊTRE
((SELECT id FROM verbes WHERE infinitif = 'être'), 'imparfait', 'je', 'Quand j''', 'petit, j''adorais les dinosaures.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'imparfait', 'tu', 'Tu', 'toujours en retard à l''époque.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'imparfait', 'il', 'Il', 'une fois un roi très sage.', 'conte', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'imparfait', 'nous', 'Nous', 'heureux dans cette maison.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'imparfait', 'vous', 'Vous', 'si jeunes à cette époque !', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'imparfait', 'ils', 'Les gens', 'plus solidaires autrefois.', 'souvenir', '2', '9'),

-- AVOIR
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'imparfait', 'je', 'J''', 'peur du noir quand j''étais enfant.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'imparfait', 'tu', 'Tu', 'toujours de bonnes idées.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'imparfait', 'il', 'Mon grand-père', 'une vieille voiture rouge.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'imparfait', 'nous', 'Nous', 'un jardin magnifique.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'imparfait', 'vous', 'Vous', 'raison de vous inquiéter.', 'quotidien', '2', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'imparfait', 'ils', 'Les enfants', 'envie de jouer dehors.', 'souvenir', '1', '9'),

-- FAIRE
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'imparfait', 'je', 'Je', 'du vélo tous les jours.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'imparfait', 'tu', 'Tu', 'toujours tes devoirs en musique.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'imparfait', 'il', 'Il', 'froid cet hiver-là.', 'météo', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'imparfait', 'nous', 'Nous', 'des cabanes dans les arbres.', 'souvenir', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'imparfait', 'vous', 'Vous', 'du tennis chaque dimanche.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'imparfait', 'ils', 'Les artisans', 'tout à la main.', 'historique', '2', '9'),

-- ============================================
-- PASSÉ SIMPLE (10e année)
-- ============================================

-- ÊTRE
((SELECT id FROM verbes WHERE infinitif = 'être'), 'passe_simple', 'je', 'Je', 'surpris par cette nouvelle.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'passe_simple', 'tu', 'Tu', 'le premier à comprendre.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'passe_simple', 'il', 'Louis XIV', 'roi de France pendant 72 ans.', 'historique', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'passe_simple', 'nous', 'Nous', 'témoins de cet événement.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'passe_simple', 'vous', 'Vous', 'les premiers arrivés.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'passe_simple', 'ils', 'Les soldats', 'courageux au combat.', 'historique', '1', '10'),

-- AVOIR
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'passe_simple', 'je', 'J''', 'soudain une idée géniale.', 'récit', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'passe_simple', 'tu', 'Tu', 'le courage de parler.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'passe_simple', 'il', 'Il', 'un moment d''hésitation.', 'récit', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'passe_simple', 'nous', 'Nous', 'beaucoup de chance ce jour-là.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'passe_simple', 'vous', 'Vous', 'la bonne réponse.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'passe_simple', 'ils', 'Les explorateurs', 'enfin accès à la grotte.', 'historique', '1', '10'),

-- FAIRE
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'passe_simple', 'je', 'Je', 'un pas en avant.', 'récit', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'passe_simple', 'tu', 'Tu', 'preuve de courage.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'passe_simple', 'il', 'Napoléon', 'la guerre à toute l''Europe.', 'historique', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'passe_simple', 'nous', 'Nous', 'halte près de la rivière.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'passe_simple', 'vous', 'Vous', 'exactement ce qu''il fallait.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'passe_simple', 'ils', 'Les révolutionnaires', 'tomber la Bastille.', 'historique', '1', '10'),

-- DIRE
((SELECT id FROM verbes WHERE infinitif = 'dire'), 'passe_simple', 'je', 'Je lui', 'toute la vérité.', 'récit', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'dire'), 'passe_simple', 'tu', 'Tu ne', 'rien ce soir-là.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'dire'), 'passe_simple', 'il', 'Le roi', 'ces mots célèbres.', 'historique', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'dire'), 'passe_simple', 'nous', 'Nous leur', 'adieu avec émotion.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'dire'), 'passe_simple', 'vous', 'Vous', 'enfin la vérité.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'dire'), 'passe_simple', 'ils', 'Les témoins', 'ce qu''ils avaient vu.', 'récit', '1', '10'),

-- PRENDRE
((SELECT id FROM verbes WHERE infinitif = 'prendre'), 'passe_simple', 'je', 'Je', 'mon courage à deux mains.', 'récit', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'prendre'), 'passe_simple', 'tu', 'Tu', 'la bonne décision.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'prendre'), 'passe_simple', 'il', 'César', 'le pouvoir à Rome.', 'historique', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'prendre'), 'passe_simple', 'nous', 'Nous', 'la route du nord.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'prendre'), 'passe_simple', 'vous', 'Vous', 'place autour de la table.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'prendre'), 'passe_simple', 'ils', 'Les Vikings', 'la mer vers l''ouest.', 'historique', '1', '10'),

-- VENIR
((SELECT id FROM verbes WHERE infinitif = 'venir'), 'passe_simple', 'je', 'Je', 'dès que possible.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'venir'), 'passe_simple', 'tu', 'Tu', 'me voir ce jour-là.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'venir'), 'passe_simple', 'il', 'Un messager', 'annoncer la nouvelle.', 'historique', '1', '10'),
((SELECT id FROM verbes WHERE infinitif = 'venir'), 'passe_simple', 'nous', 'Nous', 'de loin pour cette fête.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'venir'), 'passe_simple', 'vous', 'Vous', 'à notre secours.', 'récit', '2', '10'),
((SELECT id FROM verbes WHERE infinitif = 'venir'), 'passe_simple', 'ils', 'Les Barbares', 'envahir l''Empire.', 'historique', '1', '10'),

-- ============================================
-- FUTUR SIMPLE
-- ============================================

-- ÊTRE
((SELECT id FROM verbes WHERE infinitif = 'être'), 'futur_simple', 'je', 'Demain, je', 'à l''heure pour le rendez-vous.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'futur_simple', 'tu', 'Tu', 'content de ce cadeau.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'futur_simple', 'il', 'Ce film', 'diffusé à la télévision.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'futur_simple', 'nous', 'Nous', 'présents à la réunion.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'futur_simple', 'vous', 'Vous', 'reçus par le directeur.', 'scolaire', '2', '9'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'futur_simple', 'ils', 'Les résultats', 'publiés la semaine prochaine.', 'scolaire', '1', '9'),

-- AVOIR
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'futur_simple', 'je', 'J''', 'dix-huit ans l''année prochaine.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'futur_simple', 'tu', 'Tu', 'bientôt ta réponse.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'futur_simple', 'il', 'Il', 'le temps de finir.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'futur_simple', 'nous', 'Nous', 'des vacances en août.', 'loisirs', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'futur_simple', 'vous', 'Vous', 'une belle surprise !', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'futur_simple', 'ils', 'Les gagnants', 'un prix exceptionnel.', 'loisirs', '1', '9'),

-- FAIRE
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'futur_simple', 'je', 'Je', 'de mon mieux.', 'scolaire', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'futur_simple', 'tu', 'Tu', 'ce travail plus tard.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'futur_simple', 'il', 'Il', 'chaud cet été.', 'météo', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'futur_simple', 'nous', 'Nous', 'une grande fête pour ton anniversaire.', 'quotidien', '1', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'futur_simple', 'vous', 'Vous', 'connaissance avec nos voisins.', 'quotidien', '2', '9'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'futur_simple', 'ils', 'Les ouvriers', 'les réparations demain.', 'quotidien', '1', '9'),

-- ============================================
-- SUBJONCTIF PRÉSENT (11e année)
-- ============================================

-- ÊTRE
((SELECT id FROM verbes WHERE infinitif = 'être'), 'subjonctif_present', 'je', 'Il faut que je', 'à l''heure.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'subjonctif_present', 'tu', 'Je veux que tu', 'heureux.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'subjonctif_present', 'il', 'Il est important qu''il', 'présent.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'subjonctif_present', 'nous', 'Elle souhaite que nous', 'ensemble.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'subjonctif_present', 'vous', 'Je doute que vous', 'prêts.', 'quotidien', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'être'), 'subjonctif_present', 'ils', 'Il est nécessaire qu''ils', 'informés.', 'quotidien', '1', '11'),

-- AVOIR
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'subjonctif_present', 'je', 'Pourvu que j''', 'assez de temps.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'subjonctif_present', 'tu', 'Il faut que tu', 'confiance en toi.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'subjonctif_present', 'il', 'Je souhaite qu''il', 'du succès.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'subjonctif_present', 'nous', 'Il est essentiel que nous', 'les bons outils.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'subjonctif_present', 'vous', 'Je désire que vous', 'une bonne note.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'avoir'), 'subjonctif_present', 'ils', 'Il est impératif qu''ils', 'l''information.', 'quotidien', '1', '11'),

-- FAIRE
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'subjonctif_present', 'je', 'Il faut que je', 'attention.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'subjonctif_present', 'tu', 'Je veux que tu', 'tes devoirs.', 'scolaire', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'subjonctif_present', 'il', 'Il est temps qu''il', 'un choix.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'subjonctif_present', 'nous', 'Il importe que nous', 'des efforts.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'subjonctif_present', 'vous', 'Je demande que vous', 'silence.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'faire'), 'subjonctif_present', 'ils', 'Il est indispensable qu''ils', 'leur travail.', 'quotidien', '1', '11'),

-- POUVOIR
((SELECT id FROM verbes WHERE infinitif = 'pouvoir'), 'subjonctif_present', 'je', 'Pourvu que je', 'réussir cet examen.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'pouvoir'), 'subjonctif_present', 'tu', 'Il faut que tu', 'venir demain.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'pouvoir'), 'subjonctif_present', 'il', 'Je doute qu''il', 'finir à temps.', 'quotidien', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'pouvoir'), 'subjonctif_present', 'nous', 'Il est important que nous', 'nous exprimer librement.', 'société', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'pouvoir'), 'subjonctif_present', 'vous', 'Je souhaite que vous', 'comprendre cette leçon.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'pouvoir'), 'subjonctif_present', 'ils', 'Il est essentiel qu''ils', 'participer au débat.', 'société', '2', '11'),

-- SAVOIR
((SELECT id FROM verbes WHERE infinitif = 'savoir'), 'subjonctif_present', 'je', 'Il faut que je', 'la vérité.', 'quotidien', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'savoir'), 'subjonctif_present', 'tu', 'Je veux que tu', 'nager avant l''été.', 'loisirs', '1', '11'),
((SELECT id FROM verbes WHERE infinitif = 'savoir'), 'subjonctif_present', 'il', 'Il est temps qu''il', 'se débrouiller seul.', 'quotidien', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'savoir'), 'subjonctif_present', 'nous', 'Il est important que nous', 'parler plusieurs langues.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'savoir'), 'subjonctif_present', 'vous', 'Il est nécessaire que vous', 'utiliser cet outil.', 'scolaire', '2', '11'),
((SELECT id FROM verbes WHERE infinitif = 'savoir'), 'subjonctif_present', 'ils', 'Je doute qu''ils', 'la réponse.', 'scolaire', '2', '11');
