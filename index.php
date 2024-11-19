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

// Fetch recipes
$q = "SELECT Recipe_ID, Recipe_Name FROM RECIPE ORDER BY Recipe_Name";
$result = mysqli_query($con, $q) or die(mysqli_error($con));

// Display recipes
$n = 0;
$s = '<table align="center" dir="ltr"><tr>';
while ($row = mysqli_fetch_array($result)) {
    if ($n > 3) {
        $s .= '</tr><tr>';
        $n = 0;
    }
    $s .= ' <td align="center" id="foodName" style="padding:10px;">
                <div class="recipeImg" onClick="window.location=\'recipeDetails.php?recipeId='.$row['Recipe_ID'].'\'">
                    <a style="text-decoration:none;" href="recipeDetails.php?recipeId='.$row['Recipe_ID'].'">'.$row['Recipe_Name'].'</a>
                </div>
            </td>';
    $n++;
}
$s .= '</tr></table>';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Recipes</title>
    <link href="styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="login-link" style="text-align: right; padding: 10px;">
        <?php echo $userInfo; ?>
    </div>

    <div id="bg_top">
        <div id="wrapper">
            <div id="header">
                <h1>Oakland Recipe Board</h1>
            </div>
            <div id="content_bg" style="width:990px">
                <div id="content" style="width:920px">
                    <?php echo $s; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
