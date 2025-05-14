<?php
// juge.php
session_start();
require "cbdd.php";

// lecture état éventuellement modifié par l'arbitre
$state = $db->getState();
$_SESSION['state'] = $state;

if (isset($_COOKIE['biathlon_juge_token'])) {
    $cookieToken = $_COOKIE['biathlon_juge_token'];
    $data = $db->getTokenJuge($cookieToken);
} else {
    // vérif nbmax juge atteint si nouveau juge.
    $stmt = $db->getNbJuges();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['nb_juges'] === $row['max_juges']) {
        header("location: public.php");
        exit();
    } // if juge
} // else

// donner la page correspondante
switch ($_SESSION['state']) {
    case 0:
        header("location: juge.php");
        exit();
    case 3:
        // si bon juge on peut aller à juge3
        if ($cookieToken === $data['token']) {
            header('Location: juge3.php');
            exit();
        } // if token pas bon
        header("location: public.php");
        exit();
    // 1 ou 2 on reste sur la page
} // sw

// arrivée ici, 2 possibilités :
// 1 - Attente départ course
// 2 - nouveau juge
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biathlon : Juge</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function saveJudge() {
            alert('saveJudge');
            $.get('check_state.php', function(response) {
                    if (response.trim() === '0') {
                       $('#status').text("L'arbitre a annulé la course !");
                        window.location.ref = "juge.php";
                    } // if juges connectés
                });
            let judgeName = $('#judgeName').val();
            let runnerName = $('#runnerName').val();
            $.post('save_judge.php', { runnerName: runnerName, judgeName: judgeName }, function(response) {
                $('#status').html(response);
                waitForGo();
            });
        } // saveJudge

        function waitForGo() {
            const btSave = document.getElementById('bt-sauver');
            const textRN = document.getElementById('runnerName');
            const textJN = document.getElementById('judgeName');
            textRN.disabled = true;
            textJN.disabled = true;
            btSave.disabled = true;
            $('#status').text('Attente du démarrage de la course !');
            let interval = setInterval(function() {
                $.get('check_state.php', function(response) {
                    if (response.trim() === '3') { // L'arbitre a fait le GO
                        clearInterval(interval);
                        $('#status').text('Course démarrée !');
                        alert('waitForGo: avant juge3');
                        window.location.ref = "juge3.php";
                    } // if juges connectés
                });
            }, 1000);
        }

        $(document).ready(function() {
        <?php
        if (isset($_SESSION['runnerName'])) { // si paramétrage déjà fait
            echo "alert('waitForGo'); onload = waitForGo();";
        } else {
        ?>
            $('#configJuge').on('submit', function(e) {
                e.preventDefault();
                alert('saveJudge');
                saveJudge();
            });
        <?php
        } // else
        ?>
    });

    </script>
</head>
<body>
    <header>
        JUGE : Configuration
    </header>
    <form id="configJuge">
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
