<?php
require_once 'core/Database.php';
session_start();

if (!isset($_SESSION['user_id'])) die("Accès refusé");

$database = new Database();
$db = $database->getConnection();

$search = $_GET['search'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$whereClauses = [];
$params = [];

if ($search) {
    $whereClauses[] = "(chauffeur LIKE ? OR client LIKE ? OR matricule LIKE ? OR tc LIKE ? OR destination LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
}

if ($startDate) {
    $whereClauses[] = "date_voyage >= ?";
    $params[] = $startDate;
}

if ($endDate) {
    $whereClauses[] = "date_voyage <= ?";
    $params[] = $endDate;
}

$where = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

$query = "SELECT * FROM voyages $where ORDER BY date_voyage DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();

$filename = "Transwin_Missions_" . date('Ymd_His') . ".csv";

// Force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Headers
fputcsv($output, ['ID', 'Date', 'Client', 'Chauffeur', 'Matricule', 'Flux', 'TC', 'Depart', 'Destination', 'Type Remorque', 'Observation']);

// Data
foreach ($data as $row) {
    fputcsv($output, [
        $row['id'],
        $row['date_voyage'],
        $row['client'],
        $row['chauffeur'],
        $row['matricule'],
        $row['flux_type'],
        $row['tc'],
        $row['depart'],
        $row['destination'],
        $row['remorque_type'],
        $row['observation']
    ]);
}

fclose($output);
exit;
