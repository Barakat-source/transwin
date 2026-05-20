<?php
session_start();
require_once 'core/Database.php';
require_once 'core/Logger.php';
$database = new Database();
$db = $database->getConnection();

$pageTitle = "Création de Mission";
$activePage = "create";
$msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Data Normalization
        $client = trim($_POST['client']);
        $chauffeur = trim($_POST['chauffeur']);
        $matricule = strtoupper(trim($_POST['matricule']));
        $tc = strtoupper(trim($_POST['tc']));
        $depart = trim($_POST['depart']);
        $destination = trim($_POST['destination']);
        $remorque_type = trim($_POST['remorque_type']);
        $observation = trim($_POST['observation']);

        // Handle File Upload
        $attachment_path = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $fileExtension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'OM_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                $attachment_path = $targetPath;
            }
        }

        $stmt = $db->prepare("INSERT INTO voyages (date_voyage, client, chauffeur, matricule, tc, flux_type, depart, destination, observation, remorque_type, attachment_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['date_voyage'],
            $client,
            $chauffeur,
            $matricule,
            $tc,
            $_POST['flux_type'],
            $depart,
            $destination,
            $observation,
            $remorque_type,
            $attachment_path
        ]);
        
        // Enregistrer le véhicule s'il n'existe pas
        $stmtVehicule = $db->prepare("INSERT IGNORE INTO vehicules (matricule) VALUES (?)");
        $stmtVehicule->execute([$matricule]);
        
        // Log activity
        Logger::log($db, "CRÉATION", "Nouveau voyage pour $client vers $destination");
        
        $msg = "Voyage enregistré avec succès !";
        echo "<script>setTimeout(() => window.location.href='index.php', 1500);</script>";
    } catch (PDOException $e) {
        $msg = "Erreur : " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 gap-8">
        <!-- Center Form -->
        <div class="space-y-8">
            <div class="card-premium p-8">
                <div class="flex items-center gap-3 mb-8 border-b pb-4 border-slate-100">
                    <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                        <i data-lucide="file-edit" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Formulaire de Mission</h3>
                </div>

                <?php if ($msg): ?>
                    <div class="p-4 mb-8 rounded-xl flex items-center gap-3 <?= strpos($msg, 'Erreur') === false ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
                        <i data-lucide="<?= strpos($msg, 'Erreur') === false ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
                        <span class="font-bold text-sm"><?= $msg ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <!-- Row 1 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Date de Mission</label>
                            <input type="date" name="date_voyage" required class="input-premium" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Type de Flux</label>
                            <select name="flux_type" class="input-premium">
                                <option value="IMPORT">IMPORT</option>
                                <option value="EXPORT">EXPORT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Chauffeur</label>
                            <div class="relative">
                                <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300"></i>
                                <input type="text" name="chauffeur" required placeholder="Nom complet" class="input-premium pl-12">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Matricule</label>
                            <div class="relative">
                                <i data-lucide="truck" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300"></i>
                                <input type="text" name="matricule" required placeholder="Ex: 12345-A-10" class="input-premium pl-12">
                            </div>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Client</label>
                            <input type="text" name="client" required placeholder="Nom du Client" class="input-premium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Numéro TC</label>
                            <input type="text" name="tc" placeholder="N° Conteneur" class="input-premium">
                        </div>
                    </div>

                    <!-- Row 4 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Lieu de Départ</label>
                            <input type="text" name="depart" required placeholder="Point A" class="input-premium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Destination</label>
                            <input type="text" name="destination" required placeholder="Point B" class="input-premium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Type Remorque / Type Voyage</label>
                            <input type="text" name="remorque_type" placeholder="Ex: 40' HC / 20' ST" class="input-premium">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Document Joint (BL / Ticket)</label>
                            <input type="file" name="attachment" class="input-premium py-3">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest px-1">Observations</label>
                        <textarea name="observation" rows="2" placeholder="Notes complémentaires..." class="input-premium"></textarea>
                    </div>

                    <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                        <a href="index.php" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Annuler</a>
                        <button type="submit" class="btn-premium px-10">
                            <span>Enregistrer la Mission</span>
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side: Info Panel (Removed as per user preference for simplicity) -->
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
