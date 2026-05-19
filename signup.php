<?php
require_once 'core/Database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'EXPLOITANT'; // Default role for new signups

    if (empty($username) || empty($password) || empty($full_name)) {
        $error = "Tous les champs sont obligatoires";
    } else {
        try {
            // Check if username exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Ce nom d'utilisateur est déjà utilisé";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $full_name, $hashed_password, $role])) {
                    $success = "Compte créé avec succès ! Connectez-vous.";
                } else {
                    $error = "Erreur lors de la création du compte";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur système : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | TRANSWIN e-OM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #FAF9F6; }
        .font-display { font-family: 'Syne', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&q=80&w=2000')] bg-cover bg-center">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
    
    <div class="w-full max-w-md relative">
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl border border-white/20">
            <div class="text-center mb-8">
                <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 inline-block mb-4">
                    <img src="assets/images/logo.png" alt="TRANSWIN" class="h-8 w-auto">
                </div>
                <h2 class="text-2xl font-display font-extrabold text-slate-900 uppercase tracking-tight">Rejoindre TRANSWIN</h2>
                <p class="text-slate-500 mt-1 text-sm font-medium">Créez votre accès au portail logistique</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 flex items-center gap-3 border border-red-100">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span class="text-xs font-bold"><?= $error ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 flex items-center gap-3 border border-emerald-100">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span class="text-xs font-bold"><?= $success ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[2px] px-1">Nom Complet</label>
                    <div class="relative">
                        <i data-lucide="user-circle" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" name="full_name" required 
                               class="w-full bg-white/50 border border-slate-200 rounded-xl px-11 py-3.5 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all font-semibold text-sm"
                               placeholder="Prénom Nom">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[2px] px-1">Utilisateur</label>
                    <div class="relative">
                        <i data-lucide="at-sign" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" name="username" required 
                               class="w-full bg-white/50 border border-slate-200 rounded-xl px-11 py-3.5 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all font-semibold text-sm"
                               placeholder="Ex: ybenis">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-[2px] px-1">Mot de passe</label>
                    <div class="relative">
                        <i data-lucide="key-round" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="password" name="password" required 
                               class="w-full bg-white/50 border border-slate-200 rounded-xl px-11 py-3.5 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition-all font-semibold text-sm"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-black uppercase text-[10px] tracking-[2px] py-4 rounded-xl shadow-lg shadow-primary-600/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2 mt-4">
                    <span>Créer le compte</span>
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-xs text-slate-500 font-medium">Déjà un compte ? 
                    <a href="login.php" class="text-primary-600 font-black uppercase tracking-tighter hover:underline">Se connecter</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
