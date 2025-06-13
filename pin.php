<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $board_id = $_POST['board_id'];
    
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $image_url = $target_dir . basename($_FILES["image"]["name"]);
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_url)) {
        $stmt = $conn->prepare("INSERT INTO pins (user_id, title, description, category, image_url, board_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $user_id, $title, $description, $category, $image_url, $board_id);
        $stmt->execute();
        header("Location: profile.php");
        exit;
    } else {
        $error = "Failed to upload image.";
    }
}

$boards = $conn->query("SELECT * FROM boards WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Pin - Pinterest Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #ff6b6b, #ff8e53);
        }
        .pin-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }
        .pin-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .pin-container input, .pin-container textarea, .pin-container select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        .pin-container button {
            width: 100%;
            padding: 12px;
            background: #e60023;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .pin-container button:hover {
            background: #c7001e;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .pin-container {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="pin-container">
        <h2>Create a Pin</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="description" placeholder="Description" rows="4"></textarea>
            <select name="category" required>
                <option value="Fashion">Fashion</option>
                <option value="Art">Art</option>
                <option value="Food">Food</option>
                <option value="Travel">Travel</option>
                <option value="DIY">DIY</option>
            </select>
            <select name="board_id" required>
                <?php while ($board = $boards->fetch_assoc()): ?>
                    <option value="<?php echo $board['id']; ?>"><?php echo htmlspecialchars($board['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Create Pin</button>
        </form>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
