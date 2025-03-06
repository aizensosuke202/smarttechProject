<?php
$host = 'localhost';
$db = 'smart_db';
$user = 'admin'; // ou votre nom d'utilisateur
$pass = 'letpass'; // ou votre mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>