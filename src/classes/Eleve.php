<?php
/**
 * Classe Eleve
 */
class Eleve extends User {
    
    private ?int $classeId = null;
    private ?string $pseudo = null;
    private ?array $classeData = null;
    
    // Getters spécifiques
    public function getClasseId(): ?int { return $this->classeId; }
    public function getPseudo(): ?string { return $this->pseudo; }
    
    /**
     * Charger depuis un tableau
     */
    protected function loadFromArray(array $data): void {
        $this->id = (int) $data['id'];
        $this->classeId = (int) $data['classe_id'];
        $this->pseudo = $data['pseudo'];
        $this->prenom = $data['prenom'] ?? null;
        $this->nom = $data['nom'] ?? null;
        $this->dateCreation = $data['date_creation'] ?? null;
        $this->derniereConnexion = $data['derniere_connexion'] ?? null;
        $this->actif = (bool) ($data['actif'] ?? true);
    }
    
    /**
     * Trouver par ID
     */
    public static function findById(int $id): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM eleves WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if (!$data) return null;
        
        $eleve = new self();
        $eleve->loadFromArray($data);
        return $eleve;
    }
    
    /**
     * Trouver par pseudo et code classe
     */
    public static function findByPseudoAndCode(string $pseudo, string $codeClasse): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT e.* FROM eleves e
            JOIN classes c ON e.classe_id = c.id
            WHERE e.pseudo = ? AND c.code_classe = ? AND e.actif = 1 AND c.actif = 1
        ');
        $stmt->execute([$pseudo, strtoupper($codeClasse)]);
        $data = $stmt->fetch();
        
        if (!$data) return null;
        
        $eleve = new self();
        $eleve->loadFromArray($data);
        return $eleve;
    }
    
    /**
     * Authentification élève
     */
    public static function authenticate(string $pseudo, string $codeClasse, string $password): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('
            SELECT e.* FROM eleves e
            JOIN classes c ON e.classe_id = c.id
            WHERE e.pseudo = ? AND c.code_classe = ? AND e.actif = 1 AND c.actif = 1
        ');
        $stmt->execute([$pseudo, strtoupper($codeClasse)]);
        $data = $stmt->fetch();
        
        if (!$data || !self::verifyPassword($password, $data['mot_de_passe'])) {
            return null;
        }
        
        $eleve = new self();
        $eleve->loadFromArray($data);
        $eleve->updateLastLogin();
        
        return $eleve;
    }
    
    /**
     * Créer un nouvel élève
     */
    public static function create(int $classeId, string $pseudo, string $password, ?string $prenom = null, ?string $nom = null): ?self {
        $db = Database::getInstance();
        
        // Vérifier si le pseudo existe déjà dans cette classe
        $stmt = $db->prepare('SELECT id FROM eleves WHERE classe_id = ? AND pseudo = ?');
        $stmt->execute([$classeId, $pseudo]);
        if ($stmt->fetch()) {
            return null; // Pseudo déjà utilisé dans cette classe
        }
        
        $hash = self::hashPassword($password);
        
        $stmt = $db->prepare('
            INSERT INTO eleves (classe_id, pseudo, mot_de_passe, prenom, nom, date_creation)
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$classeId, $pseudo, $hash, $prenom, $nom]);
        
        $id = (int) $db->lastInsertId();
        
        // Initialiser les progressions pour tous les domaines
        $domaines = ['conjugaison', 'orthographe', 'grammaire', 'vocabulaire', 'figures_style', 'comprehension_texte', 'comprehension_orale', 'production_ecrite'];
        foreach ($domaines as $domaine) {
            $stmt = $db->prepare('INSERT INTO progression_eleves (eleve_id, domaine, points_totaux, niveau_actuel) VALUES (?, ?, 0, 1)');
            $stmt->execute([$id, $domaine]);
        }
        
        return self::findById($id);
    }
    
    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void {
        $stmt = $this->db->prepare('UPDATE eleves SET derniere_connexion = NOW() WHERE id = ?');
        $stmt->execute([$this->id]);
        $this->derniereConnexion = date('Y-m-d H:i:s');
    }
    
    /**
     * Obtenir les informations de la classe
     */
    public function getClasse(): ?array {
        if ($this->classeData === null) {
            $stmt = $this->db->prepare('SELECT * FROM classes WHERE id = ?');
            $stmt->execute([$this->classeId]);
            $this->classeData = $stmt->fetch() ?: null;
        }
        return $this->classeData;
    }
    
    /**
     * Obtenir l'année scolaire
     */
    public function getAnneeScolaire(): ?string {
        $classe = $this->getClasse();
        return $classe['annee_scolaire'] ?? null;
    }
    
    /**
     * Obtenir la progression dans tous les domaines
     */
    public function getProgressions(): array {
        $stmt = $this->db->prepare('
            SELECT pe.*, cn.nom_niveau, cn.points_requis as points_niveau_actuel,
                   (SELECT points_requis FROM config_niveaux cn2 WHERE cn2.domaine = pe.domaine AND cn2.niveau = pe.niveau_actuel + 1) as points_prochain_niveau
            FROM progression_eleves pe
            LEFT JOIN config_niveaux cn ON pe.domaine = cn.domaine AND pe.niveau_actuel = cn.niveau
            WHERE pe.eleve_id = ?
            ORDER BY pe.domaine
        ');
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir la progression d'un domaine spécifique
     */
    public function getProgression(string $domaine): ?array {
        $stmt = $this->db->prepare('
            SELECT pe.*, cn.nom_niveau, cn.points_requis as points_niveau_actuel,
                   (SELECT points_requis FROM config_niveaux WHERE domaine = pe.domaine AND niveau = pe.niveau_actuel + 1) as points_prochain_niveau
            FROM progression_eleves pe
            LEFT JOIN config_niveaux cn ON pe.domaine = cn.domaine AND pe.niveau_actuel = cn.niveau
            WHERE pe.eleve_id = ? AND pe.domaine = ?
        ');
        $stmt->execute([$this->id, $domaine]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Ajouter des points et mettre à jour le niveau si nécessaire
     */
    public function ajouterPoints(string $domaine, int $points): array {
        $progression = $this->getProgression($domaine);
        $nouveauxPoints = max(0, $progression['points_totaux'] + $points); // Pas de points négatifs
        
        // Calculer le nouveau niveau
        $stmt = $this->db->prepare('
            SELECT niveau FROM config_niveaux 
            WHERE domaine = ? AND points_requis <= ?
            ORDER BY niveau DESC LIMIT 1
        ');
        $stmt->execute([$domaine, $nouveauxPoints]);
        $nouveauNiveau = (int) ($stmt->fetchColumn() ?: 1);
        
        // Mettre à jour
        $stmt = $this->db->prepare('
            UPDATE progression_eleves 
            SET points_totaux = ?, niveau_actuel = ?, date_maj = NOW()
            WHERE eleve_id = ? AND domaine = ?
        ');
        $stmt->execute([$nouveauxPoints, $nouveauNiveau, $this->id, $domaine]);
        
        $levelUp = $nouveauNiveau > $progression['niveau_actuel'];
        
        return [
            'points_ajoutes' => $points,
            'points_totaux' => $nouveauxPoints,
            'niveau' => $nouveauNiveau,
            'level_up' => $levelUp
        ];
    }
    
    /**
     * Obtenir les badges obtenus
     */
    public function getBadges(): array {
        $stmt = $this->db->prepare('
            SELECT b.*, be.date_obtention
            FROM badges b
            JOIN badges_eleves be ON b.id = be.badge_id
            WHERE be.eleve_id = ?
            ORDER BY be.date_obtention DESC
        ');
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir les badges en cours (pas encore obtenus)
     */
    public function getBadgesEnCours(): array {
        $stmt = $this->db->prepare('
            SELECT b.*, COALESCE(pb.points_actuels, 0) as points_actuels
            FROM badges b
            LEFT JOIN points_badges pb ON b.id = pb.badge_id AND pb.eleve_id = ?
            LEFT JOIN badges_eleves be ON b.id = be.badge_id AND be.eleve_id = ?
            WHERE be.id IS NULL AND b.actif = 1
            ORDER BY b.domaine, b.sous_categorie
        ');
        $stmt->execute([$this->id, $this->id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Ajouter des points vers un badge
     */
    public function ajouterPointsBadge(int $badgeId, int $points): ?array {
        // Vérifier si le badge n'est pas déjà obtenu
        $stmt = $this->db->prepare('SELECT id FROM badges_eleves WHERE eleve_id = ? AND badge_id = ?');
        $stmt->execute([$this->id, $badgeId]);
        if ($stmt->fetch()) {
            return null; // Badge déjà obtenu
        }
        
        // Obtenir les infos du badge
        $stmt = $this->db->prepare('SELECT * FROM badges WHERE id = ?');
        $stmt->execute([$badgeId]);
        $badge = $stmt->fetch();
        if (!$badge) return null;
        
        // Mettre à jour ou créer les points
        $stmt = $this->db->prepare('
            INSERT INTO points_badges (eleve_id, badge_id, points_actuels)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE points_actuels = points_actuels + ?
        ');
        $stmt->execute([$this->id, $badgeId, $points, $points]);
        
        // Vérifier si le badge est obtenu
        $stmt = $this->db->prepare('SELECT points_actuels FROM points_badges WHERE eleve_id = ? AND badge_id = ?');
        $stmt->execute([$this->id, $badgeId]);
        $pointsActuels = (int) $stmt->fetchColumn();
        
        $badgeObtenu = false;
        if ($pointsActuels >= $badge['points_requis']) {
            $stmt = $this->db->prepare('INSERT INTO badges_eleves (eleve_id, badge_id) VALUES (?, ?)');
            $stmt->execute([$this->id, $badgeId]);
            $badgeObtenu = true;
        }
        
        return [
            'badge' => $badge,
            'points_actuels' => $pointsActuels,
            'badge_obtenu' => $badgeObtenu
        ];
    }
    
    /**
     * Obtenir l'historique des sessions
     */
    public function getHistorique(int $limit = 10): array {
        $stmt = $this->db->prepare('
            SELECT * FROM sessions_exercices
            WHERE eleve_id = ?
            ORDER BY date_debut DESC
            LIMIT ?
        ');
        $stmt->execute([$this->id, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir les statistiques globales
     */
    public function getStatistiques(): array {
        // Total exercices
        $stmt = $this->db->prepare('SELECT SUM(nombre_questions) FROM sessions_exercices WHERE eleve_id = ?');
        $stmt->execute([$this->id]);
        $totalExercices = (int) $stmt->fetchColumn();
        
        // Total correct
        $stmt = $this->db->prepare('SELECT SUM(nombre_correct) FROM sessions_exercices WHERE eleve_id = ?');
        $stmt->execute([$this->id]);
        $totalCorrect = (int) $stmt->fetchColumn();
        
        // Taux de réussite
        $tauxReussite = $totalExercices > 0 ? round(($totalCorrect / $totalExercices) * 100, 1) : 0;
        
        // Nombre de badges
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM badges_eleves WHERE eleve_id = ?');
        $stmt->execute([$this->id]);
        $nbBadges = (int) $stmt->fetchColumn();
        
        // Points totaux
        $stmt = $this->db->prepare('SELECT SUM(points_totaux) FROM progression_eleves WHERE eleve_id = ?');
        $stmt->execute([$this->id]);
        $pointsTotaux = (int) $stmt->fetchColumn();
        
        return [
            'total_exercices' => $totalExercices,
            'total_correct' => $totalCorrect,
            'taux_reussite' => $tauxReussite,
            'nb_badges' => $nbBadges,
            'points_totaux' => $pointsTotaux
        ];
    }
    
    /**
     * Exporter en tableau pour la session
     */
    public function toArray(): array {
        $classe = $this->getClasse();
        return [
            'id' => $this->id,
            'pseudo' => $this->pseudo,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'classe_id' => $this->classeId,
            'classe_nom' => $classe['nom'] ?? null,
            'annee_scolaire' => $classe['annee_scolaire'] ?? null
        ];
    }
}
