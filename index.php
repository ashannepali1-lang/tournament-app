<?php
require_once 'common/config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Process Post Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_content'])) {
    $content = trim($_POST['post_content']);
    $game_title = trim($_POST['game_title']);
    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, game_title) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $content, $game_title);
        $stmt->execute();
        $stmt->close();
        
        // Reward 15 XP for updating status
        $conn->query("UPDATE users SET xp = xp + 15 WHERE id = " . intval($_SESSION['user_id']));
        $_SESSION['user_xp'] += 15;
        
        header("Location: index.php");
        exit;
    }
}

// Fetch global user logs
$feed_query = "SELECT posts.*, users.username, users.gamer_tag, users.platform 
               FROM posts 
               JOIN users ON posts.user_id = users.id 
               ORDER BY posts.created_at DESC";
$feed_res = $conn->query($feed_query);
?>

<?php include 'common/header.php'; ?>

<div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 mb-6 shadow-md mt-4">
    <h2 class="text-xs font-black tracking-widest text-slate-400 uppercase mb-3 flex items-center">
        <i class="fa-solid fa-satellite-dish text-purple-500 mr-2 animate-pulse"></i> Post a Gaming Status
    </h2>
    <form action="index.php" method="POST" class="space-y-3">
        <div>
            <input type="text" name="game_title" placeholder="Game Title (e.g., Cyberpunk 2077)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 text-slate-200">
        </div>
        <div>
            <textarea name="post_content" required rows="3" placeholder="What achievements or loadouts are you pushing today?" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs focus:outline-none focus:border-purple-500 text-slate-200 resize-none"></textarea>
        </div>
        <div class="flex items-center justify-between pt-1">
            <span class="text-[10px] text-slate-500 font-mono"><i class="fa-solid fa-circle-info"></i> Broadcast earns +15 XP</span>
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-cyan-600 font-bold uppercase text-[10px] tracking-wider rounded-lg shadow-md transition-transform active:scale-95">Broadcast</button>
        </div>
    </form>
</div>

<div class="space-y-4">
    <h3 class="text-xs font-black tracking-widest text-slate-400 uppercase px-1">Global Transmission Stream</h3>
    <?php if ($feed_res->num_rows > 0): ?>
        <?php while($post = $feed_res->fetch_assoc()): ?>
            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-4 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-800 to-slate-950 border border-purple-500/40 flex items-center justify-center">
                            <i class="fa-solid fa-headset text-cyan-400 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-xs font-black text-slate-200"><?= sanitize($post['username']); ?></div>
                            <div class="text-[10px] text-slate-500 font-mono">@<?= sanitize($post['gamer_tag']); ?> • <?= sanitize($post['platform']); ?></div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 bg-slate-950 text-cyan-400 border border-cyan-500/20 rounded text-[9px] font-mono tracking-tighter uppercase">
                        <?= sanitize($post['game_title']); ?>
                    </span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed font-sans">
                    <?= nl2br(sanitize($post['content'])); ?>
                </p>
                <div class="text-[9px] text-slate-600 font-mono text-right border-t border-slate-800/40 pt-2">
                    <?= date('M d, Y - H:i', strtotime($post['created_at'])); ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="p-8 text-center bg-slate-900 border border-slate-800 rounded-2xl">
            <i class="fa-solid fa-ghost text-slate-600 text-3xl mb-2"></i>
            <p class="text-xs text-slate-400">Stream empty. Be the first to synchronize.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'common/bottom.php'; ?>