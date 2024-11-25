<?php
session_start();
include("../db.php");

$message = '';
$userInfo = '';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Get the logged-in user's ID
    $userInfo = "<p>Welcome, User #$userId. <a href='./php/logout.php' style='margin-left: 10px; color: red;'>Logout</a></p>";
} else {
    $userInfo = "<p>You are not logged in. <a href='./php/login.php'>Login</a></p>";
    $userId = null; // Ensure $userId is null if no session exists
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$userId) {
        $message = "<p style='color: red;'>You must be logged in to add a recipe.</p>";
    } else {
        // Sanitize inputs
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
            error_log("SQL Error: " . $con->error); // Log the error for debugging
            $message = "<p style='color: red;'>Error adding recipe: " . htmlspecialchars($con->error) . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link href="../css/styles.css" rel="stylesheet" type="text/css">
    <link href="../css/addrecipe.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- User Info -->
    <div id="login-link"><?php echo $userInfo; ?></div>

    <!-- Back to Home Button -->
    <div class="navigation">
        <a href="../index.php" class="home-button">🏠 Back to Home</a>
    </div>

    <!-- Recipe Form -->
    <main>
        <section class="recipe-form">
            <h2>Add a Recipe</h2>
            <?php echo $message; ?>
            <form action="add-recipe.php" method="POST">
                <label for="recipe-name">Recipe Name:</label>
                <input type="text" id="recipe-name" name="recipe-name" required>

                <label for="recipe-description">Description:</label>
                <textarea id="recipe-description" name="recipe-description" rows="3" required></textarea>

                <label for="recipe-ingredients">Ingredients:</label>
                <textarea id="recipe-ingredients" name="recipe-ingredients" rows="5" required></textarea>

                <label for="recipe-steps">Steps:</label>
                <textarea id="recipe-steps" name="recipe-steps" rows="5" required></textarea>

                <label for="recipe-category">Food Type (optional):</label>
                <select id="recipe-category" name="recipe-category">
                    <option value="">--Select--</option>
                    <option value="Appetizer">Appetizer</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Dessert">Dessert</option>
                </select>

                <button type="submit">Submit Recipe</button>
            </form>
        </section>
    </main>
</body>
</html>
