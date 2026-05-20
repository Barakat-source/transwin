<?php
session_start();
require_once 'core/Database.php';
require_once 'core/Logger.php';
$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Fetch matricule before delete for logger
        $stmt = $db->prepare("SELECT matricule FROM vehicules WHERE id = ?");
        $stmt->execute([$id]);
        $matricule = $stmt->fetchColumn();

        if ($matricule) {
            $stmt = $db->prepare("DELETE FROM vehicules WHERE id = ?");
            $stmt->execute([$id]);

            Logger::log($db, "SUPPRESSION", "Suppression du véhicule $matricule");
        }

        header("Location: vehicules.php?msg=" . urlencode("Véhicule supprimé"));
        exit();
    } catch (PDOException $e) {
        header("Location: vehicules.php?err=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: vehicules.php");
    exit();
}
