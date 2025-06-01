<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "conseption pi";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

if (isset($_POST['donnees'])) {
    $donnees = $_POST['donnees'];
    
    // Supposons que les colonnes sont : prénom, nom, email, mot_de_passe, spécialité_id, niveau_id
    echo "<h3>Données enregistrées :</h3><ul>";

    for ($i = 1; $i < count($donnees); $i++) { // commencer à 1 si la première ligne est l’en-tête
        $ligne = $donnees[$i];
        if (count($ligne) < 6) continue;

        $prenom = $conn->real_escape_string($ligne[0]);
        $nom = $conn->real_escape_string($ligne[1]);
        $email = $conn->real_escape_string($ligne[2]);
        $mot_de_passe = $conn->real_escape_string($ligne[3]);
        $specialite_id = intval($ligne[4]);
        $niveau_id = intval($ligne[5]);

        $sql = "INSERT INTO student (first_name, last_name, email, password, specialty_id, level_id)
                VALUES ('$prenom', '$nom', '$email', '$mot_de_passe', $specialite_id, $niveau_id)";
        
        if ($conn->query($sql) === TRUE) {
            echo "<li>$prenom $nom - Enregistré</li>";
        } else {
            echo "<li>Erreur pour $prenom $nom : " . $conn->error . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "Aucune donnée reçue.";
}

$conn->close();
?>