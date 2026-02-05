<?php
/**
 * Classe Exercice - Gestion et génération des exercices
 */
class Exercice {
    
    private PDO $db;
    private Conjugueur $conjugueur;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conjugueur = new Conjugueur();
    }
    
    // ========================================
    // CONJUGAISON
    // ========================================
    
    /**
     * Obtenir la liste des verbes disponibles
     */
    public function getVerbes(?string $annee = null, ?string $groupe = null): array {
        $sql = 'SELECT * FROM verbes WHERE actif = 1';
        $params = [];
        
        if ($annee) {
            $sql .= ' AND annee_per = ?';
            $params[] = $annee;
        }
        
        if ($groupe) {
            $sql .= ' AND groupe = ?';
            $params[] = $groupe;
        }
        
        $sql .= ' ORDER BY annee_per, groupe, infinitif';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir les temps disponibles par année
     */
    public function getTempsParAnnee(): array {
        return [
            '9' => [
                'present' => 'Présent de l\'indicatif',
                'imparfait' => 'Imparfait',
                'futur_simple' => 'Futur simple',
                'passe_compose' => 'Passé composé',
                'imperatif_present' => 'Impératif présent'
            ],
            '10' => [
                'present' => 'Présent de l\'indicatif',
                'imparfait' => 'Imparfait',
                'passe_simple' => 'Passé simple',
                'futur_simple' => 'Futur simple',
                'passe_compose' => 'Passé composé',
                'plus_que_parfait' => 'Plus-que-parfait',
                'conditionnel_present' => 'Conditionnel présent',
                'imperatif_present' => 'Impératif présent'
            ],
            '11' => [
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
                'imperatif_present' => 'Impératif présent'
            ]
        ];
    }
    
    /**
     * Générer des exercices de conjugaison
     */
    public function genererExercicesConjugaison(array $verbesIds, array $temps, string $mode = 'pronoms', int $nombre = 10, string $niveau = '1'): array {
        $exercices = [];
        $personnes = ['je', 'tu', 'il', 'nous', 'vous', 'ils'];
        $personnesImperatif = ['tu', 'nous', 'vous'];
        
        // Récupérer les infos des verbes
        $placeholders = implode(',', array_fill(0, count($verbesIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM verbes WHERE id IN ($placeholders)");
        $stmt->execute($verbesIds);
        $verbes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $verbesIndex = array_column($verbes, null, 'id');
        
        // Générer les combinaisons possibles
        $combinaisons = [];
        foreach ($verbesIds as $verbeId) {
            foreach ($temps as $t) {
                $listePersonnes = ($t === 'imperatif_present') ? $personnesImperatif : $personnes;
                foreach ($listePersonnes as $p) {
                    $combinaisons[] = [
                        'verbe_id' => $verbeId,
                        'temps' => $t,
                        'personne' => $p
                    ];
                }
            }
        }
        
        // Mélanger et prendre le nombre demandé
        shuffle($combinaisons);
        $combinaisons = array_slice($combinaisons, 0, $nombre);
        
        foreach ($combinaisons as $combo) {
            $verbe = $verbesIndex[$combo['verbe_id']];
            $infinitif = $verbe['infinitif'];
            $t = $combo['temps'];
            $p = $combo['personne'];
            
            // Obtenir la forme correcte
            $formeCorrecte = $this->conjugueur->conjuguer($infinitif, $t, $p);
            if ($formeCorrecte === null) continue;
            
            if ($mode === 'phrases') {
                // Mode phrases contextuelles
                $phrase = $this->getPhraseContextuelle($combo['verbe_id'], $t, $p, $niveau);
                if ($phrase) {
                    $exercices[] = [
                        'type' => 'conjugaison_phrase',
                        'verbe_id' => $combo['verbe_id'],
                        'infinitif' => $infinitif,
                        'temps' => $t,
                        'temps_label' => TEMPS_CONJUGAISON[$t] ?? $t,
                        'personne' => $p,
                        'question' => $phrase['phrase_avant'] . ' ___ ' . ($phrase['phrase_apres'] ?? ''),
                        'indication' => "($infinitif, " . (TEMPS_CONJUGAISON[$t] ?? $t) . ")",
                        'reponse_correcte' => $formeCorrecte,
                        'niveau' => $niveau
                    ];
                } else {
                    // Fallback vers mode pronoms si pas de phrase
                    $exercices[] = $this->creerExercicePronoms($infinitif, $t, $p, $formeCorrecte, $combo['verbe_id'], $niveau);
                }
            } else {
                // Mode pronoms seuls
                $exercices[] = $this->creerExercicePronoms($infinitif, $t, $p, $formeCorrecte, $combo['verbe_id'], $niveau);
            }
        }
        
        return $exercices;
    }
    
    /**
     * Créer un exercice en mode pronoms
     */
    private function creerExercicePronoms(string $infinitif, string $temps, string $personne, string $formeCorrecte, int $verbeId, string $niveau): array {
        $pronomAffiche = PERSONNES[$personne] ?? $personne;
        
        return [
            'type' => 'conjugaison_pronom',
            'verbe_id' => $verbeId,
            'infinitif' => $infinitif,
            'temps' => $temps,
            'temps_label' => TEMPS_CONJUGAISON[$temps] ?? $temps,
            'personne' => $personne,
            'question' => "$pronomAffiche ($infinitif)",
            'indication' => TEMPS_CONJUGAISON[$temps] ?? $temps,
            'reponse_correcte' => $formeCorrecte,
            'niveau' => $niveau
        ];
    }
    
    /**
     * Obtenir une phrase contextuelle
     */
    private function getPhraseContextuelle(int $verbeId, string $temps, string $personne, string $niveau): ?array {
        $stmt = $this->db->prepare('
            SELECT * FROM phrases_conjugaison
            WHERE verbe_id = ? AND temps = ? AND personne = ?
            AND (niveau_difficulte <= ? OR niveau_difficulte IS NULL)
        ');
        $stmt->execute([$verbeId, $temps, $personne, $niveau]);
        $phrases = $stmt->fetchAll();
        if (empty($phrases)) return null;
        return $phrases[array_rand($phrases)];
    }
    
    /**
     * Vérifier une réponse de conjugaison
     */
    public function verifierConjugaison(string $infinitif, string $temps, string $personne, string $reponse): array {
        $formeCorrecte = $this->conjugueur->conjuguer($infinitif, $temps, $personne);
        $reponseNormalisee = mb_strtolower(trim($reponse));
        $formeNormalisee = mb_strtolower($formeCorrecte);
        
        $correct = $reponseNormalisee === $formeNormalisee;
        
        return [
            'correct' => $correct,
            'reponse_donnee' => $reponse,
            'reponse_correcte' => $formeCorrecte,
            'explication' => $correct ? null : "La forme correcte est : $formeCorrecte"
        ];
    }
    
    // ========================================
    // ORTHOGRAPHE
    // ========================================
    
    /**
     * Obtenir les catégories d'orthographe
     */
    public function getCategoriesOrthographe(?string $type = null): array {
        $sql = 'SELECT * FROM categories_orthographe';
        $params = [];
        
        if ($type) {
            $sql .= ' WHERE type = ?';
            $params[] = $type;
        }
        
        $sql .= ' ORDER BY type, nom';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Générer des exercices d'orthographe (enrichis avec formats variés)
     */
    public function genererExercicesOrthographe(array $categoriesIds, int $nombre = 10, string $niveau = '1', ?string $annee = null): array {
        $placeholders = implode(',', array_fill(0, count($categoriesIds), '?'));
        $params = $categoriesIds;
        
        $sql = "
            SELECT e.*, c.nom as categorie_nom, c.type as categorie_type
            FROM exercices_orthographe e
            JOIN categories_orthographe c ON e.categorie_id = c.id
            WHERE e.categorie_id IN ($placeholders)
            AND e.actif = 1
            AND e.niveau_difficulte <= ?
        ";
        $params[] = $niveau;
        
        if ($annee) {
            $sql .= ' AND (e.annee_cible IS NULL OR e.annee_cible <= ?)';
            $params[] = $annee;
        }
        
        // Étape 1 : récupérer les IDs éligibles (rapide, pas de RAND)
        $sqlLimit = (int)($nombre * 2);
        $sql .= " ORDER BY e.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $exercicesDb = $stmt->fetchAll();
        
        // Étape 2 : mélanger côté PHP (beaucoup plus rapide que ORDER BY RAND)
        shuffle($exercicesDb);
        $exercicesDb = array_slice($exercicesDb, 0, $sqlLimit);
        
        $exercices = [];
        $formatIndex = 0;
        
        foreach ($exercicesDb as $ex) {
            if (count($exercices) >= $nombre) break;
            
            $options = [$ex['reponse_correcte']];
            if (!empty($ex['options_incorrectes'])) {
                $incorrectes = json_decode($ex['options_incorrectes'], true);
                if (is_array($incorrectes)) {
                    $options = array_merge($options, $incorrectes);
                }
            }
            shuffle($options);
            
            // Base commune
            $base = [
                'type' => 'orthographe_' . ($ex['type_exercice'] ?? 'choix_multiple'),
                'id' => $ex['id'],
                'categorie' => $ex['categorie_nom'],
                'categorie_type' => $ex['categorie_type'],
                'explication' => $ex['explication'] ?? null,
                'niveau' => $ex['niveau_difficulte'],
                'groupe_homophones' => $ex['groupe_homophones'] ?? null,
                'reponse_correcte' => $ex['reponse_correcte']
            ];
            
            // Niveau 1 : QCM classique uniquement
            if ((int)$niveau <= 1) {
                $exercices[] = array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options
                ]);
                continue;
            }
            
            // Niveau 2+ : varier les formats
            $format = match($formatIndex % 5) {
                0 => 'qcm',
                1 => 'vrai_faux',
                2 => 'input',
                3 => 'qcm',     // 2x QCM pour garder un bon ratio
                4 => 'vrai_faux',
                default => 'qcm'
            };
            $formatIndex++;
            
            if ($format === 'vrai_faux') {
                $incorrectes = json_decode($ex['options_incorrectes'] ?? '[]', true);
                if (!empty($incorrectes) && random_int(0, 1) === 0) {
                    // Montrer la phrase avec une erreur
                    $mauvaise = $incorrectes[array_rand($incorrectes)];
                    $exercices[] = array_merge($base, [
                        'format' => 'vrai_faux',
                        'question' => str_replace('___', $mauvaise, $ex['phrase']),
                        'indication' => 'Cette phrase est-elle correcte ?',
                        'reponse_correcte' => 'faux',
                        'correction' => str_replace('___', $ex['reponse_correcte'], $ex['phrase'])
                    ]);
                } else {
                    // Montrer la phrase correcte
                    $exercices[] = array_merge($base, [
                        'format' => 'vrai_faux',
                        'question' => str_replace('___', $ex['reponse_correcte'], $ex['phrase']),
                        'indication' => 'Cette phrase est-elle correcte ?',
                        'reponse_correcte' => 'vrai',
                        'correction' => null
                    ]);
                }
            } elseif ($format === 'input') {
                $exercices[] = array_merge($base, [
                    'format' => 'input',
                    'question' => $ex['phrase'],
                    'indication' => 'Écris le mot manquant'
                ]);
            } else {
                // QCM classique
                $exercices[] = array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options
                ]);
            }
        }
        
        shuffle($exercices);
        return array_slice($exercices, 0, $nombre);
    }
    
    /**
     * Convertir un exercice brut dans un format spécifique
     */
    private function convertToFormat(array $ex, array $options, string $format, string $niveau): ?array {
        $base = [
            'type' => 'orthographe_' . $ex['type_exercice'],
            'id' => $ex['id'],
            'categorie' => $ex['categorie_nom'],
            'categorie_type' => $ex['categorie_type'],
            'explication' => $ex['explication'],
            'niveau' => $ex['niveau_difficulte'],
            'groupe_homophones' => $ex['groupe_homophones'] ?? null
        ];
        
        switch ($format) {
            case 'qcm':
                shuffle($options);
                return array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options,
                    'reponse_correcte' => $ex['reponse_correcte']
                ]);
                
            case 'vrai_faux':
                // 50% chance: montrer la phrase correcte, 50% avec une erreur
                $showCorrect = random_int(0, 1) === 1;
                if ($showCorrect) {
                    // Phrase avec la bonne réponse
                    $phraseComplete = str_replace('___', $ex['reponse_correcte'], $ex['phrase']);
                    return array_merge($base, [
                        'format' => 'vrai_faux',
                        'question' => $phraseComplete,
                        'indication' => 'Cette phrase est-elle correcte ?',
                        'reponse_correcte' => 'vrai',
                        'correction' => null
                    ]);
                } else {
                    // Phrase avec une mauvaise réponse
                    $incorrectes = json_decode($ex['options_incorrectes'] ?? '[]', true);
                    if (empty($incorrectes)) return null;
                    $mauvaise = $incorrectes[array_rand($incorrectes)];
                    $phraseIncorrecte = str_replace('___', $mauvaise, $ex['phrase']);
                    $phraseCorrecte = str_replace('___', $ex['reponse_correcte'], $ex['phrase']);
                    return array_merge($base, [
                        'format' => 'vrai_faux',
                        'question' => $phraseIncorrecte,
                        'indication' => 'Cette phrase est-elle correcte ?',
                        'reponse_correcte' => 'faux',
                        'correction' => $phraseCorrecte
                    ]);
                }
                
            case 'transformation':
                // Transformer la phrase en changeant le sujet ou le nombre
                $transformations = $this->genererTransformation($ex);
                if ($transformations) {
                    return array_merge($base, $transformations);
                }
                // Fallback to QCM
                shuffle($options);
                return array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options,
                    'reponse_correcte' => $ex['reponse_correcte']
                ]);
                
            case 'intrus':
                // Needs grouped exercises, handled separately
                shuffle($options);
                return array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options,
                    'reponse_correcte' => $ex['reponse_correcte']
                ]);
                
            case 'multi_trous':
                // Need multiple blanks, handled by grouping
                shuffle($options);
                return array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options,
                    'reponse_correcte' => $ex['reponse_correcte']
                ]);
                
            default:
                shuffle($options);
                return array_merge($base, [
                    'format' => 'qcm',
                    'question' => $ex['phrase'],
                    'options' => $options,
                    'reponse_correcte' => $ex['reponse_correcte']
                ]);
        }
    }
    
    /**
     * Générer une transformation à partir d'un exercice
     */
    private function genererTransformation(array $ex): ?array {
        // Transformations possibles pour les homophones
        $consignes = [
            "Réécris la phrase en remplaçant le sujet par « nous ».",
            "Réécris la phrase au pluriel.",
            "Réécris la phrase en remplaçant le sujet par « ils ».",
            "Réécris cette phrase à la forme négative.",
        ];
        
        // Simple: demander de compléter la phrase en écrivant le mot manquant
        if (strpos($ex['phrase'], '___') !== false) {
            return [
                'format' => 'input',
                'question' => $ex['phrase'],
                'indication' => 'Écris le mot manquant (pas de choix multiple cette fois !)',
                'reponse_correcte' => $ex['reponse_correcte']
            ];
        }
        
        return null;
    }
    
    /**
     * Générer des exercices de type "intrus"
     */
    private function genererExercicesIntrus(array $categoriesIds, int $nombre, string $niveau): array {
        $exercices = [];
        $placeholders = implode(',', array_fill(0, count($categoriesIds), '?'));
        
        // Récupérer des groupes d'homophones
        $intrusLimit = (int)$nombre;
        $stmt = $this->db->prepare("
            SELECT DISTINCT groupe_homophones FROM exercices_orthographe 
            WHERE categorie_id IN ($placeholders) AND groupe_homophones IS NOT NULL AND actif = 1
        ");
        $stmt->execute($categoriesIds);
        $groupes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        shuffle($groupes);
        $groupes = array_slice($groupes, 0, $intrusLimit);
        
        foreach ($groupes as $groupe) {
            // Pour chaque groupe, créer un exercice intrus
            $mots = explode('/', $groupe);
            if (count($mots) < 2) continue;
            
            // L'intrus est un homophone d'un autre groupe
            $stmt2 = $this->db->prepare("
                SELECT DISTINCT groupe_homophones FROM exercices_orthographe 
                WHERE categorie_id IN ($placeholders) AND groupe_homophones IS NOT NULL 
                AND groupe_homophones != ? AND actif = 1
            ");
            $stmt2->execute(array_merge($categoriesIds, [$groupe]));
            $autresGroupes = $stmt2->fetchAll(PDO::FETCH_COLUMN);
            $autreGroupe = !empty($autresGroupes) ? $autresGroupes[array_rand($autresGroupes)] : null;
            
            if ($autreGroupe) {
                $autresMots = explode('/', $autreGroupe);
                $intrus = $autresMots[array_rand($autresMots)];
                $items = array_merge($mots, [$intrus]);
                
                $exercices[] = [
                    'type' => 'orthographe_intrus',
                    'id' => 0,
                    'format' => 'intrus',
                    'question' => 'Quel mot n\'appartient pas au même groupe d\'homophones ?',
                    'indication' => 'Trouve l\'intrus parmi ces mots',
                    'items' => $items,
                    'reponse_correcte' => $intrus,
                    'explication' => "Le groupe « $groupe » ne contient pas « $intrus » (qui fait partie de « $autreGroupe »).",
                    'niveau' => $niveau
                ];
            }
        }
        
        return $exercices;
    }
    
    /**
     * Générer des exercices de conjugaison enrichis (formats variés)
     */
    public function genererExercicesConjugaisonEnrichis(array $verbesIds, array $temps, string $mode, int $nombre, string $niveau): array {
        try {
            // D'abord, obtenir les exercices classiques
            $exercicesBase = $this->genererExercicesConjugaison($verbesIds, $temps, $mode, $nombre * 2, $niveau);
        
        $exercices = [];
        $formatCycle = 0;
        
        foreach ($exercicesBase as $ex) {
            if (count($exercices) >= $nombre) break;
            
            $format = 'input'; // default for conjugation
            
            if ((int)$niveau >= 2) {
                // Varier les formats
                switch ($formatCycle % 4) {
                    case 0: $format = 'input'; break;
                    case 1: $format = 'qcm'; break;
                    case 2: $format = 'vrai_faux'; break;
                    case 3: $format = 'transformation'; break;
                }
                $formatCycle++;
            }
            
            switch ($format) {
                case 'qcm':
                    // Générer des options incorrectes pour la conjugaison
                    $options = [$ex['reponse_correcte']];
                    $fausses = $this->genererFaussesConjugaisons($ex['infinitif'], $ex['temps'], $ex['personne'], $ex['reponse_correcte']);
                    $options = array_merge($options, array_slice($fausses, 0, 3));
                    shuffle($options);
                    $ex['format'] = 'qcm';
                    $ex['options'] = $options;
                    break;
                    
                case 'vrai_faux':
                    $showCorrect = random_int(0, 1) === 1;
                    $pronomAffiche = PERSONNES[$ex['personne']] ?? $ex['personne'];
                    if ($showCorrect) {
                        $ex['format'] = 'vrai_faux';
                        $ex['question'] = "$pronomAffiche {$ex['reponse_correcte']}";
                        $ex['indication'] = "({$ex['infinitif']}, " . (TEMPS_CONJUGAISON[$ex['temps']] ?? $ex['temps']) . ") — Correct ?";
                        $ex['reponse_correcte'] = 'vrai';
                        $ex['correction'] = null;
                    } else {
                        $fausses = $this->genererFaussesConjugaisons($ex['infinitif'], $ex['temps'], $ex['personne'], $ex['reponse_correcte']);
                        if (!empty($fausses)) {
                            $fausse = $fausses[0];
                            $ex['format'] = 'vrai_faux';
                            $ex['correction'] = "$pronomAffiche {$ex['reponse_correcte']}";
                            $ex['question'] = "$pronomAffiche $fausse";
                            $ex['indication'] = "({$ex['infinitif']}, " . (TEMPS_CONJUGAISON[$ex['temps']] ?? $ex['temps']) . ") — Correct ?";
                            $ex['reponse_correcte'] = 'faux';
                        }
                    }
                    break;
                    
                case 'transformation':
                    // Changer de personne
                    $personnes = ['je', 'tu', 'il', 'nous', 'vous', 'ils'];
                    $autrePersonne = $personnes[array_rand($personnes)];
                    while ($autrePersonne === $ex['personne']) $autrePersonne = $personnes[array_rand($personnes)];
                    $nouvelleFormeCorrecte = $this->conjugueur->conjuguer($ex['infinitif'], $ex['temps'], $autrePersonne);
                    if ($nouvelleFormeCorrecte) {
                        $pronomOriginal = PERSONNES[$ex['personne']] ?? $ex['personne'];
                        $pronomNouveau = PERSONNES[$autrePersonne] ?? $autrePersonne;
                        $ex['format'] = 'transformation';
                        $ex['consigne'] = "Conjugue avec « $pronomNouveau » au lieu de « $pronomOriginal »";
                        $ex['question'] = "$pronomOriginal {$ex['reponse_correcte']}";
                        $ex['reponse_correcte'] = "$pronomNouveau $nouvelleFormeCorrecte";
                    } else {
                        $ex['format'] = 'input';
                    }
                    break;
                    
                default:
                    $ex['format'] = 'input';
                    break;
            }
            
            $exercices[] = $ex;
        }
        
        shuffle($exercices);
        return array_slice($exercices, 0, $nombre);
        } catch (\Throwable $e) {
            // Fallback : retourner les exercices de base avec format input
            $exercicesBase = $this->genererExercicesConjugaison($verbesIds, $temps, $mode, $nombre, $niveau);
            foreach ($exercicesBase as &$ex) {
                if (!isset($ex['format'])) {
                    $ex['format'] = 'input';
                }
            }
            unset($ex);
            return $exercicesBase;
        }
    }
    
    /**
     * Générer des formes incorrectes de conjugaison (pour QCM et vrai/faux)
     */
    private function genererFaussesConjugaisons(string $infinitif, string $temps, string $personne, string $formeCorrecte): array {
        $fausses = [];
        $personnes = ['je', 'tu', 'il', 'nous', 'vous', 'ils'];
        
        // Prendre les formes d'autres personnes
        foreach ($personnes as $p) {
            if ($p === $personne) continue;
            $forme = $this->conjugueur->conjuguer($infinitif, $temps, $p);
            if ($forme && mb_strtolower($forme) !== mb_strtolower($formeCorrecte)) {
                $fausses[] = $forme;
            }
            if (count($fausses) >= 3) break;
        }
        
        // Erreurs classiques si pas assez
        if (count($fausses) < 3) {
            $erreurs = [
                $formeCorrecte . 's',
                $formeCorrecte . 'e',
                rtrim($formeCorrecte, 's'),
                rtrim($formeCorrecte, 'e'),
                str_replace('é', 'er', $formeCorrecte),
                str_replace('er', 'é', $formeCorrecte),
            ];
            foreach ($erreurs as $err) {
                if ($err !== $formeCorrecte && !in_array($err, $fausses) && strlen($err) > 1) {
                    $fausses[] = $err;
                }
                if (count($fausses) >= 3) break;
            }
        }
        
        return array_slice($fausses, 0, 3);
    }
    
    /**
     * Vérifier une réponse d'orthographe
     */
    public function verifierOrthographe(int $exerciceId, string $reponse): array {
        $stmt = $this->db->prepare('SELECT * FROM exercices_orthographe WHERE id = ?');
        $stmt->execute([$exerciceId]);
        $exercice = $stmt->fetch();
        
        if (!$exercice) {
            return ['correct' => false, 'erreur' => 'Exercice non trouvé'];
        }
        
        $reponseNormalisee = mb_strtolower(trim($reponse));
        $correcteNormalisee = mb_strtolower(trim($exercice['reponse_correcte']));
        
        $correct = $reponseNormalisee === $correcteNormalisee;
        
        return [
            'correct' => $correct,
            'reponse_donnee' => $reponse,
            'reponse_correcte' => $exercice['reponse_correcte'],
            'explication' => $exercice['explication']
        ];
    }
    
    // ========================================
    // SESSIONS ET RÉSULTATS
    // ========================================
    
    /**
     * Créer une nouvelle session d'exercices
     */
    public function creerSession(int $eleveId, string $domaine, int $nombreQuestions, string $niveau, ?string $sousCategorie = null, ?string $annee = null): int {
        $stmt = $this->db->prepare('
            INSERT INTO sessions_exercices 
            (eleve_id, domaine, sous_categorie, nombre_questions, niveau_difficulte, annee_cible, date_debut)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$eleveId, $domaine, $sousCategorie, $nombreQuestions, $niveau, $annee]);
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Enregistrer une réponse
     */
    public function enregistrerReponse(int $sessionId, string $exerciceType, int $exerciceId, string $questionPosee, string $reponseAttendue, string $reponseDonnee, bool $correct, ?int $tempsReponse = null): void {
        $stmt = $this->db->prepare('
            INSERT INTO reponses_exercices
            (session_id, exercice_type, exercice_id, question_posee, reponse_attendue, reponse_donnee, est_correct, temps_reponse)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $sessionId, $exerciceType, $exerciceId, $questionPosee, 
            $reponseAttendue, $reponseDonnee, $correct ? 1 : 0, $tempsReponse
        ]);
        
        // Mettre à jour le compteur de la session
        if ($correct) {
            $stmt = $this->db->prepare('UPDATE sessions_exercices SET nombre_correct = nombre_correct + 1 WHERE id = ?');
            $stmt->execute([$sessionId]);
        }
    }
    
    /**
     * Terminer une session et calculer les points
     */
    public function terminerSession(int $sessionId): array {
        // Récupérer la session
        $stmt = $this->db->prepare('SELECT * FROM sessions_exercices WHERE id = ?');
        $stmt->execute([$sessionId]);
        $session = $stmt->fetch();
        
        if (!$session) {
            return ['erreur' => 'Session non trouvée'];
        }
        
        $niveau = $session['niveau_difficulte'] ?? '1';
        $nbCorrect = $session['nombre_correct'];
        $nbTotal = $session['nombre_questions'];
        $nbErreurs = $nbTotal - $nbCorrect;
        
        // Récupérer la config des points
        $stmt = $this->db->prepare('SELECT cle, valeur FROM config_points');
        $stmt->execute();
        $configPoints = [];
        foreach ($stmt->fetchAll() as $row) {
            $configPoints[$row['cle']] = (int) $row['valeur'];
        }
        
        // Calculer les points
        $pointsBase = match($niveau) {
            '1' => $configPoints['points_niveau_1'] ?? 10,
            '2' => $configPoints['points_niveau_2'] ?? 20,
            '3' => $configPoints['points_niveau_3'] ?? 30,
            default => 10
        };
        
        $malusBase = match($niveau) {
            '1' => $configPoints['malus_erreur_niveau_1'] ?? 2,
            '2' => $configPoints['malus_erreur_niveau_2'] ?? 5,
            '3' => $configPoints['malus_erreur_niveau_3'] ?? 8,
            default => 2
        };
        
        $pointsGagnes = $nbCorrect * $pointsBase;
        $pointsPerdus = $nbErreurs * $malusBase;
        
        // Bonus
        $bonus = 0;
        if ($nbErreurs === 0 && $nbTotal >= 5) {
            $bonus += $configPoints['bonus_sans_erreur'] ?? 15;
        }
        
        $pointsNets = $pointsGagnes - $pointsPerdus + $bonus;
        
        // Mettre à jour la session
        $stmt = $this->db->prepare('
            UPDATE sessions_exercices 
            SET date_fin = NOW(), points_gagnes = ?, points_perdus = ?
            WHERE id = ?
        ');
        $stmt->execute([$pointsGagnes + $bonus, $pointsPerdus, $sessionId]);
        
        // Mettre à jour la progression de l'élève
        $eleve = Eleve::findById($session['eleve_id']);
        $progressionResult = $eleve->ajouterPoints($session['domaine'], $pointsNets);
        
        return [
            'session_id' => $sessionId,
            'nombre_questions' => $nbTotal,
            'nombre_correct' => $nbCorrect,
            'nombre_erreurs' => $nbErreurs,
            'taux_reussite' => round(($nbCorrect / $nbTotal) * 100, 1),
            'points_gagnes' => $pointsGagnes,
            'points_perdus' => $pointsPerdus,
            'bonus' => $bonus,
            'points_nets' => $pointsNets,
            'progression' => $progressionResult
        ];
    }
}
