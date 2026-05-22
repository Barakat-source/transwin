<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Suivi Maintenance & Alertes";
$activePage = "maintenance";

$alerts = [];
$stats_maintenance = ['total' => 0, 'urgent' => 0, 'expired' => 0];

if ($db) {
    // Stats for overview
    $stats_maintenance['total'] = $db->query("SELECT COUNT(*) FROM vehicules")->fetchColumn();
    $stats_maintenance['expired'] = $db->query("SELECT COUNT(*) FROM vehicules WHERE insurance_expiry < CURDATE() OR tech_visit_expiry < CURDATE()")->fetchColumn();
    $stats_maintenance['urgent'] = $db->query("SELECT COUNT(*) FROM vehicules WHERE (insurance_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY)) OR (tech_visit_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY))")->fetchColumn();

    // Select vehicles where insurance or tech visit expires in the next 45 days
    $query = "SELECT * FROM vehicules 
              WHERE insurance_expiry <= DATE_ADD(CURDATE(), INTERVAL 45 DAY) 
              OR tech_visit_expiry <= DATE_ADD(CURDATE(), INTERVAL 45 DAY)
              ORDER BY insurance_expiry ASC, tech_visit_expiry ASC";
    $alerts = $db->query($query)->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="space-y-8">
    <!-- Fleet Health Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card-premium p-6 bg-slate-900 text-white">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">État Global Flotte</p>
            <div class="flex items-end justify-between">
                <h4 class="text-3xl font-black"><?= $stats_maintenance['total'] ?></h4>
                <span class="text-xs font-bold text-emerald-400">VÉHICULES</span>
            </div>
            <div class="mt-4 w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full" style="width: 85%"></div>
            </div>
        </div>
        <div class="card-premium p-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Documents Expirés</p>
            <div class="flex items-end justify-between">
                <h4 class="text-3xl font-black text-red-600"><?= $stats_maintenance['expired'] ?></h4>
                <div class="p-2 bg-red-50 text-red-600 rounded-lg">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card-premium p-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Urgences (15J)</p>
            <div class="flex items-end justify-between">
                <h4 class="text-3xl font-black text-orange-500"><?= $stats_maintenance['urgent'] ?></h4>
                <div class="p-2 bg-orange-50 text-orange-500 rounded-lg">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
            </div>
        </div>
        <div class="card-premium p-6 bg-primary-600 text-white">
            <p class="text-[10px] font-black uppercase tracking-widest text-primary-200 mb-2">Disponibilité</p>
            <div class="flex items-end justify-between">
                <h4 class="text-3xl font-black">94%</h4>
                <i data-lucide="activity" class="w-6 h-6 opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="card-premium p-8">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center">
                    <i data-lucide="calendar-clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Registre des Échéances</h3>
                    <p class="text-sm text-slate-400 font-medium">Surveillance automatique des documents administratifs</p>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-[10px] font-black uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Expiré
                </span>
                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-700 text-[10px] font-black uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> Urgent
                </span>
            </div>
        </div>

        <?php if (empty($alerts)): ?>
            <div class="p-24 text-center flex flex-col items-center opacity-30">
                <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-6">
                    <i data-lucide="shield-check" class="w-10 h-10"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Tout est sous contrôle</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2">Aucune expiration détectée pour les 45 prochains jours. Votre flotte est en règle.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Véhicule</th>
                            <th class="px-6 py-4">Assurance</th>
                            <th class="px-6 py-4">Visite Technique</th>
                            <th class="px-6 py-4 text-center">Niveau d'Alerte</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $v): ?>
                        <tr class="bg-slate-50/50 hover:bg-white transition-all border border-slate-100 rounded-2xl">
                            <td class="px-6 py-6 font-black text-slate-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center">
                                        <i data-lucide="truck" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <?= htmlspecialchars($v['matricule']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <?php 
                                $days_ins = (strtotime($v['insurance_expiry']) - time()) / 86400;
                                $status_ins = $days_ins < 0 ? 'bg-red-500' : ($days_ins < 15 ? 'bg-orange-500' : 'bg-emerald-500');
                                ?>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full <?= $status_ins ?>"></span>
                                        <span class="font-bold text-slate-700"><?= date('d/m/Y', strtotime($v['insurance_expiry'])) ?></span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"><?= round($days_ins) ?> jours</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <?php 
                                $days_tech = (strtotime($v['tech_visit_expiry']) - time()) / 86400;
                                $status_tech = $days_tech < 0 ? 'bg-red-500' : ($days_tech < 15 ? 'bg-orange-500' : 'bg-emerald-500');
                                ?>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full <?= $status_tech ?>"></span>
                                        <span class="font-bold text-slate-700"><?= date('d/m/Y', strtotime($v['tech_visit_expiry'])) ?></span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase"><?= round($days_tech) ?> jours</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <?php if($days_ins < 0 || $days_tech < 0): ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-red-600 text-white shadow-lg shadow-red-600/20 uppercase tracking-tighter">Expiré</span>
                                <?php elseif($days_ins < 15 || $days_tech < 15): ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-orange-100 text-orange-700 border border-orange-200 uppercase tracking-tighter">Urgent</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-tighter">Sous Surveillance</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="edit_vehicule.php?id=<?= $v['id'] ?>" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 hover:bg-emerald-500 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Mettre à jour">
                                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
