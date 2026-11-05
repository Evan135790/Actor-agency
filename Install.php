<?php

// Database login details
$serverName = "localhost";
$userName = "root";
$password = "root";
$databaseName = "actor_agency";

try {
    // Connect to MySQL without selecting a database first
    $connection = new PDO(
        "mysql:host=$serverName;charset=utf8mb4",
        $userName,
        $password
    );

    // Display useful error messages during development
    $connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Create and select the database
    $connection->exec(
        "CREATE DATABASE IF NOT EXISTS `$databaseName`
         CHARACTER SET utf8mb4
         COLLATE utf8mb4_unicode_ci"
    );

    $connection->exec("USE `$databaseName`");

    /*
     * Remove old tables.
     * Foreign key checks are temporarily disabled because
     * some tables are connected to other tables.
     */
    $connection->exec("SET FOREIGN_KEY_CHECKS = 0");

    $tables = [
        "Bookings",
        "ActorPerformances",
        "RoleRequiredSkills",
        "ActorSkills",
        "ActorPictures",
        "FilmRoles",
        "Films",
        "Skills",
        "Actors",
        "Users"
    ];

    foreach ($tables as $table) {
        $connection->exec("DROP TABLE IF EXISTS `$table`");
    }

    $connection->exec("SET FOREIGN_KEY_CHECKS = 1");

    /*
     * 1. Users table
     */
    $connection->exec(
        "CREATE TABLE Users (
            UserID INT AUTO_INCREMENT PRIMARY KEY,
            Username VARCHAR(50) NOT NULL UNIQUE,
            PasswordHash VARCHAR(200) NOT NULL,
            Email VARCHAR(100) NOT NULL UNIQUE,
            UserRole VARCHAR(20) NOT NULL,
            CreatedAt DATE NOT NULL
        ) ENGINE=InnoDB"
    );

    /*
     * 2. Actors table
     */
    $connection->exec(
        "CREATE TABLE Actors (
            ActorID INT PRIMARY KEY,
            ActorName VARCHAR(200) NOT NULL,
            HirePrice DECIMAL(10,2) NOT NULL,
            Description VARCHAR(1000) NOT NULL,
            IsActive BOOLEAN NOT NULL
        ) ENGINE=InnoDB"
    );

    /*
     * 3. Actor pictures table
     */
    $connection->exec(
        "CREATE TABLE ActorPictures (
            PicID INT AUTO_INCREMENT PRIMARY KEY,
            ActorID INT NOT NULL,
            ImageURL VARCHAR(255) NOT NULL,
            IsPrimary BOOLEAN NOT NULL,
            DisplayOrder INT NOT NULL,

            FOREIGN KEY (ActorID)
                REFERENCES Actors(ActorID)
                ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

    /*
     * 4. Skills table
     */
    $connection->exec(
        "CREATE TABLE Skills (
            SkillID INT AUTO_INCREMENT PRIMARY KEY,
            SkillName VARCHAR(200) NOT NULL UNIQUE
        ) ENGINE=InnoDB"
    );

    /*
     * 5. ActorSkills table
     * This connects actors to their skills.
     */
    $connection->exec(
        "CREATE TABLE ActorSkills (
            ActorID INT NOT NULL,
            SkillID INT NOT NULL,

            PRIMARY KEY (ActorID, SkillID),

            FOREIGN KEY (ActorID)
                REFERENCES Actors(ActorID)
                ON DELETE CASCADE,

            FOREIGN KEY (SkillID)
                REFERENCES Skills(SkillID)
                ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

    /*
     * 6. Films table
     */
    $connection->exec(
        "CREATE TABLE Films (
            FilmID INT AUTO_INCREMENT PRIMARY KEY,
            FilmName VARCHAR(200) NOT NULL,
            ProductionCompany VARCHAR(200),
            FilmDescription VARCHAR(1000)
        ) ENGINE=InnoDB"
    );

    /*
     * 7. FilmRoles table
     */
    $connection->exec(
        "CREATE TABLE FilmRoles (
            RoleID INT AUTO_INCREMENT PRIMARY KEY,
            FilmID INT NOT NULL,
            RoleName VARCHAR(200) NOT NULL,
            RoleType VARCHAR(50) NOT NULL,
            MaximumBudget DECIMAL(10,2) NOT NULL,
            RoleDescription VARCHAR(1000),

            FOREIGN KEY (FilmID)
                REFERENCES Films(FilmID)
                ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

    /*
     * 8. RoleRequiredSkills table
     * This connects film roles to their required skills.
     */
    $connection->exec(
        "CREATE TABLE RoleRequiredSkills (
            RoleID INT NOT NULL,
            SkillID INT NOT NULL,

            PRIMARY KEY (RoleID, SkillID),

            FOREIGN KEY (RoleID)
                REFERENCES FilmRoles(RoleID)
                ON DELETE CASCADE,

            FOREIGN KEY (SkillID)
                REFERENCES Skills(SkillID)
                ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

    /*
     * 9. ActorPerformances table
     */
    $connection->exec(
        "CREATE TABLE ActorPerformances (
            PerformanceID INT AUTO_INCREMENT PRIMARY KEY,
            ActorID INT NOT NULL,
            RoleID INT NOT NULL,
            PerformanceStatus VARCHAR(20) NOT NULL,
            Rating DECIMAL(3,1),

            FOREIGN KEY (ActorID)
                REFERENCES Actors(ActorID)
                ON DELETE CASCADE,

            FOREIGN KEY (RoleID)
                REFERENCES FilmRoles(RoleID)
                ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

    /*
     * 10. Bookings table
     */
    $connection->exec(
        "CREATE TABLE Bookings (
            BookingID INT AUTO_INCREMENT PRIMARY KEY,
            RoleID INT NOT NULL,
            ActorID INT NOT NULL,
            UserID INT NOT NULL,
            StartDate DATE NOT NULL,
            EndDate DATE NOT NULL,
            Status VARCHAR(20) NOT NULL,
            CreatedAt DATE NOT NULL,

            FOREIGN KEY (RoleID)
                REFERENCES FilmRoles(RoleID)
                ON DELETE CASCADE,

            FOREIGN KEY (ActorID)
                REFERENCES Actors(ActorID)
                ON DELETE CASCADE,

            FOREIGN KEY (UserID)
                REFERENCES Users(UserID)
                ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

        /*
    * Add two test users.
    * One Producer and one Admin.
    */
    $producerPassword = password_hash(
        "producer123",
        PASSWORD_DEFAULT
    );

    $adminPassword = password_hash(
        "admin123",
        PASSWORD_DEFAULT
    );

    $userStatement = $connection->prepare(
        "INSERT INTO Users
        (Username, PasswordHash, Email, UserRole, CreatedAt)
        VALUES
        (:username, :passwordHash, :email, :userRole, :createdAt)"
    );

    // Add Producer account.
    $userStatement->execute([
        ":username" => "producer1",
        ":passwordHash" => $producerPassword,
        ":email" => "producer@example.com",
        ":userRole" => "Producer",
        ":createdAt" => "2026-08-01"
    ]);

    // Add Admin account.
    $userStatement->execute([
        ":username" => "admin1",
        ":passwordHash" => $adminPassword,
        ":email" => "admin@example.com",
        ":userRole" => "Admin",
        ":createdAt" => "2026-08-01"
    ]);

    /*
     * Add Actor A to Actor E.
     * These values match the Design test data.
     */
    $actorStatement = $connection->prepare(
        "INSERT INTO Actors
        (ActorID, ActorName, HirePrice, Description, IsActive)
        VALUES
        (:actorID, :actorName, :hirePrice, :description, :isActive)"
    );

    $actors = [
        [
            1,
            "Actor A",
            18000.00,
            "An experienced action actor.",
            1
        ],
        [
            2,
            "Actor B",
            15000.00,
            "A highly rated actor with action experience.",
            1
        ],
        [
            3,
            "Actor C",
            12000.00,
            "An actor with some related role experience.",
            1
        ],
        [
            4,
            "Actor D",
            25000.00,
            "An experienced actor with a high hire price.",
            1
        ],
        [
            5,
            "Actor E",
            10000.00,
            "A new actor with limited experience.",
            1
        ]
    ];

    foreach ($actors as $actor) {
        $actorStatement->execute([
            ":actorID" => $actor[0],
            ":actorName" => $actor[1],
            ":hirePrice" => $actor[2],
            ":description" => $actor[3],
            ":isActive" => $actor[4]
        ]);
    }

    /*
     * Add the skills used by the test role.
     */
    $connection->exec(
        "INSERT INTO Skills (SkillName)
         VALUES
         ('Swimming'),
         ('Dancing'),
         ('Horse Riding'),
         ('Singing')"
    );

    /*
     * Give skills to each actor.
     *
     * Actor A: all 3 required skills
     * Actor B: all 3 required skills
     * Actor C: 2 required skills
     * Actor D: all 3 required skills
     * Actor E: 1 required skill
     */
    $connection->exec(
        "INSERT INTO ActorSkills (ActorID, SkillID)
         VALUES
         (1, 1), (1, 2), (1, 3),
         (2, 1), (2, 2), (2, 3),
         (3, 1), (3, 2),
         (4, 1), (4, 2), (4, 3),
         (5, 1)"
    );

    /*
     * Add a test film.
     */
    $connection->exec(
        "INSERT INTO Films
        (FilmName, ProductionCompany, FilmDescription)
        VALUES
        (
            'Test Action Film',
            'Example Production Company',
            'A test film used for the Best Actor Pick algorithm.'
        )"
    );

    /*
     * Add the current role and several previous role types.
     *
     * Role 1: the role being filled
     * Role 2: same role type
     * Role 3: related role type
     */
    $connection->exec(
        "INSERT INTO FilmRoles
        (FilmID, RoleName, RoleType, MaximumBudget, RoleDescription)
        VALUES
        (
            1,
            'Main Character',
            'Action Lead',
            20000.00,
            'A lead character who performs action, swimming and dancing scenes.'
        ),
        (
            1,
            'Previous Action Lead',
            'Action Lead',
            20000.00,
            'A previous action lead role.'
        ),
        (
            1,
            'Action Supporting Character',
            'Action Supporting',
            15000.00,
            'A supporting action role.'
        )"
    );

    /*
     * The current role requires three skills.
     */
    $connection->exec(
        "INSERT INTO RoleRequiredSkills (RoleID, SkillID)
         VALUES
         (1, 1),
         (1, 2),
         (1, 3)"
    );

    /*
     * Add previous performances and ratings.
     *
     * Actor A: average rating 8.0, same role type
     * Actor B: average rating 9.5, same role type
     * Actor C: average rating 7.0, related role type
     * Actor D: average rating 9.0, same role type
     * Actor E: no previous performance
     */
    $connection->exec(
        "INSERT INTO ActorPerformances
        (ActorID, RoleID, PerformanceStatus, Rating)
        VALUES
        (1, 2, 'Completed', 8.0),
        (2, 2, 'Completed', 9.5),
        (3, 3, 'Completed', 7.0),
        (4, 2, 'Completed', 9.0)"
    );

    /*
     * Insert approved booking history.
     * The loops create the booking totals used by
     * the popularity score.
     */
    $bookingStatement = $connection->prepare(
        "INSERT INTO Bookings
        (RoleID, ActorID, UserID, StartDate, EndDate, Status, CreatedAt)
        VALUES
        (:roleID, :actorID, :userID, :startDate, :endDate, :status, :createdAt)"
    );

    $approvedBookingCounts = [
        1 => 8,
        2 => 9,
        3 => 4,
        4 => 9,
        5 => 0
    ];

    foreach ($approvedBookingCounts as $actorID => $count) {
        for ($bookingNumber = 1; $bookingNumber <= $count; $bookingNumber++) {
            $month = str_pad(
                (string) (($bookingNumber % 6) + 1),
                2,
                "0",
                STR_PAD_LEFT
            );

            $bookingStatement->execute([
                ":roleID" => 1,
                ":actorID" => $actorID,
                ":userID" => 1,
                ":startDate" => "2025-$month-01",
                ":endDate" => "2025-$month-05",
                ":status" => "Approved",
                ":createdAt" => "2025-$month-01"
            ]);
        }
    }

    /*
     * Actor B needs one more booking to give 10 bookings.
     * This booking overlaps the test dates:
     * 1 August 2026 to 20 August 2026.
     */
    $bookingStatement->execute([
        ":roleID" => 1,
        ":actorID" => 2,
        ":userID" => 1,
        ":startDate" => "2026-08-05",
        ":endDate" => "2026-08-15",
        ":status" => "Approved",
        ":createdAt" => "2026-07-20"
    ]);

    echo "<h1>Installation completed successfully</h1>";
    echo "<p>The actor_agency database was created.</p>";
    echo "<p>All tables and test data were added.</p>";

} catch (PDOException $error) {
    echo "<h1>Installation failed</h1>";
    echo "<p>" . htmlspecialchars($error->getMessage()) . "</p>";
}

?>