<?php
// script pour l'état 0 du système

session_start();
// si session déjà commencée
if (isset($_SESSION['state'])) {
    switch($_SESSION['state']) {
        case 0:
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
                require 'cbdd.php';
                $code = $_POST['code'];
                $exists = $db->isGoodCode($code);
                if ($exists) {
                    $_SESSION['id'] = $code;
                    header("Location: arbitre1.php"); // vers paramétrage course
                    exit();
                } else {
                    echo "Code invalide.";
                    exit();
                } // else
            } // if $_SERVER
            break;
        case 1: // Attente connexion des juges
            header("Location: arbitre1.php"); // vers attente des juges
            break;
        case 2: // Formulaire go
            header("Location: arbitre2.php"); // formulaire go ou raz
            break;
        case 3: // suivi course et stop
            header("Location: arbitre3.php");  // suivi de la course et FIN
            break;
    } // sw
// Si première connexion
} else { // première fois
    $_SESSION['state'] = 0;  // état attente connexion et paramétrage course par l'arbitre
} // else

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        BIATHLON ESPACE ARBITRE
    </header>

    <main>
        <h1>Vérification du Code</h1>
        <form action="#" method="POST" class="form-container">
            <label for="codeInput">Entrez un code à 6 chiffres :</label>
            <input type="password" name="code" id="codeInput" maxlength="6" required>
            <button type="submit">Commencer</button>
        </form>
    </main>

    <footer>
        © 2025 - Biathlon Supervision System
    </footer>

</body>
</html>
