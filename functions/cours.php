<?php

function getCourses(PDO $db): array
{
    $stmt = $db->query("SELECT * FROM cours ORDER BY code ASC");
    return $stmt->fetchAll();
}

function getCourseById(PDO $db, int $id): ?array
{
    $stmt = $db->prepare("SELECT * FROM cours WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $course = $stmt->fetch();
    return $course ?: null;
}

function insertCours(PDO $db, string $code, string $nom): int
{
    $stmt = $db->prepare("INSERT INTO cours (code, nom) VALUES (:code, :nom)");
    $stmt->execute(['code' => $code, 'nom' => $nom]);
    return (int) $db->lastInsertId();
}

function deleteCours(PDO $db, int $id): bool
{
    $stmt = $db->prepare("DELETE FROM cours WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount() > 0;
}
