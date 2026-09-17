<?php
require_once __DIR__ . '/../connexion/db.php';
require_once __DIR__ . '/../functions/classes.php';

$db = getDb();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $annee = trim($_POST['annee_scolaire'] ?? '');

    if ($nom === '' || !preg_match('/^\d{4}-\d{4}$/', $annee)) {
        $error = 'Veuillez saisir un nom et une année scolaire au format 2026-2027.';
    } else {
        try {
            insertClasse($db, $nom, $annee);
            $message = 'Classe ajoutée.';
        } catch (PDOException $e) {
            $error = 'Cette classe existe déjà.';
        }
    }
}

if (isset($_GET['delete'])) {
    deleteClasse($db, (int) $_GET['delete']);
    header('Location: classes.php');
    exit;
}

$classes = getClasses($db);
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <h1>Classes</h1>
    <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label for="nom">Nom de la classe</label>
        <input id="nom" name="nom" placeholder="I.DA-P3A" required>
        <label for="annee">Année scolaire</label>
        <input id="annee" name="annee_scolaire" placeholder="2026-2027" pattern="\d{4}-\d{4}" required>
        <button type="submit">Ajouter la classe</button>
    </form>
</div>

<table>
    <tr><th>ID</th><th>Nom</th><th>Année scolaire</th><th>Action</th></tr>
    <?php foreach ($classes as $classe): ?>
        <tr>
            <td><?= (int) $classe['id'] ?></td>
            <td><?= htmlspecialchars($classe['nom']) ?></td>
            <td><?= htmlspecialchars($classe['annee_scolaire']) ?></td>
            <td><a href="?delete=<?= (int) $classe['id'] ?>" onclick="return confirm('Supprimer cette classe ?')">Supprimer</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
