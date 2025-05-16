<?php
/**
 * EATLY Application Test Script
 * 
 * This script checks if your environment meets the requirements for running EATLY.
 */

echo "EATLY Environment Test\n";
echo "======================\n\n";

// Check PHP version
echo "PHP Version: " . phpversion() . "\n";
if (version_compare(phpversion(), '7.4.0', '<')) {
    echo "❌ PHP version must be 7.4 or higher\n";
} else {
    echo "✅ PHP version OK\n";
}

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'session', 'gd'];
echo "\nRequired Extensions:\n";
foreach ($requiredExtensions as $extension) {
    if (extension_loaded($extension)) {
        echo "✅ $extension: Loaded\n";
    } else {
        echo "❌ $extension: Not loaded\n";
    }
}

// Check if config file exists
echo "\nConfiguration Files:\n";
if (file_exists('config/database.php')) {
    echo "✅ Database config: Found\n";
    
    // Try to include the database config
    include_once 'config/database.php';
    
    // Check if database constants are defined
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        echo "✅ Database config: Properly configured\n";
    } else {
        echo "❌ Database config: Missing required constants\n";
    }
} else {
    echo "❌ Database config: Not found\n";
}

// Check directory permissions
echo "\nDirectory Permissions:\n";
$directories = ['assets', 'views', 'models', 'controllers', 'includes'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_readable($dir)) {
            echo "✅ $dir: Readable\n";
        } else {
            echo "❌ $dir: Not readable\n";
        }
    } else {
        echo "❌ $dir: Not found\n";
    }
}

// Try to connect to the database
echo "\nDatabase Connection:\n";
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Database connection: Successful\n";
        
        // Check if tables exist
        $tables = ['users', 'recipes', 'ingredients', 'bookmarks'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table': Exists\n";
            } else {
                echo "❌ Table '$table': Does not exist\n";
            }
        }
    } catch (PDOException $e) {
        echo "❌ Database connection: Failed - " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Database connection: Cannot test (config not loaded)\n";
}

echo "\nTest completed. If you see any ❌ errors above, please fix them before running the application.\n";
?>
