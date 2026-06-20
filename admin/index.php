<?php
require_once '../common/config.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

// Handle Tournament Additions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tournament'])) {
    $title = trim($_POST['title']);
    $game = trim($_POST['game']);
    $prize = trim($_POST['prize_pool']);
    $date = $_POST['event_date'];

    if(!empty($title) && !empty($game) && !empty($prize) && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO tournaments (title, game, prize_pool, event_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $game, $prize, $date);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}

// Handle Direct Ecosystem Object Drops / Deletions
if (isset($_GET['delete_listing'])) {
    $id = intval($_GET['delete_listing']);
    $conn->query("DELETE FROM marketplace WHERE id = $id");
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete_tournament'])) {
    $id = intval($_GET['delete_tournament']);
    $conn->query("DELETE FROM tournaments WHERE id = $id");
    header("Location: index.php");
    exit;
}

// Fetch Ecosystem Metrics Data
$users_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$listings_count = $conn->query("SELECT COUNT(*) as total FROM marketplace")->fetch_assoc()['total'];
$tourneys_count = $conn->query("SELECT COUNT(*) as total FROM tournaments")->fetch_assoc()['total'];

// Fetch Entities Arrays for Moderation Blocks
$tourneys_res = $conn->query("SELECT * FROM tournaments ORDER BY event_date ASC");
$market_res = $conn->query("SELECT id, title, price FROM marketplace ORDER BY created_at DESC");
?>

<?php include 'common/header.php'; ?>

<div class="mt-4 mb-5">
    <h1 class="text-lg font-black tracking-wide uppercase text-slate-100">HQ Dashboard Infrastructure</h1>
    <p class="text-[11px] text-slate-500 font-mono">Platform operations & content enforcement.</p>
</div>

<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-slate-900 border border-slate-800 p-3 rounded-xl text-center">
        <div class="text-[10px] font-mono text-slate-500 uppercase">Users</div>
        <div class="text-base font-black font-mono text-purple-400 mt-0.5"><?= $users_count; ?></div>
    </div>
    <div class="bg-slate-900 border border-slate-800 p-3 rounded-xl text-center">
        <div class="text-[10px] font-mono text-slate-500 uppercase">Loot</div>
        <div class="text-base font-black font-mono text-cyan-400 mt-0.5"><?= $listings_count; ?></div>
    </div>
    <div class="bg-slate-900 border border-slate-800 p-3 rounded-xl text-center">
        <div class="text-[10px] font-mono text-slate-500 uppercase">Arenas</div>
        <div class="text-base font-black font-mono text-red-400 mt-0.5"><?= $tourneys_count; ?></div>
    </div>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-6 shadow-sm">
    <h3 class="text-xs font-black tracking-widest text-slate-400 uppercase mb-3 font-mono"><i class="fa-solid fa-tower-broadcast text-red-500 mr-1.5"></i> Deploy Tournament</h3>
    <form action="index.php" method="POST" class="space-y-3 text-xs">
        <div>
            <input type="text" name="title" required placeholder="Tournament Headline Title" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 font-mono text-slate-200 focus:outline-none focus:border-red-500">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <input type="text" name="game" required placeholder="Game Title Target" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 font-mono text-slate-200 focus:outline-none focus:border-red-500">
            <input type="text" name="prize_pool" required placeholder="Prize Cash Pool (e.g. $1000)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 font-mono text-slate-200 focus:outline-none focus:border-red-500">
        </div>
        <div>
            <input type="datetime-local" name="event_date" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 font-mono text-slate-400 focus:outline-none focus:border-red-500">
        </div>
        <button type="submit" name="add_tournament" class="w-full py-2 bg-red-900/30 hover:bg-red-900/50 border border-red-700/40 text-red-400 font-bold uppercase text-[10px] tracking-widest rounded-lg font-mono transition-all">Initialize Event Matrix</button>
    </form>
</div>

<div class="space-y-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm">
        <h3 class="text-xs font-black tracking-widest text-slate-400 uppercase mb-3 font-mono">Championship Registry</h3>
        <div class="space-y-2 max-h-48 overflow-y-auto">
            <?php if($tourneys_res->num_rows > 0): ?>
                <?php while($t = $tourneys_res->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-2 bg-slate-950 border border-slate-800 rounded-lg text-xs font-mono">
                        <span class="truncate pr-2 text-slate-300"><?= sanitize($t['title']); ?></span>
                        <a href="index.php?delete_tournament=<?= $t['id']; ?>" class="text-red-400 hover:text-red-500 text-[10px] bg-red-500/10 px-2 py-0.5 rounded border border-red-900/40 font-bold">Purge</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-[10px] text-slate-500 font-mono py-1">No operational arenas recorded.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm">
        <h3 class="text-xs font-black tracking-widest text-slate-400 uppercase mb-3 font-mono">Vault Marketplace Inventory</h3>
        <div class="space-y-2 max-h-48 overflow-y-auto">
            <?php if($market_res->num_rows > 0): ?>
                <?php while($m = $market_res->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-2 bg-slate-950 border border-slate-800 rounded-lg text-xs font-mono">
                        <span class="truncate pr-2 text-slate-300"><?= sanitize($m['title']); ?> (<?= sanitize($m['price']); ?>)</span>
                        <a href="index.php?delete_listing=<?= $m['id']; ?>" class="text-red-400 hover:text-red-500 text-[10px] bg-red-500/10 px-2 py-0.5 rounded border border-red-900/40 font-bold">Purge</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-[10px] text-slate-500 font-mono py-1">Vault Inventory records are empty.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'common/bottom.php'; ?>