<?php
require_once 'core/Database.php';
require_once 'core/Logger.php';
$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $stmt = $db->prepare("DELETE FROM voyages WHERE id = ?");
        $stmt->execute([$id]);

        Logger::log($db, "SUPPRESSION", "Suppression mission ID #$id");

        header("Location: index.php?msg=" . urlencode("Mission supprimée"));
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?err=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
