<?php
/**
 * Classe Enseignant
 */
class Enseignant extends User {
    
    private ?string $email = null;
    private ?string $etablissement = null;
    private bool $estAdmin = false;
    
    // Getters spécifiques
    public function getEmail(): ?string { return $this->email; }
    public function getEtablissement(): ?string { return $this->etablissement; }
    public function isAdmin(): bool { return $this->estAdmin; }
    
    /**
     * Charger depuis un tableau
     */
    protected function loadFromArray(array $data): void {
        $this->id = (int) $data['id'];
        $this->email = $data['email'];
        $this->prenom = $data['prenom'];
        $this->nom = $data['nom'];
        $this->etablissement = $data['etablissement'] ?? null;
        $this->estAdmin = (bool) ($data['est_admin'] ?? false);
        $this->dateCreation = $data['date_creation'] ?? null;
        $this->derniereConnexion = $data['derniere_connexion'] ?? null;
        $this->actif = (bool) ($data['actif'] ?? true);
    }
    
    /**
     * Trouver par email
     */
    public static function findByEmail(string $email): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM enseignants WHERE email = ? AND actif = 1');
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        
        if (!$data) return null;
        
        $enseignant = new self();
        $enseignant->loadFromArray($data);
        return $enseignant;
    }
    
    /**
     * Trouver par ID
     */
    public static function findById(int $id): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM enseignants WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if (!$data) return null;
        
        $enseignant = new self();
        $enseignant->loadFromArray($data);
        return $enseignant;
    }
    
    /**
     * Authentification
     */
    public static function authenticate(string $email, string $password): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM enseignants WHERE email = ? AND actif = 1');
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        
        if (!$data || !self::verifyPassword($password, $data['mot_de_passe'])) {
            return null;
        }
        
        $enseignant = new self();
        $enseignant->loadFromArray($data);
        $enseignant->updateLastLogin();
        
        return $enseignant;
    }
    
    /**
     * Créer un nouveau compte enseignant
     */
    public static function create(string $email, string $password, string $prenom, string $nom, ?string $etablissement = null): ?self {
        $db = Database::getInstance();
        
        // Vérifier si l'email existe déjà
        $stmt = $db->prepare('SELECT id FROM enseignants WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return null; // Email déjà utilisé
        }
        
        $hash = self::hashPassword($password);
        
        $stmt = $db->prepare('
            INSERT INTO enseignants (email, mot_de_passe, prenom, nom, etablissement, date_creation)
            VALUES (?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$email, $hash, $prenom, $nom, $etablissement]);
        
        $id = (int) $db->lastInsertId();
        return self::findById($id);
    }
    
    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void {
        $stmt = $this->db->prepare('UPDATE enseignants SET derniere_connexion = NOW() WHERE id = ?');
        $stmt->execute([$this->id]);
        $this->derniereConnexion = date('Y-m-d H:i:s');
    }
    
    /**
     * Obtenir les classes de cet enseignant
     */
    public function getClasses(): array {
        $stmt = $this->db->prepare('
            SELECT * FROM classes 
            WHERE enseignant_id = ? AND actif = 1 
            ORDER BY annee_scolaire, nom
        ');
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Créer une nouvelle classe
     */
    public function creerClasse(string $nom, string $anneeScolaire): ?array {
        // Générer un code unique
        $code = strtoupper(substr(md5(uniqid()), 0, 6));
        
        $stmt = $this->db->prepare('
            INSERT INTO classes (enseignant_id, nom, annee_scolaire, code_classe, date_creation)
            VALUES (?, ?, ?, ?, NOW())
        ');
        $stmt->execute([$this->id, $nom, $anneeScolaire, $code]);
        
        $id = (int) $this->db->lastInsertId();
        
        $stmt = $this->db->prepare('SELECT * FROM classes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir les statistiques globales
     */
    public function getStatistiques(): array {
        // Nombre de classes
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM classes WHERE enseignant_id = ? AND actif = 1');
        $stmt->execute([$this->id]);
        $nbClasses = (int) $stmt->fetchColumn();
        
        // Nombre d'élèves
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM eleves e
            JOIN classes c ON e.classe_id = c.id
            WHERE c.enseignant_id = ? AND e.actif = 1 AND c.actif = 1
        ');
        $stmt->execute([$this->id]);
        $nbEleves = (int) $stmt->fetchColumn();
        
        // Nombre d'exercices réalisés
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM sessions_exercices se
            JOIN eleves e ON se.eleve_id = e.id
            JOIN classes c ON e.classe_id = c.id
            WHERE c.enseignant_id = ?
        ');
        $stmt->execute([$this->id]);
        $nbExercices = (int) $stmt->fetchColumn();
        
        return [
            'nb_classes' => $nbClasses,
            'nb_eleves' => $nbEleves,
            'nb_exercices' => $nbExercices
        ];
    }
    
    /**
     * Exporter en tableau pour la session
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'etablissement' => $this->etablissement,
            'est_admin' => $this->estAdmin
        ];
    }
}
