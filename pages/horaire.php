<?php
require_once __DIR__ . '/../connexion/db.php';
require_once __DIR__ . '/../functions/classes.php';
require_once __DIR__ . '/../functions/creneaux.php';

$db = getDb();
$classes = getClasses($db);
$nomClasse = $_GET['classe'] ?? ($classes[0]['nom'] ?? '');
$classe = $nomClasse ? getClassByName($db, $nomClasse) : null;
$horaires = $classe ? getSlotsByClassName($db, $nomClasse) : [];
include __DIR__ . '/../includes/header.php';
?>
<div class="card" >
    <h1>Horaire d'une classe</h1>
    <form method="get">
        <label for="classe">Classe</label>
        <select id="classe" name="classe">
            <?php foreach ($classes as $c): ?>
                <option value="<?= htmlspecialchars($c['nom']) ?>" <?= $c['nom'] === $nomClasse ? 'selected' : '' ?>><?= htmlspecialchars($c['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Afficher</button>
    </form>
</div>

<?php if ($classe): ?>
    <div class="card" >
        <h2><?= htmlspecialchars($classe['nom']) ?> - <?= htmlspecialchars($classe['annee_scolaire']) ?></h2>
        <?php if (!$horaires): ?><p>Aucun horaire enregistré.</p><?php else: ?>
        <table>
            <tr><th>Jour</th><th>Heure</th><th>Cours</th><th>Salle</th></tr>
            <?php foreach ($horaires as $horaire): ?>
            <tr>
                <td><?= htmlspecialchars(ucfirst($horaire['jour'])) ?></td>
                <td><?= substr($horaire['heure_debut'], 0, 5) ?> - <?= substr($horaire['heure_fin'], 0, 5) ?></td>
                <td><?= htmlspecialchars($horaire['code_cours'] . ' - ' . $horaire['cours']) ?></td>
                <td><?= htmlspecialchars($horaire['salle']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
