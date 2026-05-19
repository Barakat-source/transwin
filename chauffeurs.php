<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Gestion des Chauffeurs";
$activePage = "chauffeurs";

$items = [];
if ($db) {
    // Grouping by chauffeur to show unique drivers and their mission count
    $query = "SELECT chauffeur as name, COUNT(*) as mission_count, MAX(date_voyage) as last_mission 
              FROM voyages 
              GROUP BY chauffeur 
              ORDER BY mission_count DESC";
    $items = $db->query($query)->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="card-premium overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-primary-600"></i>
            Liste des Chauffeurs
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                <tr>
                    <th class="px-6 py-4 border-r border-slate-100">Nom du Chauffeur</th>
                    <th class="px-6 py-4 border-r border-slate-100 text-center">Missions</th>
                    <th class="px-6 py-4">Dernière Mission</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($items as $item): ?>
                <tr class="hover:bg-slate-50 transition-all">
                    <td class="px-6 py-5 border-r border-slate-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">
                                <?= strtoupper(substr($item['name'], 0, 1)) ?>
                            </div>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-5 border-r border-slate-50 text-center">
                        <span class="bg-primary-50 text-primary-700 px-3 py-1 rounded-full text-xs font-black">
                            <?= $item['mission_count'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-slate-500 font-medium">
                        <?= date('d M Y', strtotime($item['last_mission'])) ?>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <a href="index.php?search=<?= urlencode($item['name']) ?>" class="text-primary-600 hover:underline font-bold text-xs">Voir missions</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
