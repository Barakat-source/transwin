<?php
require_once 'core/Database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Identifiants incorrects";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '42S02') { // Table missing
            header("Location: install.php");
            exit();
        }
        $error = "Erreur système : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | TRANSWIN e-OM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #FAF9F6; }
        .font-display { font-family: 'Syne', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=2000')] bg-cover bg-center">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]"></div>
    
    <div class="w-full max-w-md relative">
        <div class="glass p-10 rounded-[2.5rem] shadow-2xl border border-white/20">
            <div class="text-center mb-10">
                <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 inline-block mb-6">
                    <img src="assets/images/logo.png" alt="TRANSWIN" class="h-10 w-auto">
                </div>
                <h2 class="text-3xl font-display font-extrabold text-slate-900 uppercase tracking-tight">Accès Portail</h2>
                <p class="text-slate-500 mt-2 font-medium">Connectez-vous pour gérer les missions</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-8 flex items-center gap-3 border border-red-100 animate-bounce">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <span class="text-sm font-bold"><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[2px] px-1">Utilisateur</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                        <input type="text" name="username" required 
                               class="w-full bg-white/50 border border-slate-200 rounded-2xl px-12 py-4 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold"
                               placeholder="Ex: admin">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[2px] px-1">Mot de passe</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                        <input type="password" name="password" required 
                               class="w-full bg-white/50 border border-slate-200 rounded-2xl px-12 py-4 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase text-xs tracking-[2px] py-5 rounded-2xl shadow-xl shadow-emerald-600/30 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <span>Démarrer Session</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">Nouveau ? 
                    <a href="signup.php" class="text-emerald-600 font-black uppercase tracking-tighter hover:underline">Créer un compte</a>
                </p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-6">
                    TRANSWIN LOGISTICS SOLUTIONS <br>
                    <span class="text-emerald-600">Plateforme Digitale e-OM</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
