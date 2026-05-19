<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Statistiques & Analyses";
$activePage = "stats";

$monthlyStats = [];
$fluxStats = [];
$topClients = [];
$topDestinations = [];

if ($db) {
    // Monthly Evolution
    $query = "SELECT DATE_FORMAT(date_voyage, '%Y-%m') as month, COUNT(*) as count 
              FROM voyages 
              GROUP BY month 
              ORDER BY month DESC 
              LIMIT 12";
    $monthlyStats = array_reverse($db->query($query)->fetchAll());

    // Flux Type Distribution
    $query = "SELECT flux_type, COUNT(*) as count FROM voyages GROUP BY flux_type";
    $fluxStats = $db->query($query)->fetchAll();

    // Top 5 Clients
    $query = "SELECT client, COUNT(*) as count FROM voyages GROUP BY client ORDER BY count DESC LIMIT 5";
    $topClients = $db->query($query)->fetchAll();

    // Top 5 Destinations
    $query = "SELECT destination, COUNT(*) as count FROM voyages GROUP BY destination ORDER BY count DESC LIMIT 5";
    $topDestinations = $db->query($query)->fetchAll();
}

require_once 'includes/header.php';
?>

<!-- Scripts for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Monthly Evolution Chart -->
    <div class="card-premium p-8">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
            <i data-lucide="trending-up" class="w-5 h-5 text-primary-600"></i>
            Évolution Mensuelle
        </h3>
        <div class="h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Flux Distribution Chart -->
    <div class="card-premium p-8">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-5 h-5 text-indigo-600"></i>
            Répartition des Flux
        </h3>
        <div class="h-64 flex items-center justify-center">
            <canvas id="fluxChart"></canvas>
        </div>
    </div>

    <!-- Top Clients -->
    <div class="card-premium p-8">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-orange-600"></i>
            Top 5 Clients
        </h3>
        <div class="h-64">
            <canvas id="clientsChart"></canvas>
        </div>
    </div>

    <!-- Top Destinations -->
    <div class="card-premium p-8">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
            <i data-lucide="map-pin" class="w-5 h-5 text-red-600"></i>
            Top 5 Destinations
        </h3>
        <div class="h-64">
            <canvas id="destChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
            datasets: [{
                label: 'Missions',
                data: <?= json_encode(array_column($monthlyStats, 'count')) ?>,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#22c55e',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // Flux Chart
    const fluxCtx = document.getElementById('fluxChart').getContext('2d');
    new Chart(fluxCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($fluxStats, 'flux_type')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($fluxStats, 'count')) ?>,
                backgroundColor: ['#6366f1', '#f97316', '#10b981'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%'
        }
    });

    // Clients Chart
    const clientsCtx = document.getElementById('clientsChart').getContext('2d');
    new Chart(clientsCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($topClients, 'client')) ?>,
            datasets: [{
                label: 'Missions',
                data: <?= json_encode(array_column($topClients, 'count')) ?>,
                backgroundColor: '#f97316',
                borderRadius: 8,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { display: false } },
                y: { grid: { display: false } }
            }
        }
    });

    // Destinations Chart
    const destCtx = document.getElementById('destChart').getContext('2d');
    new Chart(destCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($topDestinations, 'destination')) ?>,
            datasets: [{
                label: 'Missions',
                data: <?= json_encode(array_column($topDestinations, 'count')) ?>,
                backgroundColor: '#ef4444',
                borderRadius: 8,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
