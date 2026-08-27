<?php
    require_once __DIR__ . '/connexion/db.php';
    include_once __DIR__ . '/includes/header.php';
    
    echo "<h1>Bienvenue</h1>";

    $db = getDb();
    if ($db == null) {
        echo "non";
    }
    else {
        echo "oui";
    }

include_once __DIR__ . '/includes/footer.php';
