

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    include_once __DIR__ . '/../includes/header.php';
?>
<div id="" >
    <form action="" method="post">
        <label for="nomClasse">Nom de la classe :</label>
        <input type="text" name="nomClasse" id="nomClasse">
        <label for="anneeClasse">Annee scolaire :</label>
        <input type="date" name="anneeClasse" id="anneeClasse">
        <button type="submit">Créer la classe</button>
    </form>
</div>




<?php
    include_once __DIR__ . '/../includes/footer.php';
?>
</body>
</html>