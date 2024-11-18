<?php
session_start();
include("db.php");

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['User_ID'];
$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Update user details
    $updateQuery = "UPDATE USER SET Name_First = ?, Name_Last = ?" . ($password ? ", Password = ?" : "") . " WHERE User_ID = ?";
    $stmt = $con->prepare($updateQuery);
    
    if ($password) {
        $stmt->bind_param("sssi", $firstName, $lastName, $password, $userId);
    } else {
        $stmt->bind_param("ssi", $firstName, $lastName, $userId);
    }

    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully.";
        $_SESSION['Name_First'] = $firstName;
        $_SESSION['Name_Last'] = $lastName;
    } else {
        $errorMessage = "Error updating profile. Please try again.";
    }
}

// Fetch the current user data
$query = "SELECT Name_First, Name_Last FROM USER WHERE User_ID = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php elseif ($successMessage): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <form method="post" action="edit_profile.php">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['Name_First']); ?>" required><br><br>
        
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['Name_Last']); ?>" required><br><br>
        
        <label for="password">New Password (leave blank to keep current password):</label>
        <input type="password" name="password" id="password"><br><br>
        
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
