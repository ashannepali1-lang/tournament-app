<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full select-none touch-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gamers Life</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { -webkit-touch-callout: none; -webkit-user-select: none; user-select: none; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-full flex flex-col font-sans pb-24 pt-16">

    <header class="fixed top-0 left-0 right-0 h-16 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 flex items-center justify-between px-4 z-50">
        <div class="flex items-center space-x-2">
            <span class="text-xl font-black uppercase tracking-wider bg-gradient-to-r from-purple-500 to-cyan-400 bg-clip-text text-transparent">
                <i class="fa-solid def:fa-gamepad text-purple-500 mr-1"></i> G-Life
            </span>
        </div>
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="text-xs font-bold text-slate-300"><?= sanitize($_SESSION['username']); ?></div>
                    <div class="text-[10px] text-cyan-400 tracking-wider uppercase font-mono">XP: <?= $_SESSION['user_xp'] ?? 0; ?></div>
                </div>
                <div class="w-8 h-8 rounded-full border border-purple-500 overflow-hidden bg-slate-800 flex items-center justify-center">
                    <i class="fa-solid fa-user-ninja text-purple-400 text-sm"></i>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="text-xs font-bold uppercase tracking-wider text-cyan-400 border border-cyan-500/30 px-3 py-1.5 rounded-lg bg-cyan-500/10">Sign In</a>
        <?php endif; ?>
    </header>

    <main class="flex-1 w-full max-w-md mx-auto px-4 overflow-x-hidden">