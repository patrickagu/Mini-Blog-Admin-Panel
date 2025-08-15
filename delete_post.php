<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    die("Post ID not specified");
}

$postId = $_GET['id'];

// fetch the post to get the image filename
try {
    $stmt = $db->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        die("Post not found");
    }
} catch(PDOException $e) {
    die("Error fetching post: " . $e->getMessage());
}

// delete the post from the database
try {
    $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    
    // delete the associated image if it exists
    if ($post['image'] && file_exists('uploads/' . $post['image'])) {
        unlink('uploads/' . $post['image']);
    }
    
    header("Location: posts.php?deleted=1");
    exit();
} catch(PDOException $e) {
    die("Error deleting post: " . $e->getMessage());
}
?>