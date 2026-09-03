<?php
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/../functions/classes.php";
require_once __DIR__ . "/../functions/cours.php";
require_once __DIR__ . "/../functions/crenaux.php";
require_once __DIR__ . "/../utils/utils.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nomClasse = filter_var($data['nomClasse'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $annee = $_GET["annee"] ?? "";


    if (!preg_match('/^\d{4}-\d{4}$/', $annee)) {
        jsonResponse([KEY_MESSAGE => "format.invalide"], 404);
    }

    [$debut, $fin] = explode("-", $annee);

    if ((int)$fin !== (int)$debut + 1 || (int)$debut >= date("Y")) {
        jsonResponse([KEY_MESSAGE => "annee.invalide"], 404);
    }
    
    if ($nomClasse == "") {
        jsonResponse([KEY_MESSAGE => "nom.vide"], 404);
    }
    $classe = insertClasse($db, $nomClasse, $annee);
    

}
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $title = filter_var($data['title'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_var($data['description'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $price = $data['price'] ?? '';
    $amount = filter_var($price['amount'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $currency = filter_var($price['currency'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $msgError = [];
    $idAd = isset($_GET['id']) ? (int) $_GET['id'] : null;


    if (!$idAd) {
        http_response_code(400);
        echo json_encode(["error" => "missing.ad.id"]);
        exit;
    }

    if (empty($idAd) && isset($data['id'])) {
        $idAd = $data['id'];
    }

    if (strlen($title) < 2) {
        $msgError[] = ["field" => "title", "message" => "min.2.chars"];
    }
    if ($amount <= 0) {
        $msgError[] = ["field" => "amount", "message" => "not.negative.number"];
    }
    if (!in_array($currency, CURRENCY_CODE_LIST)) {
        $msgError[] = ["field" => "currency", "message" => "invalid.currency.code"];
    }

    if (!doesAdExist($db, $idAd)) {
        http_response_code(404);
        echo json_encode(["error" => "ad.dont.exist"]);
        exit;
    }

    $idUser = getUserIdByToken($db, $getToken);
    $userAds = getAdsByUserId($db, $idUser);
    $belongsToUser = false;
    foreach ($userAds as $ad) {
        if ($ad["idAd"] == $idAd) {
            $belongsToUser = true;
            break;
        }
    }

    if (!$belongsToUser) {
        http_response_code(403);
        echo json_encode(["error" => "not.your.ad"]);
        exit;
    }

    if (!isUserValidByToken($db, $getToken)) {
        http_response_code(401);
        echo json_encode(["error" => "invalid.token"]);
        exit; 
    }
    else if (empty($msgError)) {
        $idUser = getUserIdByToken($db, $getToken);
        $userAds = getAdsByUserId($db, $idUser);
        $ok = false;
        foreach ($userAds as $ad) {
            if ($ad["idAd"] == $idAd) {
                updateAd($db, $title, $description, $amount, $currency, $idAd);
                http_response_code(204);
                $ok = true;
            }
        }
        if (!$ok) {
            http_response_code(403);
            echo json_encode(["error" => "UnAuthorized"]);
        }
    } else {
        http_response_code(400);
        echo json_encode($msgError);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $idAd = $_GET['id'];

    if (!doesAdExist($db, $idAd)) {
        http_response_code(404);
        echo json_encode(["error" => "ad.dont.exist"]);
        exit;
    }

    if (!isUserValidByToken($db, $getToken)) {
        http_response_code(401);
        echo json_encode(["error" => "invalid.token"]);
    } else {
        $idUser = getUserIdByToken($db, $getToken);
        $userAds = getAdsByUserId($db, $idUser);
        $ok = false;
        foreach ($userAds as $ad) {
            if ($ad["idAd"] == $idAd) {
                deleteAd($db, $idAd);
                http_response_code(204);
                $ok = true;
            }
        }
        if (!$ok) {
            http_response_code(403);
            echo json_encode(["error" => "UnAuthorized"]);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}
