<?php
session_start();
require "db.php";

// Only logged-in users allowed here (admins can also view, but normally go to admin.php)
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch only verified posts
$posts = $conn->query("
    SELECT posts.id, posts.title, posts.content, posts.created_at, users.email
    FROM posts
    JOIN users ON posts.author_id = users.id
    WHERE posts.is_verified = 1
    ORDER BY posts.created_at DESC
");
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Posts</title>
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
            <h2>Posts</h2>
            <div>
              <span class="me-3">Logged in as <?php echo htmlspecialchars($_SESSION["email"]); ?></span>
              <a href="logout.php" class="btn btn-dark btn-sm">Logout</a>
            </div>
          </div>

          <?php if ($posts->num_rows === 0): ?>
            <div class="alert alert-secondary">No posts available yet.</div>
          <?php endif; ?>

          <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="card mb-3 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($post["title"]); ?></h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post["content"])); ?></p>
                <p class="card-text">
                  <small class="text-muted">
                    By <?php echo htmlspecialchars($post["email"]); ?> on <?php echo $post["created_at"]; ?>
                  </small>
                </p>
              </div>
            </div>
          <?php endwhile; ?>

        </div>
      </div>
    </div>
  </body>
</html>
