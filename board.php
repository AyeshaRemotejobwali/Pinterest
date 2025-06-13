<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_board'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO boards (user_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $name);
    $stmt->execute();
    header("Location: board.php");
    exit;
}

if (isset($_GET['board_id'])) {
    $board_id = $_GET['board_id'];
    $pins = $conn->query("SELECT pins.* FROM pins JOIN board_pins ON pins.id = board_pins.pin_id WHERE board_pins.board_id = $board_id");
} else {
    $pins = $conn->query("SELECT * FROM pins WHERE user_id = $user_id");
}

$boards = $conn->query("SELECT * FROM boards WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boards - Pinterest Clone</title>
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
        .board-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .board-form {
            margin-bottom: 20px;
        }
        .board-form input {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
        }
        .board-form button {
            padding: 12px 20px;
            background: #e60023;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .board-grid, .pin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .board-card, .pin-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .board-card:hover, .pin-card:hover {
            transform: scale(1.05);
        }
        .pin-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .board-card h3, .pin-card h3 {
            padding: 10px;
            font-size: 16px;
        }
        @media (max-width: 600px) {
            .board-grid, .pin-grid {
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
    <div class="board-container">
        <h2>Create a Board</h2>
        <form class="board-form" method="POST">
            <input type="text" name="name" placeholder="Board Name" required>
            <button type="submit" name="create_board">Create</button>
        </form>
        <h2>Your Boards</h2>
        <div class="board-grid">
            <?php while ($board = $boards->fetch_assoc()): ?>
                <div class="board-card">
                    <h3><a href="#" onclick="navigate('board.php?board_id=<?php echo $board['id']; ?>')"><?php echo htmlspecialchars($board['name']); ?></a></h3>
                </div>
            <?php endwhile; ?>
        </div>
        <h2>Pins in Board</h2>
        <div class="pin-grid">
            <?php while ($pin = $pins->fetch_assoc()): ?>
                <div class="pin-card">
                    <img src="<?php echo htmlspecialchars($pin['image_url']); ?>" alt="<?php echo htmlspecialchars($pin['title']); ?>">
                    <h3><?php echo htmlspecialchars($pin['title']); ?></h3>
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
