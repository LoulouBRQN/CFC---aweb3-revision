<?php

require_once __DIR__ . "/../config/database.php";

function getSlotsByClassName($nom)
{
    global $pdo;

    $sql = "
        SELECT 
            creneaux.id,
            classes.nom AS classe,
            cours.code,
            cours.nom AS cours,
            creneaux.jour,
            creneaux.heure_debut,
            creneaux.heure_fin,
            creneaux.salle
        FROM creneaux
        INNER JOIN classes 
            ON creneaux.classe_id = classes.id
        INNER JOIN cours 
            ON creneaux.cours_id = cours.id
        WHERE classes.nom = :nom
        ORDER BY 
            FIELD(
                creneaux.jour,
                'lundi',
                'mardi',
                'mercredi',
                'jeudi',
                'vendredi'
            ),
            creneaux.heure_debut
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nom" => $nom
    ]);

    return $stmt;
}