<?php
// arbitre2.php
session_start(); // Toujours ouvrir la session en début de script
require 'cbdd.php';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_arbitre_token'])) {
    header("Location: raz.php");
    exit();
}

$token = $_COOKIE['biathlon_arbitre_token'];
$tokenBdd = $db->getTokenArbitre();

if ($token !== $tokenBdd) {
    header("Location: raz.php");
    exit();
} // if token

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 0:
        case 1:
            header("Location: arbitre.php");
            exit();
        case 3:
            header("Location: arbitre3.php");
            exit();
        // 2 : On reste sur la page
    } // sw
} // isset
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIATHLON LANCEMENT COURSE ARBITRE</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        BIATHLON LANCEMENT COURSE ARBITRE
    </header>
    <?php
        // afficher les paramètres de la course
        $stmt = $db->getParamsCourse();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
        <div id="params">
            Nom de la course : <?php echo $row['nom_course']; ?><br>
            Nombre de coureurs/juges : <?php echo $row['max_juges']; ?><br>
        </div>
    <form action="arbitre3.php" method="post">
        <button type="submit" name="bt-go" id="bt-go" value="bt-go" >GO</button>
        <a href="raz.php"> RAZ</a>

    </form>
    <div id="status">Cliquez pour démarrer la course !</div>
    <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
