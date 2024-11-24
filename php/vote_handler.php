<?php
session_start();
include("../db.php");

$userInfo = '';


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit(); 
}

$userId = $_SESSION['user_id'];


if (!isset($_POST['recipeId']) || !isset($_POST['ratingValue'])) {
    echo json_encode(['error' => 'Missing recipeId or ratingValue']);
    exit();
}

$recipeId = $_POST['recipeId']; 
$ratingValue = $_POST['ratingValue']; 




if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$queryCheck = "SELECT * FROM RATING WHERE User_ID = ? AND Recipe_ID = ?";
$stmt = mysqli_prepare($con, $queryCheck);
mysqli_stmt_bind_param($stmt, "ii", $userId, $recipeId);
mysqli_stmt_execute($stmt);
$resultCheck = mysqli_stmt_get_result($stmt);

if (!$resultCheck) {
    echo json_encode(['error' => 'Error checking for existing vote: ' . mysqli_error($con)]);
    exit(); 
}

if (mysqli_num_rows($resultCheck) > 0) {
    
    $queryUpdate = "UPDATE RATING SET Rating_Value = ?, Created_At = NOW() WHERE User_ID = ? AND Recipe_ID = ?";
    $stmtUpdate = mysqli_prepare($con, $queryUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "iii", $ratingValue, $userId, $recipeId);
    $resultUpdate = mysqli_stmt_execute($stmtUpdate);
    if (!$resultUpdate) {
        echo json_encode(['error' => 'Error updating vote: ' . mysqli_error($con)]);
        exit(); 
    }
} else {
    
    $queryInsert = "INSERT INTO RATING (User_ID, Recipe_ID, Rating_Value, Created_At) VALUES (?, ?, ?, NOW())";
    $stmtInsert = mysqli_prepare($con, $queryInsert);
    mysqli_stmt_bind_param($stmtInsert, "iii", $userId, $recipeId, $ratingValue);
    $resultInsert = mysqli_stmt_execute($stmtInsert);
    if (!$resultInsert) {
        echo json_encode(['error' => 'Error inserting vote: ' . mysqli_error($con)]);
        exit(); 
    }
}


$queryVoteCount = "SELECT SUM(Rating_Value) AS vote_count FROM RATING WHERE Recipe_ID = ?";
$stmtVoteCount = mysqli_prepare($con, $queryVoteCount);
mysqli_stmt_bind_param($stmtVoteCount, "i", $recipeId);
mysqli_stmt_execute($stmtVoteCount);
$resultVoteCount = mysqli_stmt_get_result($stmtVoteCount);
$voteRow = mysqli_fetch_assoc($resultVoteCount);
$currentVotes = $voteRow['vote_count'] ?? 0; 


echo json_encode(['vote_count' => $currentVotes]);
?>
