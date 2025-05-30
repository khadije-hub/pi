<?php
$pdo = new PDO("mysql:host=localhost;dbname=competition;charset=utf8", 'root', '');
$student_id = $_POST['student_id'] ?? null;
$team_id = $_POST['team_id'] ?? null;
$role = $_POST['role'] ?? 'Membre';

if (!$student_id || !$team_id) {
    echo "<p>Données invalides. <a href='select_team.php'>Retour</a></p>";
    exit;
}

// Vérifier si l'étudiant est déjà inscrit dans une équipe
$check = $pdo->prepare("SELECT * FROM inscription WHERE student_id = ?");
$check->execute([$student_id]);
if ($check->fetch()) {
    echo "<p>Vous êtes déjà membre d'une équipe. <a href='select_team.php'>Retour</a></p>";
    exit;
}

// Vérifier le nombre de membres de l'équipe
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM inscription WHERE team_id = ?");
$countStmt->execute([$team_id]);
$memberCount = $countStmt->fetchColumn();

if ($memberCount >= 6) {
    echo "<p>Cette équipe est complète. <a href='select_team.php'>Retour</a></p>";
    exit;
}

// Ajouter l'étudiant à l'équipe
$insert = $pdo->prepare("INSERT INTO inscription (student_id, team_id, role) VALUES (?, ?, ?)");
$insert->execute([$student_id, $team_id, $role]);

echo "<p>Inscription réussie. <a href='select_team.php'>Retour</a></p>";
?>