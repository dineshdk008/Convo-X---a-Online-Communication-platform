<?php
session_start();

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email_or_username = $_POST['email_or_username'];
    $password = $_POST['password'];

    // DB connection
    $conn = new mysqli("localhost:3307", "root", "", "chatterhub");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query user by email or username
    $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: menu.php"); 
            exit;
        } else {
            $error_message = "Invalid email/username or password.";
        }
    } else {
        $error_message = "No user found with that email or username.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ChatterHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FAF3DD;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: black;
            color: white;
        }

        header h1 {
            font-size: 2.5em;
            margin: 0;
            color: pink;
            cursor: pointer;
            font-family:'Venite Adoremus Straight';
        }

        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.5s, color 0.5s;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        .logo {
            height: 40px;
            margin-right: 10px;
        }

        .logo-title {
            display: flex;
            align-items: center;
        }

        main {
            text-align: center;
            padding: 50px 20px;
            flex-grow: 1;
        }

        h2 {
            font-size: 2.0em;
            color: #202124;
            margin-bottom: 20px;
        }

        form {
            display: inline-block;
            text-align: left;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px; 
            margin: 0 auto;
        }

        label {
            font-size: 1em;
            color: #202124;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="password"] {
            width: 94%; 
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1em;
        }

        input[type="submit"] {
            width: 100%; 
            padding: 15px;
            background-color: #202124;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.2em;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #FFD700;
            color: #202124;
        }

        .signup-container {
            margin-top: 15px;
        }

        .signup-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-link:hover {
            color: #0056b3;
        }

        .error {
            color: #D32F2F;
            margin-top: 10px;
        }

        footer {
            background-color: #222;
            color: white;
            text-align: center;
            padding: 10px 20px;
        }
        .footer-links a {
            color: skyblue;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-title">
            <img src="chatgpt.jpg" alt="Logo" class="logo">
            <h1>Convo X</h1>
        </div>
        <nav>
            <a href="welcome.php">Home</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <main>
        <h3 style="font-family:'Venite Adoremus Straight';font-size:30px;">Login to Convo X</h3>
        <form method="POST" action="login.php">
            <label for="email_or_username">Email or Username:</label>
            <input type="text" name="email_or_username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">

            <div class="signup-container">
                <p style="text-align:center">Don't have an account? <a href="register.php" class="signup-link">Sign up</a></p>
            </div>

            <?php if (!empty($error_message)) : ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </form>
    </main>

    <footer>
    <div class="footer-links">
        <a href="#">About Us</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Contact Support</a>
    </div>
        <p>&copy; 2024 Convo X. All rights reserved.</p>
    </footer>
</body>
</html>