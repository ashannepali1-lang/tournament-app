<?php
require_once 'common/config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $gamer_tag = trim($_POST['gamer_tag']);
    $platform = $_POST['platform'];

    $stmt = $conn->prepare("UPDATE users SET gamer_tag = ?, platform = ? WHERE id = ?");
    $stmt->bind_param("ssi", $gamer_tag, $platform, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: profile.php");
    exit;
}

$stmt = $conn->prepare("SELECT username, email, gamer_tag, platform, xp, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Compute dynamic gamer tier rankings
$xp = $user['xp'];
$level = floor($xp / 100) + 1;
$next_level_xp = ($level) * 100;
$progress_percent = min(100, floor(($xp % 100)));
?>

<?php include 'common/header.php'; ?>

<div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-md mt-4 mb-6 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="flex items-center space-x-4 mb-5">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-600 to-cyan-500 border border-purple-400 flex items-center justify-center shadow-lg">
            <i class="fa-solid fa-circle-user text-white text-3xl"></i>
        </div>
        <div>
            <h2 class="text-base font-black text-slate-100 leading-tight"><?= sanitize($user['username']); ?></h2>
            <div class="text-[10px] text-purple-400 uppercase tracking-widest font-mono font-bold mt-0.5">Tier Matrix Level <?= $level; ?></div>
        </div>
    </div>

    <div class="space-y-1.5 mb-2 font-mono">
        <div class="flex items-center justify-between text-[10px]">
            <span class="text-slate-400 uppercase tracking-wider">Progression Track</span>
            <span class="text-cyan-400"><?= $xp; ?> / <?= $next_level_xp; ?> XP</span>
        </div>
        <div class="w-full h-2 bg-slate-950 rounded-full overflow-hidden border border-slate-800/60 p-0.5">
            <div class="h-full bg-gradient-to-r from-purple-500 to-cyan-400 rounded-full" style="width: <?= $progress_percent; ?>%"></div>
        </div>
    </div>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
    <h3 class="text-xs font-black tracking-widest text-slate-400 uppercase mb-4">Gamer Profile Details</h3>
    <form action="profile.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-500 mb-1.5">Email Destination Account</label>
            <input type="text" readonly disabled value="<?= sanitize($user['email']); ?>" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl py-2.5 px-4 text-xs text-slate-600 font-mono outline-none cursor-not-allowed">
        </div>
        <div>
            <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Gamer Tag Alias</label>
            <input type="text" name="gamer_tag" value="<?= sanitize($user['gamer_tag']); ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-xs focus:outline-none focus:border-purple-500 text-slate-200">
        </div>
        <div>
            <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Core Deployment Rig</label>
            <select name="platform" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-xs focus:outline-none focus:border-purple-500 text-slate-200">
                <option value="PC" <?= $user['platform'] == 'PC' ? 'selected' : ''; ?>>PC Master Race</option>
                <option value="PlayStation" <?= $user['platform'] == 'PlayStation' ? 'selected' : ''; ?>>PlayStation 5</option>
                <option value="Xbox" <?= $user['platform'] == 'Xbox' ? 'selected' : ''; ?>>Xbox Series X</option>
                <option value="Nintendo" <?= $user['platform'] == 'Nintendo' ? 'selected' : ''; ?>>Nintendo Switch</option>
            </select>
        </div>
        <div class="pt-2 flex gap-3">
            <button type="submit" name="update_profile" class="flex-1 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold uppercase text-[10px] tracking-widest rounded-xl shadow transition-all active:scale-95">
                Save Modifications
            </button>
            <a href="logout.php" class="px-4 py-2.5 bg-red-900/20 border border-red-500/30 text-red-400 font-bold uppercase text-[10px] tracking-widest rounded-xl hover:bg-red-950/40 text-center transition-all">
                Disconnect
            </a>
        </div>
    </form>
</div>

<?php include 'common/bottom.php'; ?>