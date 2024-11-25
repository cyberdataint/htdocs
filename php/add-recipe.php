<?php
session_start();
include("../db.php");

$message = '';
$userInfo = '';

session_start(); // Start the session at the top of the script
if (!isset($_SESSION['user_id'])) {
    header("Location: ./php/login.php"); // Redirect if not logged in
    exit(); // Stop further execution
}


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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_SESSION['user_id'])) {
        $message = "<p style='color: red;'>You must be logged in to add a recipe.</p>";
    } else {
        $recipeName = $con->real_escape_string($_POST['recipe-name']);
        $ingredients = $con->real_escape_string($_POST['recipe-ingredients']);
        $steps = $con->real_escape_string($_POST['recipe-steps']);
        $description = $con->real_escape_string($_POST['recipe-description']);
        $foodType = $con->real_escape_string($_POST['recipe-category'] ?? '');

        $insertQuery = "INSERT INTO recipe (Recipe_Name, Ingredients, Instructions, Created_By, Food_Type, Description)
                        VALUES ('$recipeName', '$ingredients', '$steps', {$_SESSION['user_id']}, '$foodType', '$description')";

        if ($con->query($insertQuery) === TRUE) {
            header("Location: ../index.php");
            exit();
        } else {
            $message = "<p style='color: red;'>Error adding recipe: " . htmlspecialchars($con->error) . "</p>";
        }
    }
}


?>