<?php
session_start();
include("db.php");

$userInfo = '';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT Name_First, Email FROM USER WHERE User_ID = $userId";
    $userResult = mysqli_query($con, $userQuery);

    if ($userResult) {
        $user = mysqli_fetch_assoc($userResult);
        $userInfo = "<p>Welcome, " . htmlspecialchars($user['Name_First']) . " (" . htmlspecialchars($user['Email']) . ") 
        <a href='logout.php' style='margin-left: 10px; color: red;'>Logout</a></p>";
    } else {
        $userInfo = "<p>Unable to fetch user details.</p>";
    }
} else {
    $userInfo = "<p>You are not logged in. <a href='login.php'>Login</a></p>";
}

if (isset($_GET['recipeId'])) {
    $recipeId = $_GET['recipeId'];

    // Calculate the average rating using Rating_Value column from the Rating table
    $q0 = "SELECT AVG(Rating_Value) AS avg_rating FROM RATING WHERE Recipe_ID = $recipeId;";
    $result0 = mysqli_query($con, $q0) or die(mysqli_error($con));
    $row0 = mysqli_fetch_assoc($result0);
    $avgRating = $row0['avg_rating'];
    
    // Set the star image based on average rating
    $ratingImg = "stars1.png"; // Default image
    if ($avgRating >= 5) {
        $ratingImg = "stars5.png";
    } elseif ($avgRating >= 4) {
        $ratingImg = "stars4.png";
    } elseif ($avgRating >= 3) {
        $ratingImg = "stars3.png";
    } elseif ($avgRating >= 2) {
        $ratingImg = "stars2.png";
    }

    // Fetch recipe details
    $q = "SELECT Recipe_Name, Description, Ingredients, Instructions FROM RECIPE WHERE Recipe_ID = $recipeId;";
    $result = mysqli_query($con, $q) or die(mysqli_error($con));
    $row = mysqli_fetch_assoc($result);

    $s = '<table align="center" dir="ltr">';
    $s .= '<tr><td align="left" id="foodName" colspan=2 style="padding:10px;"><h1>' . htmlspecialchars($row['Recipe_Name']) . '</h1></td></tr>';
    $s .= '<tr><td><h3>Rating: ' . number_format($avgRating, 1) . ' <img src="image/' . $ratingImg . '"/></h3></td></tr>';
    $s .= '<tr><td><h2>Ingredients</h2><p>' . htmlspecialchars($row['Ingredients']) . '</p></td></tr>';
    $s .= '<tr><td><h2>Instructions</h2><p>' . htmlspecialchars($row['Instructions']) . '</p></td></tr>';
    $s .= '</table>';

    // Fetch and display comments
    $ss = '<h2>Visitor Comments:</h2><table>';
    $q3 = "SELECT c.Comment_Text, c.Created_At, u.Name_First FROM COMMENT c JOIN USER u ON c.User_ID = u.User_ID WHERE Recipe_ID = $recipeId ORDER BY c.Created_At DESC;";
    $result3 = mysqli_query($con, $q3) or die(mysqli_error($con));

    while ($row3 = mysqli_fetch_assoc($result3)) {
        $commentDate = date('Y-m-d H:i:s', strtotime($row3['Created_At']));
        $ss .= '<tr><td><strong>' . htmlspecialchars($row3['Name_First']) . ':</strong> ' . htmlspecialchars($row3['Comment_Text']) . ' <br><small>' . $commentDate . '</small></td></tr>';
    }
    $ss .= '</table>';
} else {
    echo "<p>No recipe selected.</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recipe Details</title>
    <link href="styles.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- Login status -->
    <div id="login-link" style="text-align: right; padding: 10px;">
        <?php echo $userInfo; ?>
    </div>

    <!-- Home Button -->
    <div class="home-btn-container">
        <a href="index.php" class="home-btn">Home</a>
        <a href="comnotes.php" class="home-btn">Community Notes</a>
    </div>

    <div id="content">
        <?php echo $s; ?>
        <br>
        <?php echo $ss; ?>
    </div>
</body>
</html>
