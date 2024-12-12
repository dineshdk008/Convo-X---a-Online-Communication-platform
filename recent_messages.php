<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to view messages.";
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

// Get logged-in user ID from session
$user_id = $_SESSION['user_id'];

// Fetch messages where the logged-in user is the receiver
$sql = "SELECT messages.sender_id, messages.receiver_id, messages.message, messages.sent_at, users.username AS sender_name 
        FROM messages 
        JOIN users ON messages.sender_id = users.id
        WHERE messages.receiver_id = ?
        ORDER BY messages.sent_at DESC";

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
echo '.send-message-btn { background-color: black; color: white; padding: 10px 20px; font-size: 16px; border-radius: 50px; text-decoration: none; }';
echo '.send-message-btn:hover { background-color: gold; color: black; }';
echo '</style>';
echo '</head>';
echo '<body>';

if ($result->num_rows > 0) {
    echo "<h3><center>RECEIVED MESSAGES</center></h3>";
    echo "<table>";
    echo "<thead><tr><th>From</th><th>Message</th><th>Time Sent</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        // Display message details in table format
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['sender_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['message']) . "</td>";
        echo "<td>" . date("F j, Y, g:i a", strtotime($row['sent_at'])) . "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p><center>No messages received yet.</center></p>";
}

// Send Message Button (this button can link to a new page or modal)
echo '<div style="text-align: center; margin-top: 20px;">';
echo '<a href="available_users.php" class="send-message-btn">Send Message</a>';
echo '</div>';

echo '</body></html>';

$stmt->close();
$conn->close();
?>
