<?php
    #create varialbes with server details on
    $servername="localhost";
    $username="root";
    $password="password";
    $conn=new PDO("mysql:host=$servername",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql="CREATE DATABASE IF NOT EXISTS ACTOR_AGENCY"; 
    $conn->exec($sql);
    $sql="USE ACTOR_AGENCY";
    $conn->exec($sql);
    echo("DB made");
    #users table
    $stmt= $conn->prepare("DROP TABLE IF EXISTS tblusers;                
    CREATE TABLE tblusers 
    (UserID INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(20) NOT NULL,
    Surname VARCHAR(20) NOT NULL,
    Forename VARCHAR(20) NOT NULL,
    Password VARCHAR(200) NOT NULL,
    Role TINYINT(1));
    ");
    $stmt->execute();
    #users test data
    $hashedpassword=password_hash("password",PASSWORD_DEFAULT);
    echo($hashedpassword);
    $stmt= $conn->prepare("INSERT INTO tblusers
    (UserID,Username, Surname, Forename, Password, Role)
    VALUES
    (NULL, 'Evan135790','Gao', 'Evan', :Password,1),
    (NULL,'LXFinality','Sans', 'Arctic', :Password,0) 
    ");
    
    $stmt->bindParam(":Password",$hashedpassword);
    
    $stmt->execute();

    #actors table
    $stmt= $conn->prepare("DROP TABLE IF EXISTS tblactors;
    CREATE TABLE tblactors
    (ActorID INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(40) NOT NULL,
    Description VARCHAR(200) NOT NULL,
    Skills VARCHAR(20) NOT NULL,
    HirePrice DECIMAL (15,2) NOT NULL);
    ");
    $stmt->execute();
    
    #actors test data
    $stmt=$conn->prepare("INSERT INTO tblactors
    (ActorID,Name,Description,Skills,HirePrice)
    VALUES
    (NULL,'Dilraba','Placeholder description','Placeholder',20000000),
    (NULL,'Venge Weiguang','Placeholder description','Placeholder',676767)
    ");
    $stmt->execute();
    echo("Actor table made");

    #films table
    $stmt=$conn->prepare("DROP TABLE IF EXISTS tblfilm;
    CREATE TABLE tblfilm
    (FilmName  VARCHAR(20) DEFAULT 1,
    FilmID INT(4) NOT NULL);
    ");
    $stmt->execute();
    echo("Film table Made");
?>