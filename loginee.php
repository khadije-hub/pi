<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "competition";

// Connexion
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = "Veuillez fournir un email et un mot de passe.";
    } else {
        // 🔍 1. Vérifier dans table user + role
        $sqlUser = "SELECT u.*, r.name as role_name 
                    FROM user u 
                    JOIN role r ON u.role_id = r.id 
                    WHERE u.email = ?";
        $stmt = $conn->prepare($sqlUser);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ⚠️ Comparaison directe (si mot de passe n'est pas hashé)
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role_name'];

                // Rediriger selon le rôle
                switch ($user['role_name']) {
                    case 'Administrateur':
                        header("Location: admin.php"); break;
                    case 'Evaluateur':
                        header("Location: evaluateur.php"); break;
                    case 'Coordinateur':
                        header("Location: coordinateur.php"); break;
                    default:
                        header("Location: dashboard.php"); break;
                }
                exit();
            } else {
                $message = "Mot de passe incorrect pour utilisateur.";
            }

        } else {
            // 🔍 2. Vérifier dans table student
            $sqlStudent = "SELECT * FROM student WHERE email = ?";
            $stmt = $conn->prepare($sqlStudent);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $student = $result->fetch_assoc();

                // ⚠️ Comparaison directe (si mot de passe non chiffré)
                if ($password === $student['password']) {
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['first_name'];
                    header("Location: student.php");
                    exit();
                } else {
                    $message = "Mot de passe incorrect pour étudiant.";
                }
            } else {
                $message = "Utilisateur non trouvé.";
            }
        }
    }
}

$conn->close();
?>

<!-- HTML Formulaire -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h2>Connexion</h2>

    <?php if ($message): ?>
        <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe:</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Se connecter">
    </form>
</body>
</html>