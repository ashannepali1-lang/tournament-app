<?php
require_once 'common/config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$user_id = intval($_SESSION['user_id']);

// Handle Tournament Join Processing Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_tournament_id'])) {
    $tourney_id = intval($_POST['join_tournament_id']);
    
    // Prevent duplicated signups
    $check = $conn->prepare("SELECT id FROM tournament_registrations WHERE tournament_id = ? AND user_id = ?");
    $check->bind_param("ii", $tourney_id, $user_id);
    $check->execute();
    $check->store_result();
    
    if($check->num_rows === 0) {
        $check->close();
        $reg = $conn->prepare("INSERT INTO tournament_registrations (tournament_id, user_id) VALUES (?, ?)");
        $reg->bind_param("ii", $tourney_id, $user_id);
        $reg->execute();
        $reg->close();
        
        // Grant 50 XP for competing
        $conn->query("UPDATE users SET xp = xp + 50 WHERE id = " . $user_id);
        $_SESSION['user_xp'] += 50;
    } else {
        $check->close();
    }
    header("Location: tournaments.php");
    exit;
}

// Fetch Tournament list coupled with registration state indicators
$sql = "SELECT t.*, 
        (SELECT COUNT(*) FROM tournament_registrations WHERE tournament_id = t.id) as players,
        (SELECT COUNT(*) FROM tournament_registrations WHERE tournament_id = t.id AND user_id = $user_id) as signed_up
        FROM tournaments t ORDER BY t.event_date ASC";
$result = $conn->query($sql);
?>

<?php include 'common/header.php'; ?>

<div class="mt-4 mb-6">
    <h1 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 tracking-wide uppercase">Championship Arena</h1>
    <p class="text-xs text-slate-500">Enlist in hyper-competitive events and secure rewards.</p>
</div>

<div class="space-y-4">
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-md flex flex-col justify-between">
                <div class="p-4 border-b border-slate-800/50 bg-gradient-to-r from-purple-950/20 to-transparent">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-cyan-400 font-mono bg-cyan-950/50 border border-cyan-800/40 px-2 py-0.5 rounded">
                            <?= sanitize($row['game']); ?>
                        </span>
                        <span class="text-xs font-bold text-emerald-400 flex items-center font-mono">
                            <i class="fa-solid fa-gift mr-1 text-[10px]"></i> <?= sanitize($row['prize_pool']); ?>
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-100 mb-2 mt-1"><?= sanitize($row['title']); ?></h3>
                    <div class="flex items-center space-x-4 text-[10px] text-slate-400 font-mono">
                        <div><i class="fa-solid fa-calendar-days text-purple-500 mr-1"></i> <?= date('M d, Y - H:i', strtotime($row['event_date'])); ?></div>
                        <div><i class="fa-solid fa-users text-cyan-500 mr-1"></i> <?= $row['players']; ?> Enlisted</div>
                    </div>
                </div>
                <div class="p-3 bg-slate-950 flex items-center justify-between">
                    <span class="text-[9px] text-slate-500 uppercase font-mono tracking-wider">Entry Fee: FREE</span>
                    <?php if($row['signed_up'] > 0): ?>
                        <button disabled class="px-4 py-1.5 bg-slate-800 text-slate-500 font-bold uppercase text-[10px] tracking-widest rounded-lg border border-slate-700 flex items-center space-x-1">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> <span>Enlisted</span>
                        </button>
                    <?php else: ?>
                        <form action="tournaments.php" method="POST">
                            <input type="hidden" name="join_tournament_id" value="<?= $row['id']; ?>">
                            <button type="submit" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold uppercase text-[10px] tracking-widest rounded-lg shadow transition-transform active:scale-95">
                                Lock Slot
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="p-8 text-center bg-slate-900 border border-slate-800 rounded-2xl">
            <i class="fa-solid fa-hourglass text-slate-600 text-3xl mb-2"></i>
            <p class="text-xs text-slate-400">No active tournaments listed.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'common/bottom.php'; ?>