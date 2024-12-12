<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $dob = $_POST['dob'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $conn = new mysqli("localhost:3307", "root", "", "chatterhub");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO users (username, email, password_hash, full_name, phone_number, dob) 
            VALUES ('$username', '$email', '$password_hash', '$full_name', '$phone_number', '$dob')";

    if ($conn->query($sql) === TRUE) {
        header("Location: register.php?success=1");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ConvoX</title>
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
            font-size: 24px;
            font-weight: bold;
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

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: inline-block;
            text-align: left;
            width: 100%;
        }

        label {
            font-size: 1em;
            color: #202124;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], input[type="date"] {
            width: 96%; 
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1em;
        }

        input[type="submit"] {
            width: 100%; 
            padding: 10px;
            background-color: #202124;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            border-radius:30px;
        }

        input[type="submit"]:hover {
            background-color: #FFD700;
            color: #202124;
        }

        .error {
            color: red;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .login-redirect {
            margin-top: 15px;
            text-align: center;
            font-size: 1em;
        }

        .login-redirect a {
            color: #202124;
            font-weight: bold;
            text-decoration: none;
        }

        .login-redirect a:hover {
            text-decoration: underline;
        }

        .success-message {
            color: green;
            font-size: 1em;
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fields = [
                {
                    field: document.getElementById('full_name'),
                    regex: /^[a-zA-Z\s]+$/,
                    message: "Full Name should only contain letters and spaces."
                },
                {
                    field: document.getElementById('username'),
                    regex: /^[a-zA-Z0-9_]{3,16}$/,
                    message: "Username should be 3-16 characters, alphanumeric with underscores."
                },
                {
                    field: document.getElementById('email'),
                    regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: "Enter a valid email address."
                },
                {
                    field: document.getElementById('password'),
                    regex: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/,
                    message: "Password must be at least 8 characters, including a letter and a number."
                },
                {
                    field: document.getElementById('phone_number'),
                    regex: /^\d{10}$/,
                    message: "Phone Number must be 10 digits."
                },
                {
                    field: document.getElementById('dob'),
                    regex: /.+/,
                    message: "Please select your date of birth."
                }
            ];

            fields.forEach(({ field, regex, message }) => {
                field.addEventListener('blur', () => {
                    const error = field.nextElementSibling;
                    if (!regex.test(field.value)) {
                        if (!error || !error.classList.contains('error')) {
                            const errorMessage = document.createElement('div');
                            errorMessage.textContent = message;
                            errorMessage.classList.add('error');
                            field.parentNode.insertBefore(errorMessage, field.nextSibling);
                        }
                    } else {
                        if (error && error.classList.contains('error')) {
                            error.remove();
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <header>
        <div class="logo-title">
            <img src="chatgpt.jpg" alt="Logo" class="logo">
            <h1>Convo X</h1>
        </div>
        <nav>
            <a href="welcome.php">Home</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <main>
        <h2 style="font-family:'Venite Adoremus Straight'">Register to Convo X</h2>
        <div class="container">
            <form method="POST" action="register.php" id="registrationForm">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" required>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <label for="phone_number">Phone Number:</label>
                <input type="tel" name="phone_number" id="phone_number" required>

                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" required>

                <input type="submit" value="Register Now">

                <!-- Login section below submit button -->
                <div class="login-redirect">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
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
