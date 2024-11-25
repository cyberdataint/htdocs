<?php
session_start();
include("../db.php");

$message = '';
$userInfo = '';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];  // Get the logged-in user's ID
    $userInfo = "<p>Welcome, User #$userId. <a href='./php/logout.php' style='margin-left: 10px; color: red;'>Logout</a></p>";
} else {
    $userInfo = "<p>You are not logged in. <a href='./php/login.php'>Login</a></p>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipeName = $con->real_escape_string($_POST['recipe-name']);
    $ingredients = $con->real_escape_string($_POST['recipe-ingredients']);
    $steps = $con->real_escape_string($_POST['recipe-steps']);
    $description = $con->real_escape_string($_POST['recipe-description']);
    $foodType = $con->real_escape_string($_POST['recipe-category'] ?? '');

    // Insert into recipes table
$insertQuery = "INSERT INTO recipes (Recipe_Name, Ingredients, Instructions, Created_By, Food_Type, Description) 
                VALUES ('$recipeName', '$ingredients', '$steps', $userId, '$foodType', '$description')";

    if ($con->query($insertQuery) === TRUE) {
        $message = "<p style='color: green;'>Recipe added successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Error adding recipe: " . $con->error . "</p>";
    }
}
?>
