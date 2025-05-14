<?php
require 'cbdd.php';

// Cas où l'arbitre entre un code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['code'])) {
    $code = $_POST['code'];

    if ($db->isGoodCode($code)) {
        // Suppression des cookies existants
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', time() - 3600, '/');
        }

        session_start();
        $_SESSION['id'] = $code;
        $_SESSION['state'] = 0;

        $db->setState(0);
        $db->setNbJuges(0);
        $db->viderTableRace();

        // Création et stockage du token
        $token = bin2hex(random_bytes(16));
        setcookie('biathlon_arbitre_token', $token, time() + 3600, "/");
        $db->setTokenArbitre($token);
        $_SESSION['tokenArbitre'] = $token;

        header("Location: arbitre1.php");
        exit();
    } else {
        echo "<br>arbitre : Code invalide.";
        exit();
    }
}

// Cas où un cookie est présent
if (!empty($_COOKIE['biathlon_arbitre_token'])) {
    $token = $_COOKIE['biathlon_arbitre_token'];
    $tokenBdd = $db->getTokenArbitre();

    if ($token === $tokenBdd) {
        session_start();
        $_SESSION['state'] = $db->getState();

        switch ($_SESSION['state']) {
            case 0:
            case 1:
                header("Location: arbitre1.php");
                break;
            case 2:
                header("Location: arbitre2.php");
                break;
            case 3:
                header("Location: arbitre3.php");
                break;
        } // sw
        exit();
    } // if token
    // Token invalide : on ne redirige pas ici, intentionnellement
} // if cookie
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>BIATHLON CODE ARBITRE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    BIATHLON CODE ARBITRE
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
