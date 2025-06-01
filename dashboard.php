<?php
session_start();
include 'connexion.php'; // قاعدة البيانات

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// ✅ Importation du fichier Excel
if (isset($_POST['import'])) {
    $file = $_FILES['excel']['tmp_name'];
    if ($file) {
        $spreadsheet = IOFactory::load($file);
        $data = $spreadsheet->getActiveSheet()->toArray();

        foreach ($data as $index => $row) {
            if ($index == 0) continue; // Ignorer l'en-tête
            $prenom = $conn->real_escape_string($row[0]);
            $nom = $conn->real_escape_string($row[1]);
            $email = $conn->real_escape_string($row[2]);
            $mot_de_passe = $conn->real_escape_string($row[3]);
            $specialite_id = intval($row[4]);
            $niveau_id = intval($row[5]);

            $conn->query("INSERT INTO student (first_name, last_name, email, password, specialty_id, level_id) VALUES ('$prenom', '$nom', '$email', '$mot_de_passe', '$specialite_id', '$niveau_id')");
        }
        header("Location: dashboard.php");
        exit();
    }
}

// ✅ Ajouter un étudiant manuellement
if (isset($_POST['ajouter'])) {
    $prenom = $conn->real_escape_string($_POST['first_name']);
    $nom = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $mot_de_passe = $conn->real_escape_string($_POST['password']);
    $specialite_id = intval($_POST['specialty_id']);
    $niveau_id = intval($_POST['level_id']);

    $conn->query("INSERT INTO student (first_name, last_name, email, password, specialty_id, level_id) VALUES ('$prenom', '$nom', '$email', '$mot_de_passe', '$specialite_id', '$niveau_id')");
    header("Location: dashboard.php");
    exit();
}

// ✅ Modifier un étudiant
if (isset($_POST['modifier'])) {
    $id = intval($_POST['id']);
    $prenom = $conn->real_escape_string($_POST['first_name']);
    $nom = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $specialite_id = intval($_POST['specialty_id']);
    $niveau_id = intval($_POST['level_id']);
    
    $conn->query("UPDATE student SET first_name='$prenom', last_name='$nom', email='$email', specialty_id='$specialite_id', level_id='$niveau_id' WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}

// ✅ Supprimer un étudiant
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $conn->query("DELETE FROM student WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #6B73FF 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-text {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .user-email {
            color: white;
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
        }

        .actions-bar {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .import-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            flex: 1;
            min-width: 300px;
        }

        .add-student-btn {
            background: linear-gradient(135deg, #4285F4, #5A9FFF);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 25px 35px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(66, 133, 244, 0.3);
        }

        .add-student-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(66, 133, 244, 0.4);
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
            width: 100%;
        }

        .file-input {
            display: none;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .file-input-label:hover {
            border-color: #4285F4;
            background: #f0f7ff;
        }

        .import-btn {
            background: linear-gradient(135deg, #1E88E5, #42A5F5);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3);
        }

        .import-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.4);
        }

        .students-table {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table-header {
            background: linear-gradient(135deg, #4285F4, #667eea);
            color: white;
            padding: 20px;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 8px;
            text-decoration: none;
            display: inline-block;
        }

        .edit-btn {
            background: #2196F3;
            color: white;
        }

        .edit-btn:hover {
            background: #1976D2;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #2196F3;
            color: white;
        }

        .delete-btn:hover {
            background: #1976D2;
            transform: translateY(-1px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #4285F4;
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.2);
        }

        .submit-btn {
            background: linear-gradient(135deg, #4285F4, #667eea);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(66, 133, 244, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(66, 133, 244, 0.4);
        }

        .edit-row {
            background: #fff3cd !important;
        }

        .edit-input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #4285F4;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .save-btn {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            margin-right: 8px;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .actions-bar {
                flex-direction: column;
            }
            
            .add-student-btn {
                min-width: auto;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="welcome-text">
                <i class="fas fa-graduation-cap"></i>
                Dashboard Étudiants
            </div>
            <div class="user-email">
                <i class="fas fa-user"></i>
                yahya.ouldsidi@eum.edu.mr
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="actions-bar">
            <!-- Import Section -->
            <div class="import-section">
                <h3 style="margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-excel"></i>
                    Importer depuis Excel
                </h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="file-input-wrapper">
                        <input type="file" name="excel" id="excel-file" class="file-input" accept=".xlsx,.xls" required>
                        <label for="excel-file" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Choisir un fichier Excel</span>
                        </label>
                    </div>
                    <button type="submit" name="import" class="import-btn">
                        <i class="fas fa-upload"></i>
                        Importer les données
                    </button>
                </form>
            </div>

            <!-- Add Student Button -->
            <button class="add-student-btn" onclick="openModal()">
                <i class="fas fa-plus"></i>
                Ajouter Étudiant
            </button>
        </div>

        <!-- Students Table -->
        <div class="students-table">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-users"></i>
                    Liste des Étudiants
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM student ORDER BY id DESC");
                    if ($res && $res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            if (isset($_GET['modifier']) && $_GET['modifier'] == $row['id']) {
                                // Mode édition
                                ?>
                                <tr class="edit-row">
                                    <form method="POST">
                                        <td><?= $row['id'] ?></td>
                                        <td><input type="text" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" class="edit-input" required></td>
                                        <td><input type="text" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" class="edit-input" required></td>
                                        <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" class="edit-input" required></td>
                                        <td>
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="specialty_id" value="<?= $row['specialty_id'] ?>">
                                            <input type="hidden" name="level_id" value="<?= $row['level_id'] ?>">
                                            <button type="submit" name="modifier" class="save-btn">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                            <a href="dashboard.php" class="cancel-btn">
                                                <i class="fas fa-times"></i> Annuler
                                            </a>
                                        </td>
                                    </form>
                                </tr>
                                <?php
                            } else {
                                // Mode lecture
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <a href="dashboard.php?modifier=<?= $row['id'] ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="dashboard.php?supprimer=<?= $row['id'] ?>" 
                                           class="action-btn delete-btn" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #666;">
                                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 10px; opacity: 0.3;"></i><br>
                                Aucun étudiant trouvé. Ajoutez votre premier étudiant!
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Student -->
    <div class="modal" id="addStudentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-user-plus"></i>
                    Ajouter un Étudiant
                </h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="first_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="last_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">ID Spécialité</label>
                    <input type="number" name="specialty_id" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">ID Niveau</label>
                    <input type="number" name="level_id" class="form-input" required>
                </div>
                <button type="submit" name="ajouter" class="submit-btn">
                    <i class="fas fa-save"></i>
                    Ajouter l'étudiant
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('addStudentModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addStudentModal').style.display = 'none';
        }

        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('addStudentModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Afficher le nom du fichier sélectionné
        document.getElementById('excel-file').addEventListener('change', function(e) {
            const label = document.querySelector('.file-input-label span');
            if (e.target.files.length > 0) {
                label.textContent = e.target.files[0].name;
            } else {
                label.textContent = 'Choisir un fichier Excel';
            }
        });
    </script>
</body>
</html>