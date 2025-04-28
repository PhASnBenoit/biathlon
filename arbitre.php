<?php

/* possibilités :
 * L'arbitre ne s'est pas encore connecté, pas de cookie
 * -> demande du code, si code bon, création du cookie
 * L'arbitre s'est déjà connecté, cookie
 * -> Examen des variables session et state de la cbdd et
 *    aiguillage vers la bonne page.
 */
require 'cbdd.php';

// seulement dans le cas ou on a entré le code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = $_POST['code'];
    $exists = $db->isGoodCode($code);
    if ($exists) {
        // effacement des éventuels cookies
        if (isset($_COOKIE)) {
           foreach ($_COOKIE as $name => $value) {
              setcookie($name, '', time() - 3600, '/'); // date négative
           } // for
        } // if
        session_start();
        $_SESSION['id'] = $code;
        // init de la course
        $_SESSION['state'] = 0; // première fois.
        $db->setState(0);
        $db->setNbJuges(0);
        $db->viderTableRace();
        // token session
        $token = bin2hex(random_bytes(16));
        setcookie('biathlon_arbitre_token', $token, time() + 1800, "/");
        $db->setTokenArbitre($token);
        $_SESSION['tokenArbitre'] = $token;
//        echo "<br>arbitre : Session créée, cookie créé, ";
//        echo '<br>arbitre : $_SESSION = ';
//        var_dump($_SESSION);
        header("Location: arbitre1.php"); // vers paramétrage course
        exit();
    } else {
        echo "<br>arbitre : Code invalide.";
        exit();
    } // else
} // if $_SERVER

if (isset($_COOKIE['biathlon_arbitre_token'])) {
    $token = $_COOKIE['biathlon_arbitre_token']; // cookie local
    // on prend celui de la cbdd
    $tokenBdd = $db->getTokenArbitre();
    // comparaison
//    echo "<br>arbitre(si cookie) : récupération du token";
    if ($token === $tokenBdd) {
        session_start();
        $_SESSION['state'] = $db->getState();
//        echo "<br>arbitre(si cookie) : Bon token<br>";
//        echo '<br>arbitre(si cookie) : $_SESSION = ';
//        var_dump($_SESSION);
        // si session déjà commencée
        if (isset($_SESSION['state'])) {
            switch($_SESSION['state']) {
                case 0:
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
        } // if isset
        exit();
    } else {
//        echo "<br>arbitre(si cookie) : Mauvais token<br>";
       // header("Location: raz.php");
    }// else pas bon token
} // if cookie
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
