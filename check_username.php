
<?php
include 'db.php';

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "taken";
    } else {
        echo "available";
    }

    $stmt->close();
    $conn->close();
}
?>