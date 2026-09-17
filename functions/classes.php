<?php

function getClasses(PDO $db): array
{
    $stmt = $db->query("SELECT * FROM classes ORDER BY nom ASC");
    return $stmt->fetchAll();
}

function getClassById(PDO $db, int $id): ?array
{
    $stmt = $db->prepare("SELECT * FROM classes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $class = $stmt->fetch();
    return $class ?: null;
}

function getClassByName(PDO $db, string $nom): ?array
{
    $stmt = $db->prepare("SELECT * FROM classes WHERE nom = :nom");
    $stmt->execute(['nom' => $nom]);
    $class = $stmt->fetch();
    return $class ?: null;
}

function insertClasse(PDO $db, string $nom, string $annee): int
{
    $stmt = $db->prepare("INSERT INTO classes (nom, annee_scolaire) VALUES (:nom, :annee)");
    $stmt->execute(['nom' => $nom, 'annee' => $annee]);
    return (int) $db->lastInsertId();
}

function deleteClasse(PDO $db, int $id): bool
{
    $stmt = $db->prepare("DELETE FROM classes WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount() > 0;
}
