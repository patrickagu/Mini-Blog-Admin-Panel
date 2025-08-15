<?php
require_once 'db.php';
require_once 'header.php';

$errors = [];
$success = '';

if (!isset($_GET['id'])) {
    die("Post ID not specified");
}

$postId = $_GET['id'];

// Fetch existing post
try {
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        die("Post not found");
    }
} catch(PDOException $e) {
    die("Error fetching post: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    // Validation
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    if (empty($content)) {
        $errors[] = "Content is required";
    }
    
    $image = $post['image'];
    
    // Handle file upload if new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
        } else {
            $uploadDir = 'uploads/';
            $image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
            
            // Delete old image if it exists
            if ($post['image'] && file_exists($uploadDir . $post['image'])) {
                unlink($uploadDir . $post['image']);
            }
        }
    }
    
    // Update database if no errors
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $content, $image, $postId]);
            $success = "Post updated successfully!";
            
            // Refresh post data
            $post['title'] = $title;
            $post['content'] = $content;
            $post['image'] = $image;
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<h2>Edit Post</h2>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div>
        <label for="title">Title*:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    </div>
    <div>
        <label for="content">Content*:</label>
        <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    <div>
        <label for="image">Image (optional):</label>
        <input type="file" id="image" name="image">
        <?php if ($post['image']): ?>
            <p>Current image: <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" width="100"></p>
        <?php endif; ?>
    </div>
    <button type="submit">Update Post</button>
</form>

<?php require_once 'footer.php'; ?>