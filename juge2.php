<?php
require 'cbdd.php';

// TODO tester si existence session ouverte
if (isset($_COOKIE['client_token'])) {
    // le juge s'est déjà connecté

} // si token


// vérifier qu'on accède que si state=1
$stmt = $db->getState();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['state'] == '0')
    header("location: juge.php");
if ($row['state'] > '1')
    header("location: juge3.php");
// vérifier que nbJudge pas atteint le max.
$stmt = $db->getNbJuges();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['nb_juges'] == $row['max_juges'])
    header("location: public.php");
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
        function saveJudge(event) {
            event.preventDefault();
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
    </script>
</head>
<body>
    <header>
        JUGE : Configuration
    </header>
    <form onsubmit="saveJudge(event); return false;">
        <label for="judgeName">Nom du juge :</label>
        <input type="text" name="judgeName" id="judgeName" required>
        <br>
        <label for="runnerName">Nombre du coureur :</label>
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
