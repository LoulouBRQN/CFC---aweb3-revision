<?php
require_once __DIR__ . '/../connexion/db.php';
require_once __DIR__ . '/../functions/classes.php';
require_once __DIR__ . '/../functions/cours.php';
require_once __DIR__ . '/../functions/creneaux.php';
require_once __DIR__ . '/../config/constants.php';

$db = getDb();
$message = '';
$error = '';

if (isset($_GET['delete'])) {
    deleteCreneau($db, (int) $_GET['delete']);
    header('Location: creneaux.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classeId = (int) ($_POST['classe_id'] ?? 0);
    $coursId = (int) ($_POST['cours_id'] ?? 0);
    $jour = $_POST['jour'] ?? '';
    $debut = $_POST['heure_debut'] ?? '';
    $fin = $_POST['heure_fin'] ?? '';
    $salle = trim($_POST['salle'] ?? '');

    if (!$classeId || !$coursId || !in_array($jour, JOURS, true) || !$debut || !$fin || $debut >= $fin || $salle === '') {
        $error = 'Veuillez remplir correctement tous les champs. L’heure de fin doit être après l’heure de début.';
    } else {
        insertCreneau($db, $classeId, $coursId, $jour, $debut, $fin, $salle);
        $message = 'Créneau ajouté.';
    }
}

$classes = getClasses($db);
$cours = getCourses($db);
$creneaux = getCreneaux($db);
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <h1>Créneaux</h1>
    <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Classe</label>
        <select name="classe_id" required>
            <option value="">Choisir</option>
            <?php foreach ($classes as $classe): ?><option value="<?= $classe['id'] ?>"><?= htmlspecialchars($classe['nom']) ?></option><?php endforeach; ?>
        </select>
        <label>Cours</label>
        <select name="cours_id" required>
            <option value="">Choisir</option>
            <?php foreach ($cours as $course): ?><option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['code'] . ' - ' . $course['nom']) ?></option><?php endforeach; ?>
        </select>
        <label>Jour</label>
        <select name="jour" required>
            <?php foreach (JOURS as $jour): ?><option value="<?= $jour ?>"><?= ucfirst($jour) ?></option><?php endforeach; ?>
        </select>
        <label>Heure de début</label><input type="time" name="heure_debut" required>
        <label>Heure de fin</label><input type="time" name="heure_fin" required>
        <label>Salle</label><input name="salle" placeholder="R104" required>
        <br><button type="submit">Ajouter le créneau</button>
    </form>
</div>
<table>
    <tr><th>Classe</th><th>Cours</th><th>Jour</th><th>Heure</th><th>Salle</th><th>Action</th></tr>
    <?php foreach ($creneaux as $creneau): ?>
        <tr>
            <td><?= htmlspecialchars($creneau['classe']) ?></td>
            <td><?= htmlspecialchars($creneau['code_cours'] . ' - ' . $creneau['cours']) ?></td>
            <td><?= htmlspecialchars(ucfirst($creneau['jour'])) ?></td>
            <td><?= substr($creneau['heure_debut'], 0, 5) ?> - <?= substr($creneau['heure_fin'], 0, 5) ?></td>
            <td><?= htmlspecialchars($creneau['salle']) ?></td>
            <td><a href="?delete=<?= (int) $creneau['id'] ?>" onclick="return confirm('Supprimer ce créneau ?')">Supprimer</a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php include __DIR__ . '/../includes/footer.php'; ?>
