<!DOCTYPE HTML>
<html>
<head>          
    <title>Add users</title>
</head>

<body>
    <form action="adduser.php" method = "POST">
        Surname: <input type="text" name="surname"><br>
        Forename: <input type="text" name="forename"><br>
        Password: <input type="password" name="password"><br>
        Role: <br>
        <input type="radio" name="role" value="Guest" checked>User<br>
        <input type="radio" name="role" value="Admin" >Admin<br>
        <input type="submit" value="Add user"><br>
    </form>

    <?php
        include_once("connection.php");
        $stmt= $conn->prepare("SELECT * FROM tblusers");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            echo($row["Forename"]." ".$row["Surname"]."<br>");
        }
    ?>
</body>
</html>