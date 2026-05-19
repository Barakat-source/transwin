<?php
/**
 * TRANSWIN e-OM - Core Database
 */

class Database {
    private $host = '127.0.0.1';
    private $port = '3309'; // Port XAMPP personnalisé
    private $db_name = 'transwin_db';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Tentative de connexion (ajustez le port si nécessaire : 3306 ou 3309)
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $exception) {
            // En production, ne pas afficher l'erreur brute
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
