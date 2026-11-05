<?php
    header("Location: users.php");
    print_r($_POST);
    include_once("connection.php");
    $username=$_POST["surname"].".".$_POST["forename"][0];
    //$username="bob";
    if($_POST["role"]=="Guest"){
        $role=0;
    }else{
        $role=1;
    }
   
    $stmt= $conn->prepare("INSERT INTO tblusers
    (UserID,Username, Surname, Forename, Password,Role)
    VALUES
    (NULL,:Username, :Surname, :Forename, :Password, :Role)
    ");
    $stmt->bindParam(":Username",$username);
    $stmt->bindParam(":Surname",$_POST["surname"]);
    $stmt->bindParam(":Forename",$_POST["forename"]);
    $stmt->bindParam(":Password",$_POST["password"]);
    $stmt->bindParam(":Role",$role);
    $stmt->execute();
?>