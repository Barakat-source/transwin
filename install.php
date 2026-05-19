<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

if (!$db) die("Erreur de connexion");

try {
    // Disable foreign key checks for clean setup
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");

    // 1. Create Users Table
    $db->exec("DROP TABLE IF EXISTS users");
    $db->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        role ENUM('ADMIN', 'EXPLOITANT', 'LECTURE') DEFAULT 'EXPLOITANT',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Create Clients Table
    $db->exec("DROP TABLE IF EXISTS clients");
    $db->exec("CREATE TABLE clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Create Chauffeurs Table
    $db->exec("DROP TABLE IF EXISTS chauffeurs");
    $db->exec("CREATE TABLE chauffeurs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20),
        license_number VARCHAR(50),
        status ENUM('ACTIF', 'INACTIF') DEFAULT 'ACTIF',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Create Vehicules Table
    $db->exec("DROP TABLE IF EXISTS vehicules");
    $db->exec("CREATE TABLE vehicules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        matricule VARCHAR(50) UNIQUE NOT NULL,
        brand VARCHAR(50),
        model VARCHAR(50),
        insurance_expiry DATE,
        tech_visit_expiry DATE,
        status ENUM('DISPONIBLE', 'MAINTENANCE', 'MISSION') DEFAULT 'DISPONIBLE',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Create Activity Logs Table
    $db->exec("DROP TABLE IF EXISTS activity_logs");
    $db->exec("CREATE TABLE activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(255),
        details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 6. Update Voyages Table for Attachments
    try {
        // We use a regular ALTER TABLE here without IF NOT EXISTS to be more compatible
        // The try-catch will handle cases where the column already exists
        $db->exec("ALTER TABLE voyages ADD COLUMN attachment_path VARCHAR(255) AFTER remorque_type");
    } catch (Exception $e) { /* Column might already exist */ }

    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 7. Migrate existing data to new tables
    $db->exec("INSERT IGNORE INTO clients (name) SELECT DISTINCT client FROM voyages");
    $db->exec("INSERT IGNORE INTO chauffeurs (name) SELECT DISTINCT chauffeur FROM voyages");
    $db->exec("INSERT IGNORE INTO vehicules (matricule) SELECT DISTINCT matricule FROM voyages");

    // 6. Add a default admin user if not exists
    $admin_user = 'admin';
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$admin_user]);
    if (!$stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, 'Administrateur PFE', 'ADMIN')");
        $stmt->execute([$admin_user, $admin_pass]);
    }

    echo "Migration réussie ! <br>";
    echo "Utilisateur par défaut : <b>admin</b> / <b>admin123</b> <br>";
    echo "<a href='index.php'>Retour au Dashboard</a>";

} catch (PDOException $e) {
    echo "Erreur lors de la migration : " . $e->getMessage();
}
