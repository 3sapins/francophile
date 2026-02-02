#!/bin/bash
# Script d'installation de la base de données Francophile

echo "🚀 Installation de la base de données Francophile..."

# Attendre que MySQL soit prêt (pour Docker)
if [ "$1" == "--docker" ]; then
    echo "⏳ Attente de MySQL..."
    sleep 10
fi

# Variables (à adapter si nécessaire)
DB_HOST=${DB_HOST:-localhost}
DB_USER=${DB_USER:-root}
DB_PASS=${DB_PASS:-}
DB_NAME=${DB_NAME:-francophile}

# Fonction pour exécuter SQL
run_sql() {
    if [ -z "$DB_PASS" ]; then
        mysql -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" < "$1"
    else
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$1"
    fi
}

# Créer la base si elle n'existe pas
echo "📦 Création de la base de données..."
if [ -z "$DB_PASS" ]; then
    mysql -h "$DB_HOST" -u "$DB_USER" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
else
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi

# Exécuter les fichiers SQL dans l'ordre
echo "📋 Création des tables (schema.sql)..."
run_sql "database/schema.sql"

echo "📝 Insertion des verbes (verbes_per.sql)..."
run_sql "database/verbes_per.sql"

echo "💬 Insertion des phrases (phrases_conjugaison.sql)..."
run_sql "database/phrases_conjugaison.sql"

echo "✍️ Insertion des exercices d'accords..."
run_sql "database/exercices_accords.sql"

echo "🏆 Insertion des badges..."
run_sql "database/badges_complets.sql"

# Créer un compte enseignant de test
echo "👤 Création d'un compte enseignant de test..."
if [ -z "$DB_PASS" ]; then
    mysql -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" -e "
    INSERT INTO enseignants (email, mot_de_passe, prenom, nom, etablissement, est_admin, date_creation) 
    VALUES ('test@francophile.ch', '\$2y\$12\$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4C/pPtYgcM7UvAHa', 'Test', 'Enseignant', 'École de test', 0, NOW())
    ON DUPLICATE KEY UPDATE email=email;
    "
else
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
    INSERT INTO enseignants (email, mot_de_passe, prenom, nom, etablissement, est_admin, date_creation) 
    VALUES ('test@francophile.ch', '\$2y\$12\$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4C/pPtYgcM7UvAHa', 'Test', 'Enseignant', 'École de test', 0, NOW())
    ON DUPLICATE KEY UPDATE email=email;
    "
fi

echo ""
echo "✅ Installation terminée !"
echo ""
echo "📌 Compte enseignant de test :"
echo "   Email: test@francophile.ch"
echo "   Mot de passe: password123"
echo ""
echo "🌐 Accédez à http://localhost:8080 (Docker) ou votre URL locale"
