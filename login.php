<?php
session_start();
include("db.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT User_ID, Name_First, Name_Last FROM USER WHERE Email = '$email' AND Password = '$password'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Set only the user ID in the session
        $_SESSION['user_id'] = $row['User_ID'];

        // Redirect to index.php after successful login
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login to Your Account</h2>
    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <button type="submit">Login</button>
    </form>

    <p>
        <a href="register.php">Create an Account</a> | 
        <a href="edit_profile.php">Edit Profile</a>
    </p>
</body>
</html>