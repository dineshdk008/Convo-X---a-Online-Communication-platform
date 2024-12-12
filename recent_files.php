<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
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
$sql = "SELECT f.file_name, f.file_size, f.file_path, f.uploaded_at, u.username AS sender_name
        FROM files f
        JOIN users u ON f.sender_id = u.id
        WHERE f.receiver_id = ?
        ORDER BY f.uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<style>';
echo 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
echo 'th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }';
echo 'th { background-color: #28282B; color: white; }';
echo 'td { background-color: #f4f4f4; }';
echo '.file-link { text-decoration: none; color: white; padding: 5px 10px; background-color: black; border-radius: 5px; }';
echo '.file-link:hover { background-color: gold; color: black; }';
echo '</style>';
echo '</head>';
echo '<body>';

if ($result->num_rows > 0) {
    echo "<h3><center>RECEIVED FILES</center></h3>";
    echo "<table>";
    echo "<thead><tr><th>File Name</th><th>Sender</th><th>File Size</th><th>Time Sent</th><th>Action</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        // Display file details in table format
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['file_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sender_name']) . "</td>";
        echo "<td>" . round($row['file_size'] / 1024, 2) . " KB</td>";
        echo "<td>" . date("F j, Y, g:i a", strtotime($row['uploaded_at'])) . "</td>";
        echo "<td><a href='" . htmlspecialchars($row['file_path']) . "' class='file-link' download>Download</a></td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p><center>No files received yet.</center></p>";
}

echo '<div style="text-align: center; margin-top: 20px;">
        <a href="available_users.php" style="text-decoration: none; background-color: black; color: white; padding: 10px 20px; font-size: 16px; border-radius: 50px; cursor: pointer; "
        onmouseover="this.style.backgroundColor=\'#FFD700\'; this.style.color=\'black\';"
        onmouseout="this.style.backgroundColor=\'black\'; this.style.color=\'white\';">Upload Files</a>
      </div>';

$stmt->close();
$conn->close();
?>

</body>
</html>
