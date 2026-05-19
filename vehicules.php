<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Gestion des Véhicules";
$activePage = "vehicules";

$items = [];
if ($db) {
    $query = "SELECT matricule as name, COUNT(*) as mission_count, MAX(date_voyage) as last_mission 
              FROM voyages 
              GROUP BY matricule 
              ORDER BY mission_count DESC";
    $items = $db->query($query)->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="card-premium overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="truck" class="w-5 h-5 text-indigo-600"></i>
            Parc Véhicules (Matricules)
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                <tr>
                    <th class="px-6 py-4 border-r border-slate-100">Matricule</th>
                    <th class="px-6 py-4 border-r border-slate-100 text-center">Missions</th>
                    <th class="px-6 py-4">Dernière Utilisation</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-slate-50 transition-all">
                    <td class="px-6 py-5 border-r border-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                <i data-lucide="truck" class="w-5 h-5"></i>
                            </div>
                            <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded border border-slate-200"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-5 border-r border-slate-50 text-center">
                        <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-black">
                            <?= $item['mission_count'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-slate-500 font-medium">
                        <?= date('d M Y', strtotime($item['last_mission'])) ?>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <a href="index.php?search=<?= urlencode($item['name']) ?>" class="text-indigo-600 hover:underline font-bold text-xs">Voir missions</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
