<?php

session_start();
require_once("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Get the actor ID from the URL.
$actorID = 0;

if (isset($_GET["actor_id"])) {
    $actorID = (int) $_GET["actor_id"];
}

// Stop if the actor ID is invalid.
if ($actorID <= 0) {
    die("Invalid actor ID.");
}

// Retrieve the selected actor.
$actorSQL = "
    SELECT
        ActorID,
        ActorName,
        HirePrice
    FROM Actors
    WHERE ActorID = ?
    AND IsActive = TRUE
";

$actorStatement = $connection->prepare($actorSQL);
$actorStatement->execute([$actorID]);

$actor = $actorStatement->fetch(PDO::FETCH_ASSOC);

// Stop if the actor cannot be found.
if (!$actor) {
    die("Actor not found.");
}

// Retrieve film roles for the drop-down list.
$roleSQL = "
    SELECT
        RoleID,
        RoleName,
        RoleType
    FROM FilmRoles
    ORDER BY RoleName
";

$roleStatement = $connection->prepare($roleSQL);
$roleStatement->execute();

$roles = $roleStatement->fetchAll(PDO::FETCH_ASSOC);

// Variables used by the form.
$message = "";
$roleID = "";
$startDate = "";
$endDate = "";

// Process the booking form.
if (isset($_POST["submit"])) {
    $roleID = (int) $_POST["role_id"];
    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];

    if ($roleID <= 0) {
        $message = "Please select a film role.";
    } elseif($startDate < date("Y-m-d")) {
        $message = "The start date cannot be in the past.";
    } elseif ($endDate < $startDate) {
        $message = "The end date cannot be earlier than the start date.";
    } else {
        // Check whether the actor already has an approved booking.
        $bookingSQL = "
            SELECT BookingID
            FROM Bookings
            WHERE ActorID = ?
            AND Status = 'Approved'
            AND StartDate <= ?
            AND EndDate >= ?
        ";

        $bookingStatement = $connection->prepare($bookingSQL);
        $bookingStatement->execute([
            $actorID,
            $endDate,
            $startDate
        ]);

        $existingBooking = $bookingStatement->fetch(PDO::FETCH_ASSOC);

        if ($existingBooking) {
            $message = "This actor is not available during these dates.";
        } else {
            // Use the logged-in user's ID.
            $userID = $_SESSION["user_id"];

            $insertSQL = "
                INSERT INTO Bookings
                (
                    RoleID,
                    ActorID,
                    UserID,
                    StartDate,
                    EndDate,
                    Status,
                    CreatedAt
                )
                VALUES
                (?, ?, ?, ?, ?, 'Pending', CURDATE())
            ";

            $insertStatement = $connection->prepare($insertSQL);
            $insertStatement->execute([
                $roleID,
                $actorID,
                $userID,
                $startDate,
                $endDate
            ]);

            $message = "Booking request submitted successfully. Status: Pending.";

            $roleID = "";
            $startDate = "";
            $endDate = "";
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

    <title>Book Actor</title>

    <link
        rel="stylesheet"
        href="booking.css"
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

        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="page-content">

    <a
        class="back-link"
        href="actor_details.php?id=<?php echo (int) $actor["ActorID"]; ?>"
    >
        ← Back to Actor
    </a>

    <section class="booking-section">

        <h1>Book Actor</h1>

        <div class="actor-summary">
            <h2>
                <?php echo htmlspecialchars($actor["ActorName"]); ?>
            </h2>

            <p>
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
        </div>

        <?php if ($message != ""): ?>

            <div class="booking-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            action=""
            class="booking-form"
        >

            <label for="role_id">
                Film Role
            </label>

            <select
                id="role_id"
                name="role_id"
                required
            >
                <option value="">
                    Select a film role
                </option>

                <?php foreach ($roles as $role): ?>

                    <option
                        value="<?php echo (int) $role["RoleID"]; ?>"
                        <?php
                        if ($roleID == $role["RoleID"]) {
                            echo "selected";
                        }
                        ?>
                    >
                        <?php
                        echo htmlspecialchars(
                            $role["RoleName"] . " - " . $role["RoleType"]
                        );
                        ?>
                    </option>

                <?php endforeach; ?>
            </select>

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

            <button
                type="submit"
                name="submit"
            >
                Submit Booking
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
