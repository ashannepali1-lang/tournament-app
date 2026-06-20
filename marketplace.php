<?php
require_once 'common/config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = intval($_SESSION['user_id']);

// Add Listing Submission Process Block
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_listing'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $price = trim($_POST['price']);
    $cond = $_POST['condition'];
    $contact = trim($_POST['contact']);

    if (!empty($title) && !empty($price) && !empty($contact)) {
        $stmt = $conn->prepare("INSERT INTO marketplace (user_id, title, description, price, item_condition, contact) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $title, $desc, $price, $cond, $contact);
        $stmt->execute();
        $stmt->close();
        
        // Grant 20 XP for commercial engagement
        $conn->query("UPDATE users SET xp = xp + 20 WHERE id = " . $user_id);
        $_SESSION['user_xp'] += 20;

        header("Location: marketplace.php");
        exit;
    }
}

$market_res = $conn->query("SELECT m.*, u.username FROM marketplace m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
?>

<?php include 'common/header.php'; ?>

<div class="mt-4 mb-5 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 tracking-wide uppercase">Loot Marketplace</h1>
        <p class="text-xs text-slate-500">Trade accounts, hardware rigs, or collectables.</p>
    </div>
    <button onclick="document.getElementById('listing-modal').classList.remove('hidden')" class="px-3 py-1.5 bg-cyan-600 font-bold uppercase text-[10px] tracking-wider rounded-lg shadow transition-all active:scale-95">
        <i class="fa-solid fa-plus mr-1"></i> List Item
    </button>
</div>

<div id="listing-modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-sm rounded-2xl p-5 relative">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-300">Create Vault Listing</h3>
            <button onclick="document.getElementById('listing-modal').classList.add('hidden')" class="text-slate-500 hover:text-slate-400 text-sm"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="marketplace.php" method="POST" class="space-y-3">
            <div>
                <label class="block text-[9px] font-bold tracking-wider uppercase text-slate-400 mb-1">Item Headline</label>
                <input type="text" name="title" required placeholder="e.g. RTX 4070 Ti Founders Edition" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-purple-500 text-slate-200">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[9px] font-bold tracking-wider uppercase text-slate-400 mb-1">Price / Valuation</label>
                    <input type="text" name="price" required placeholder="e.g. $450 USD" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-purple-500 text-slate-200">
                </div>
                <div>
                    <label class="block text-[9px] font-bold tracking-wider uppercase text-slate-400 mb-1">Condition Status</label>
                    <select name="condition" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-purple-500 text-slate-200">
                        <option value="Mint">Mint / Factory</option>
                        <option value="Used Like New">Used (Like New)</option>
                        <option value="Fair">Fair / Operational</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[9px] font-bold tracking-wider uppercase text-slate-400 mb-1">Product Description</label>
                <textarea name="description" rows="2" placeholder="Item technical specifications..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-xs focus:outline-none focus:border-purple-500 text-slate-200 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-[9px] font-bold tracking-wider uppercase text-slate-400 mb-1">Comms Matrix (Discord / Email)</label>
                <input type="text" name="contact" required placeholder="e.g. neon_ninja#4432" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-purple-500 text-slate-200">
            </div>
            <button type="submit" name="add_listing" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-cyan-600 font-bold uppercase text-[10px] tracking-widest rounded-xl shadow-md mt-2">Publish Matrix Listing</button>
        </form>
    </div>
</div>

<div class="space-y-4">
    <?php if($market_res->num_rows > 0): ?>
        <?php while($item = $market_res->fetch_assoc()): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-sm relative">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h4 class="text-xs font-bold text-slate-200 leading-tight mb-0.5"><?= sanitize($item['title']); ?></h4>
                        <span class="text-[9px] font-mono text-slate-500">Listed by <?= sanitize($item['username']); ?></span>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-black text-cyan-400 font-mono"><?= sanitize($item['price']); ?></div>
                        <span class="inline-block text-[8px] tracking-wide uppercase px-1.5 py-0.5 rounded bg-slate-950 text-purple-400 border border-purple-900/40 font-mono mt-0.5">
                            <?= sanitize($item['item_condition']); ?>
                        </span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mb-3 leading-relaxed font-sans"><?= nl2br(sanitize($item['description'])); ?></p>
                <div class="bg-slate-950 p-2.5 rounded-xl border border-slate-800/80 flex items-center justify-between text-[10px]">
                    <span class="text-slate-500 font-mono uppercase tracking-wider"><i class="fa-solid fa-headset mr-1"></i> Contact Agent:</span>
                    <span class="font-mono text-slate-200 font-bold bg-slate-900 px-2 py-0.5 border border-slate-800 rounded"><?= sanitize($item['contact']); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="p-8 text-center bg-slate-900 border border-slate-800 rounded-2xl">
            <i class="fa-solid fa-box-open text-slate-600 text-3xl mb-2"></i>
            <p class="text-xs text-slate-400">Market empty. Be the first to liquidate gear.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'common/bottom.php'; ?>