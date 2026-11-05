<?php

session_start();
require_once __DIR__ . "/config.php";

// Store the search text.
// If nothing has been entered, an empty string is used.
$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

// Retrieve active actors whose names match the search.
$sql = "
    SELECT
        ActorID,
        ActorName,
        Description,
        HirePrice
    FROM Actors
    WHERE IsActive = TRUE
    AND ActorName LIKE ?
    ORDER BY ActorName
";

$statement = $connection->prepare($sql);

$statement->execute([
    "%" . $search . "%"
]);

$actors = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Actor Agency</title>

    <link
        rel="stylesheet"
        href="index.css"
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

        <?php if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == "Admin"): ?>
            <a href="manage_actor.php">Manage Actors</a>
        <?php endif; ?>

        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>

    </nav>

</header>

<main class="page-content">

    <section class="intro-section">

        <h1>Find an Actor</h1>

        <p>
            Browse actors represented by the agency
            or search for an actor by name.
        </p>

        <form
            class="search-form"
            method="GET"
            action="index.php"
        >

            <input
                type="text"
                name="search"
                placeholder="Enter actor name"
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">
                Search
            </button>

        </form>

    </section>

    <section class="actor-section">

        <h2>Featured Actors</h2>

        <?php if (count($actors) == 0): ?>

            <div class="no-results">

                <p>
                    No actors were found.
                </p>

                <a href="index.php">
                    Show all actors
                </a>

            </div>

        <?php else: ?>

            <div class="actor-grid">

                <?php foreach ($actors as $actor): ?>

                    <article class="actor-card">

                        <div class="actor-photo">
                            No Image
                        </div>

                        <div class="actor-details">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $actor["ActorName"]
                                );
                                ?>
                            </h3>

                            <p class="actor-description">
                                <?php
                                echo htmlspecialchars(
                                    $actor["Description"]
                                );
                                ?>
                            </p>

                            <p class="actor-price">

                                Hire Price:

                                <strong>
                                    £<?php
                                    echo number_format(
                                        (float) $actor["HirePrice"],
                                        2
                                    );
                                    ?>
                                </strong>

                            </p>

                            <a
                                class="view-button"
                                href="actor_details.php?id=<?php
                                echo (int) $actor["ActorID"];
                                ?>"
                            >
                                View Details
                            </a>
        
                        </div>
           
                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<footer class="page-footer">

    <p>
        Actor Agency Coursework Project
    </p>

</footer>

</body>

</html>