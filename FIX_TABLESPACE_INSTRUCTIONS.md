# Correction du problème de tablespace MySQL pour la table migrations

## Problème
Erreur MySQL : "Tablespace for table '`saas_master`.`migrations`' exists. Please DISCARD the tablespace before IMPORT"

Cela se produit lorsque le fichier `.ibd` (tablespace) existe dans le répertoire de données MySQL, mais que la définition de la table n'existe pas dans la base de données.

## Solution

### Option 1 : Supprimer le fichier tablespace manuellement (Recommandé)

1. **Arrêter le service MySQL :**
   - Ouvrez le panneau de contrôle WAMP/XAMPP
   - Arrêtez le service MySQL

2. **Supprimer le fichier tablespace orphelin :**
   - Naviguez vers : `C:\wamp64\bin\mysql\mysql8.2.0\data\saas_master\`
   - Supprimez le fichier : `migrations.ibd`
   - (Si vous utilisez XAMPP, le chemin serait : `C:\xampp\mysql\data\saas_master\migrations.ibd`)

3. **Démarrer le service MySQL :**
   - Démarrez MySQL depuis le panneau de contrôle WAMP/XAMPP

4. **Exécuter les migrations :**
   ```bash
   php artisan migrate --database=saas_master --force
   ```

### Option 2 : Utiliser la ligne de commande MySQL (Alternative)

Si vous ne pouvez pas arrêter MySQL, vous pouvez essayer cette approche :

1. **Ouvrir la ligne de commande MySQL :**
   ```bash
   mysql -u root -p
   ```

2. **Exécuter ces commandes :**
   ```sql
   USE saas_master;
   
   -- Essayer de créer une table factice pour tester
   CREATE TABLE migrations_dummy LIKE migrations;
   
   -- Si cela fonctionne, supprimer les deux
   DROP TABLE IF EXISTS migrations_dummy;
   DROP TABLE IF EXISTS migrations;
   ```

   Si la méthode ci-dessus ne fonctionne pas, vous devrez utiliser l'Option 1.

### Option 3 : Script PowerShell (Automatisé)

Si vous préférez une solution automatisée, vous pouvez créer un script PowerShell pour arrêter MySQL, supprimer le fichier et redémarrer MySQL. Cependant, cela nécessite des privilèges d'administrateur.

## Après la correction

Une fois le fichier tablespace supprimé, exécutez :

```bash
php artisan migrate --database=saas_master --force
```

Les migrations devraient maintenant s'exécuter avec succès !

## Commandes utiles

### Vérifier si le fichier existe
```powershell
Test-Path "C:\wamp64\bin\mysql\mysql8.2.0\data\saas_master\migrations.ibd"
```

### Supprimer le fichier (si MySQL est arrêté)
```powershell
Remove-Item "C:\wamp64\bin\mysql\mysql8.2.0\data\saas_master\migrations.ibd" -Force
```
