<?php
// arbitre1.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_arbitre_token'])) {
    exit();
} // if cookie

$token = $_COOKIE['biathlon_arbitre_token'];
$tokenBdd = $db->getTokenArbitre();

if ($token !== $tokenBdd) {
    exit();
} // if token

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 2:
            header("Location: arbitre2.php");
            exit();
        case 3:
            header("Location: arbitre3.php");
            exit();
        // 0 et 1 : On reste sur la page
    } // sw
} // if session

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raceName'])) {
    $_SESSION['raceName'] = $_POST['raceName'];
    $_SESSION['judgeCount'] = $_POST['judgeCount'];
    $res = $db->saveParamsRace($_SESSION['raceName'], $_SESSION['judgeCount']);
    $db->setState(1);
    $_SESSION['state'] = 1;
} // if
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>BIATHLON CONFIGURATION ARBITRE</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/biathlon/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function waitForJudges() {
            // Ici on protège PHP avec isset pour éviter erreur JS si pas défini
            <?php
            echo "let wraceName = '".$_SESSION['raceName']."';\n";
            echo "let wjudgeCount = ".$_SESSION['judgeCount'].";\n";
            ?>
            $('#raceName').val(wraceName);
            $('#judgeCount').val(wjudgeCount);
            $('#bt-sauver').hide();
            $('#raceName').prop('disabled', true);
            $('#judgeCount').prop('disabled', true);
            $('#status').html("<img src='/biathlon/images/wondering-pondering.gif' alt='Patience...' width='100'> Attente des juges...");

            let interval = setInterval(function() {
                $.get('/biathlon/check_state.php', function(response) {
                    if (response.trim() === '2') {
                        clearInterval(interval);
                        $('#status').html('Tous les juges sont connectés !<br>Vous pouvez lancer la course.');
                        window.location.href = 'arbitre2.php';
                    }
                });
            }, 1000);
        } // waitForJudges

        $(document).ready(function() {
            <?php
            if (isset($_SESSION['state'])) { // si paramétrage déjà fait
                echo "window.onload = waitForJudges();";
            }
            ?>
        });
    </script>
</head>
<body>
    <header>
        BIATHLON CONFIGURATION ARBITRE
    </header>

    <form id="configCourse" method="post">
        <label for="raceName">Nom course :</label>
        <input type="text" name="raceName" id="raceName" required><br>

        <label for="judgeCount">Nombre de juge :</label>
        <input type="number" name="judgeCount" id="judgeCount" min="1" max="8" required><br>

        <button type="submit" id="bt-sauver" value="bt-sauver">Sauver et attendre les juges</button>
        <a href="raz.php">RAZ</a>
    </form>

    <div id="status">Définissez les paramètres...</div>

    <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
