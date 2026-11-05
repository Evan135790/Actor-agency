<?php

session_start();

require_once("config.php");

$message = "";
$username = "";

if (isset($_POST["login"])) {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username == "" || $password == "") {

        $message = "Please enter your username and password.";

    } else {

        $sql = "
            SELECT
                UserID,
                Username,
                PasswordHash,
                UserRole
            FROM Users
            WHERE Username = ?
        ";

        $statement = $connection->prepare($sql);
        $statement->execute([$username]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (
            $user &&
            password_verify(
                $password,
                $user["PasswordHash"]
            )
        ) {

            $_SESSION["user_id"] =
                $user["UserID"];

            $_SESSION["username"] =
                $user["Username"];

            $_SESSION["user_role"] =
                $user["UserRole"];

            header("Location: index.php");
            exit;

        } else {

            $message =
                "Incorrect username or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login</title>

    <link
        rel="stylesheet"
        href="login.css"
    >
</head>

<body>

<header class="page-header">

    <div class="website-title">
        Actor Agency
    </div>

    <nav class="navigation">

        <a href="index.php">
            Home
        </a>

        <a href="best_actor.php">
            Best Actor Pick
        </a>

    </nav>

</header>


<main class="page-content">

    <section class="login-section">

        <h1>Login</h1>

        <p>
            Enter your username and password.
        </p>


        <?php if ($message != ""): ?>

            <div class="message">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form method="POST">

            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                value="<?php
                echo htmlspecialchars($username);
                ?>"
                required
            >


            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >


            <button
                type="submit"
                name="login"
            >
                Login
            </button>

        </form>

    </section>

</main>


<footer class="page-footer">

    <p>
        Actor Agency Coursework Project
    </p>

</footer>

</body>
</html>