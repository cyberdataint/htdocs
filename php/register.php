<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../db.php");

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs to prevent XSS
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password']; // Use plain text password


    // Check if the email already exists
    $checkQuery = "SELECT * FROM USER WHERE Email = ?";
    $stmt = $con->prepare($checkQuery);
    if ($stmt === false) {
        die('Prepare failed: ' . $con->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->error) {
        die('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "An account with this email already exists.";
    } else {
        // Insert the new user into the USER table (without User_ID, since it's auto-incremented)
        $insertQuery = "INSERT INTO USER (Name_Last, Name_First, Email, Password, User_Guest) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($insertQuery);
        if ($stmt === false) {
            die('Prepare failed: ' . $con->error);
        }

        $stmt->bind_param("ssssi", $lastName, $firstName, $email, $password, $isGuest);

        if ($stmt->execute()) {
            $successMessage = "Account created successfully! You can now <a href='../php/login.php'>login</a>.";
        } else {
            $errorMessage = "Error creating account. Please try again.";
            die('Execute failed: ' . $stmt->error); // Debugging error message
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Recipes</title>
    <link href="../css/styles.css" rel="stylesheet" type="text/css" />
    <script>
        // JavaScript to toggle dark and light modes
        function toggleTheme() {
            const currentTheme = document.body.classList.toggle('dark-mode');
            // Store the selected theme in localStorage
            localStorage.setItem('theme', currentTheme ? 'dark' : 'light');
        }

        // On page load, set the theme based on localStorage
        window.onload = function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
        };
    </script>
</head>
<body>
    <h1>Create an Account</h1>
                    <!-- Dark/Light Mode Toggle Button -->
                <button id="theme-toggle" onclick="toggleTheme()">🌙</button> <!-- Replace with icon if needed -->
            <!-- Home Button -->
        <div class="home-btn-container">
        <a href="/index.php" class="home-btn">Home</a>
        <a href="../php/comnotes.php" class="home-btn">Community Notes</a>
        </div>
    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php elseif ($successMessage): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <form method="post" action="../php/register.php">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" required><br><br>
        
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <label for="guest">Guest User:</label>
        <input type="checkbox" name="guest" id="guest"><br><br>
        
        <button type="submit">Register</button>
    </form>
</body>
</html>
