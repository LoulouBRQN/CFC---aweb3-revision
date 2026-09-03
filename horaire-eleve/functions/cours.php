<?php

require_once __DIR__ . "/../config/database.php";

function getCourses()
{
    global $pdo;

    $sql = "SELECT * FROM cours ORDER BY nom ASC";

    return $pdo->query($sql);
}