<?php
require_once 'db.php';
require_once 'header.php';

$errors = [];
$success = '';

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
    
    // to handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
        } else {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        }
    }
    
    // insert into database if there are no errors
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO posts (title, content, image) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $image]);
            $success = "Post created successfully!";
        } catch(PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<h2>Create New Post</h2>

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
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="content">Content*:</label>
        <textarea id="content" name="content" rows="5" required></textarea>
    </div>
    <div>
        <label for="image">Image (optional):</label>
        <input type="file" id="image" name="image">
    </div>
    <button type="submit">Create Post</button>
</form>

<?php require_once 'footer.php'; ?>