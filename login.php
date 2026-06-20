<?php
require_once 'common/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";
$mode = isset($_POST['action']) ? $_POST['action'] : 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_submit'])) {
        $mode = 'register';
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $gamer_tag = trim($_POST['gamer_tag']);
        $platform = $_POST['platform'];

        if (empty($username) || empty($email) || empty($password)) {
            $error = "All mandatory fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Malformed email address structure.";
        } else {
            // Check for preexisting accounts
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error = "Username or email is already registered.";
                $stmt->close();
            } else {
                $stmt->close();
                $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
                $ins_stmt = $conn->prepare("INSERT INTO users (username, email, password, gamer_tag, platform) VALUES (?, ?, ?, ?, ?)");
                $ins_stmt->bind_param("sssss", $username, $email, $hashed_pass, $gamer_tag, $platform);
                
                if ($ins_stmt->execute()) {
                    $success = "Account established. Authenticate below.";
                    $mode = 'login';
                } else {
                    $error = "Execution failure during database provisioning.";
                }
                $ins_stmt->close();
            }
        }
    } elseif (isset($_POST['login_submit'])) {
        $mode = 'login';
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = "Provide both identity credentials.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, xp FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($uid, $uname, $uhash, $uxp);
                $stmt->fetch();
                if (password_verify($password, $uhash)) {
                    $_SESSION['user_id'] = $uid;
                    $_SESSION['username'] = $uname;
                    $_SESSION['user_xp'] = $uxp;
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid credential combinations.";
                }
            } else {
                $error = "Account records not found.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gamers Life - Gateway</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
        
        <div class="text-center mb-6">
            <span class="text-3xl font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-cyan-400">GAMERS LIFE</span>
            <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">The Ultimate Mobile Social Hub</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-500/10 border border-red-500/30 text-red-400 text-xs rounded-xl flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i> <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i> <span><?= $success; ?></span>
            </div>
        <?php endif; ?>

        <div class="flex border-b border-slate-800 mb-6">
            <button onclick="toggleForm('login')" id="tab-login" class="flex-1 pb-3 text-sm font-bold uppercase tracking-wider text-center border-b-2 <?= $mode == 'login' ? 'border-purple-500 text-purple-400' : 'border-transparent text-slate-500' ?>">Sign In</button>
            <button onclick="toggleForm('register')" id="tab-register" class="flex-1 pb-3 text-sm font-bold uppercase tracking-wider text-center border-b-2 <?= $mode == 'register' ? 'border-purple-500 text-purple-400' : 'border-transparent text-slate-500' ?>">Create</button>
        </div>

        <form id="form-login" action="login.php" method="POST" class="<?= $mode == 'login' ? '' : 'hidden'; ?> space-y-4">
            <input type="hidden" name="action" value="login">
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                    <input type="text" name="username" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                    <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
                </div>
            </div>
            <button type="submit" name="login_submit" class="w-full py-3 mt-2 bg-purple-600 hover:bg-purple-700 font-bold uppercase text-xs tracking-widest rounded-xl transition-all active:scale-95 shadow-md">Authenticate</button>
        </form>

        <form id="form-register" action="login.php" method="POST" class="<?= $mode == 'register' ? '' : 'hidden'; ?> space-y-4">
            <input type="hidden" name="action" value="register">
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Username *</label>
                <input type="text" name="username" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Email Address *</label>
                <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Password *</label>
                <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Gamer Tag / Alias</label>
                <input type="text" name="gamer_tag" placeholder="e.g. Shroud" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1.5">Primary Rig / Platform</label>
                <select name="platform" class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-purple-500 text-slate-200">
                    <option value="PC">PC Master Race</option>
                    <option value="PlayStation">PlayStation 5</option>
                    <option value="Xbox">Xbox Series X</option>
                    <option value="Nintendo">Nintendo Switch</option>
                </select>
            </div>
            <button type="submit" name="register_submit" class="w-full py-3 mt-2 bg-gradient-to-r from-purple-600 to-cyan-600 font-bold uppercase text-xs tracking-widest rounded-xl transition-all active:scale-95 shadow-md">Register Fleet Account</button>
        </form>
    </div>

    <script>
        function toggleForm(target) {
            const loginForm = document.getElementById('form-login');
            const registerForm = document.getElementById('form-register');
            const loginTab = document.getElementById('tab-login');
            const registerTab = document.getElementById('tab-register');

            if(target === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTab.className = "flex-1 pb-3 text-sm font-bold uppercase tracking-wider text-center border-b-2 border-purple-500 text-purple-400";
                registerTab.className = "flex-1 pb-3 text-sm font-bold uppercase tracking-wider text-center border-b-2 border-transparent text-slate-500";
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                loginTab.className = "flex-1 pb-3 text-sm font-bold uppercase tracking-wider text-center border-b-2 border-transparent text-slate-500";
                registerTab.className = "flex-1 pb-3 text-sm font-bold uppercase tracking-wider text-center border-b-2 border-purple-500 text-purple-400";
            }
        }
    </script>
</body>
</html>