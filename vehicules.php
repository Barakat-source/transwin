<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Gestion des Véhicules";
$activePage = "vehicules";

$items = [];
if ($db) {
    $query = "SELECT v.*, 
              (SELECT COUNT(*) FROM voyages WHERE matricule = v.matricule) as mission_count, 
              (SELECT MAX(date_voyage) FROM voyages WHERE matricule = v.matricule) as last_mission 
              FROM vehicules v 
              ORDER BY v.matricule ASC";
    $items = $db->query($query)->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="card-premium overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <i data-lucide="truck" class="w-5 h-5 text-indigo-600"></i>
            Gestion du Parc Véhicules
        </h3>
        <a href="create_vehicule.php" class="btn-premium py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-600/20 text-xs">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Ajouter un véhicule
        </a>
    </div>

    <?php if (isset($_GET['msg']) || isset($_GET['err'])): ?>
        <div class="m-6 p-4 rounded-xl flex items-center justify-between <?= isset($_GET['msg']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
            <div class="flex items-center gap-3">
                <i data-lucide="<?= isset($_GET['msg']) ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
                <span class="font-bold text-sm"><?= htmlspecialchars($_GET['msg'] ?? $_GET['err']) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                <tr>
                    <th class="px-6 py-4 border-r border-slate-100">Véhicule (Matricule)</th>
                    <th class="px-6 py-4 border-r border-slate-100">Marque / Modèle</th>
                    <th class="px-6 py-4 border-r border-slate-100 text-center">Statut</th>
                    <th class="px-6 py-4 border-r border-slate-100 text-center">Missions</th>
                    <th class="px-6 py-4 border-r border-slate-100">Dernière Mission</th>
                    <th class="px-6 py-4 border-r border-slate-100">Assurance</th>
                    <th class="px-6 py-4 border-r border-slate-100">Visite Tech</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                        Aucun véhicule enregistré.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="px-6 py-5 border-r border-slate-50 font-medium">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                    <i data-lucide="truck" class="w-5 h-5"></i>
                                </div>
                                <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded border border-slate-200"><?= htmlspecialchars($item['matricule']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-slate-700 font-semibold">
                            <?= !empty($item['brand']) ? htmlspecialchars($item['brand'] . ' ' . ($item['model'] ?? '')) : '<span class="text-slate-300 italic">Non renseigné</span>' ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-center">
                            <?php
                            $statusColors = [
                                'DISPONIBLE' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'MAINTENANCE' => 'bg-red-50 text-red-700 border-red-100',
                                'MISSION' => 'bg-blue-50 text-blue-700 border-blue-100'
                            ];
                            $statusColor = $statusColors[$item['status'] ?? 'DISPONIBLE'] ?? $statusColors['DISPONIBLE'];
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black border <?= $statusColor ?>">
                                <?= htmlspecialchars($item['status'] ?? 'DISPONIBLE') ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-center">
                            <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-black">
                                <?= $item['mission_count'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-slate-500 font-medium">
                            <?= !empty($item['last_mission']) ? date('d M Y', strtotime($item['last_mission'])) : '<span class="text-slate-300 italic">Aucune</span>' ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-slate-700 font-bold">
                            <?php if (!empty($item['insurance_expiry'])): ?>
                                <?= date('d/m/Y', strtotime($item['insurance_expiry'])) ?>
                            <?php else: ?>
                                <span class="text-slate-300 italic">Non renseigné</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 border-r border-slate-50 text-slate-700 font-bold">
                            <?php if (!empty($item['tech_visit_expiry'])): ?>
                                <?= date('d/m/Y', strtotime($item['tech_visit_expiry'])) ?>
                            <?php else: ?>
                                <span class="text-slate-300 italic">Non renseigné</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="index.php?search=<?= urlencode($item['matricule']) ?>" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-all shadow-sm" title="Missions">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="edit_vehicule.php?id=<?= $item['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Modifier">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <button onclick="if(confirm('Supprimer ce véhicule ?')) window.location.href='delete_vehicule.php?id=<?= $item['id'] ?>'" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Supprimer">
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

<?php require_once 'includes/footer.php'; ?>
