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

// Get all skills.
$skillSQL = "
    SELECT SkillID, SkillName
    FROM Skills
    ORDER BY SkillName
";

$skillStatement = $connection->prepare($skillSQL);
$skillStatement->execute();

$skills = $skillStatement->fetchAll(PDO::FETCH_ASSOC);


// Add a new actor.
if (isset($_POST["add_actor"])) {

    $actorID = (int) $_POST["actor_id"];
    $actorName = trim($_POST["actor_name"]);
    $hirePrice = (float) $_POST["hire_price"];
    $description = trim($_POST["description"]);
    $selectedSkills = $_POST["skills"] ?? [];

    if (
        $actorID > 0 &&
        $actorName != "" &&
        $hirePrice > 0 &&
        $description != "" &&
        count($selectedSkills) > 0
    ) {

        // Add the actor.
        $actorSQL = "
            INSERT INTO Actors
            (ActorID, ActorName, HirePrice, Description, IsActive)
            VALUES (?, ?, ?, ?, TRUE)
        ";

        $actorStatement = $connection->prepare($actorSQL);

        $actorStatement->execute([
            $actorID,
            $actorName,
            $hirePrice,
            $description
        ]);


        // Add the actor's skills.
        $actorSkillSQL = "
            INSERT INTO ActorSkills
            (ActorID, SkillID)
            VALUES (?, ?)
        ";

        $actorSkillStatement = $connection->prepare($actorSkillSQL);

        foreach ($selectedSkills as $skillID) {

            $actorSkillStatement->execute([
                $actorID,
                (int) $skillID
            ]);
        }

        $message = "Actor added successfully.";

    } else {

        $message = "Please enter all actor details and select at least one skill.";
    }
}


// Remove an actor from the agency.
if (isset($_POST["remove_actor"])) {

    $actorID = (int) $_POST["actor_id"];

    $sql = "
        UPDATE Actors
        SET IsActive = FALSE
        WHERE ActorID = ?
    ";

    $statement = $connection->prepare($sql);
    $statement->execute([$actorID]);

    $message = "Actor removed from the agency.";
}



// Get active actors.
$sql = "
    SELECT
        ActorID,
        ActorName,
        HirePrice
    FROM Actors
    WHERE IsActive = TRUE
    ORDER BY ActorID
";

$statement = $connection->prepare($sql);
$statement->execute();

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

    <title>Manage Actors</title>

    <link
        rel="stylesheet"
        href="manage_actor.css"
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


<main>

    <h1>Manage Actors</h1>

    <?php if ($message != ""): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <h2>Add Actor</h2>

    <form method="POST">

        <label>Actor ID</label>

        <input
            type="number"
            name="actor_id"
            required
        >


        <label>Actor Name</label>

        <input
            type="text"
            name="actor_name"
            required
        >


        <label>Hire Price</label>

        <input
            type="number"
            name="hire_price"
            required
        >


        <label>Description</label>

        <input
            type="text"
            name="description"
            required
        >


        <label>Skills</label>

        <?php foreach ($skills as $skill): ?>

            <div>
                <input
                    type="checkbox"
                    name="skills[]"
                    value="<?php echo $skill["SkillID"]; ?>"
                    style="width: auto;"
                >

                <?php
                echo htmlspecialchars(
                    $skill["SkillName"]
                );
                ?>
            </div>

        <?php endforeach; ?>


        <button
            type="submit"
            name="add_actor"
        >
            Add Actor
        </button>

    </form>
    
    <h2>Actor List</h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Hire Price</th>
            <th>Action</th>
        </tr>


        <?php foreach ($actors as $actor): ?>

            <tr>

                <td>
                    <?php echo $actor["ActorID"]; ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $actor["ActorName"]
                    );
                    ?>
                </td>

                <td>
                    £<?php echo $actor["HirePrice"]; ?>
                </td>

                <td>
            
                    <button>
                    <a 
                        class="edit-button"
                        href="editactor.php?id=<?php echo $actor["ActorID"]; ?>"
                        >
                        
                        Edit Actor
                    </a>
                    </button>

                    <form method="POST" class="remove-form">

                        <input
                            type="hidden"
                            name="actor_id"
                            value="<?php echo $actor["ActorID"]; ?>"
                        >

                        <button
                            type="submit"
                            name="remove_actor"
                        >
                            Remove Actor
                        </button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</main>

</body>

</html>
