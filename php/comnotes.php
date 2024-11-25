<?php
session_start();
include("../db.php");


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





// Include the database connection
if (!file_exists("../db.php")) {
    die("Database connection file not found.");
}
include("../db.php");

// Validate Recipe ID
if (!isset($_GET['recipeId']) || !is_numeric($_GET['recipeId'])) {
    die("Invalid or missing Recipe ID.");
}
$recipeId = intval($_GET['recipeId']);

// Notes form logic
$notesForm = '';
if (isset($_SESSION['user_id'])) {
    $notesForm = '
    <form method="POST" action="./php/add_note.php">
        <textarea name="note-text" rows="4" cols="50" placeholder="Add your community note here..." required></textarea>
        <input type="hidden" name="recipe-id" value="' . htmlspecialchars($recipeId) . '">
        <button type="submit">Add Note</button>
    </form>';
} else {
    $notesForm = '<p>You must <a href="./php/login.php">log in</a> to add community notes.</p>';
}

// Fetch notes from the database
$notesQuery = "SELECT n.Note_Text, n.Created_At, u.Name_First 
               FROM COMMUNITY_NOTES n 
               JOIN USER u ON n.User_ID = u.User_ID 
               WHERE n.Recipe_ID = ? 
               ORDER BY n.Created_At DESC";
$stmt = $con->prepare($notesQuery);
$notesSection = '<h2>Community Notes:</h2><table>';
if ($stmt) {
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $notesResult = $stmt->get_result();

    while ($noteRow = $notesResult->fetch_assoc()) {
        $noteDate = date('Y-m-d H:i:s', strtotime($noteRow['Created_At']));
        $notesSection .= '<tr><td><strong>' . htmlspecialchars($noteRow['Name_First']) . ':</strong> ' 
                        . htmlspecialchars($noteRow['Note_Text']) 
                        . ' <br><small>' . $noteDate . '</small></td></tr>';
    }
    $notesResult->close();
    $notesSection .= '</table>';
} else {
    $notesSection .= '<tr><td>No notes available for this recipe.</td></tr></table>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Notes</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <div id="community-notes-section">
        <?php echo $notesForm; ?>
        <br>
        <?php echo $notesSection; ?>
    </div>
</body>
</html>



