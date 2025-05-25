<?php
// Connexion ou inclusion de ton objet $db ici
// require_once 'db.php'; // par exemple
require '../cbdd.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie si le champ 'code' est bien envoyé et correspond à 6 chiffres
    if (isset($_POST['codeRaz']) && preg_match('/^\d{6}$/', $_POST['codeRaz'])) {
        $code = $_POST['codeRaz'];
        // Récupération du mot de passe chiffré depuis la base via l'objet $db
        $hashedPassword = $db->getCodeMaster(); // Supposé retourner le hash bcrypt du mot de passe
        // Vérification du mot de passe
        if (password_verify($code, $hashedPassword)) {
            // Le mot de passe est correct, on inclut (ou redirige vers) raz.php
            header("Location: raz.php");
            exit;
        } // if pass
    } // if isset
    header("Location: /master/");
} // if server
?>
