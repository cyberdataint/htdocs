<?php
session_start();
include("db.php");

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Debugging: Output entered values (remove in production)
    echo "Email: $email<br>";
    echo "Password: $password<br>";

    // Query for plain text password matching
    $query = "SELECT User_ID, Name_First, Name_Last FROM USER WHERE Email = '$email' AND Password = '$password'";
    $result = mysqli_query($con, $query);

    // Debugging: Check for query errors
    if (!$result) {
        die("Query failed: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['User_ID'];
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Link to external CSS file -->
    <link rel="stylesheet" href="styles.css">  <!-- Update with correct path -->
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <!-- You can add a logo here if needed -->
            <div id="logo">
                <img src="logo.png" alt="Logo">
            </div>
        </div>

        <div id="content_bg">
            <div id="content">
                <h2>Login to Your Account</h2>
                
                <?php if (!empty($errorMessage)): ?>
                    <p style="color: red;"><?php echo $errorMessage; ?></p>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="input-field" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" class="input-field" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="login-btn">Login</button>
                    </div>
                </form>

                <p>
                    <a href="register.php">Create an Account</a> | 
                    <a href="edit_profile.php">Edit Profile</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
