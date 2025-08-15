<?php
require_once 'db.php';
require_once 'header.php';

// Fetch all posts from database
try {
    $stmt = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching posts: " . $e->getMessage());
}
?>

<h2>All Posts</h2>

<?php if (empty($posts)): ?>
    <p>No posts found.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Content</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?php echo htmlspecialchars($post['id']); ?></td>
            <td><?php echo htmlspecialchars($post['title']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($post['content'])); ?></td>
            <td>
                <?php if ($post['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" width="100">
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($post['created_at']); ?></td>
            <td>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a>
                <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php require_once 'footer.php'; ?>