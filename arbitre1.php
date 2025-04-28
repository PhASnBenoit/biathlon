<?php
// arbitre1.php
require 'cbdd.php';
session_start(); // Toujours ouvrir la session en début de script

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
}

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
    }
}
// Si pas de state : continuer et afficher la page
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ARBITRE : Configuration de la Course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function saveRace() {
            let raceName = $('#raceName').val();
            let judgeCount = $('#judgeCount').val();
            alert('Dans saveRace');
            $.post('save_race.php', { raceName: raceName, judgeCount: judgeCount }, function(response) {
                $('#status').text(response);
                waitForJudges();
            });
        } // saveRace

        function waitForJudges() {
            alert('Dans waitForJudges');
            // Ici on protège PHP avec isset pour éviter erreur JS si pas défini
            let raceName = <?php echo isset($_SESSION['raceName']) ? json_encode($_SESSION['raceName']) : '""'; ?>;
            let judgeCount = <?php echo isset($_SESSION['judgeCount']) ? json_encode($_SESSION['judgeCount']) : '""'; ?>;
            $('#raceName').val(raceName);
            $('#judgeCount').val(judgeCount);

            $('#bt-sauver').hide();
            $('#raceName').prop('disabled', true);
            $('#judgeCount').prop('disabled', true);
            $('#status').html('Attente des juges...');

            let interval = setInterval(function() {
                $.get('check_state.php', function(response) {
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
            if (isset($_SESSION['raceName'])) { // si paramétrage déjà fait
            echo "alert('On attend directement les juges');";
            echo "window.onload = waitForJudges();";
            } else {
            ?>
                $('#configCourse').on('submit', function(e) {
                    e.preventDefault();
                    alert("Avant saveRace");
                    saveRace();
                    alert("Après saveRace");
                });
            <?php
            } // else
            ?>
        });
    </script>
</head>
<body>
    <header>
        ARBITRE : Configuration de la Course
    </header>

    <form id="configCourse">
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
