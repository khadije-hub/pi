l<?php
// Paramètres de connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "conseption pi"; // ← هذا هو اسم قاعدة البيانات الخاص بك

// Création de la connexion
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// UTF-8 pour supporter les caractères arabes et spéciaux
$conn->set_charset("utf8");
?>