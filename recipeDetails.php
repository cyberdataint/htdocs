<?php
session_start();
include("./db.php");

$userInfo = '';


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

if (isset($_GET['recipeId'])) {
    $recipeId = $_GET['recipeId'];

    
    $q0 = "SELECT AVG(Rating_Value) AS avg_rating FROM RATING WHERE Recipe_ID = $recipeId;";
    $result0 = mysqli_query($con, $q0) or die(mysqli_error($con));
    $row0 = mysqli_fetch_assoc($result0);
    $avgRating = $row0['avg_rating'];
    
    
    
    $q = "SELECT r.Recipe_Name, r.Description, r.Ingredients, r.Instructions, u.Name_First AS Creator 
          FROM RECIPE r 
          JOIN USER u ON r.User_ID = u.User_ID 
          WHERE r.Recipe_ID = $recipeId;";
    $result = mysqli_query($con, $q) or die(mysqli_error($con));
    $row = mysqli_fetch_assoc($result);

    $s = '<table align="center" dir="ltr">';
    $s .= '<tr><td align="left" id="foodName" colspan=2 style="padding:10px;"><h1>' . htmlspecialchars($row['Recipe_Name']) . '</h1></td></tr>';
    $s .= '<tr><td><h3>Created by: ' . htmlspecialchars($row['Creator']) . '</h3></td></tr>'; // Display creator
    $s .= '<tr><td><h2>Ingredients</h2><p>' . htmlspecialchars($row['Ingredients']) . '</p></td></tr>';
    $s .= '<tr><td><h2>Instructions</h2><p>' . htmlspecialchars($row['Instructions']) . '</p></td></tr>';
    $s .= '</table>';

    
$voteQuery = "SELECT SUM(Rating_Value) AS vote_total FROM RATING WHERE Recipe_ID = $recipeId";
$voteResult = mysqli_query($con, $voteQuery);
$voteRow = mysqli_fetch_assoc($voteResult);
$currentVotes = $voteRow['vote_total'] ?? 0; // Default to 0 if no votes


$s .= '<div id="votes" style="text-align:center; margin-top: 20px;">
            <button onclick="submitVote(' . $recipeId . ', 1)">👍 Upvote</button>
            <button onclick="submitVote(' . $recipeId . ', -1)">👎 Downvote</button>
            <p id="vote-count">Votes: ' . $currentVotes . '</p>
       </div>';


    

    
    $ss = '<h2>Visitor Comments:</h2><table>';
    $q3 = "SELECT c.Comment_Text, c.Created_At, u.Name_First 
           FROM COMMENT c 
           JOIN USER u ON c.User_ID = u.User_ID 
           WHERE Recipe_ID = $recipeId 
           ORDER BY c.Created_At DESC;";
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
    <link href="../css/styles.css" rel="stylesheet" type="text/css">  <!-- Updated path -->
    <script>
        
        function toggleTheme() {
            const currentTheme = document.body.classList.toggle('dark-mode');
            
            localStorage.setItem('theme', currentTheme ? 'dark' : 'light');
        }

       
        window.onload = function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
        };

        function submitVote(recipeId, ratingValue) {
     const xhr = new XMLHttpRequest();
     xhr.open("POST", "php/vote_handler.php", true); 
     xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
     xhr.onreadystatechange = function () {
         if (xhr.readyState === 4 && xhr.status === 200) {
             const response = JSON.parse(xhr.responseText); 
             document.getElementById("vote-count").textContent = "Votes: " + response.vote_count; 
         }
     };
     xhr.send(`recipeId=${recipeId}&ratingValue=${ratingValue}`);
 }
    </script>
</head>
<body>
    <!-- Login status -->
    <div id="login-link" style="text-align: right; padding: 10px;">
        <?php echo $userInfo; ?>
    </div>

    <!-- Dark/Light Mode Toggle Button -->
    <button id="theme-toggle" onclick="toggleTheme()">🌙</button>

    <!-- Home Button -->
    <div class="home-btn-container">
        <a href="../index.php" class="home-btn">Home</a>
        <a href="../php/comnotes.php" class="home-btn">Community Notes</a>
    </div>

    <!-- Recipe and comments display -->
    <div id="content">
        <?php echo $s; ?>
        <br>
        <?php echo $ss; ?>
    </div>
</body>
</html>