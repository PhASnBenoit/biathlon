<?php
// conditions pour enclencher cette page : state = 1


// pour info, si state=3, directement aller à juge3.php

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
            let judgeName = $('#judgeName').val();
            let runnerName = $('#runnerName').val();
            $.post('save_judge.php', { runnerName: runnerName, judgeName: judgeName }, function(response) {
                $('#status').text(response);
                const textRN = document.getElementById('runnerName');
                const textJN = document.getElementById('judgeName');
                textRN.disabled = true;
                textJN.disabled = true;
                waitForGo();
            });
        }

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
