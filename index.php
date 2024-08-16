<?php
global $conn;
include 'db.php';

// Add Post, Comment, or Reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $user_id = 1; // Assume user_id is 1 for simplicity
    $content = $conn->real_escape_string($_POST['content']);
    $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : NULL;
    $type = $_POST['type']; // 'post', 'comment', or 'reply'

    if ($action == 'add') {
        $sql = "INSERT INTO posts (user_id, content, parent_id, type) 
                VALUES ('$user_id', '$content', ".($parent_id ? "'$parent_id'" : "NULL").", '$type')";
    } elseif ($action == 'edit') {
        $id = $_POST['id'];
        $sql = "UPDATE posts SET content='$content', updated_at=CURRENT_TIMESTAMP WHERE id=$id";
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM posts WHERE id=$id OR parent_id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); // Redirect after action
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch all posts
$posts = $conn->query("SELECT * FROM posts WHERE type='post' AND parent_id IS NULL ORDER BY created_at DESC");

// Function to fetch and display replies recursively
function display_replies($parent_id, $conn) {
    $replies = $conn->query("SELECT * FROM posts WHERE parent_id=$parent_id ORDER BY created_at ASC");

    while ($reply = $replies->fetch_assoc()) { ?>
        <div class="reply" style="margin-left: 40px;">
            <p><?php echo $reply['content']; ?></p>
            <form method="POST" action="index.php" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $reply['id']; ?>">
                <textarea name="content"><?php echo $reply['content']; ?></textarea>
                <input type="hidden" name="action" value="edit">
                <button type="submit">Edit</button>
            </form>
            <form method="POST" action="index.php" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $reply['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit">Delete</button>
            </form>
            <br>
            <form method="POST" action="index.php">
                <textarea name="content" placeholder="Write a reply..."></textarea>
                <input type="hidden" name="type" value="reply">
                <input type="hidden" name="parent_id" value="<?php echo $reply['id']; ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit">Reply</button>
            </form>

            <!-- Recursively display nested replies -->
            <?php display_replies($reply['id'], $conn); ?>
        </div>
    <?php }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs</title>
    <style>
        .post, .comment, .reply {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .comment, .reply {
            margin-left: 20px;
        }
    </style>
</head>
<body>

<h1>Blogs</h1>

<!-- Add New Post -->
<form method="POST" action="index.php">
    <textarea name="content" placeholder="Write a new post..."></textarea><br>
    <input type="hidden" name="type" value="post">
    <input type="hidden" name="action" value="add">
    <button type="submit">Post</button>
</form>

<hr>

<!-- Display Posts -->
<?php while ($post = $posts->fetch_assoc()) { ?>
    <div class="post">
        <p><?php echo $post['content']; ?></p>
        <form method="POST" action="index.php" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <textarea name="content"><?php echo $post['content']; ?></textarea>
            <input type="hidden" name="action" value="edit">
            <button type="submit">Edit</button>
        </form>
        <form method="POST" action="index.php" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit">Delete</button>
        </form>
        <br>
        <form method="POST" action="index.php">
            <textarea name="content" placeholder="Write a comment..."></textarea>
            <input type="hidden" name="type" value="comment">
            <input type="hidden" name="parent_id" value="<?php echo $post['id']; ?>">
            <input type="hidden" name="action" value="add">
            <button type="submit">Comment</button>
        </form>

        <!-- Fetch and display comments -->
        <?php display_replies($post['id'], $conn); ?>
    </div>
<?php } ?>

</body>
</html>
