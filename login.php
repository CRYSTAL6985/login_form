<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="style2.css">
  </head>
  <body>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css"
      integrity="sha256-3sPp8BkKUE7QyPSl6VfBByBroQbKxKG7tsusY2mhbVY="
      crossorigin="anonymous"
    />
    <div class="container">
      <div class="row">
        <div class="col-md-11 mt-60 mx-md-auto">
          <div class="login-box bg-white pl-lg-5 pl-0 ">
            <div class="row no-gutters align-items-center">
              <div class="col-md-6">
                <div class="form-wrap bg-white">
                  <h4 class="btm-sep pb-3 mb-5">Login</h4>

                  <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                  <?php endif; ?>

                  <form class="form" method="post" action="login.php">
                    <div class="row row-gap-3">
                      <div class="col-12">
                        <div class="form-group position-relative">
                          <span class="zmdi zmdi-account"></span>
                          <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            placeholder="Email Address"
                            required
                          />
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="form-group position-relative">
                          <span class="zmdi zmdi-email"></span>
                          <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Password"
                            required
                          />
                        </div>
                      </div>
                      <div class="col-12 text-lg-right">
                        <a href="#" class="c-black">Forgot password ?</a>
                      </div>
                      <div class="col- mt-30">
                        <button
                          type="submit"
                          id="submit"
                          class="btn btn-lg btn-custom btn-dark btn-block col-12">
                          Sign In
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <div class="col-md-6">
                <div class="content text-center">
                  <div class="border-bottom pb-5 mb-5">
                    <h3 class="c-black">First time here?</h3>
                    <a href="register.php" class="btn btn-custom">Sign up</a>
                  </div>
                  <h5 class="c-black mb-4 mt-n1">Or Sign In With</h5>
                  <div class="socials">
                    <a href="#" class="zmdi zmdi-facebook"></a>
                    <a href="#" class="zmdi zmdi-twitter"></a>
                    <a href="#" class="zmdi zmdi-google"></a>
                    <a href="#" class="zmdi zmdi-instagram"></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
