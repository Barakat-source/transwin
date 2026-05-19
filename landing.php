<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRANSWIN LOGISTICS SOLUTIONS | Digital e-OM Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; scroll-behavior: smooth; }
        .font-display { font-family: 'Syne', sans-serif; }
        .hero-gradient { background: radial-gradient(circle at top right, #004d80, #002B5B); }
        .glass-nav { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>
</head>
<body class="bg-[#FAF9F6] text-[#002B5B]">
    <!-- Navbar -->
    <nav class="fixed w-full z-[100] glass-nav border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-8 h-24 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                    <img src="assets/images/logo.png" alt="TRANSWIN" class="h-8 w-auto">
                </div>
                <span class="font-display font-black text-xs uppercase tracking-[3px] hidden md:block text-slate-800">Logistics Solutions</span>
            </div>
            <div class="flex items-center gap-10">
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#features" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-emerald-600 transition-colors">Solutions</a>
                    <a href="signup.php" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-emerald-600 transition-colors">S'inscrire</a>
                </div>
                <a href="login.php" class="bg-slate-900 text-white px-8 py-3.5 rounded-full font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:scale-105 active:scale-95 transition-all">Accès Portail</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="absolute inset-0 hero-gradient"></div>
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-emerald-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-500/20 rounded-full blur-[120px]"></div>
        
        <div class="max-w-7xl mx-auto px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-full mb-8">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Digital Transformation 2026</span>
                    </div>
                    <h1 class="text-6xl lg:text-8xl font-display font-extrabold text-white mb-8 leading-[0.9] tracking-tighter">
                        L'AVENIR DE LA <br><span class="text-emerald-400">LOGISTIQUE.</span>
                    </h1>
                    <p class="text-slate-300 text-lg lg:text-xl mb-12 max-w-xl leading-relaxed font-medium">
                        Propulsez vos opérations avec **TRANSWIN e-OM**. Une écosystème digital conçu pour une traçabilité totale et une performance inégalée.
                    </p>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-6">
                        <a href="login.php" class="bg-emerald-500 hover:bg-emerald-400 text-slate-900 px-12 py-6 rounded-2xl font-black uppercase text-xs tracking-[3px] shadow-2xl shadow-emerald-500/30 transition-all hover:-translate-y-2">
                            Démarrer la Mission
                        </a>
                        <a href="#features" class="group flex items-center gap-4 text-white font-black uppercase text-xs tracking-widest">
                            <span class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center group-hover:bg-white group-hover:text-slate-900 transition-all">
                                <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                            </span>
                            Découvrir la Solution
                        </a>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="animate-float">
                        <div class="bg-white/5 backdrop-blur-xl p-4 rounded-[3rem] border border-white/10 shadow-2xl relative z-20">
                            <img src="https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&q=80&w=1200" alt="Interface" class="rounded-[2.5rem] shadow-2xl border border-white/5">
                        </div>
                    </div>
                    <div class="absolute -top-10 -right-10 bg-white p-6 rounded-3xl shadow-2xl z-30 animate-float" style="animation-delay: -2s">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase">Performance</p>
                                <p class="text-xl font-black text-slate-900">+45%</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-10 -left-10 bg-slate-900 p-6 rounded-3xl shadow-2xl z-30 animate-float" style="animation-delay: -4s">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary-600 text-white rounded-xl flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase">Audit Trail</p>
                                <p class="text-xl font-black text-white">100%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                <div class="lg:col-span-1">
                    <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[4px] mb-4 block">Capacités</span>
                    <h2 class="text-5xl font-display font-extrabold text-slate-900 mb-8 leading-tight">L'Excellence Opérationnelle.</h2>
                    <p class="text-slate-500 font-medium leading-relaxed mb-10">
                        Nous avons conçu une architecture robuste pour répondre aux défis de TRANSWIN LOGISTICS SOLUTIONS.
                    </p>
                    <a href="signup.php" class="inline-flex items-center gap-3 text-primary-600 font-black uppercase text-xs tracking-widest hover:gap-5 transition-all">
                        Créer un compte <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="p-10 rounded-[3rem] bg-[#FAF9F6] border border-slate-100 hover:border-emerald-200 transition-all group">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            <i data-lucide="fingerprint" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-2xl font-black mb-4">Sécurité Totale</h3>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed">Contrôle d'accès par rôles et journal d'audit complet pour une traçabilité sans faille.</p>
                    </div>
                    <div class="p-10 rounded-[3rem] bg-[#FAF9F6] border border-slate-100 hover:border-primary-200 transition-all group">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-8 shadow-sm group-hover:bg-primary-600 group-hover:text-white transition-all">
                            <i data-lucide="cloud-lightning" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-2xl font-black mb-4">Zéro Papier</h3>
                        <p class="text-slate-500 text-sm font-medium leading-relaxed">Dématérialisation instantanée des documents de transport avec archivage intelligent.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 py-20 text-white">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="flex items-center gap-4">
                    <img src="assets/images/logo.png" alt="TRANSWIN" class="h-8 invert opacity-50">
                    <p class="text-slate-500 font-bold uppercase text-[10px] tracking-widest border-l border-slate-700 pl-4">TRANSWIN LOGISTICS SOLUTIONS</p>
                </div>
                <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">
                    Projet de Stage 2026 • Réalisé pour TRANSWIN
                </p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
