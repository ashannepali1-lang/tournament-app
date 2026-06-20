<?php
require_once '../common/config.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($admin_id, $admin_hash);
            $stmt->fetch();
            if (password_verify($password, $admin_hash)) {
                $_SESSION['admin_id'] = $admin_id;
                header("Location: index.php");
                exit;
            } else {
                $error = "Incorrect command authorization codes.";
            }
        } else {
            $error = "Administrative clearance denied.";
        }
        $stmt->close();
    } else {
        $error = "Input missing required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>HQ Command Terminal Gateway</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-slate-900 border border-red-900/30 rounded-2xl p-6 shadow-2xl">
        
        <div class="text-center mb-6">
            <span class="text-2xl font-black tracking-widest text-red-500 block uppercase"><i class="fa-solid fa-shield-halved mr-1"></i> HQ TERMINAL</span>
            <span class="text-[9px] text-slate-500 font-mono tracking-wider block uppercase mt-1">Gamers Life Control Module</span>
        </div>

        <?php if(!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/30 text-red-400 text-xs rounded-xl flex items-center space-x-2 font-mono">
                <i class="fa-solid fa-bug"></i> <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-[9px] font-bold tracking-wider font-mono uppercase text-slate-400 mb-1.5">Root Identity Identifier</label>
                <input type="text" name="username" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-xs font-mono text-slate-200 focus:outline-none focus:border-red-500">
            </div>
            <div>
                <label class="block text-[9px] font-bold tracking-wider font-mono uppercase text-slate-400 mb-1.5">Authorization Key Pass</label>
                <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-xs font-mono text-slate-200 focus:outline-none focus:border-red-500">
            </div>
            <button type="submit" class="w-full py-3 mt-2 bg-red-900/40 hover:bg-red-900/60 border border-red-700/40 text-red-400 font-bold font-mono uppercase text-xs tracking-widest rounded-xl transition-all active:scale-95">Access Command Console</button>
        </form>
    </div>
</body>
</html>