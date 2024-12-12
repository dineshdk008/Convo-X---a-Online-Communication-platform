<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$error_message = '';
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    error_log("Form is being submitted!");
    $message = $_POST['message'];
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['user_id'];
    error_log("Message: " . $message);
    error_log("Receiver ID: " . $receiver_id);
    error_log("Sender ID: " . $sender_id);
    if (empty($message) || empty($receiver_id)) {
        $error_message = "Message or receiver is missing.";
    } else {
        $conn = new mysqli("localhost:3307", "root", "", "chatterhub");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Message sent successfully!";
            header("Location: send_message.php");  
            exit();  
        } else {
            $_SESSION['error_message'] = "Error: " . $conn->error;
        }
        $stmt->close();
        $conn->close();
    }
}
$conn = new mysqli("localhost:3307", "root", "", "chatterhub");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$available_users_sql = "SELECT id, username FROM users WHERE id != ?";
$stmt = $conn->prepare($available_users_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$available_users_result = $stmt->get_result();
$users = [];
while ($row = $available_users_result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message - Convo X</title>
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #FAF3DD;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px; 
            background-color: black;
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header .logo-title {
            display: flex;
            align-items: center;
        }

        header h1 {
            font-size: 2.5em;
            color: pink;
            margin: 0;
            cursor: pointer;
            font-family:'Venite Adoremus Straight';
        }

        .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            border-radius: 50%;
            width: 60px;
            height: 40px;
            margin-right: 20px;
        }

        .profile form button {
            background-color: black;
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 10px;
            border-radius: 30px;
            transition: background-color 0.5s, color 0.5s;
        }

        .profile form button:hover {
            background-color: gold;
            color: black;
            transform: scale(1.1); 
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
        }

        .logo {
            height: 40px;
            margin-right: 10px;
        }

        h2 {
            color: black;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        textarea, input[type="text"], input[type="number"], select {
            width: 96%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: gold;
            color:black;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }

        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
        }

        .back-button input[type="button"] {
            width: 100%;
            padding: 10px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
        }

        .back-button input[type="button"]:hover {
            background-color: gold;
            color:black;
        }

        footer {
            background-color: #222;
            color: white;
            text-align: center;
            padding: 10px 20px;
            margin-top: auto;
        }

        #dynamicContent {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        a.button {
            display: inline-block;
            padding: 10px 20px;
            background-color: black;
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-size: 20px;
            cursor: pointer;
            margin-right: 20px;
            text-align: center;
            transition: background-color 0.5s, color 0.5s;
        }

        a.button:hover {
            background-color: gold;
            color: black;
            transform: scale(1.1); 
        }
        #editProfileModal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    max-width: 500px;  
    background-color: #fff;  
    padding: 30px;  
    border-radius: 10px; 
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); 
    z-index: 1000;
}

#editProfileModal form {
    display: flex;
    flex-direction: column;
    gap: 15px;  
}

#editProfileModal label {
    font-size: 16px; 
    color: #333;  
    font-weight: bold;
}

#editProfileModal input {
    padding: 12px;
    font-size: 16px; 
    border: 1px solid #ccc;  
    border-radius: 30px; 
    width: 96%;
}

#editProfileModal button {
    margin-top: 20px;
    padding: 12px;
    background-color: #000;  
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-size: 16px;
}

#editProfileModal button:hover {
    background-color: gold;  
    color: black;
}

#overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);  
    z-index: 999;
}
    </style>
</head>
<body>
    <header>
        <div class="logo-title">
            <img src="chatgpt.jpg" alt="Logo" class="logo">
            <h1>Convo X</h1>
        </div>

        <div class="profile">
            <a href="#" id="editProfileBtn" class="button">Edit Profile</a>
            <img src="profilepic.png" alt="Profile Logo">
            <form method="POST" action="">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    </header>
    <div class="container">
        <h2 style="font-family:'Venite Adoremus Straight'">Send Message</h2>
        <form method="POST" action="send_message.php">
            <!-- Message -->
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea name="message" required placeholder="Type your message here..."></textarea>
            </div>

            <!-- Receiver Username (Dropdown) -->
            <div class="form-group">
                <label for="receiver_id">Receiver:</label>
                <select name="receiver_id" required>
                    <option value="" disabled selected>Select receiver</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" value="Send Message">
            </div>

            <?php if (!empty($_SESSION['success_message'])): ?>
                <p class="success-message"><?php echo $_SESSION['success_message']; ?></p>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error_message'])): ?>
                <p class="error-message"><?php echo $_SESSION['error_message']; ?></p>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        </form>
        <div class="back-button">
            <a href="menu.php">
                <input type="button" value="Back to Menu">
            </a>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Convo X. All rights reserved.</p>
    </footer>
</body>
</html>
