<?php
session_start();

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];
    $conn = new mysqli("localhost:3307", "root", "", "chatterhub");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO blogs (title, content, author_id) VALUES ('$title', '$content', '$author_id')";
    if ($conn->query($sql) === TRUE) {
        $blog_id = $conn->insert_id;
        $sql_users = "SELECT id FROM users";
        $result_users = $conn->query($sql_users);
        if ($result_users->num_rows > 0) {
            while ($row = $result_users->fetch_assoc()) {
                $user_id = $row['id'];
                $sql_message = "INSERT INTO messages (group_chat_id, sender_id, message, is_broadcast) 
                                VALUES (NULL, '$author_id', 'New blog posted: $title - $content', 1)";
                $conn->query($sql_message);
            }
        }
        echo "<div class='alert success'>Blog created and broadcasted successfully!</div>";
    } else {
        echo "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog - Convo X</title>
    <style>
        /* Include the same styles from send_message.php here */
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
            padding: 15px 20px; /* Reduced padding */
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
            border-radius: 50px;
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

        textarea, input[type="text"], input[type="number"] {
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
#editProfileModal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    max-width: 400px;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    z-index: 1000;
}

#editProfileModal form {
    display: flex;
    flex-direction: column;
}

#editProfileModal label {
    margin-top: 10px;
    font-weight: bold;
}

#editProfileModal input {
    margin-top: 5px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 30px;
}

#editProfileModal button {
    margin-top: 20px;
    padding: 10px;
    background-color: black;
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
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

        .footer-links a {
            color: skyblue;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;        
}
.profile button {
    background-color: black;
    color: white;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 10px;
    border-radius: 50px;
    margin-right: 20px;
    transition: background-color 0.5s, color 0.5s;
}

.profile button:hover {
    background-color: gold;
    color: black;
    transform: scale(1.1); 
}
.back-button {
    margin-top: 20px;
    text-align: center;
}

.back-button .menu-button {
    width: 100%;
    padding: 10px;
    background-color: black;
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    cursor: pointer;
}

.back-button .menu-button:hover {
    background-color: gold;
    color: black;
}
#dynamicContent {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
        <button id="editProfileButton">Edit Profile</button>
            <img src="profilepic.png" alt="Profile Logo">
            <form method="POST" action="">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Create and Broadcast Blog</h2>
        <form method="POST" action="create_blog.php">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required placeholder="Enter the title">

            <label for="content">Content:</label>
            <textarea id="content" name="content" required placeholder="Write your content here"></textarea>

            <div class="button-container">
                <input type="submit" value="Create Blog">
                <div class="back-button">
        <button class="menu-button" onclick="window.location.href='menu.php'">Back to Menu</button>
    </div>
            </div>
        </form>
    </div>
    <div id="dynamicContent"></div>

<div id="overlay"></div>
<!-- Edit Profile Modal -->
<div id="editProfileModal">
    <form method="POST">
        <center>
            <h3>Edit Profile</h3>
        </center>
        <label for="newUsername">New Username</label>
        <input type="text" name="newUsername" id="newUsername" placeholder="Enter your new username" required>
        <label for="newPassword">New Password</label>
        <input type="password" name="newPassword" id="newPassword" placeholder="Enter your new password" required>
        <button type="submit" name="updateProfile">Update Profile</button>
        <button type="button" id="closeModal">Cancel</button>
    </form>
</div>

<script>
// Modal logic
const editProfileBtn = document.getElementById('editProfileButton');
    const editProfileModal = document.getElementById('editProfileModal');
    const overlay = document.getElementById('overlay');
    const closeModalBtn = document.getElementById('closeModal');

    editProfileBtn.addEventListener('click', function () {
        editProfileModal.style.display = 'block';
        overlay.style.display = 'block';
    });

    closeModalBtn.addEventListener('click', function () {
        editProfileModal.style.display = 'none';
        overlay.style.display = 'none';
    });

    overlay.addEventListener('click', function () {
        editProfileModal.style.display = 'none';
        overlay.style.display = 'none';
    });
    function loadContent(url) {
        var dynamicContent = document.getElementById('dynamicContent');
        dynamicContent.style.display = 'block';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                dynamicContent.innerHTML = xhr.responseText;
            } else {
                dynamicContent.innerHTML = '<p style="color: red;">Error loading content. Please try again.</p>';
            }
        };
        xhr.send();
    }
</script>
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
