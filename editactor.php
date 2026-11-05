<?php
session_start();

require_once("config.php");

// Only Admin can open this page.
if (
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] != "Admin"
) {
    die("Access denied.");
}

$message = "";

// Get actor ID from the URL.
$actorID = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($actorID <= 0) {
    die("Invalid actor ID.");
}

// Get the selected actor.
$sql = "
    SELECT
        ActorID,
        ActorName,
        HirePrice,
        Description
    FROM Actors
    WHERE ActorID = ?
    AND IsActive = TRUE
";

$statement = $connection->prepare($sql);
$statement->execute([$actorID]);

$actor = $statement->fetch(PDO::FETCH_ASSOC);

if (!$actor) {
    die("Actor not found.");
}

// Get all skills.
$sql = "
    SELECT
        SkillID,
        SkillName
    FROM Skills
    ORDER BY SkillName
";

$statement = $connection->prepare($sql);
$statement->execute();

$skills = $statement->fetchAll(PDO::FETCH_ASSOC);

// Get the selected actor's skills.
$sql = "
    SELECT SkillID
    FROM ActorSkills
    WHERE ActorID = ?
";

$statement = $connection->prepare($sql);
$statement->execute([$actorID]);

$actorSkills = $statement->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Actors Details</title>
    <link rel="stylesheet" href="manage_actor.css">
</head>

<body>

<header class="page-header">
    <div class="website-title">
        Actor Agency
    </div>

    <nav class="navigation">
        <a href="index.php">Home</a>
        <a href="best_actor.php">Best Actor Pick</a>
        <a href="manage_actor.php">Manage Actors</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>

    <h1>Edit Actor Details</h1>

    <!-- Presenting database information onto textboxes -->

    <label>Actor ID</label>
    <input
        type="text"
        value="<?php echo $actor["ActorID"]; ?>"
        disabled
    >

    <label>Actor Name</label>
    <input
        type="text"
        name="actor_name"
        value="<?php echo htmlspecialchars($actor["ActorName"]); ?>"
    >

    <label>Hire Price</label>
    <input
        type="number"
        name="hire_price"
        value="<?php echo $actor["HirePrice"]; ?>"
    >

    <label>Description</label>
    <input
        type="text"
        name="description"
        value="<?php echo htmlspecialchars($actor["Description"]); ?>"
    >

    <label>Skills</label>

    <?php foreach ($skills as $skill): ?>

        <div>
            <input
                type="checkbox"
                name="skills[]"
                value="<?php echo $skill["SkillID"]; ?>"
                style="width: auto;"
                <?php
                if (in_array($skill["SkillID"], $actorSkills)) {
                    echo "checked";
                }
                ?>
            >

            <?php echo htmlspecialchars($skill["SkillName"]); ?>
        </div>

    <?php endforeach; ?>

</main>

</body>

</html>