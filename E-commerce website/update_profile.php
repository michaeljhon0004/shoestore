<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT username, email, photo FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        $photo = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo);

        $update_query = "UPDATE users SET username = ?, email = ?, photo = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssi", $username, $email, $photo, $user_id);
    } else {
        $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssi", $username, $email, $user_id);
    }
    $update_stmt->execute();
    $update_stmt->close();

    echo "Profile updated successfully!";
}
?>

<form action="update_profile.php" method="post" enctype="multipart/form-data">
    Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>
    Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
    Profile Photo: <input type="file" name="photo"><br>
    <img src="<?php echo htmlspecialchars($user['photo']); ?>" width="100" height="100" alt="Profile Photo"><br>
    <button type="submit">Update Profile</button>
</form>
