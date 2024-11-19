<?php
session_start();
include("../db.php");

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errorMessage = '';
$userInfo = '';
// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT Name_First, Email FROM USER WHERE User_ID = $userId";
    $userResult = mysqli_query($con, $userQuery);

    if ($userResult) {
        $user = mysqli_fetch_assoc($userResult);
        $userInfo = "<p>Welcome, " . htmlspecialchars($user['Name_First']) . " (" . htmlspecialchars($user['Email']) . ") 
        <a href='./php/logout.php' style='margin-left: 10px; color: red;'>Logout</a></p>";
    } else {
        $userInfo = "<p>Unable to fetch user details.</p>";
    }
} else {
    $userInfo = "<p>You are not logged in. <a href='./php/login.php'>Login</a></p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Query for plain text password matching
    $query = "SELECT User_ID, Name_First, Name_Last, Password FROM USER WHERE Email = '$email'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Check if entered password matches the stored plain text password
        if ($password === $row['Password']) {
            $_SESSION['user_id'] = $row['User_ID'];
            header("Location: /index.php");
            exit();
        } else {
            $errorMessage = "Invalid email or password.";
        }
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
    <link rel="stylesheet" href="../css/styles.css">  <!-- Update with correct path -->
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <!-- You can add a logo here if needed -->
            <div id="logo">
                <img src="logo.png" alt="Logo">
            </div>
        </div>

        <!-- Home Button -->
        <div class="home-btn-container">
        <a href="/index.php" class="home-btn">Home</a>
        <a href="../php/comnotes.php" class="home-btn">Community Notes</a>
        </div>

        <div id="content_bg">
            <div id="content">
                <h2>Login to Your Account</h2>
                
                <?php if (!empty($errorMessage)): ?>
                    <p style="color: red;"><?php echo $errorMessage; ?></p>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="post" action="./login.php">
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
                    <a href="../php/register.php">Create an Account</a>  
                </p>
            </div>
        </div>
    </div>
</body>
</html>
