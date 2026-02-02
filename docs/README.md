# FRANCOPHILE.CH - Documentation du projet

## Vue d'ensemble

Francophile est une plateforme d'exercices de français pour les élèves de 9e à 11e année (système suisse), alignée sur le Plan d'études romand (PER).

### Fonctionnalités principales

1. **Gestion des utilisateurs**
   - Comptes enseignants (création de classes, suivi des élèves)
   - Comptes élèves (pseudo + mot de passe, liés à une classe)
   - Administration (gestion globale, paramètres)

2. **Modules d'exercices**
   - Conjugaison (génération dynamique)
   - Orthographe (homophones, accords)
   - *(À venir : grammaire, vocabulaire, etc.)*

3. **Gamification**
   - Points par exercice réussi (10/20/30 selon niveau)
   - Malus en cas d'erreur (2/5/8 selon niveau)
   - Badges thématiques alignés PER (9e, 10e, 11e × niv. 1, 2, 3)
   - Niveaux globaux par domaine

---

## Architecture technique

### Stack
- **Backend** : PHP 8+ (vanilla, bien structuré)
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML5, CSS3, JavaScript (vanilla)
- **Hébergement** : Infomaniak (mutualisé)

### Structure des fichiers

```
francophile.ch/
├── public/                     # Racine web (document root)
│   ├── index.php              # Page d'accueil
│   ├── login.php              # Connexion
│   ├── register.php           # Inscription enseignant
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   │   └── app.js
│   │   └── images/
│   ├── eleve/                 # Espace élève
│   │   ├── dashboard.php
│   │   ├── exercices.php
│   │   └── profil.php
│   ├── enseignant/            # Espace enseignant
│   │   ├── dashboard.php
│   │   ├── classes.php
│   │   ├── exercices.php
│   │   └── resultats.php
│   └── admin/                 # Administration
│       ├── dashboard.php
│       ├── config.php
│       └── badges.php
├── src/
│   ├── config/
│   │   └── config.php         # Configuration globale
│   ├── classes/
│   │   ├── Database.php       # Connexion PDO
│   │   ├── Conjugueur.php     # Moteur de conjugaison
│   │   ├── Session.php        # Gestion des sessions
│   │   ├── User.php           # Classe utilisateur
│   │   ├── Eleve.php          # Classe élève
│   │   ├── Enseignant.php     # Classe enseignant
│   │   ├── Classe.php         # Classe (groupe d'élèves)
│   │   ├── Exercice.php       # Classe exercice générique
│   │   ├── ExerciceConjugaison.php
│   │   ├── ExerciceOrthographe.php
│   │   ├── Progression.php    # Gestion des points/niveaux
│   │   └── Badge.php          # Gestion des badges
│   ├── includes/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── functions.php
│   └── api/                   # Endpoints AJAX
│       ├── verifier_reponse.php
│       ├── get_exercices.php
│       └── save_session.php
└── database/
    ├── schema.sql             # Structure des tables
    ├── verbes_per.sql         # Liste des verbes
    ├── phrases_conjugaison.sql
    └── exercices_orthographe.sql
```

---

## Base de données

### Tables principales

| Table | Description |
|-------|-------------|
| `enseignants` | Comptes enseignants |
| `classes` | Classes (liées à un enseignant) |
| `eleves` | Comptes élèves (liés à une classe) |
| `progression_eleves` | Points par domaine |
| `badges` | Définition des badges |
| `badges_eleves` | Badges obtenus |
| `verbes` | Liste des verbes PER |
| `conjugaisons` | Formes conjuguées |
| `phrases_conjugaison` | Phrases contextuelles |
| `categories_orthographe` | Types d'exercices ortho |
| `exercices_orthographe` | Exercices homophones/accords |
| `sessions_exercices` | Sessions de travail |
| `reponses_exercices` | Détail des réponses |
| `config_points` | Paramètres de points |
| `config_niveaux` | Paliers de niveaux |

---

## Module Conjugaison

### Fonctionnement

