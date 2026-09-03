<?php

require_once __DIR__ . "/../config/database.php";

function getClasses()
{
    global $pdo;

    $sql = "SELECT * FROM classes ORDER BY nom ASC";

    return $pdo->query($sql);
}

function getClassByName($nom)
{
    global $pdo;

    $sql = "SELECT * FROM classes WHERE nom = :nom";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nom" => $nom
    ]);

    return $stmt;
}

?>