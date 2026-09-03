<?php
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/../functions/classes.php";
require_once __DIR__ . "/../functions/cours.php";
require_once __DIR__ . "/../functions/crenaux.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET");

$resource = $_GET["resource"] ?? null;

switch ($resource) {
    case "classes":
        jsonResponse(getClasses()->fetchAll());

    case "cours":
        if (isset($_GET["classe"])) {
            $class = getClassByName($_GET["classe"])->fetch();
            if (!$class) {
                jsonResponse([KEY_MESSAGE => "class.notFound"], 404);
            }
            jsonResponse([
                "classe" => $class["nom"],
                "annee_scolaire" => $class["annee_scolaire"],
                "horaires" => getSlotsByClassName($_GET["classe"])->fetchAll(),
            ]);
        }
        jsonResponse(getCourses()->fetchAll());

    default:
        jsonResponse([KEY_MESSAGE => "resource.notFound"], 404);
}