<?php
    session_start();
    if (!isset($_SESSION['id']))       // tentative hacking
       header("Location: logout.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration de la Course par l'arbitre</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function saveRace(event) {
            event.preventDefault();
            let raceName = $('#raceName').val();
            let judgeCount = $('#judgeCount').val();
            $.post('save_race.php', { raceName: raceName, judgeCount: judgeCount }, function(response) {
                $('#status').text(response);
                const boutonS = document.getElementById('bt-sauver');
                const textRN = document.getElementById('raceName');
                const numberJC = document.getElementById('judgeCount');
                boutonS.style.display = 'none'; // ou JQuery : $('#bt-sauver').prop('disabled', true);
                numberJC.disabled = true;
                textRN.disabled = true;
                waitForJudges();
            });
        }

        function waitForJudges() {
            let interval = setInterval(function() {
                $.get('check_state.php', function(response) {
                    if (response.trim() === '2') { // tous les juges sont connectés
                        clearInterval(interval);
                        $('#status').text('Tous les juges sont connectés !');
                        const boutonGo = document.getElementById('bt-go');
                        boutonGo.style.display = 'inline'; // ou JQuery : $('#bt-sauver').prop('disabled', true);
                    } // if juges connectés
                });
            }, 1000);
        }
    </script>
</head>
<body>
    <header>
        ARBITRE : Configuration de la Course
    </header>
    <form onsubmit="saveRace(event);">
        <label for="raceName">Nom course :</label>
        <input type="text" name="raceName" id="raceName" required>
        <br>
        <label for="judgeCount">Nombre de juge :</label>
        <input type="number" name="judgeCount" id="judgeCount" min="1" max="8" required>
        <br>
        <button type="submit" name="bt-sauver" id="bt-sauver" value="sauver">Sauver et attendre les juges</button>
        <a href="raz.php">RAZ</a>
        <br>
        <button type="submit" name="bt-go" id="bt-go" value="go" hidden>GO</button>
    </form>
    <div id="status">Définissez les paramètres...</div>
    <footer>
        © 2025 - Biathlon Verification System
    </footer>
</body>
</html>
