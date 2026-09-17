<?php
require_once __DIR__ . '/../connexion/db.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../functions/classes.php';
require_once __DIR__ . '/../functions/cours.php';
require_once __DIR__ . '/../functions/creneaux.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function response(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function inputJson(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        response(['error' => 'JSON invalide'], 400);
    }
    return $data;
}

$db = getDb();
$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts = explode('/', $path);
$resource = $_GET['resource'] ?? '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (in_array('api', $parts, true)) {
    $apiIndex = array_search('api', $parts, true);
    if (isset($parts[$apiIndex + 1]) && $parts[$apiIndex + 1] !== 'index.php') {
        $resource = $parts[$apiIndex + 1];
    }
    if (!$id && isset($parts[$apiIndex + 2]) && is_numeric($parts[$apiIndex + 2])) {
        $id = (int) $parts[$apiIndex + 2];
    }
}

try {
    if ($method === 'GET') {
        if ($resource === 'classes') {
            response(getClasses($db));
        }

        if ($resource === 'cours') {
            if (isset($_GET['classe'])) {
                $classe = getClassByName($db, $_GET['classe']);
                if (!$classe){
                    response(['error' => 'Classe introuvable'], 404);
                }
                response([
                    'classe' => $classe['nom'],
                    'annee_scolaire' => $classe['annee_scolaire'],
                    'horaires' => getSlotsByClassName($db, $classe['nom'])
                ]);
            }
            response(getCourses($db));
        }

        if ($resource === 'creneaux') {
            if ($id) {
                $creneau = getCreneauById($db, $id);
                if (!$creneau){
                    response(['error' => 'Créneau introuvable'], 404);
                }
                response($creneau);
            }
            response(getCreneaux($db));
        }

        response(['error' => 'Ressource introuvable'], 404);
    }

    $data = inputJson();

    if ($method === 'POST') {
        if ($resource === 'classes') {
            if (empty($data['nom']) || empty($data['annee_scolaire'])){
                response(['error' => 'nom et annee_scolaire sont obligatoires'], 400);
            }
            $newId = insertClasse($db, trim($data['nom']), trim($data['annee_scolaire']));
            response(['id' => $newId, 'message' => 'Classe créée'], 201);
        }
        if ($resource === 'cours') {
            if (empty($data['code']) || empty($data['nom'])){
                response(['error' => 'code et nom sont obligatoires'], 400);
            }
            $newId = insertCours($db, trim($data['code']), trim($data['nom']));
            response(['id' => $newId, 'message' => 'Cours créé'], 201);
        }
        if ($resource === 'creneaux') {
            foreach (['classe_id','cours_id','jour','heure_debut','heure_fin','salle'] as $field) {
                if (!isset($data[$field]) || $data[$field] === ''){
                    response(['error' => "Champ obligatoire : $field"], 400);
                }
            }
            if (!in_array($data['jour'], JOURS, true) || $data['heure_debut'] >= $data['heure_fin']){
                response(['error' => 'Jour ou heures invalides'], 400);
            }
            $newId = insertCreneau($db, (int)$data['classe_id'], (int)$data['cours_id'], $data['jour'], $data['heure_debut'], $data['heure_fin'], trim($data['salle']));
            response(['id' => $newId, 'message' => 'Créneau créé'], 201);
        }
        response(['error' => 'Ressource introuvable'], 404);
    }

    if ($method === 'PUT') {
        if ($resource !== 'creneaux' || !$id){
            response(['error' => 'ID du créneau obligatoire'], 400);
        }
        if (!getCreneauById($db, $id)){
            response(['error' => 'Créneau introuvable'], 404);
        }
        foreach (['classe_id','cours_id','jour','heure_debut','heure_fin','salle'] as $field) {
            if (!isset($data[$field]) || $data[$field] === '') response(['error' => "Champ obligatoire : $field"], 400);
        }
        if (!in_array($data['jour'], JOURS, true) || $data['heure_debut'] >= $data['heure_fin']){
            response(['error' => 'Jour ou heures invalides'], 400);
        }
        updateCreneau($db, $id, (int)$data['classe_id'], (int)$data['cours_id'], $data['jour'], $data['heure_debut'], $data['heure_fin'], trim($data['salle']));
        response(['message' => 'Créneau modifié']);
    }

    if ($method === 'DELETE') {
        if (!$id){
            response(['error' => 'ID obligatoire'], 400);
        }
        if ($resource === 'classes'){
            $deleted = deleteClasse($db, $id);
        }
        elseif ($resource === 'cours'){
            $deleted = deleteCours($db, $id);
        }
        elseif ($resource === 'creneaux'){
            $deleted = deleteCreneau($db, $id);
        }
        else{
            response(['error' => 'Ressource introuvable'], 404);
        }
        if (!$deleted){
            response(['error' => 'Donnée introuvable'], 404);
        }
        response([], 204);
    }

    response(['error' => 'Méthode non autorisée'], 405);
} catch (PDOException $e) {
    response(['error' => 'Erreur de base de données'], 500);
}
