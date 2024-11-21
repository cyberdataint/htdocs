<?php
session_start();
include('db.php');


if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$userId = $_SESSION['user_id'];
$recipeId = $_POST['recipeId']; 
$ratingValue = $_POST['ratingValue']; 


$queryCheck = "SELECT * FROM RATING WHERE User_ID = ? AND Recipe_ID = ?";
$stmt = mysqli_prepare($con, $queryCheck);
mysqli_stmt_bind_param($stmt, "ii", $userId, $recipeId);
mysqli_stmt_execute($stmt);
$resultCheck = mysqli_stmt_get_result($stmt);

if (!$resultCheck) {
    die('Error checking for existing vote: ' . mysqli_error($con)); 
}

if (mysqli_num_rows($resultCheck) > 0) {
    // User has voted before, update the vote
    $queryUpdate = "UPDATE RATING SET Rating_Value = ?, Created_At = NOW() WHERE User_ID = ? AND Recipe_ID = ?";
    $stmtUpdate = mysqli_prepare($con, $queryUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "iii", $ratingValue, $userId, $recipeId);
    $resultUpdate = mysqli_stmt_execute($stmtUpdate);
    if (!$resultUpdate) {
        die('Error updating vote: ' . mysqli_error($con)); 
    }
} else {
    // User hasn't voted, insert a new vote
    $queryInsert = "INSERT INTO RATING (User_ID, Recipe_ID, Rating_Value, Created_At) VALUES (?, ?, ?, NOW())";
    $stmtInsert = mysqli_prepare($con, $queryInsert);
    mysqli_stmt_bind_param($stmtInsert, "iii", $userId, $recipeId, $ratingValue);
    $resultInsert = mysqli_stmt_execute($stmtInsert);
    if (!$resultInsert) {
        die('Error inserting vote: ' . mysqli_error($con)); 
    }
}


$queryVoteCount = "SELECT COUNT(*) AS vote_count FROM RATING WHERE Recipe_ID = ?";
$stmtVoteCount = mysqli_prepare($con, $queryVoteCount);
mysqli_stmt_bind_param($stmtVoteCount, "i", $recipeId);
mysqli_stmt_execute($stmtVoteCount);
$resultVoteCount = mysqli_stmt_get_result($stmtVoteCount);
$voteRow = mysqli_fetch_assoc($resultVoteCount);
$currentVotes = $voteRow['vote_count'] ?? 0; // Default to 0 if no votes

echo json_encode(['vote_count' => $currentVotes]);
?>
