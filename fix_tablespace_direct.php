<?php

/**
 * Script de correction directe du tablespace MySQL
 * Ce script contourne Laravel et se connecte directement à MySQL pour corriger le problème de tablespace orphelin
 * 
 * Usage : php fix_tablespace_direct.php
 */

// Load environment variables (simple approach for XAMPP/WAMP)
$envFile = __DIR__ . '/.env';
$envVars = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Remove quotes if present
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        
        $envVars[$key] = $value;
    }
}

// Get database configuration
$host = $envVars['DB_HOST'] ?? '127.0.0.1';
$port = $envVars['DB_PORT'] ?? '3306';
$database = $envVars['SAAS_MASTER_DB'] ?? 'saas_master';
$username = $envVars['DB_USERNAME'] ?? 'root';
$password = $envVars['DB_PASSWORD'] ?? '';

echo "Connexion à MySQL...\n";
echo "Hôte : {$host}\n";
echo "Base de données : {$database}\n";
echo "Nom d'utilisateur : {$username}\n\n";

try {
    // Connect directly to MySQL
    $dsn = "mysql:host={$host};port={$port};charset=utf8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Connecté à MySQL\n\n";
    
    // Select the database
    $pdo->exec("USE `{$database}`");
    echo "✅ Utilisation de la base de données : {$database}\n\n";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "La table 'migrations' existe. Tentative de suppression du tablespace...\n";
        try {
            $pdo->exec("ALTER TABLE migrations DISCARD TABLESPACE");
            echo "✅ Tablespace supprimé\n";
        } catch (PDOException $e) {
            echo "⚠️  Impossible de supprimer le tablespace : " . $e->getMessage() . "\n";
            echo "Tentative de suppression de la table...\n";
            $pdo->exec("DROP TABLE migrations");
            echo "✅ Table supprimée\n";
        }
    } else {
        echo "La table 'migrations' n'existe pas dans le schéma.\n";
        echo "Fichier tablespace orphelin détecté.\n\n";
        
        echo "Tentative de création de la structure de table pour récupérer le tablespace...\n";
        
        // The issue: MySQL won't let us create the table because the .ibd file exists
        // Solution: We need to create it with a workaround
        
        // Try method 1: Create with MyISAM first (doesn't use .ibd files)
        try {
            echo "Méthode 1 : Création avec le moteur MyISAM...\n";
            $pdo->exec("
                CREATE TABLE migrations (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    migration VARCHAR(191) NOT NULL,
                    batch INT NOT NULL,
                    PRIMARY KEY (id)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
            ");
            echo "✅ Table créée avec MyISAM\n";
            
            // Convert to InnoDB (this will create a new .ibd file, ignoring the old one)
            echo "Conversion en InnoDB...\n";
            $pdo->exec("ALTER TABLE migrations ENGINE=InnoDB");
            echo "✅ Convertie en InnoDB\n";
            
            // Now drop it
            echo "Suppression de la table pour nettoyer...\n";
            $pdo->exec("DROP TABLE migrations");
            echo "✅ Table supprimée (tablespace nettoyé)\n";
            
        } catch (PDOException $e) {
            echo "❌ Méthode 1 échouée : " . $e->getMessage() . "\n\n";
            
            echo "⚠️  Correction automatique échouée. Intervention manuelle requise.\n\n";
            echo "Veuillez faire l'une des choses suivantes :\n\n";
            echo "Option 1 - Supprimer le fichier tablespace manuellement :\n";
            echo "1. Arrêtez le service MySQL\n";
            echo "2. Supprimez le fichier : C:\\xampp\\mysql\\data\\{$database}\\migrations.ibd\n";
            echo "   (ou C:\\wamp64\\bin\\mysql\\mysql8.2.0\\data\\{$database}\\migrations.ibd pour WAMP)\n";
            echo "3. Démarrez le service MySQL\n";
            echo "4. Exécutez : php artisan migrate --database=saas_master\n\n";
            
            echo "Option 2 - Utiliser la ligne de commande MySQL :\n";
            echo "1. Ouvrez la ligne de commande MySQL : mysql -u {$username} -p\n";
            echo "2. Exécutez : USE {$database};\n";
            echo "3. Exécutez : CREATE TABLE migrations (id INT UNSIGNED NOT NULL AUTO_INCREMENT, migration VARCHAR(191) NOT NULL, batch INT NOT NULL, PRIMARY KEY (id)) ENGINE=MyISAM;\n";
            echo "4. Exécutez : ALTER TABLE migrations ENGINE=InnoDB;\n";
            echo "5. Exécutez : DROP TABLE migrations;\n";
            echo "6. Puis exécutez : php artisan migrate --database=saas_master\n";
            
            exit(1);
        }
    }
    
    echo "\n✅ Problème de tablespace résolu !\n";
    echo "Vous pouvez maintenant exécuter : php artisan migrate --database=saas_master\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "\nVeuillez vérifier vos identifiants de base de données dans le fichier .env\n";
    exit(1);
}

