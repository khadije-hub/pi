<?php
session_start();

include "layout/app.php";

// Définir la langue
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'french';

switch ($lang) {
    case 'en': $lang_file = "english.php"; break;
    case 'fr': $lang_file = "french.php"; break;
    default:   $lang_file = "lang/arabic.php";
}

$T = file_exists($lang_file) ? include($lang_file) : include("arabic.php"); 

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=competition;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur lors de la connexion à la base de données : " . $e->getMessage());
}

// Vérifier si les données sont envoyées par formulaire
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM student WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $student = $stmt->fetch();

    if (!$student) {
        echo "<p>{$T['email_error']} <a href='login.php'>Retour</a></p>";
        exit;
    }

    $_SESSION['student'] = $student;
} elseif (isset($_SESSION['student'])) {
    $student = $_SESSION['student'];
} else {
    header("Location: login.php");
    exit;
}

// Vérifier si l'étudiant est déjà inscrit à une équipe
$insc = $pdo->prepare("SELECT t.name AS team_name, i.role FROM inscription i JOIN team t ON i.team_id = t.id WHERE i.student_id = ?");
$insc->execute([$student['id']]);
$existing = $insc->fetch();

// Récupérer toutes les équipes
$teams = $pdo->query("SELECT * FROM team")->fetchAll();
?>

<!DOCTYPE html>
<html lang="<?= ($lang === 'arabic') ? 'ar' : ($lang === 'english' ? 'en' : 'fr') ?>" dir="<?= ($lang === 'arabic') ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $T['title'] ?></title>
</head>
<body>

<div class="lang-select">
    <form method="get">
        <select name="lang" onchange="this.form.submit()">
            <option value="arabic" <?= ($lang === 'arabic') ? 'selected' : '' ?>>العربية</option>
            <option value="fr" <?= ($lang === 'fr') ? 'selected' : '' ?>>Français</option>
            <option value="en" <?= ($lang === 'en') ? 'selected' : '' ?>>englais</option>
        </select>
    </form>
</div>

<div class="container">
    <div class="student-info">
        <p><?= $T['welcome'] ?> <strong><?= htmlspecialchars($student['first_name']) ?></strong></p>
        <p><?= $T['student_id'] ?>: <strong><?= $student['id'] ?></strong></p>
    </div>

    <?php if ($existing): ?>
        <p><?= $T['already_in_team'] ?>: <strong><?= htmlspecialchars($existing['team_name']) ?></strong></p>
        <p><?= $T['your_role'] ?>: <strong><?= htmlspecialchars($existing['role']) ?></strong></p>
    <?php else: ?>
        <form action="join_team.php" method="post">
            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
            <input type="hidden" name="role" value="Membre">
            <label><?= $T['choose_team'] ?>:</label>
            <select name="team_id" required>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="<?= $T['join_team'] ?>">
        </form>
    <?php endif; ?>
</div>

</body>
</html>








