<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("db.php");

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs to prevent XSS
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $isGuest = isset($_POST['guest']) ? 1 : 0;

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
            $successMessage = "Account created successfully! You can now <a href='login.php'>login</a>.";
        } else {
            $errorMessage = "Error creating account. Please try again.";
            die('Execute failed: ' . $stmt->error); // Debugging error message
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Create an Account</h2>
    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php elseif ($successMessage): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <form method="post" action="register.php">
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