1. L'enseignant sélectionne :
   - Verbes (depuis la liste PER)
   - Temps (présent, imparfait, passé simple, etc.)
   - Mode d'exercice (pronoms seuls ou phrases)
   - Niveau de difficulté (1, 2 ou 3)

2. Le système génère automatiquement les exercices :
   - **Mode pronoms** : `Je (faire, présent) → ?` ➝ `fais`
   - **Mode phrases** : `Demain, je ___ (faire) mes devoirs.` ➝ `ferai`

3. Correction automatique :
   - Pas de tolérance sur les accents
   - Feedback immédiat
   - Explication en cas d'erreur

### Verbes par année

| Année | Types de verbes |
|-------|-----------------|
| 9e | Auxiliaires, 1er/2e groupe réguliers, 3e groupe essentiels |
| 10e | Verbes en -cer/-ger/-yer, composés, 3e groupe courants |
| 11e | Verbes défectifs, littéraires, particularités |

---

## Module Orthographe

### Catégories implémentées

**Homophones grammaticaux :**
- a / à
- et / est / ai
- son / sont
- on / ont
- ou / où
- ce / se
- ces / ses / c'est / s'est
- leur / leurs
- la / là / l'a

**Homophones lexicaux :**
- vers / vert / verre / ver
- cou / coup / coût
- sot / seau / sceau / saut
- mer / mère / maire
- fin / faim

**Accords :**
- Sujet-verbe
- Dans le groupe nominal
- Participe passé

### Types d'exercices

1. **Choix multiple** : Sélectionner la bonne forme
2. **Texte à trous** : Compléter avec la forme correcte
3. **Correction** : Trouver et corriger l'erreur
4. **Dictée** : Transcrire un texte audio

---

## Système de gamification

### Points

| Niveau difficulté | Points gagnés | Malus erreur |
|-------------------|---------------|--------------|
| Niveau 1 | 10 | -2 |
| Niveau 2 | 20 | -5 |
| Niveau 3 | 30 | -8 |

**Bonus :**
- Série de 5 bonnes réponses : +10
- Série de 10 bonnes réponses : +25
- Session sans erreur : +15

### Niveaux globaux (par domaine)

| Niveau | Points requis | Titre |
|--------|---------------|-------|
| 1 | 0 | Débutant |
| 2 | 100 | Apprenti |
| 3 | 300 | Intermédiaire |
| 4 | 600 | Avancé |
| 5 | 1000 | Expert |
| 6 | 1500 | Maître |

### Badges

Structure : `[Domaine]_[Sous-catégorie]_[Année]_[Niveau]`

Exemples :
- `conj_present_9_1` : Apprenti du présent (9e)
- `ortho_homo_a_9_2` : Artisan a/à (9e)
- `conj_passe_simple_10_3` : Maître du passé simple (10e)

---

## Prochaines étapes

### Phase 1 (MVP)
- [x] Schéma de base de données
- [x] Liste des verbes PER
- [x] Classe Conjugueur
- [x] Exercices homophones
- [ ] Interface de connexion
- [ ] Dashboard élève
- [ ] Module conjugaison (génération)
- [ ] Module orthographe (homophones)
- [ ] Système de points basique

### Phase 2
- [ ] Dashboard enseignant
- [ ] Création de classes
- [ ] Suivi des résultats
- [ ] Badges et niveaux complets
- [ ] Exercices d'accords

### Phase 3
- [ ] Interface d'administration
- [ ] Création d'exercices par l'enseignant
- [ ] Dictées audio
- [ ] Export des résultats
- [ ] Statistiques avancées

---

## Installation sur Infomaniak

1. Créer une base de données MySQL
2. Importer les fichiers SQL dans l'ordre :
   - `schema.sql`
   - `verbes_per.sql`
   - `phrases_conjugaison.sql`
   - `exercices_orthographe.sql`
3. Configurer `src/config/config.php` avec les identifiants
4. Uploader les fichiers via FTP
5. Pointer le domaine vers `/public`

---

## Contact

Projet développé pour l'enseignement du français en Suisse romande.
