<?php
    require_once __DIR__ . '/connexion/db.php';


    $db = getDb();
    if ($db == null) {
        echo "non";
    }
    else {
        echo "oui";
    }

