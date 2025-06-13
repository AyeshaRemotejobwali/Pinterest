<?php
session_start();
require 'db.php';

$query = isset($_GET['q']) ? $_GET['q'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT pins.*, users.username FROM pins JOIN users ON pins.user_id = users.id WHERE 1=1";
$params = [];
if ($query) {
    $sql .= " AND (pins.title LIKE ? OR pins.description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}
if ($category) {
    $sql .= " AND pins.category = ?";
    $params[] = $category;
}

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$pins = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Pinterest Clone</title>
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
        .navbar input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 300px;
        }
        .filter-bar {
            padding: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-bar select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
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
        <input type="text" placeholder="Search pins..." value="<?php echo htmlspecialchars($query); ?>" onkeypress="if(event.key === 'Enter') navigate('search.php?q=' + this.value)">
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
    <div class="filter-bar">
        <select onchange="navigate('search.php?q=<?php echo urlencode($query); ?>&category=' + this.value)">
            <option value="">All Categories</option>
            <option value="Fashion" <?php if ($category == 'Fashion') echo 'selected'; ?>>Fashion</option>
            <option value="Art" <?php if ($category == 'Art') echo 'selected'; ?>>Art</option>
            <option value="Food" <?php if ($category == 'Food') echo 'selected'; ?>>Food</option>
            <option value="Travel" <?php if ($category == 'Travel') echo 'selected'; ?>>Travel</option>
            <option value="DIY" <?php if ($category == 'DIY') echo 'selected'; ?>>DIY</option>
        </select>
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
