<?php
require_once 'header.php';

// Fetch posts from external API
$apiUrl = 'https://jsonplaceholder.typicode.com/posts';
$response = file_get_contents($apiUrl);
$posts = json_decode($response, true);

// Limit to first 5 posts
$posts = array_slice($posts, 0, 5);
?>

<h2>External Posts (First 5)</h2>

<?php if (empty($posts)): ?>
    <p>No posts found from the API.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Body</th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?php echo htmlspecialchars($post['id']); ?></td>
            <td><?php echo htmlspecialchars($post['title']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($post['body'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php require_once 'footer.php'; ?>