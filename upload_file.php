<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to upload files.";
    exit();
}

// Database connection details
$host = 'localhost:3307';
$dbname = 'chatterhub';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    // Handle file upload
    $receiver_username = $_POST['receiver_username'];
    $file = $_FILES['fileToUpload'];

    // Check if the file is uploaded without errors
    if ($file['error'] == 0) {
        // Specify upload directory
        $uploadDir = 'uploads/';
        $fileName = basename($file['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Get file details
        $fileSize = $file['size'];
        $fileType = $file['type'];

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            // Fetch receiver's user ID from the database
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $receiver_username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $receiver = $result->fetch_assoc();
                $receiver_id = $receiver['id'];

                // Insert the file details into the database
                $stmt = $conn->prepare("INSERT INTO files (sender_id, receiver_id, file_path, file_name, file_size, file_type) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissis", $_SESSION['user_id'], $receiver_id, $uploadFilePath, $fileName, $fileSize, $fileType);
                if ($stmt->execute()) {
                    echo "<script>alert('File uploaded successfully!');</script>";
                } else {
                    echo "<script>alert('Error inserting file into database.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Receiver not found.');</script>";
            }
        } else {
            echo "<script>alert('Error moving uploaded file.');</script>";
        }
    } else {
        echo "<script>alert('Error uploading file: " . $file['error'] . "');</script>";
    }
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Fetch all usernames for the dropdown
$sql = "SELECT username FROM users WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$usernames_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - Convo X</title>
    <style>
        /* Your existing styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #FAF3DD;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            justify-content: center; 
            align-items: center;   
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: black;
            color: white;
            width: 100%;
        }

        header h1 {
            font-size: 2.5em;
            color: pink;
            margin: 0;
            display: flex;
            align-items: center;
            font-family: 'Venite Adoremus Straight';
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
        }

        .profile form button {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 20px;
            transition: background-color 0.5s, color 0.5s;
        }

        .profile form button:hover {
            background-color: gold;
            color: black;
        }

        footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px 20px;
            margin-top: auto;
            width: 100%;
            position: relative;
        }

        .footer-links a {
            color: skyblue;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .upload-container {
            padding: 20px;
            text-align: center;
            width: 100%;
            max-width: 500px; 
            max-height: 500px;
            font-size: 24px;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        input[type="file"], input[type="text"] {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
            font-size: 18px;
        }

        button {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
        }

        button:hover {
            background-color: gold;
            color: black;
        }

        .back-button {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .back-button:hover {
            background-color: gold;
            color: black;
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

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
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
    width: 94%;
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
    <h1><img src="chatgpt.jpg" alt="Convo X Logo">Convo X</h1>
    <div class="profile">
        <img src="profilepic.png" alt="User Avatar">
        <button id="editProfileBtn">Edit Profile</button>
        <form method="POST" action="">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</header>

<div class="upload-container">
    <h2>Upload File</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="receiver_username">Select receiver's username</label>
        <select name="receiver_username" id="receiver_username" required>
            <option value="" disabled selected>Select a receiver</option>
            <?php
            // Populate the dropdown with usernames
            while ($row = $usernames_result->fetch_assoc()) {
                echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
            }
            ?>
        </select>
        <input type="file" name="fileToUpload" required>
        <button type="submit">Upload File</button>
    </form>

    <!-- Back to Menu Button -->
    <form method="get" action="menu.php">
        <button class="back-button" type="submit">Back to Menu</button>
    </form>
</div>

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
<div id="overlay"></div>

<footer>
    <div class="footer-links">
        <a href="#">About Us</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Contact Support</a>
    </div>
    <p>&copy; 2024 Convo X. All rights reserved.</p>
</footer>

<script>
    // JavaScript for opening and closing the modal
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
        if (event.target == editProfileModal) {
            editProfileModal.style.display = "none";
            overlay.style.display = "none";
        }
    };
</script>
</body>
</html>
