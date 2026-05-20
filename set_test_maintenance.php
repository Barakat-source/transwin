<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Connexion échouée");
}

// 1. Fetch some vehicles
$stmt = $db->query("SELECT matricule FROM vehicules LIMIT 3");
$vehicles = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($vehicles)) {
    die("Aucun véhicule trouvé dans la table. Créez d'abord une mission avec un matricule.");
}

// Set test dates
// Past date (expired)
if (isset($vehicles[0])) {
    $db->prepare("UPDATE vehicules SET insurance_expiry = ?, tech_visit_expiry = ?, brand = 'Volvo', model = 'FH16' WHERE matricule = ?")
       ->execute([
           date('Y-m-d', strtotime('-10 days')),
           date('Y-m-d', strtotime('+30 days')),
           $vehicles[0]
       ]);
    echo "Véhicule " . $vehicles[0] . " mis à jour en : Assurance expirée et Visite technique sous surveillance.<br>";
}

// Near future date (urgent, < 15 days)
if (isset($vehicles[1])) {
    $db->prepare("UPDATE vehicules SET insurance_expiry = ?, tech_visit_expiry = ?, brand = 'Scania', model = 'R500' WHERE matricule = ?")
       ->execute([
           date('Y-m-d', strtotime('+8 days')),
           date('Y-m-d', strtotime('+40 days')),
           $vehicles[1]
       ]);
    echo "Véhicule " . $vehicles[1] . " mis à jour en : Assurance urgente (< 15 jours).<br>";
}

// Under surveillance (< 45 days)
if (isset($vehicles[2])) {
    $db->prepare("UPDATE vehicules SET insurance_expiry = ?, tech_visit_expiry = ?, brand = 'Renault', model = 'T-Range' WHERE matricule = ?")
       ->execute([
           date('Y-m-d', strtotime('+25 days')),
           date('Y-m-d', strtotime('+25 days')),
           $vehicles[2]
       ]);
    echo "Véhicule " . $vehicles[2] . " mis à jour en : Assurance et Visite sous surveillance.<br>";
}

echo "<br><strong>Terminé ! Vous pouvez maintenant aller sur la page de Maintenance pour voir les alertes.</strong>";
