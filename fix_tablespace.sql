-- Fix tablespace issue for migrations table in saas_master database
-- Run this script in MySQL: mysql -u root -p < fix_tablespace.sql
-- Or connect to MySQL and run these commands manually

USE saas_master;

-- Method 1: Try to create table, discard its tablespace, then import the orphaned one
-- If this fails, the tablespace file needs to be manually deleted

-- Step 1: Create table structure (this will fail if tablespace exists, so we catch the error)
-- We'll use a workaround: create with MyISAM first, then convert

SET @sql = 'CREATE TABLE IF NOT EXISTS migrations_temp LIKE migrations';
SET @sql = 'CREATE TABLE migrations_temp (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    migration VARCHAR(191) NOT NULL,
    batch INT NOT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

-- Actually, the best approach is to directly handle the orphaned tablespace
-- Create the table, immediately discard its tablespace, import the old one, then drop

-- For XAMPP/Windows, you may need to:
-- 1. Stop MySQL
-- 2. Delete the file: C:\xampp\mysql\data\saas_master\migrations.ibd
-- 3. Start MySQL
-- 4. Run: php artisan migrate --database=saas_master

-- OR use this SQL approach (run in MySQL command line):
-- The trick is to create the table, but MySQL won't let us because of the orphaned file
-- So we need to manually delete the .ibd file first

-- Alternative: Use MySQL's ability to force recreate
DROP TABLE IF EXISTS migrations;

-- If the above doesn't work, you need to manually delete:
-- C:\xampp\mysql\data\saas_master\migrations.ibd
-- Then run: php artisan migrate --database=saas_master

