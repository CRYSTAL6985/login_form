<?php
// Run this once in your browser (e.g. http://localhost/make_hash.php?pass=admin123)
// to get a password hash you can paste into the database.

if (isset($_GET['pass'])) {
    echo "Hash for '" . htmlspecialchars($_GET['pass']) . "': <br>";
    echo password_hash($_GET['pass'], PASSWORD_DEFAULT);
} else {
    echo "Add ?pass=yourpassword to the URL to generate a hash.";
}
