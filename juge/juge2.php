<?php
// juge.php
require "../cbdd.php";

if (isset($_COOKIE['biathlon_juge_token'])) {
    $cookieToken = $_COOKIE['biathlon_juge_token'];
    $data = $db->getTokenJuge($cookieToken);
    if ($data['token'] !== $cookieToken) {
        header("location: /juge/");
    } // if token diff
    echo "Cookie existe, token égaux.<br>";
    session_start();
    // lecture état éventuellement modifié par l'arbitre
    $state = $db->getState();
    $_SESSION['state'] = $state;
    // donner la page correspondante
    switch ($_SESSION['state']) {
        case 0:
            header("location: /juge/");
            exit();
        case 3:
            // si bon juge on peut aller à juge3
            if ($cookieToken === $data['token']) {
                header('Location: juge3.php');
            } // if token pas bon
            header("location: /public/");
        // 1 ou 2 on reste sur la page
    } // sw
    echo "On reste sur cette page.<br>";
} else {
    // vérif nbmax juge atteint si nouveau juge.
    $stmt = $db->getNbJuges();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['nb_juges'] === $row['max_juges']) {
        header("location: /public/");
    } // if juge
} // else

// arrivée ici, 2 possibilités :
// 1 - Attente départ course
// 2 - nouveau juge

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['judgeName'])) {
    $runnerName = $_POST['runnerName'];
    $judgeName = $_POST['judgeName'];
    echo "Validation du formulaire $runnerName $judgeName <br>";
    // bloquer la table config
    $db->lockTable("activity WRITE, config WRITE, race ", "WRITE");  // BLOCAGE DES TABLES
        // lire la valeur de nb_juges
        $req = $db->getNbJuges();
        $row = $req->fetch(PDO::FETCH_ASSOC);
        $noJudge = $row['nb_juges'];
        $maxJudges = $row['max_juges'];
        echo "noJuge = $noJudge et max = $maxJudges<br>";
        if ($noJudge < $maxJudges) { // pas créer plus de juges que le max défini
            echo "Création d'un juge<br>";
            // Suppression du cookie existant
            setcookie('biathlon_juge_token', '', time() - 3600, '/');
            $noJudge++;
            // créer le cookie
            $token = bin2hex(random_bytes(16));
            setcookie('biathlon_juge_token', $token, time() + 1800, "/"); //
            session_start();
            $_SESSION = array();
            $_SESSION['state'] = $db->getState();
            $_SESSION['no'] = $noJudge;
            $_SESSION['token'] = $token;
            $_SESSION['judgeState'] = 1;
            $_SESSION['runnerName'] = $runnerName;
            $_SESSION['judgeName'] = $judgeName;
            $_SESSION['maxJudges'] = $maxJudges;
            // créer la ligne dans la table race
            $res = $db->addJudge($runnerName, $judgeName, $noJudge, $token);
            $db->setNbJuges($noJudge);// augmente nb_juges+1
            if ($noJudge == $maxJudges) {
                $db->setState(2); // tous les juges sont présents.
                $_SESSION['state'] = 2;
            } // if
            // ouvrir session et sauver nojuge et les noms
            //echo "paramètres sauvegardés.<br>Vous êtes le juge $noJudge<br>En attente du départ de la course...";
        } else {// if pas plus de juge
            echo "Le nombre de juge est atteint ! impossible de continuer !<br>";
            //echo "Vous pouvez suivre la course : www.biathlon.lab/public/";
            $db->unlockTable();     //               DEBLOCAGE DES TABLES
            //exit();
        } // else max juges
        // débloquer la table
        $db->unlockTable();     //               DEBLOCAGE DES TABLES
} // if
echo "On va afficher la page WEB.<br>";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biathlon : Juge</title>
    <link rel="stylesheet" href="/biathlon/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        function waitForGo() {
            <?php
            if (isset($_SESSION['judgeName'])) { // nécessaire pour une reprise de connexion
                echo "wjudgeName = '".$_SESSION['judgeName']."';\n";
                echo "wrunnerName = '".$_SESSION['runnerName']."';\n";
            } else {
                echo "wjudgeName = 'NomJuge';\n";
                echo "wrunnerName = 'NomCoureur';\n";
            } // else
            ?>
            $('#judgeName').val(wjudgeName);
            $('#runnerName').val(wrunnerName);
            $('#bt-sauver').hide();
            $('#runnerName').prop('disabled', true);
            $('#judgeName').prop('disabled', true);
            $('#status').html("<img src='/biathlon/images/wondering-pondering.gif' alt='Patience...' width='100'> Attente du démarrage de la course !");
            alert('dans waitForGo');
            let interval = setInterval(function() {
                $.get('/biathlon/check_state.php', function(response) {
                    if (response.trim() === '3') { // L'arbitre a fait le GO
                        clearInterval(interval);
                        $('#status').text('Course démarrée !');
                        window.location.ref = "juge3.php";
                    } // if juges connectés
                });
            }, 1000);
        }

        <?php
              if (isset($_SESSION['runnerName'])) { // si paramétrage déjà fait
                echo ' $(document).ready(function() {';
                echo " alert('SESSION OK');\n  onload = waitForGo();";
                echo "});";
              } // if
        ?>
    </script>

</head>
<body>
    <header>
        JUGE : Configuration
    </header>
    <form id="configJuge" method="post" action="juge2.php">
        <label for="judgeName">Votre nom (juge) :</label>
        <input type="text" name="judgeName" id="judgeName" required>
        <br>
        <label for="runnerName">Nom du coureur :</label>
        <input type="text" name="runnerName" id="runnerName" required>
        <br>
        <button type="submit" name="bt-sauver" id="bt-sauver" value="bt-sauver">Sauver et attendre le départ</button>
    </form>
    <div id="status">Définissez les paramètres...</div>
    <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
