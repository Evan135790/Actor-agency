<?php

session_start();
require_once("config.php");

// Get the actor ID from the URL.
$actorID = 0;

if (isset($_GET["id"])) {
    $actorID = (int) $_GET["id"];
}

// Stop if the actor ID is invalid.
if ($actorID <= 0) {
    die("Invalid actor ID.");
}


// Retrieve the main actor information.
$actorSQL = "
    SELECT
        ActorID,
        ActorName,
        HirePrice,
        Description
    FROM Actors
    WHERE ActorID = ?
    AND IsActive = TRUE
";

$actorStatement = $connection->prepare($actorSQL);
$actorStatement->execute([$actorID]);

$actor = $actorStatement->fetch(PDO::FETCH_ASSOC);

// Stop if no actor matches the ID.
if (!$actor) {
    die("Actor not found.");
}


// Retrieve the actor's main picture.
$imageSQL = "
    SELECT ImageURL
    FROM ActorPictures
    WHERE ActorID = ?
    ORDER BY IsPrimary DESC, DisplayOrder ASC
    LIMIT 1
";

$imageStatement = $connection->prepare($imageSQL);
$imageStatement->execute([$actorID]);

$image = $imageStatement->fetch(PDO::FETCH_ASSOC);

$imageURL = "";

if ($image) {
    $imageURL = $image["ImageURL"];
}


// Retrieve the actor's skills.
$skillSQL = "
    SELECT Skills.SkillName
    FROM ActorSkills
    INNER JOIN Skills
        ON ActorSkills.SkillID = Skills.SkillID
    WHERE ActorSkills.ActorID = ?
    ORDER BY Skills.SkillName
";

$skillStatement = $connection->prepare($skillSQL);
$skillStatement->execute([$actorID]);

$skills = $skillStatement->fetchAll(PDO::FETCH_ASSOC);


// Retrieve completed previous performances.
$performanceSQL = "
    SELECT
        FilmRoles.RoleName,
        FilmRoles.RoleType,
        ActorPerformances.Rating
    FROM ActorPerformances
    INNER JOIN FilmRoles
        ON ActorPerformances.RoleID = FilmRoles.RoleID
    WHERE ActorPerformances.ActorID = ?
    AND ActorPerformances.PerformanceStatus = 'Completed'
    ORDER BY ActorPerformances.PerformanceID DESC
";

$performanceStatement = $connection->prepare(
    $performanceSQL
);

$performanceStatement->execute([$actorID]);

$performances = $performanceStatement->fetchAll(
    PDO::FETCH_ASSOC
);


// Calculate the average rating.
$totalRating = 0;
$ratingCount = 0;

foreach ($performances as $performance) {

    if ($performance["Rating"] !== null) {

        $totalRating =
            $totalRating + (float) $performance["Rating"];

        $ratingCount++;
    }
}

$averageRating = null;

if ($ratingCount > 0) {
    $averageRating = $totalRating / $ratingCount;
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

    <title>
        <?php
        echo htmlspecialchars($actor["ActorName"]);
        ?>
    </title>

    <link
        rel="stylesheet"
        href="actor_details.css"
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

        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>

    </nav>

</header>

<main class="page-content">

    <a
        class="back-link"
        href="index.php"
    >
        ← Back to Home
    </a>

    <section class="actor-profile">

        <div class="actor-picture">

            <?php if ($imageURL != ""): ?>

                <img
                    src="<?php
                    echo htmlspecialchars($imageURL);
                    ?>"
                    alt="<?php
                    echo htmlspecialchars(
                        $actor["ActorName"]
                    );
                    ?>"
                >

            <?php else: ?>

                <div class="image-placeholder">
                    No Image
                </div>

            <?php endif; ?>

        </div>

        <div class="actor-information">

            <h1>
                <?php
                echo htmlspecialchars(
                    $actor["ActorName"]
                );
                ?>
            </h1>

            <p class="description">

                <?php
                echo htmlspecialchars(
                    $actor["Description"]
                );
                ?>

            </p>

            <p class="important-information">

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

            <p class="important-information">

                Average Rating:

                <strong>

                    <?php

                    if ($averageRating !== null) {

                        echo number_format(
                            $averageRating,
                            1
                        );

                        echo " / 10";

                    } else {

                        echo "No previous rating";
                    }

                    ?>

                </strong>

            </p>

            <a
                class="booking-button"
                href="booking.php?actor_id=<?php
                echo (int) $actor["ActorID"];
                ?>"
            >
                Book Actor
            </a>

        </div>

    </section>


    <section class="information-section">

        <h2>Skills</h2>

        <?php if (count($skills) == 0): ?>

            <p>
                No skills have been recorded.
            </p>

        <?php else: ?>

            <ul class="skill-list">

                <?php foreach ($skills as $skill): ?>

                    <li>
                        <?php
                        echo htmlspecialchars(
                            $skill["SkillName"]
                        );
                        ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        <?php endif; ?>

    </section>


    <section class="information-section">

        <h2>Previous Performances</h2>

        <?php if (count($performances) == 0): ?>

            <p>
                No previous performances have been recorded.
            </p>

        <?php else: ?>

            <table>

                <thead>

                    <tr>
                        <th>Role Name</th>
                        <th>Role Type</th>
                        <th>Rating</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($performances as $performance): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $performance["RoleName"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $performance["RoleType"]
                                );
                                ?>
                            </td>

                            <td>

                                <?php

                                if (
                                    $performance["Rating"]
                                    !== null
                                ) {

                                    echo number_format(
                                        (float)
                                        $performance["Rating"],
                                        1
                                    );

                                    echo " / 10";

                                } else {

                                    echo "Not rated";
                                }

                                ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

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