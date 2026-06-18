<?php
session_start();
require "db.php";

// Only logged-in admins allowed here
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle new post creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_post"])) {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $author_id = $_SESSION["user_id"];

    if ($title !== "" && $content !== "") {
        $stmt = $conn->prepare("INSERT INTO posts (title, content, author_id, is_verified) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("ssi", $title, $content, $author_id);
        $stmt->execute();
        $stmt->close();
        $message = "Post created and published.";
    } else {
        $message = "Title and content cannot be empty.";
    }
}

// Handle verify action
if (isset($_GET["verify"])) {
    $id = intval($_GET["verify"]);
    $stmt = $conn->prepare("UPDATE posts SET is_verified = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "Post #$id verified.";
}

// Handle delete action
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "Post #$id deleted.";
}

// Fetch all posts (verified and unverified) with author email
$posts = $conn->query("
    SELECT posts.id, posts.title, posts.content, posts.is_verified, posts.created_at, users.email
    FROM posts
    JOIN users ON posts.author_id = users.id
    ORDER BY posts.created_at DESC
");
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style2.css">
  </head>
  <body>
    <div class="container mt-4">
      <div class="row">
        <div class="col-md-10 mx-auto">

          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Admin Dashboard</h2>
            <div>
              <span class="me-3">Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?> (admin)</span>
              <a href="logout.php" class="btn btn-dark btn-sm">Logout</a>
            </div>
          </div>

          <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title mb-3">Create New Post</h5>
              <form method="post" action="admin.php">
                <div class="mb-3">
                  <label class="form-label">Title</label>
                  <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Content</label>
                  <textarea name="content" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="create_post" class="btn btn-custom btn-dark">Publish Post</button>
              </form>
            </div>
          </div>

          <h5 class="mb-3">All Posts</h5>
          <div class="table-responsive bg-white p-3 rounded shadow-sm">
            <table class="table table-bordered align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Content</th>
                  <th>Author</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($post = $posts->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $post["id"]; ?></td>
                    <td><?php echo htmlspecialchars($post["title"]); ?></td>
                    <td><?php echo htmlspecialchars($post["content"]); ?></td>
                    <td><?php echo htmlspecialchars($post["email"]); ?></td>
                    <td>
                      <?php if ($post["is_verified"]): ?>
                        <span class="badge bg-success">Verified</span>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo $post["created_at"]; ?></td>
                    <td>
                      <?php if (!$post["is_verified"]): ?>
                        <a href="admin.php?verify=<?php echo $post['id']; ?>" class="btn btn-sm btn-success">Verify</a>
                      <?php endif; ?>
                      <a href="admin.php?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?');">Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </body>
</html>
