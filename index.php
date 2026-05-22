<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Suivi des Transports";
$activePage = "dashboard";

$voyages = [];
$stats = ['total' => 0, 'import' => 0, 'export' => 0];

if ($db) {
    // Search & Filter logic
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

    $query = "SELECT * FROM voyages $where ORDER BY date_voyage DESC, created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $voyages = $stmt->fetchAll();

    // Stats calculation (Synchronized with search and filters)
    $statsQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN flux_type = 'IMPORT' THEN 1 ELSE 0 END) as import,
        SUM(CASE WHEN flux_type = 'EXPORT' THEN 1 ELSE 0 END) as export
    FROM voyages $where";
    $statsStmt = $db->prepare($statsQuery);
    $statsStmt->execute($params);
    $stats = $statsStmt->fetch();
}

require_once 'includes/header.php';
?>

<?php if (isset($_GET['msg']) || isset($_GET['err'])): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
         class="mb-6 p-4 rounded-2xl flex items-center justify-between transition-all duration-500 <?= isset($_GET['msg']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
        <div class="flex items-center gap-3">
            <i data-lucide="<?= isset($_GET['msg']) ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
            <span class="font-bold text-sm"><?= htmlspecialchars($_GET['msg'] ?? $_GET['err']) ?></span>
        </div>
        <button @click="show = false" class="opacity-50 hover:opacity-100"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
<?php endif; ?>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="card-premium p-6 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center">
            <i data-lucide="package" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Missions Totales</p>
            <h4 class="text-3xl font-black text-slate-800"><?= $stats['total'] ?? 0 ?></h4>
        </div>
    </div>
    <div class="card-premium p-6 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
            <i data-lucide="arrow-down-left" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Imports</p>
            <h4 class="text-3xl font-black text-slate-800"><?= $stats['import'] ?? 0 ?></h4>
        </div>
    </div>
    <div class="card-premium p-6 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
            <i data-lucide="arrow-up-right" class="w-7 h-7"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Exports</p>
            <h4 class="text-3xl font-black text-slate-800"><?= $stats['export'] ?? 0 ?></h4>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div x-data="{ 
    showFilters: <?= ($startDate || $endDate) ? 'true' : 'false' ?>,
    previewUrl: null,
    previewType: null
}">
    <!-- Top Bar -->
    <div class="flex flex-col lg:flex-row gap-4 mb-8">
        <form id="searchForm" class="flex-1 flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative group">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Chauffeur, client, matricule..." class="input-premium pl-12">
            </div>
            
            <!-- Filter Toggle -->
            <button type="button" @click="showFilters = !showFilters" 
                    :class="showFilters ? 'bg-primary-600 text-white' : 'bg-white text-slate-600'"
                    class="px-6 py-4 rounded-2xl border border-slate-200 font-bold text-xs uppercase tracking-widest flex items-center gap-2 transition-all shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                Filtres
            </button>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="export.php?search=<?= urlencode($search) ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl hover:bg-emerald-100 transition-all text-emerald-600 shadow-sm" title="Exporter Excel">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                </a>
                <button type="button" onclick="window.print()" class="bg-white border border-slate-200 p-4 rounded-2xl hover:bg-slate-50 transition-all text-slate-600 shadow-sm" title="Imprimer">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Expanded Filters -->
    <div x-show="showFilters" x-collapse x-cloak class="mb-10">
        <div class="card-premium p-6 bg-slate-50/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Date Début</label>
                    <input type="date" form="searchForm" name="start_date" value="<?= $startDate ?>" class="input-premium">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Date Fin</label>
                    <input type="date" form="searchForm" name="end_date" value="<?= $endDate ?>" class="input-premium">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" form="searchForm" class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">
                        Appliquer les filtres
                    </button>
                    <a href="index.php" class="p-4 bg-white border border-slate-200 rounded-2xl text-slate-400 hover:text-red-500 transition-all" title="Réinitialiser">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div x-show="previewUrl" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/90 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="previewUrl = null"
         x-cloak>
        <div class="relative max-w-4xl w-full h-full flex flex-col">
            <div class="flex justify-between items-center mb-4 text-white">
                <h3 class="text-xl font-bold">Aperçu du document</h3>
                <button @click="previewUrl = null" class="p-2 bg-white/10 rounded-full hover:bg-white/20 transition-all">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="flex-1 bg-white rounded-3xl overflow-hidden shadow-2xl relative">
                <img :src="previewUrl" class="w-full h-full object-contain" x-show="previewType === 'image'">
                <div x-show="previewType !== 'image'" class="h-full flex flex-col items-center justify-center text-slate-400">
                    <i data-lucide="file-text" class="w-20 h-20 mb-4 opacity-20"></i>
                    <p class="font-bold">Aperçu non disponible pour ce type de fichier.</p>
                    <a :href="previewUrl" target="_blank" class="mt-4 bg-primary-600 text-white px-6 py-2 rounded-full font-bold">Ouvrir dans un nouvel onglet</a>
                </div>
            </div>
        </div>
        </div>

