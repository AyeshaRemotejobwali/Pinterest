<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: index.php");
        exit;
    } else {
        $error = "Username or email already exists.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Pinterest Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #ff6b6b, #ff8e53);
        }
        .signup-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .signup-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .signup-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        .signup-container button {
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
        .signup-container button:hover {
            background: #c7001e;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        @media (max-width: 600px) {
            .signup-container {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="#" onclick="navigate('login.php')">Login</a></p>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
