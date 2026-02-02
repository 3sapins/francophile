<?php
/**
 * Classe User - Classe de base pour les utilisateurs
 */
abstract class User {
    
    protected PDO $db;
    protected ?int $id = null;
    protected ?string $prenom = null;
    protected ?string $nom = null;
    protected ?string $dateCreation = null;
    protected ?string $derniereConnexion = null;
    protected bool $actif = true;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Getters
    public function getId(): ?int { return $this->id; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function getNom(): ?string { return $this->nom; }
    public function getNomComplet(): string { return trim($this->prenom . ' ' . $this->nom); }
    public function getDateCreation(): ?string { return $this->dateCreation; }
    public function getDerniereConnexion(): ?string { return $this->derniereConnexion; }
    public function isActif(): bool { return $this->actif; }
    
    /**
     * Hasher un mot de passe
     */
    protected static function hashPassword(string $password): string {
        return password_hash($password, HASH_ALGO, ['cost' => HASH_COST]);
    }
    
    /**
     * Vérifier un mot de passe
     */
    protected static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Mettre à jour la dernière connexion
     */
    abstract public function updateLastLogin(): void;
    
    /**
     * Charger depuis un tableau de données
     */
    abstract protected function loadFromArray(array $data): void;
}
