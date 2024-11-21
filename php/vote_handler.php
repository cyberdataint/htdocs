<?php
include("./db.php");
session_start();


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$userId = $_SESSION['user_id'];
$recipeId = $_POST['recipeId'];
$ratingValue = $_POST['ratingValue'];


if (!is_numeric($recipeId) || !in_array($ratingValue, [-1, 1])) {
    http_response_code(400);
    echo "Invalid input";
    exit;
}

$queryCheck = "SELECT * FROM RATING WHERE User_ID = $userId AND Recipe_ID = $recipeId";
$resultCheck = mysqli_query($con, $queryCheck);

if (mysqli_num_rows($resultCheck) > 0) {
    $queryUpdate = "UPDATE RATING SET Rating_Value = $ratingValue, Created_At = NOW() WHERE User_ID = $userId AND Recipe_ID = $recipeId";
    $resultUpdate = mysqli_query($con, $queryUpdate);

    if ($resultUpdate) {
        echo "Vote updated successfully";
    } else {
        echo "Error updating vote: " . mysqli_error($con);
    }
} else {
  
    $queryInsert = "INSERT INTO RATING (User_ID, Recipe_ID, Rating_Value, Created_At) VALUES ($userId, $recipeId, $ratingValue, NOW())";
    $resultInsert = mysqli_query($con, $queryInsert);

    if ($resultInsert) {
        echo "Vote inserted successfully";
    } else {
        echo "Error inserting vote: " . mysqli_error($con);
    }
}

$querySum = "SELECT SUM(Rating_Value) AS vote_total FROM RATING WHERE Recipe_ID = $recipeId";
$resultSum = mysqli_query($con, $querySum);
$rowSum = mysqli_fetch_assoc($resultSum);
echo $rowSum['vote_total'] ?? 0;
?>
