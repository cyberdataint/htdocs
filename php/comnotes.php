<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
if (!file_exists("../db.php")) {
    die("Database connection file not found.");
}
include("../db.php");

// Check if the request is POST and the user is logged in
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $recipeId = intval($_POST['recipe-id']);
    $noteText = trim($_POST['note-text']);

    if ($noteText === '') {
        die("Note text cannot be empty.");
    }

    // Insert the note into the database using prepared statements
    $insertNoteQuery = "INSERT INTO COMMUNITY_NOTES (Recipe_ID, User_ID, Note_Text) VALUES (?, ?, ?)";
    $stmt = $con->prepare($insertNoteQuery);
    if ($stmt) {
        $stmt->bind_param("iis", $recipeId, $userId, $noteText);
        if ($stmt->execute()) {
            header("Location: ../recipe.php?recipeId=$recipeId");
            exit();
        } else {
            echo "Error executing query: " . $stmt->error;
        }
    } else {
        echo "Error preparing query: " . $con->error;
    }
} else {
    die("Invalid request or not logged in.");
}
?>



