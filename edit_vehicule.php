<?php
session_start();
require_once 'core/Database.php';
require_once 'core/Logger.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Modifier le Véhicule";
$activePage = "vehicules";
$msg = null;

if (!isset($_GET['id'])) {
    header("Location: vehicules.php");
    exit();
}

$id = $_GET['id'];

// Fetch vehicle
$stmt = $db->prepare("SELECT * FROM vehicules WHERE id = ?");
$stmt->execute([$id]);
$v = $stmt->fetch();

if (!$v) {
    header("Location: vehicules.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $matricule = strtoupper(trim($_POST['matricule']));
        $brand = trim($_POST['brand']);
        $model = trim($_POST['model']);
        $insurance_expiry = !empty($_POST['insurance_expiry']) ? $_POST['insurance_expiry'] : null;
        $tech_visit_expiry = !empty($_POST['tech_visit_expiry']) ? $_POST['tech_visit_expiry'] : null;
        $status = $_POST['status'];

        $stmt = $db->prepare("UPDATE vehicules SET matricule = ?, brand = ?, model = ?, insurance_expiry = ?, tech_visit_expiry = ?, status = ? WHERE id = ?");
        $stmt->execute([$matricule, $brand, $model, $insurance_expiry, $tech_visit_expiry, $status, $id]);

        Logger::log($db, "MODIFICATION", "Modification du véhicule $matricule");

        $msg = "Véhicule mis à jour avec succès !";
        echo "<script>setTimeout(() => window.location.href='vehicules.php', 1500);</script>";
    } catch (PDOException $e) {
        $msg = "Erreur : " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="card-premium p-8">
        <div class="flex items-center gap-3 mb-8 border-b pb-4 border-slate-100">
            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                <i data-lucide="edit" class="w-6 h-6"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight">Modifier le Véhicule</h3>
        </div>

        <?php if ($msg): ?>
            <div class="p-4 mb-8 rounded-xl flex items-center gap-3 <?= strpos($msg, 'Erreur') === false ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
                <i data-lucide="<?= strpos($msg, 'Erreur') === false ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
                <span class="font-bold text-sm"><?= $msg ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Matricule *</label>
                    <div class="relative">
                        <i data-lucide="truck" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300"></i>
                        <input type="text" name="matricule" required placeholder="Ex: 12345-A-10" class="input-premium pl-12" value="<?= htmlspecialchars($v['matricule']) ?>">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Statut</label>
                    <select name="status" class="input-premium">
                        <option value="DISPONIBLE" <?= $v['status'] == 'DISPONIBLE' ? 'selected' : '' ?>>DISPONIBLE</option>
                        <option value="MAINTENANCE" <?= $v['status'] == 'MAINTENANCE' ? 'selected' : '' ?>>MAINTENANCE</option>
                        <option value="MISSION" <?= $v['status'] == 'MISSION' ? 'selected' : '' ?>>MISSION</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Marque</label>
                    <input type="text" name="brand" placeholder="Ex: Volvo" class="input-premium" value="<?= htmlspecialchars($v['brand'] ?? '') ?>">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Modèle</label>
                    <input type="text" name="model" placeholder="Ex: FH16" class="input-premium" value="<?= htmlspecialchars($v['model'] ?? '') ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Échéance Assurance</label>
                    <input type="date" name="insurance_expiry" class="input-premium" value="<?= $v['insurance_expiry'] ?>">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Échéance Visite Technique</label>
                    <input type="date" name="tech_visit_expiry" class="input-premium" value="<?= $v['tech_visit_expiry'] ?>">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                <a href="vehicules.php" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Annuler</a>
                <button type="submit" class="btn-premium px-10 bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/30">
                    <span>Enregistrer</span>
                    <i data-lucide="save" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
