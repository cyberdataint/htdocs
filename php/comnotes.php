<?php
// Start session
session_start();
include("../db.php"); // Include your database connection

// Handle New Post Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        $stmt = $con->prepare("INSERT INTO forum_post (User_ID, Content) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $content);

        if ($stmt->execute()) {
            header("Location: forum.php"); // Reload the page after submission
            exit();
        } else {
            $error = "Failed to add post: " . $stmt->error;
        }
    } else {
        $error = "Post content cannot be empty.";
    }
}

// Fetch All Posts
$posts = [];
$query = "SELECT fp.Content, fp.Created_At, u.Name_First 
          FROM forum_post fp 
          JOIN USER u ON fp.User_ID = u.User_ID 
          ORDER BY fp.Created_At DESC";
$result = $con->query($query);
if ($result) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $error = "Error fetching posts: " . $con->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Forum</title>
    <link rel="stylesheet" href="../css/forum-styles.css">
</head>
<body>
<!-- Home Button -->
                <!-- Dark/Light Mode Toggle Button -->
                <button id="theme-toggle" onclick="toggleTheme()">🌙</button> <!-- Replace with icon if needed -->
            </div>

            <!-- Buttons aligned to the right -->
            <div class="home-btn-container">
                <a href="../index.php" class="home-btn">Home</a>
                <a href="addrecipe.php" class="home-btn">Add Recipe</a>
            </div>
    <div id="forum-section">
        <h1>Community Forum</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="forum.php">
                <textarea name="content" rows="4" cols="50" placeholder="Write your post here..." required></textarea>
                <button type="submit">Post</button>
            </form>
        <?php else: ?>
            <p>You must <a href="login.php">log in</a> to post in the forum.</p>
        <?php endif; ?>

        <h2>Posts</h2>
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <p><strong><?php echo htmlspecialchars($post['Name_First']); ?>:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($post['Content'])); ?></p>
                    <small>Posted on: <?php echo date('Y-m-d H:i:s', strtotime($post['Created_At'])); ?></small>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts yet. Be the first to post!</p>
        <?php endif; ?>
    </div>
</body>
</html>
