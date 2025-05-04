<?php
// juge.php
session_start();
require "cbdd.php";

// Est-ce une reprise de connexion ?
if (isset($_COOKIE['biathlon_token'])) {
    // le juge s'est déjà connecté il y a longtemps ou pas !
    $cookieToken = $_COOKIE['biathlon_juge_token'];
    $datas = $db->getTokenJuge();    // charge l'eventuel token corrrespondant à un juge.
    if ($cookieToken === $data['token']) {  // Si token reconnu
        // lire l'état dans la base
        $state = $db = getState();
        $_SESSION['state'] = $state;
        // donner la page correspondante
        if ($state == '0')
            header("location: juge.php");
        if ($state == '3')
            header("location: juge3.php");
    } else {  // mauvais token
        $stmt = $db->getNbJuges();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['nb_juges'] == $row['max_juges'])
            header("location: public.php");
    } // else
} // si token

// vérifier que nbJudge pas atteint le max.
$stmt = $db->getNbJuges();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['nb_juges'] === $row['max_juges'])
    header("location: public.php");

// 2 possibilités : Attente départ course ou entrée comme nouveau juge

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
                const textRN = document.getElementById('runnerName');
                const textJN = document.getElementById('judgeName');
                textRN.disabled = true;
                textJN.disabled = true;
                waitForGo();
            });
        } // saveJudge

        function waitForGo() {
            let interval = setInterval(function() {
                $.get('check_state.php', function(response) {
                    if (response.trim() === '3') { // L'arbitre a fait le GO
                        clearInterval(interval);
                        $('#status').text('Course démarrée !');
                        window.location.ref = "juge3.php";
                    } // if juges connectés
                });
            }, 1000);
        }

        $(document).ready(function() {
        <?php
        if (isset($_SESSION['runnerName'])) { // si paramétrage déjà fait
            echo "window.onload = waitForGo();";
        } else {
        ?>
            $('#configJuge').on('submit', function(e) {
                e.preventDefault();
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
        <button type="submit" name="bt-sauver" id="bt-sauver" value="sauver">Sauver et attendre le départ</button>
    </form>
    <div id="status">Définissez les paramètres...</div>
    <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
