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

function insertClasse(PDO $db, string $nomClasse, string $anneeClasse): ?int
{
    $sql = "INSERT INTO Ads (nom, annee_scolaire) VALUES (:nom, :annee_scolaire)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":nom", $nomClasse);
    $stmt->bindParam(":annee_scolaire", $anneeClasse);
    $stmt->execute();
    return $db->getLastClasseId();
}
function getLastClasseId(PDO $db): ?int
{
    $sql = "SELECT id FROM classes ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

?>