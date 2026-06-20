<?php require_once __DIR__ . '/../../common/config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="h-full select-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>G-Life - HQ Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-full flex flex-col font-sans pb-24 pt-16">

    <header class="fixed top-0 left-0 right-0 h-16 bg-red-950/20 backdrop-blur-md border-b border-red-900/30 flex items-center justify-between px-4 z-50">
        <div class="flex items-center space-x-1">
            <span class="text-xs font-black uppercase tracking-widest text-red-500 border border-red-500/30 px-2 py-0.5 bg-red-500/10 rounded">HQ PANEL</span>
        </div>
        <div class="text-right flex items-center space-x-3">
            <span class="text-[10px] font-mono font-bold text-slate-400">Session: admin</span>
            <a href="logout.php" class="text-[10px] uppercase font-bold text-red-400 bg-red-500/10 border border-red-500/20 px-2 py-1 rounded">Kill Session</a>
        </div>
    </header>

    <main class="flex-1 w-full max-w-md mx-auto px-4 overflow-x-hidden">