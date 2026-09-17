<?php

function getCreneaux(PDO $db): array
{
    $sql = "SELECT creneaux.id, classes.nom AS classe, classes.annee_scolaire,
                   cours.code AS code_cours, cours.nom AS cours,
                   creneaux.jour, creneaux.heure_debut, creneaux.heure_fin,
                   creneaux.salle
            FROM creneaux
            INNER JOIN classes ON creneaux.classe_id = classes.id
            INNER JOIN cours ON creneaux.cours_id = cours.id
            ORDER BY FIELD(creneaux.jour, 'lundi','mardi','mercredi','jeudi','vendredi'), creneaux.heure_debut";
    return $db->query($sql)->fetchAll();
}

function getCreneauById(PDO $db, int $id): ?array
{
    $sql = "SELECT creneaux.id, creneaux.classe_id, creneaux.cours_id,
                   classes.nom AS classe, cours.code AS code_cours, cours.nom AS cours,
                   creneaux.jour, creneaux.heure_debut, creneaux.heure_fin, creneaux.salle
            FROM creneaux
            INNER JOIN classes ON creneaux.classe_id = classes.id
            INNER JOIN cours ON creneaux.cours_id = cours.id
            WHERE creneaux.id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id]);
    $slot = $stmt->fetch();
    return $slot ?: null;
}

function getSlotsByClassName(PDO $db, string $nom): array
{
    $sql = "SELECT creneaux.id, creneaux.jour, creneaux.heure_debut, creneaux.heure_fin,
                   cours.nom AS cours, cours.code AS code_cours, creneaux.salle
            FROM creneaux
            INNER JOIN classes ON creneaux.classe_id = classes.id
            INNER JOIN cours ON creneaux.cours_id = cours.id
            WHERE classes.nom = :nom
            ORDER BY FIELD(creneaux.jour, 'lundi','mardi','mercredi','jeudi','vendredi'), creneaux.heure_debut";
    $stmt = $db->prepare($sql);
    $stmt->execute(['nom' => $nom]);
    return $stmt->fetchAll();
}

function insertCreneau(PDO $db, int $classeId, int $coursId, string $jour, string $debut, string $fin, string $salle): int
{
    $sql = "INSERT INTO creneaux (classe_id, cours_id, jour, heure_debut, heure_fin, salle)
            VALUES (:classe_id, :cours_id, :jour, :debut, :fin, :salle)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'classe_id' => $classeId,
        'cours_id' => $coursId,
        'jour' => $jour,
        'debut' => $debut,
        'fin' => $fin,
        'salle' => $salle
    ]);
    return (int) $db->lastInsertId();
}

function updateCreneau(PDO $db, int $id, int $classeId, int $coursId, string $jour, string $debut, string $fin, string $salle): bool
{
    $sql = "UPDATE creneaux SET classe_id = :classe_id, cours_id = :cours_id,
                jour = :jour, heure_debut = :debut, heure_fin = :fin, salle = :salle
            WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'id' => $id,
        'classe_id' => $classeId,
        'cours_id' => $coursId,
        'jour' => $jour,
        'debut' => $debut,
        'fin' => $fin,
        'salle' => $salle
    ]);
    return $stmt->rowCount() > 0;
}

function deleteCreneau(PDO $db, int $id): bool
{
    $stmt = $db->prepare("DELETE FROM creneaux WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount() > 0;
}
