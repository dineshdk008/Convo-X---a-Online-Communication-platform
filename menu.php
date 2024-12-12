<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost:3307';
$dbname = 'chatterhub';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProfile'])) {
    $newUsername = htmlspecialchars($_POST['newUsername'], ENT_QUOTES, 'UTF-8');
    $newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

    $updateQuery = "UPDATE users SET username = ?, password_hash = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);

    if ($stmt) {
        $stmt->bind_param("ssi", $newUsername, $newPassword, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $_SESSION['username'] = $newUsername; 
            echo "<script>alert('Profile updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating profile: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Failed to prepare statement');</script>";
    }
}

// Get logged-in user details from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch received files for the logged-in user
$sql = "SELECT * FROM files WHERE receiver_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$files = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Convo X</title>
    <style>
body {
            font-family: Arial, sans-serif;
            background-color: #FAF3DD;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            color: pink;
            font-family:'Venite Adoremus Straight';
            margin: 0;
            display: flex;
            align-items: center;
        }

        header h1 img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            border-radius: 50%;
            width: 60px;
            height: 40px;
            margin-right: 10px;
        }

        .profile a {
            margin-right: 15px;
            text-decoration: none;
            color: white;
            font-size: 20px;
            background-color: black;
            padding: 5px 10px;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.5s, color 0.5s;
        }

        .profile a:hover {
            background-color: gold;
            color: black;
            transform: scale(1.1); 
        }

        .profile form button {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 20px;
        }

        .profile form button:hover {
            background-color: gold;
            color: black;
        }

        .welcome-section {
            text-align: center;
            padding: 20px;
            background-color: #28282B;
            color: pink;
            font-size:20px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
            text-align: center;
        }

        .menu-item {
            background-color: #28282B;
            border-radius: 30px;
            font-size:20px;
            font-weight:bold;
            padding: 20px;
            color: lightgreen;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .menu-item:hover {
            background-color: #FFD700;
            color: black;
        }

        footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px 20px;
            margin-top: auto;
        }

        .footer-links a {
            color: skyblue;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        #dynamicContent {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .received-files {
            padding: 20px;
            background-color: #f4f4f4;
            margin-top: 20px;
            border-radius: 10px;
        }

        .file-item {
            padding: 10px;
            background-color: #28282B;
            color: gold;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-item:hover {
            background-color: #FFD700;
            color: black;
        }

        .file-name {
            font-size: 16px;
            font-weight: bold;
        }

        .file-size {
            font-size: 14px;
            color: #aaa;
        }

        .file-link {
            color: gold;
            text-decoration: none;
            font-size: 14px;
        }

        .file-link:hover {
            text-decoration: underline;
        }
        .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4); 
        padding-top: 60px;
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 400px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    label {
        font-weight: bold;
    }

    input[type="email"], input[type="password"], input[type="text"] 
    {
    width: 100%;
    padding: 10px;
    margin: 5px 0 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.12);
    font-size: 14px;
}

    button {
        background-color: black;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: gold;
        color: black;
    }
.message-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.message-table th, .message-table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #FFD700;
}

.message-table th {
    background-color: #28282B;
    color: gold;
}

.message-table tr:nth-child(even) {
    background-color: #333;
}

.message-table tr:hover {
    background-color: #FFD700;
    color: black;
}

.message-table td {
    color: gold;
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
    width:94%;
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
    </style>
</head>
<body>
<header>
    <h1>
        <img src="chatgpt.jpg" alt="Logo"> Convo X
    </h1>
    <div class="profile">
        <a href="#" id="editProfileBtn">Edit Profile</a>
        <img src="profilepic.png" alt="Profile">
        <form method="POST" action="logout.php">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</header>

<section class="welcome-section">
    <h2 id="welcomeMessage">Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>What would you like to do today?</p>
</section>

<section class="menu-grid">
    <a href="javascript:void(0);" class="menu-item" id="messagesBtn">Messages</a>
    <a href="javascript:void(0);" class="menu-item" id="createBlogBtn">Broadcasting</a>
    <a href="javascript:void(0);" class="menu-item" id="uploadFileBtn">File Uploads</a>
</section>
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
<div id="overlay"></div>

<div id="dynamicContent"></div>

<footer>
    <div class="footer-links">
        <a href="#">About Us</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Contact Support</a>
    </div>
    <p>&copy; 2024 Convo X. All rights reserved.</p>
</footer>

<script>
    // JavaScript for modal and dynamic content
    const editProfileBtn = document.getElementById('editProfileBtn');
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


    // Close modal if user clicks outside the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    function loadContent(url) {
        var dynamicContent = document.getElementById('dynamicContent');
        dynamicContent.style.display = 'block';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                dynamicContent.innerHTML = xhr.responseText;
                // Ensure the welcome message color stays gold
                document.getElementById('welcomeMessage').style.color = 'pink';
            } else {
                dynamicContent.innerHTML = '<p style="color: red;">Error loading content. Please try again.</p>';
            }
        };
        xhr.send();
    }

    document.getElementById('createBlogBtn').addEventListener('click', function () {
        loadContent('recent_broadcasts.php');
    });

    document.getElementById('messagesBtn').addEventListener('click', function () {
    loadContent('recent_messages.php'); // Load messages from the new PHP file
});


    document.getElementById('uploadFileBtn').addEventListener('click', function () {
        loadContent('recent_files.php'); 
    });
</script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
