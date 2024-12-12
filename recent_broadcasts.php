<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access.";
    exit();
}

$host = 'localhost:3307';
$dbname = 'chatterhub';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch all broadcasts and their authors (assuming `author_id` in `blogs` refers to `users.id`)
$sql = "SELECT b.content, b.created_at, u.username
        FROM blogs b
        JOIN users u ON b.author_id = u.id
        ORDER BY b.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<style>';
echo '.create-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: black; color: white; border: none; border-radius: 30px; text-decoration: none; font-size: 16px; cursor: pointer; }';
echo '.create-btn:hover { background-color: gold;color:black; }';
echo 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
echo 'th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }';
echo 'th { background-color: #28282B; color: white; }';
echo 'td { background-color: #f4f4f4; }';
echo '</style>';
echo '</head>';
echo '<body>';

if ($result->num_rows > 0) {
    echo "<h3><center>RECENT BROADCASTS</center></h3>";
    echo "<table>";
    echo "<thead><tr><th>Author</th><th>Broadcast Message</th><th>Time Sent</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        // Display the broadcast message with author's name and timestamp
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['content']) . "</td>";
        echo "<td>" . date("F j, Y, g:i a", strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
    echo '<center><button class="create-btn" onclick="location.href=\'create_blog.php\'">Create Broadcast</button></center>';
} else {
    echo "<p><center>No broadcasts found.</center></p>";
    echo '<center><button class="create-btn" onclick="location.href=\'create_blog.php\'">Create Broadcast</button></center>';
}

echo '</body>';
echo '</html>';

$stmt->close();
$conn->close();
?>
