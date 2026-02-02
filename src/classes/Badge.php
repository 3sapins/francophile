<?php
/**
 * Classe Badge - Gestion des badges et de leur attribution
 */
class Badge {
    
    private PDO $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Vérifier et attribuer les badges après une session
     */
    public function verifierEtAttribuerBadges(int $eleveId, string $domaine, array $sessionData): array {
        $badgesObtenus = [];
        $eleve = Eleve::findById($eleveId);
        if (!$eleve) return [];
        
        $annee = $eleve->getAnneeScolaire();
        $stats = $this->getStatsEleve($eleveId, $domaine);
        
        // Badges potentiels non encore obtenus
        $stmt = $this->db->prepare('
            SELECT b.* FROM badges b
            LEFT JOIN badges_eleves be ON b.id = be.badge_id AND be.eleve_id = ?
            WHERE b.domaine = ? AND b.actif = 1 AND be.id IS NULL
            AND (b.annee_cible IS NULL OR b.annee_cible <= ?)
        ');
        $stmt->execute([$eleveId, $domaine, $annee]);
        $badgesPotentiels = $stmt->fetchAll();
        
        foreach ($badgesPotentiels as $badge) {
            if ($this->verifierConditions($badge, $stats, $sessionData)) {
                $this->attribuerBadge($eleveId, $badge['id']);
                $badgesObtenus[] = $badge;
            }
        }
        
        return $badgesObtenus;
    }
    
    private function getStatsEleve(int $eleveId, string $domaine): array {
        $stmt = $this->db->prepare('SELECT * FROM progression_eleves WHERE eleve_id = ? AND domaine = ?');
        $stmt->execute([$eleveId, $domaine]);
        $progression = $stmt->fetch() ?: ['points_totaux' => 0, 'niveau_actuel' => 1];
        
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM sessions_exercices WHERE eleve_id = ? AND domaine = ?');
        $stmt->execute([$eleveId, $domaine]);
        $nbSessions = (int) $stmt->fetchColumn();
        
        $stmt = $this->db->prepare('
            SELECT SUM(nombre_questions) as total, SUM(nombre_correct) as correct
            FROM sessions_exercices WHERE eleve_id = ? AND domaine = ?
        ');
        $stmt->execute([$eleveId, $domaine]);
        $totaux = $stmt->fetch();
        
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM sessions_exercices 
            WHERE eleve_id = ? AND domaine = ? 
            AND nombre_correct = nombre_questions AND nombre_questions >= 5
        ');
        $stmt->execute([$eleveId, $domaine]);
        $sessionsParfaites = (int) $stmt->fetchColumn();
        
        $stmt = $this->db->prepare('
            SELECT sous_categorie, SUM(nombre_questions) as total, SUM(nombre_correct) as correct
            FROM sessions_exercices WHERE eleve_id = ? AND domaine = ?
            GROUP BY sous_categorie
        ');
        $stmt->execute([$eleveId, $domaine]);
        $parSousCategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'points' => $progression['points_totaux'],
            'niveau' => $progression['niveau_actuel'],
            'nb_sessions' => $nbSessions,
            'total_questions' => $totaux['total'] ?? 0,
            'total_correct' => $totaux['correct'] ?? 0,
            'taux_reussite' => ($totaux['total'] ?? 0) > 0 ? round(($totaux['correct'] / $totaux['total']) * 100) : 0,
            'sessions_parfaites' => $sessionsParfaites,
            'par_sous_categorie' => $parSousCategorie
        ];
    }
    
    private function verifierConditions(array $badge, array $stats, array $sessionData): bool {
        $conditions = json_decode($badge['conditions'] ?? '{}', true);
        
        if (empty($conditions)) {
            return $stats['points'] >= ($badge['points_requis'] ?? 0);
        }
        
        foreach ($conditions as $type => $valeur) {
            switch ($type) {
                case 'points_min':
                    if ($stats['points'] < $valeur) return false;
                    break;
                case 'sessions_min':
                    if ($stats['nb_sessions'] < $valeur) return false;
                    break;
                case 'questions_min':
                    if ($stats['total_questions'] < $valeur) return false;
                    break;
                case 'taux_min':
                    if ($stats['taux_reussite'] < $valeur) return false;
                    break;
                case 'sessions_parfaites_min':
                    if ($stats['sessions_parfaites'] < $valeur) return false;
                    break;
                case 'session_parfaite':
                    if ($sessionData['nombre_correct'] != $sessionData['nombre_questions']) return false;
                    if ($sessionData['nombre_questions'] < ($valeur['questions_min'] ?? 5)) return false;
                    break;
            }
        }
        return true;
    }
    
    public function attribuerBadge(int $eleveId, int $badgeId): bool {
        $stmt = $this->db->prepare('SELECT id FROM badges_eleves WHERE eleve_id = ? AND badge_id = ?');
        $stmt->execute([$eleveId, $badgeId]);
        if ($stmt->fetch()) return false;
        
        $stmt = $this->db->prepare('INSERT INTO badges_eleves (eleve_id, badge_id, date_obtention) VALUES (?, ?, NOW())');
        return $stmt->execute([$eleveId, $badgeId]);
    }
    
    public function getBadgesEleve(int $eleveId): array {
        $stmt = $this->db->prepare('
            SELECT b.*, be.date_obtention FROM badges b
            JOIN badges_eleves be ON b.id = be.badge_id
            WHERE be.eleve_id = ? ORDER BY be.date_obtention DESC
        ');
        $stmt->execute([$eleveId]);
        return $stmt->fetchAll();
    }
    
    public function getBadgesDisponibles(int $eleveId, ?string $annee = null): array {
        $sql = 'SELECT b.*, 
                CASE WHEN be.id IS NOT NULL THEN 1 ELSE 0 END as obtenu,
                be.date_obtention
                FROM badges b
                LEFT JOIN badges_eleves be ON b.id = be.badge_id AND be.eleve_id = ?
                WHERE b.actif = 1';
        $params = [$eleveId];
        
        if ($annee) {
            $sql .= ' AND (b.annee_cible IS NULL OR b.annee_cible <= ?)';
            $params[] = $annee;
        }
        
        $sql .= ' ORDER BY b.domaine, b.sous_categorie, b.niveau_difficulte';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
