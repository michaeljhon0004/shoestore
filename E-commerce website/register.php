<?php include 'navbar.php';
// Start the session
session_start();
include 'db_connect.php'; // Assumes you have a db_connect.php file with your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $profile_photo = ''; // File upload for profile photo can be added here if needed

    $query = "INSERT INTO users (username, email, password, profile_photo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $email, $password, $profile_photo);

    if ($stmt->execute()) {
        echo "Registration successful. Please log in.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML form for registration -->
<form action="register.php" method="post">
    Username: <input type="text" name="username" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
