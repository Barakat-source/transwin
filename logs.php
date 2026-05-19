<?php
require_once 'core/Database.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Journal d'Audit";
$activePage = "logs";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: index.php");
    exit();
}

$logs = [];
if ($db) {
    $query = "SELECT l.*, u.username, u.full_name 
              FROM activity_logs l 
              LEFT JOIN users u ON l.user_id = u.id 
              ORDER BY l.created_at DESC 
              LIMIT 150";
    $logs = $db->query($query)->fetchAll();
}

require_once 'includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="card-premium p-10">
        <div class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-xl">
                    <i data-lucide="shield-check" class="w-7 h-7"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Journal d'Audit</h2>
                    <p class="text-sm text-slate-400 font-medium italic">Traçabilité complète des actions système</p>
                </div>
            </div>
            <div class="bg-primary-50 text-primary-700 px-4 py-2 rounded-xl border border-primary-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-primary-600 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-widest">Live Monitoring</span>
            </div>
        </div>

        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-[21px] top-4 bottom-4 w-0.5 bg-slate-100"></div>

            <div class="space-y-10">
                <?php if (empty($logs)): ?>
                <div class="text-center py-20 opacity-30">
                    <i data-lucide="inbox" class="w-16 h-16 mx-auto mb-4"></i>
                    <p class="font-bold">Aucune activité enregistrée.</p>
                </div>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <div class="relative flex gap-8 group">
                        <!-- Dot -->
                        <div class="relative z-10 w-11 h-11 rounded-full bg-white border-4 border-slate-50 flex items-center justify-center shadow-sm transition-all group-hover:border-primary-100 group-hover:scale-110">
                            <?php 
                            $icon = 'activity';
                            $color = 'text-slate-400';
                            if (stripos($log['action'], 'créé') !== false) { $icon = 'plus-circle'; $color = 'text-emerald-500'; }
                            if (stripos($log['action'], 'modifié') !== false) { $icon = 'edit-3'; $color = 'text-blue-500'; }
                            if (stripos($log['action'], 'supprimé') !== false) { $icon = 'trash-2'; $color = 'text-red-500'; }
                            ?>
                            <i data-lucide="<?= $icon ?>" class="w-5 h-5 <?= $color ?>"></i>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-black text-slate-900"><?= htmlspecialchars($log['full_name'] ?? 'Système') ?></span>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-bold uppercase"><?= $log['username'] ?? 'sys' ?></span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">
                                    <?= date('d M Y à H:i', strtotime($log['created_at'])) ?>
                                </span>
                            </div>
                            
                            <div class="bg-slate-50 p-5 rounded-3xl border border-slate-100 group-hover:bg-white group-hover:shadow-xl group-hover:shadow-slate-200/50 transition-all">
                                <p class="text-sm font-bold text-slate-700 mb-1">
                                    <span class="text-primary-600 mr-1"><?= strtoupper($log['action']) ?></span>
                                </p>
                                <p class="text-sm text-slate-500 leading-relaxed"><?= htmlspecialchars($log['details']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
