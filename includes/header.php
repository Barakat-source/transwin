<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Africa/Casablanca'); 

// Authentication Guard
$currentFile = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['user_id']) && $currentFile !== 'login.php' && $currentFile !== 'install.php') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> | TRANSWIN e-OM</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        display: ['Syne', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
                            400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
                            800: '#166534', 900: '#14532d', 950: '#052e16',
                        },
                        brand: {
                            dark: '#002B5B',
                            green: '#5EB372',
                            cream: '#FAF9F6'
                        }
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body { @apply font-sans bg-[#FAF9F6] text-[#002B5B]; }
            h1, h2, h3 { @apply font-display font-extrabold uppercase tracking-tight text-[#002B5B]; }
        }
        @layer components {
            .glass-sidebar { @apply bg-white border-r border-slate-200 shadow-[4px_0_24px_rgba(0,0,0,0.02)]; }
            .nav-link { @apply flex items-center gap-3 p-3.5 rounded-xl transition-all duration-200 font-bold uppercase text-[11px] tracking-wider; }
            .nav-link-active { @apply bg-primary-600 text-white shadow-lg shadow-primary-600/20 translate-x-1; }
            .nav-link-inactive { @apply text-slate-400 hover:bg-white hover:text-primary-600; }
            .card-premium { @apply bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300; }
            .btn-premium { @apply bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-full shadow-lg shadow-primary-600/30 transition-all active:scale-95 font-bold uppercase text-xs tracking-widest flex items-center justify-center gap-2; }
            .input-premium { @apply w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all outline-none font-medium text-sm; }
        }
    </style>
</head>
<body class="min-h-screen" x-data="{ mobileMenu: false }">
    
    <!-- Mobile Header -->
    <div class="lg:hidden bg-white/80 backdrop-blur-md border-b sticky top-0 z-[60] p-4 flex items-center justify-between">
        <img src="assets/images/logo.png" alt="Logo" class="h-8 w-auto">
        <button @click="mobileMenu = true" class="p-2 bg-slate-100 rounded-lg">
            <i data-lucide="menu" class="w-6 h-6 text-slate-600"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <aside :class="mobileMenu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 w-72 glass-sidebar flex flex-col p-8 z-50 transition-transform duration-300 overflow-y-auto scrollbar-hide">
        
        <div x-show="mobileMenu" @click="mobileMenu = false" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm lg:hidden"></div>

        <div class="flex flex-col h-full relative">
            <!-- Logo Section -->
            <div class="mb-12 flex flex-col gap-4">
                <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-100 w-full">
                    <img src="assets/images/logo.png" alt="TRANSWIN" class="w-full h-auto mix-blend-multiply">
                </div>
                <!-- Live Clock -->
                <div x-data="{ time: new Date().toLocaleTimeString('fr-FR') }" x-init="setInterval(() => time = new Date().toLocaleTimeString('fr-FR'), 1000)" 
                     class="bg-slate-900 text-white p-4 rounded-2xl flex items-center justify-between border border-white/10 shadow-xl mb-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-widest text-primary-400">Heure Locale</span>
                        <span class="text-xl font-display font-black tracking-tighter" x-text="time"></span>
                    </div>
                    <i data-lucide="clock" class="w-6 h-6 text-primary-400 opacity-50"></i>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-3">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] mb-4 px-2">Menu Principal</p>
                <a href="index.php" class="nav-link <?= ($activePage ?? '') == 'dashboard' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    <span>Tableau de bord</span>
                </a>
                <a href="stats.php" class="nav-link <?= ($activePage ?? '') == 'stats' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Statistiques</span>
                </a>
                <a href="create.php" class="nav-link <?= ($activePage ?? '') == 'create' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Nouvelle Mission</span>
                </a>

                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] mt-8 mb-4 px-2">Gestion Flotte</p>
                <a href="chauffeurs.php" class="nav-link <?= ($activePage ?? '') == 'chauffeurs' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Chauffeurs</span>
                </a>
                <a href="vehicules.php" class="nav-link <?= ($activePage ?? '') == 'vehicules' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    <span>Véhicules</span>
                </a>
                <a href="maintenance.php" class="nav-link <?= ($activePage ?? '') == 'maintenance' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    <span>Maintenance</span>
                </a>
                <a href="clients.php" class="nav-link <?= ($activePage ?? '') == 'clients' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="building-2" class="w-5 h-5"></i>
                    <span>Clients</span>
                </a>
                
                <?php if (($_SESSION['role'] ?? '') === 'ADMIN'): ?>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] mt-8 mb-4 px-2">Administration</p>
                <a href="logs.php" class="nav-link <?= ($activePage ?? '') == 'logs' ? 'nav-link-active' : 'nav-link-inactive' ?>">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    <span>Journal d'Audit</span>
                </a>
                <?php endif; ?>
            </nav>

            <!-- Footer Sidebar -->
            <div class="pt-6 border-t border-slate-100 mb-8">
                <div class="bg-slate-50 p-4 rounded-2xl flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold shadow-lg shadow-primary-600/20">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900"><?= $_SESSION['full_name'] ?? 'Admin' ?></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider"><?= $_SESSION['role'] ?? 'USER' ?></p>
                        </div>
                    </div>
                    <a href="logout.php" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Déconnexion">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-72 min-h-screen flex flex-col">
        <header class="pt-12 lg:pt-20 px-8 lg:px-12 pb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2"><?= $pageTitle ?? 'Tableau de Bord' ?></h1>
                    <p class="text-slate-500 font-medium">Gestion digitale des ordres de mission TRANSWIN</p>
                </div>
                <?php if(($activePage ?? '') == 'dashboard'): ?>
                <a href="create.php" class="btn-premium">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Créer un voyage
                </a>
                <?php endif; ?>
            </div>
        </header>

        <main class="px-8 lg:px-12 pb-12 flex-1">
