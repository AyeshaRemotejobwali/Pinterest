<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
$pins = $conn->query("SELECT * FROM pins WHERE user_id = $user_id ORDER BY created_at DESC");
$boards = $conn->query("SELECT * FROM boards WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Pinterest Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
        }
        .navbar {
            background: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            font-weight: bold;
        }
        .profile-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .profile-header h1 {
            font-size: 32px;
            color: #333;
        }
        .pin-grid, .board-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .pin-card, .board-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .pin-card:hover, .board-card:hover {
            transform: scale(1.05);
        }
        .pin-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .pin-card h3, .board-card h3 {
            padding: 10px;
            font-size: 16px;
        }
        @media (max-width: 600px) {
            .pin-grid, .board-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="#" onclick="navigate('index.php')">Home</a>
            <a href="#" onclick="navigate('pin.php')">Create Pin</a>
            <a href="#" onclick="navigate('board.php')">Boards</a>
        </div>
        <div>
            <a href="#" onclick="navigate('profile.php')">Profile</a>
            <a href="#" onclick="navigate('logout.php')">Logout</a>
        </div>
    </div>
    <div class="profile-container">
        <div class="profile-header">
            <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
        </div>
        <h2>Your Pins</h2>
        <div class="pin-grid">
            <?php while ($pin = $pins->fetch_assoc()): ?>
                <div class="pin-card">
                    <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
                    <h3><?php echo htmlspecialchars($pin['title']); ?></h3>
                </div>
            <?php endwhile; ?>
        </div>
        <h2>Your Boards</h2>
        <div class="board-grid">
            <?php while ($board = $boards->fetch_assoc()): ?>
                <div class="board-card">
                    <h3><?php echo htmlspecialchars($board['name']); ?></h3>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
