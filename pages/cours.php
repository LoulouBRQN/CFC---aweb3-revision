<?php
require_once __DIR__ . '/../connexion/db.php';
require_once __DIR__ . '/../functions/cours.php';

$db = getDb();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    if ($code === '' || $nom === '') {
        $error = 'Le code et le nom sont obligatoires.';
    } else {
        insertCours($db, $code, $nom);
        $message = 'Cours ajouté.';
    }
}

if (isset($_GET['delete'])) {
    deleteCours($db, (int) $_GET['delete']);
    header('Location: cours.php');
    exit;
}

$cours = getCourses($db);
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <h1>Cours</h1>
    <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label for="code">Code</label>
        <input id="code" name="code" placeholder="AWEB3" required>
        <label for="nom">Nom</label>
        <input id="nom" name="nom" placeholder="Atelier Web" required>
        <button type="submit">Ajouter le cours</button>
    </form>
</div>
<table>
    <tr><th>ID</th><th>Code</th><th>Nom</th><th>Action</th></tr>
    <?php foreach ($cours as $course): ?>
        <tr>
            <td><?= (int) $course['id'] ?></td>
            <td><?= htmlspecialchars($course['code']) ?></td>
            <td><?= htmlspecialchars($course['nom']) ?></td>
            <td><a href="?delete=<?= (int) $course['id'] ?>" onclick="return confirm('Supprimer ce cours ?')">Supprimer</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
