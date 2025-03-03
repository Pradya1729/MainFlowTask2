<?php
session_start();

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'user_db';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle User Registration
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        echo "Signup successful! <a href='login.php'>Login</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle User Login
// Handle User Login
// Handle User Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user'] = $username;
            header("Location: index.php");
            exit();
        } else {
            echo "Invalid password!<br>";
            echo "Entered: " . htmlspecialchars($password) . "<br>";
            echo "Stored Hash: " . $hashed_password . "<br>";
        }
    } else {
        echo "User not found!";
    }
    $stmt->close();
}


// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login System</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background:url("4.avif") repeat center center / cover}
        .container { background: #fff; padding: 45px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); border-radius: 5px; }
        input, button { display: block; width: 100%; margin: 10px 0; padding: 10px; }
        button { background: #28a745; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user'])): ?>
            <h2>Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>
            <a href="index.php?logout=true">Logout</a>
        <?php else: ?>
            <h2>Login</h2>
            <form method="POST">
                <input type="text" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <h2>Sign Up</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup">Sign Up</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
