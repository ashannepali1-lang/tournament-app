<?php
$host = "127.0.0.1";
$user = "root";
$pass = "root";

// Establish initial connection without DB selected
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("<div style='color:red;'>Connection failed: " . $conn->connect_error . "</div>");
}

// Create Database
$sql = "CREATE DATABASE IF NOT EXISTS gamers_life_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    $conn->select_db("gamers_life_db");
} else {
    die("Error creating database: " . $conn->error);
}

// Table: Users
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    gamer_tag VARCHAR(50) DEFAULT 'NoTag',
    platform VARCHAR(50) DEFAULT 'PC',
    avatar VARCHAR(100) DEFAULT 'avatar1.png',
    xp INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Table: Admins
$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Table: Posts/Feed
$conn->query("CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    game_title VARCHAR(100) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Table: Tournaments
$conn->query("CREATE TABLE IF NOT EXISTS tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    game VARCHAR(100) NOT NULL,
    prize_pool VARCHAR(50) NOT NULL,
    event_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Table: Tournament Registrations
$conn->query("CREATE TABLE IF NOT EXISTS tournament_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reg (tournament_id, user_id)
)");

// Table: Marketplace Listings
$conn->query("CREATE TABLE IF NOT EXISTS marketplace (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price VARCHAR(50) NOT NULL,
    item_condition VARCHAR(50) NOT NULL,
    contact VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Seed default Admin if empty
$checkAdmin = $conn->query("SELECT * FROM admins WHERE username = 'admin'");
if ($checkAdmin->num_index === 0 || $checkAdmin->num_rows == 0) {
    $adminPass = password_hash("admin123", PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES ('admin', ?)");
    $stmt->bind_param("s", $adminPass);
    $stmt->execute();
    $stmt->close();
}

// Seed mock tournaments if empty
$checkTournaments = $conn->query("SELECT * FROM tournaments");
if ($checkTournaments->num_rows == 0) {
    $conn->query("INSERT INTO tournaments (title, game, prize_pool, event_date) VALUES 
    ('Valorant Champions Arena', 'Valorant', '$5,000 USD', '2026-07-15 18:00:00'),
    ('Apex Predators League', 'Apex Legends', '$2,500 USD', '2026-08-01 15:30:00')");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Installation Complete</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-950 text-slate-100 flex flex-col items-center justify-center min-h-screen px-4 font-sans">
    <div class="bg-slate-900 border border-purple-500/30 p-8 rounded-2xl shadow-xl max-w-sm text-center">
        <div class="text-purple-500 text-5xl mb-4">💎</div>
        <h1 class="text-2xl font-black tracking-wide uppercase mb-2 text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Database Active</h1>
        <p class="text-slate-400 text-sm mb-6 leading-relaxed">System initialized successfully. Default admin credentials configured.</p>
        <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 text-left text-xs space-y-1 mb-6 font-mono text-cyan-400">
            <div><span class="text-slate-500">User:</span> admin</div>
            <div><span class="text-slate-500">Pass:</span> admin123</div>
        </div>
        <a href="login.php" class="inline-block w-full py-3 bg-gradient-to-r from-purple-600 to-cyan-600 font-bold uppercase text-sm tracking-wider rounded-xl shadow-lg hover:brightness-110 active:scale-95 transition-all">Launch App</a>
    </div>
</body>
</html>