<?php
session_start();
require 'db.php';

$pins = $conn->query("SELECT pins.*, users.username FROM pins JOIN users ON pins.user_id = users.id ORDER BY created_at DESC LIMIT 12");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinterest Clone - Homepage</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background: #f5f5f5;
        }
        .navbar {
            background: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar a {
            text-decoration: none;
            color: #333;
            margin: 0 15px;
            font-weight: bold;
        }
        .navbar input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 300px;
        }
        .pin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .pin-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .pin-card:hover {
            transform: scale(1.05);
        }
        .pin-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .pin-card h3 {
            padding: 10px;
            font-size: 16px;
        }
        .pin-card p {
            padding: 0 10px 10px;
            color: #666;
        }
        @media (max-width: 600px) {
            .pin-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            .navbar input {
                width: 200px;
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
        <input type="text" placeholder="Search pins..." onkeypress="if(event.key === 'Enter') navigate('search.php?q=' + this.value)">
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="navigate('profile.php')">Profile</a>
                <a href="#" onclick="navigate('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="navigate('login.php')">Login</a>
                <a href="#" onclick="navigate('signup.php')">Signup</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="pin-grid">
        <?php while ($pin = $pins->fetch_assoc()): ?>
            <div class="pin-card">
                <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
                <h3><?php echo htmlspecialchars($pin['title']); ?></h3>
                <p>By <?php echo htmlspecialchars($pin['username']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
