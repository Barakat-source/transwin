<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();
if ($db) {
    $db->exec("TRUNCATE TABLE voyages");
    echo "Succès : La table est vide et le compteur est à 1.";
}
?>
