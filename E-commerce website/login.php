<?php include 'navbar.php';
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT id, password, role, is_active FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role, $is_active);
        $stmt->fetch();

        if (password_verify($password, $hashed_password) && $is_active) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit;
        } else {
            echo "Incorrect password or account is inactive.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML form for login -->
<form action="login.php" method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Log In</button>
</form>