<!-- Statistics Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <div class="lg:col-span-2 card-premium p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="line-chart" class="w-5 h-5 text-primary-600"></i>
                Performance Mensuelle
            </h3>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1 rounded-full">Derniers 6 mois</span>
        </div>
        <div class="h-[250px] relative">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
    <div class="card-premium p-6 flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-800 mb-6">Répartition Flux</h3>
            <div class="h-[180px] relative mb-6">
                <canvas id="fluxDonut"></canvas>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-2xl bg-indigo-50 border border-indigo-100">
                <span class="text-xs font-bold text-indigo-700">IMPORT</span>
                <span class="text-sm font-black text-indigo-900"><?= $stats['import'] ?></span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-2xl bg-orange-50 border border-orange-100">
                <span class="text-xs font-bold text-orange-700">EXPORT</span>
                <span class="text-sm font-black text-orange-900"><?= $stats['export'] ?></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Performance Chart (Line)
    const ctxPerf = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctxPerf, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Missions',
                data: [12, 19, 15, 25, 22, <?= $stats['total'] ?>],
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#16a34a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // 2. Flux Donut
    const ctxFlux = document.getElementById('fluxDonut').getContext('2d');
    new Chart(ctxFlux, {
        type: 'doughnut',
        data: {
            labels: ['Import', 'Export'],
            datasets: [{
                data: [<?= $stats['import'] ?>, <?= $stats['export'] ?>],
                backgroundColor: ['#4f46e5', '#f97316'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<!-- Main Table -->
<div class="card-premium overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="list" class="w-5 h-5 text-primary-600"></i>
            Registre des Missions
        </h3>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-white px-3 py-1.5 rounded-lg border border-slate-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            MIS À JOUR
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                <tr>
                    <th class="px-6 py-4 border-r border-slate-100">Date</th>
                    <th class="px-6 py-4 border-r border-slate-100">Chauffeur</th>
                    <th class="px-6 py-4 border-r border-slate-100">Matricule</th>
                    <th class="px-6 py-4 border-r border-slate-100 text-primary-600">Client</th>
                    <th class="px-6 py-4 border-r border-slate-100">TC</th>
                    <th class="px-6 py-4 border-r border-slate-100">Flux</th>
                    <th class="px-6 py-4 border-r border-slate-100">Départ</th>
                    <th class="px-6 py-4 border-r border-slate-100">Destination</th>
                    <th class="px-6 py-4 border-r border-slate-100 font-bold text-primary-600">Type</th>
                    <th class="px-6 py-4 border-r border-slate-100">Obs.</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($voyages)): ?>
                <tr>
                    <td colspan="11" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center opacity-40">
                            <i data-lucide="folder-open" class="w-12 h-12 mb-3"></i>
                            <p class="font-medium">Aucun voyage enregistré pour le moment.</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($voyages as $v): ?>
                    <tr class="hover:bg-primary-50/30 transition-all group">
                        <td class="px-6 py-5 border-r border-slate-50 whitespace-nowrap font-medium text-slate-600">
                            <?= date('d M Y', strtotime($v['date_voyage'])) ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                    <?= strtoupper(substr($v['chauffeur'], 0, 2)) ?>
                                </div>
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($v['chauffeur']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 font-mono text-xs text-slate-500">
                            <span class="bg-slate-100 px-2 py-1 rounded border border-slate-200"><?= htmlspecialchars($v['matricule']) ?></span>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 font-black text-primary-700">
                            <?= htmlspecialchars($v['client']) ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-slate-500"><?= htmlspecialchars($v['tc']) ?></td>
                        <td class="px-6 py-5 border-r border-slate-50 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black <?= $v['flux_type'] == 'IMPORT' ? 'bg-indigo-100 text-indigo-700' : 'bg-orange-100 text-orange-700' ?>">
                                <?= htmlspecialchars($v['flux_type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 font-bold text-xs">
                            <?= htmlspecialchars($v['depart']) ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 font-bold text-xs text-primary-600">
                            <?= htmlspecialchars($v['destination']) ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-xs font-bold text-slate-600">
                            <?= htmlspecialchars($v['remorque_type']) ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-[10px] text-slate-400 italic max-w-[120px] truncate" title="<?= htmlspecialchars($v['observation']) ?>">
                            <?= htmlspecialchars($v['observation']) ?>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="pdf.php?id=<?= $v['id'] ?>" target="_blank" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="PDF">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </a>
                                <?php if ($v['attachment_path']): ?>
                                <?php 
                                    $ext = strtolower(pathinfo($v['attachment_path'], PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                ?>
                                <button @click.stop="previewUrl = '<?= $v['attachment_path'] ?>'; previewType = '<?= $isImage ? 'image' : 'other' ?>'" 
                                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Voir PJ">
                                    <i data-lucide="paperclip" class="w-4 h-4"></i>
                                </button>
                                <?php endif; ?>
                                <a href="edit.php?id=<?= $v['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Modifier">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <button onclick="if(confirm('Supprimer cette mission ?')) window.location.href='delete.php?id=<?= $v['id'] ?>'" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Supprimer">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>
