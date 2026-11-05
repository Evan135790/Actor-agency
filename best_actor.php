<?php

session_start();
require_once("config.php");

// Retrieve all skills.
$skillSQL = "
    SELECT SkillID, SkillName
    FROM Skills
    ORDER BY SkillName
";

$skillStatement = $connection->prepare($skillSQL);
$skillStatement->execute();
$skills = $skillStatement->fetchAll(PDO::FETCH_ASSOC);

// Retrieve the different role types.
$roleSQL = "
    SELECT DISTINCT RoleType
    FROM FilmRoles
    ORDER BY RoleType
";

$roleStatement = $connection->prepare($roleSQL);
$roleStatement->execute();
$roleTypes = $roleStatement->fetchAll(PDO::FETCH_ASSOC);

// Form variables.
$message = "";
$selectedSkills = [];
$startDate = "";
$endDate = "";
$maximumBudget = "";
$roleType = "";
$results = [];

// Run the algorithm when the button is clicked.
if (isset($_POST["find_actor"])) {
    if (isset($_POST["skills"])) {
        $selectedSkills = $_POST["skills"];
    }

    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];
    $maximumBudget = (float) $_POST["budget"];
    $roleType = $_POST["role_type"];

    // Validate the input.
    if (count($selectedSkills) == 0) {
        $message = "Please select at least one skill.";
    } elseif ($endDate < $startDate) {
        $message = "The end date cannot be earlier than the start date.";
    } elseif ($maximumBudget <= 0) {
        $message = "The budget must be greater than zero.";
    } else {

        // Retrieve all active actors.
        $actorSQL = "
            SELECT ActorID, ActorName, HirePrice
            FROM Actors
            WHERE IsActive = TRUE
        ";

        $actorStatement = $connection->prepare($actorSQL);
        $actorStatement->execute();
        $actors = $actorStatement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($actors as $actor) {
            $actorID = $actor["ActorID"];

            // Check whether the actor is available.
            $availabilitySQL = "
                SELECT COUNT(*)
                FROM Bookings
                WHERE ActorID = ?
                AND Status = 'Approved'
                AND StartDate <= ?
                AND EndDate >= ?
            ";

            $availabilityStatement =
                $connection->prepare($availabilitySQL);

            $availabilityStatement->execute([
                $actorID,
                $endDate,
                $startDate
            ]);

            $bookingCount =
                $availabilityStatement->fetchColumn();

            // Only calculate scores for available actors.
            if ($bookingCount == 0) {

                // Skill score.
                $actorSkillSQL = "
                    SELECT SkillID
                    FROM ActorSkills
                    WHERE ActorID = ?
                ";

                $actorSkillStatement =
                    $connection->prepare($actorSkillSQL);

                $actorSkillStatement->execute([$actorID]);

                $actorSkills =
                    $actorSkillStatement->fetchAll(
                        PDO::FETCH_COLUMN
                    );

                $matchedSkills = 0;

                foreach ($selectedSkills as $skillID) {
                    if (in_array($skillID, $actorSkills)) {
                        $matchedSkills++;
                    }
                }

                $skillScore = round(
                    $matchedSkills /
                    count($selectedSkills) * 40,
                    1
                );

                // Rating score.
                $ratingSQL = "
                    SELECT AVG(Rating)
                    FROM ActorPerformances
                    WHERE ActorID = ?
                    AND PerformanceStatus = 'Completed'
                    AND Rating IS NOT NULL
                ";

                $ratingStatement =
                    $connection->prepare($ratingSQL);

                $ratingStatement->execute([$actorID]);

                $averageRating =
                    $ratingStatement->fetchColumn();

                if ($averageRating == null) {
                    $ratingScore = 0;
                } else {
                    $ratingScore = round(
                        $averageRating / 10 * 20,
                        1
                    );
                }

                // Price score.
                $hirePrice = $actor["HirePrice"];

                if ($hirePrice <= $maximumBudget * 0.7) {
                    $priceScore = 15;
                } elseif ($hirePrice <= $maximumBudget) {
                    $priceScore = 10;
                } else {
                    $priceScore = 0;
                }

                // Popularity score.
                $popularitySQL = "
                    SELECT COUNT(*)
                    FROM Bookings
                    WHERE ActorID = ?
                    AND Status = 'Approved'
                ";

                $popularityStatement =
                    $connection->prepare($popularitySQL);

                $popularityStatement->execute([$actorID]);

                $approvedBookings =
                    $popularityStatement->fetchColumn();

                if ($approvedBookings >= 10) {
                    $popularityScore = 15;
                } elseif ($approvedBookings >= 5) {
                    $popularityScore = 10;
                } elseif ($approvedBookings >= 1) {
                    $popularityScore = 5;
                } else {
                    $popularityScore = 0;
                }

                // Experience score.
                $experienceSQL = "
                    SELECT FilmRoles.RoleType
                    FROM ActorPerformances
                    INNER JOIN FilmRoles
                    ON ActorPerformances.RoleID = FilmRoles.RoleID
                    WHERE ActorPerformances.ActorID = ?
                    AND ActorPerformances.PerformanceStatus =
                    'Completed'
                ";

                $experienceStatement =
                    $connection->prepare($experienceSQL);

                $experienceStatement->execute([$actorID]);

                $previousRoles =
                    $experienceStatement->fetchAll(
                        PDO::FETCH_COLUMN
                    );

                $experienceScore = 0;

                foreach ($previousRoles as $previousRole) {
                    if ($previousRole == $roleType) {
                        $experienceScore = 10;
                        break;
                    }
                }

                // Calculate the total score.
                $totalScore =
                    $skillScore +
                    $ratingScore +
                    $priceScore +
                    $popularityScore +
                    $experienceScore;

                $totalScore = round($totalScore, 1);

                // Store the result.
                $results[] = [
                    "ActorName" => $actor["ActorName"],
                    "SkillScore" => $skillScore,
                    "RatingScore" => $ratingScore,
                    "PriceScore" => $priceScore,
                    "PopularityScore" => $popularityScore,
                    "ExperienceScore" => $experienceScore,
                    "TotalScore" => $totalScore
                ];
            }
        }

        // Sort actors from highest score to lowest.
        for ($i = 0; $i < count($results); $i++) {
            for ($j = $i + 1; $j < count($results); $j++) {
                if (
                    $results[$j]["TotalScore"] >
                    $results[$i]["TotalScore"]
                ) {
                    $temp = $results[$i];
                    $results[$i] = $results[$j];
                    $results[$j] = $temp;
                }
            }
        }

        if (count($results) == 0) {
            $message = "No suitable actors are available.";
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

    <title>Best Actor Pick</title>

    <link
        rel="stylesheet"
        href="best_actor.css"
    >
</head>

<body>

<header class="page-header">
    <div class="website-title">
        Actor Agency
    </div>

    <nav class="navigation">
        <a href="index.php">Home</a>

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

    <section class="form-section">

        <h1>Best Actor Pick</h1>

        <p>
            Enter the requirements for the film role.
        </p>

        <?php if ($message != ""): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label>
                Required Skills
            </label>

            <div class="skill-box">

                <?php foreach ($skills as $skill): ?>

                    <label>
                        <input
                            type="checkbox"
                            name="skills[]"
                            value="<?php
                            echo $skill["SkillID"];
                            ?>"
                            <?php
                            if (
                                in_array(
                                    $skill["SkillID"],
                                    $selectedSkills
                                )
                            ) {
                                echo "checked";
                            }
                            ?>
                        >

                        <?php
                        echo htmlspecialchars(
                            $skill["SkillName"]
                        );
                        ?>
                    </label>

                <?php endforeach; ?>

            </div>

            <label for="start_date">
                Filming Start Date
            </label>

            <input
                type="date"
                id="start_date"
                name="start_date"
                value="<?php echo htmlspecialchars($startDate); ?>"
                required
            >

            <label for="end_date">
                Filming End Date
            </label>

            <input
                type="date"
                id="end_date"
                name="end_date"
                value="<?php echo htmlspecialchars($endDate); ?>"
                required
            >

            <label for="budget">
                Maximum Budget (£)
            </label>

            <input
                type="number"
                id="budget"
                name="budget"
                value="<?php
                echo htmlspecialchars($maximumBudget);
                ?>"
                required
            >

            <label for="role_type">
                Required Role Type
            </label>

            <select
                id="role_type"
                name="role_type"
                required
            >

                <option value="">
                    Select role type
                </option>

                <?php foreach ($roleTypes as $role): ?>

                    <option
                        value="<?php
                        echo htmlspecialchars(
                            $role["RoleType"]
                        );
                        ?>"
                        <?php
                        if ($roleType == $role["RoleType"]) {
                            echo "selected";
                        }
                        ?>
                    >
                        <?php
                        echo htmlspecialchars(
                            $role["RoleType"]
                        );
                        ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <button
                type="submit"
                name="find_actor"
            >
                Find Best Actor
            </button>

        </form>

    </section>


    <?php if (count($results) > 0): ?>

        <section class="result-section">

            <h2>Best Actor Pick</h2>

            <h3>
                <?php
                echo htmlspecialchars(
                    $results[0]["ActorName"]
                );
                ?>
            </h3>

            <p class="total-score">
                Total Score:
                <strong>
                    <?php
                    echo $results[0]["TotalScore"];
                    ?>
                    / 100
                </strong>
            </p>

            <table>

                <tr>
                    <th>Score</th>
                    <th>Result</th>
                </tr>

                <tr>
                    <td>Skill Score</td>
                    <td>
                        <?php echo $results[0]["SkillScore"]; ?>
                        / 40
                    </td>
                </tr>

                <tr>
                    <td>Rating Score</td>
                    <td>
                        <?php echo $results[0]["RatingScore"]; ?>
                        / 20
                    </td>
                </tr>

                <tr>
                    <td>Price Score</td>
                    <td>
                        <?php echo $results[0]["PriceScore"]; ?>
                        / 15
                    </td>
                </tr>

                <tr>
                    <td>Popularity Score</td>
                    <td>
                        <?php
                        echo $results[0]["PopularityScore"];
                        ?>
                        / 15
                    </td>
                </tr>

                <tr>
                    <td>Experience Score</td>
                    <td>
                        <?php
                        echo $results[0]["ExperienceScore"];
                        ?>
                        / 10
                    </td>
                </tr>

            </table>

        </section>


        <?php if (count($results) > 1): ?>

            <section class="alternative-section">

                <h2>Alternative Actors</h2>

                <?php
                for ($i = 1; $i < count($results); $i++):
                ?>

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $results[$i]["ActorName"]
                        );
                        ?>

                        —

                        <strong>
                            <?php
                            echo $results[$i]["TotalScore"];
                            ?>
                        </strong>
                    </p>

                <?php endfor; ?>

            </section>

        <?php endif; ?>

    <?php endif; ?>

</main>

<footer class="page-footer">
    <p>
        Actor Agency Coursework Project
    </p>
</footer>

</body>
</html>