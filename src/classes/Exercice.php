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
            ORDER BY RAND()
            LIMIT 1
        ');
        $stmt->execute([$verbeId, $temps, $personne, $niveau]);
        return $stmt->fetch() ?: null;
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
     * Générer des exercices d'orthographe
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
        
        $sql .= ' ORDER BY RAND() LIMIT ?';
        $params[] = $nombre;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $exercicesDb = $stmt->fetchAll();
        
        $exercices = [];
        foreach ($exercicesDb as $ex) {
            $options = [$ex['reponse_correcte']];
            if ($ex['options_incorrectes']) {
                $incorrectes = json_decode($ex['options_incorrectes'], true);
                if (is_array($incorrectes)) {
                    $options = array_merge($options, $incorrectes);
                }
            }
            shuffle($options);
            
            $exercices[] = [
                'type' => 'orthographe_' . $ex['type_exercice'],
                'id' => $ex['id'],
                'categorie' => $ex['categorie_nom'],
                'categorie_type' => $ex['categorie_type'],
                'question' => $ex['phrase'],
                'options' => $options,
                'reponse_correcte' => $ex['reponse_correcte'],
                'explication' => $ex['explication'],
                'niveau' => $ex['niveau_difficulte'],
                'groupe_homophones' => $ex['groupe_homophones']
            ];
        }
        
        return $exercices;
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
