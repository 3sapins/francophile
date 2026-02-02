# 📚 Francophile.ch

Plateforme d'exercices de français pour le cycle 3 (9e-11e années), alignée sur le Plan d'études romand (PER).

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Railway](https://img.shields.io/badge/Railway-Deploy-0B0D0E?style=flat&logo=railway&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🚀 Déploiement rapide sur Railway

[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/template)

### Étapes :
1. **Fork ce repo** sur GitHub
2. **Connecte-toi à [Railway](https://railway.app)** avec GitHub
3. **New Project → Deploy from GitHub repo**
4. **Ajoute une base MySQL** : New → Database → MySQL
5. **Importe la base** : Dans MySQL, exécute le contenu de `database/init.sql`
6. **C'est prêt !** Railway génère une URL automatiquement

### Variables d'environnement (automatiques avec Railway MySQL)
Railway configure automatiquement : `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

## ✨ Fonctionnalités

### Pour les élèves
- 🎯 Exercices de **conjugaison** (tous les temps, 40+ verbes irréguliers)
- ✍️ Exercices d'**orthographe** (homophones, accords sujet-verbe, GN, participe passé)
- 📊 Suivi de progression détaillé avec graphiques
- 🏆 Système de **badges** et points (gamification)
- 📈 Niveaux par domaine

### Pour les enseignants
- 👥 Gestion des classes avec code d'accès
- 📋 Suivi des résultats par élève
- 📊 Statistiques de classe
- 🏅 Classement des élèves

## 🚀 Installation rapide (Docker)

```bash
# Cloner le repo
git clone https://github.com/votre-username/francophile.git
cd francophile

# Lancer avec Docker
docker-compose up -d

# Accéder à l'application
# http://localhost:8080
# phpMyAdmin: http://localhost:8081
```

La base de données sera automatiquement initialisée avec les fichiers SQL.

## 💻 Installation manuelle (XAMPP/MAMP)

### Prérequis
- PHP 8.0+
- MySQL 8.0+
- Serveur web (Apache/Nginx)

### Étapes

1. **Cloner le projet**
```bash
git clone https://github.com/votre-username/francophile.git
```

2. **Configurer la base de données**
```bash
# Créer la base de données
mysql -u root -p -e "CREATE DATABASE francophile CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importer les fichiers SQL (dans l'ordre)
mysql -u root -p francophile < database/schema.sql
mysql -u root -p francophile < database/verbes_per.sql
mysql -u root -p francophile < database/phrases_conjugaison.sql
mysql -u root -p francophile < database/exercices_accords.sql
mysql -u root -p francophile < database/badges_complets.sql
```

3. **Configurer l'application**
```bash
# Copier et éditer la configuration
cp src/config/config.local.php src/config/config.php
# Modifier DB_HOST, DB_USER, DB_PASS selon votre configuration
```

4. **Configurer le serveur web**
- Pointer le DocumentRoot vers le dossier `public/`
- S'assurer que mod_rewrite est activé (Apache)

## 📁 Structure du projet

```
francophile/
├── database/               # Fichiers SQL
│   ├── schema.sql         # Structure des tables
│   ├── verbes_per.sql     # Liste des verbes
│   ├── phrases_conjugaison.sql
│   ├── exercices_accords.sql
│   └── badges_complets.sql
├── public/                 # Racine web
│   ├── index.php          # Page d'accueil
│   ├── login.php          # Connexion
│   ├── eleve/             # Espace élève
│   ├── enseignant/        # Espace enseignant
│   ├── api/               # Endpoints API
│   └── assets/            # CSS, JS
├── src/
│   ├── classes/           # Classes PHP
│   │   ├── Conjugueur.php # Moteur de conjugaison
│   │   ├── Exercice.php   # Générateur d'exercices
│   │   ├── Eleve.php
│   │   ├── Enseignant.php
│   │   └── Badge.php
│   ├── config/            # Configuration
│   ├── data/              # Données (verbes irréguliers)
│   └── includes/          # Header, footer
├── docker-compose.yml
└── README.md
```

## 🔑 Compte de test

Après installation :

| Type | Email / Pseudo | Mot de passe |
|------|---------------|--------------|
| Enseignant | test@francophile.ch | password123 |

Créez ensuite une classe et ajoutez des élèves depuis l'interface.

## 🎮 Système de gamification

### Points
| Niveau | Points/bonne réponse | Malus/erreur |
|--------|---------------------|--------------|
| 1 (facile) | 10 pts | -2 pts |
| 2 (moyen) | 20 pts | -5 pts |
| 3 (difficile) | 30 pts | -8 pts |

**Bonus** : +15 pts pour une session sans erreur (min. 5 questions)

### Badges
- 🥉 Bronze : Débutant
- 🥈 Argent : Intermédiaire  
- 🥇 Or : Expert

~60 badges disponibles couvrant tous les domaines.

## 📚 Contenu pédagogique

### Conjugaison
- **Temps simples** : présent, imparfait, passé simple, futur simple, conditionnel, subjonctif, impératif
- **Temps composés** : passé composé, plus-que-parfait, etc.
- **40+ verbes irréguliers** du 3e groupe entièrement conjugués
- **Modes** : exercices avec pronoms ou phrases contextuelles

### Orthographe
- **Homophones grammaticaux** : a/à, et/est, son/sont, on/ont, ou/où, ce/se, ces/ses/c'est/s'est, leur/leurs
- **Homophones lexicaux** : vers/vert/verre/ver, cou/coup/coût, etc.
- **Accords** : sujet-verbe, groupe nominal, participe passé (avec être, avoir, pronominaux)

## 🛠️ Développement

### Technologies
- **Backend** : PHP 8+ (POO, PDO)
- **Base de données** : MySQL 8
- **Frontend** : HTML5, CSS3 (vanilla), JavaScript (ES6+)
- **Pas de framework** : code simple et maintenable

### Contribuer
1. Fork le projet
2. Créer une branche (`git checkout -b feature/ma-fonctionnalite`)
3. Commit (`git commit -m 'Ajout de ma fonctionnalité'`)
4. Push (`git push origin feature/ma-fonctionnalite`)
5. Ouvrir une Pull Request

## 📄 Licence

MIT License - voir [LICENSE](LICENSE)

## 🙏 Crédits

Développé pour l'enseignement du français en Suisse romande.
