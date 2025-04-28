







<?php
// arbitre1.php
// formulaire de paramétrage de la course
require 'cbdd.php';
if (isset($_COOKIE['biathlon_arbitre_token'])) {
    $token = $_COOKIE['biathlon_arbitre_token']; // cookie local
    // on prend celui de la cbdd
    $tokenBdd = $db->getTokenArbitre();
    // comparaison
    if ($token === $tokenBdd) {
        session_start();
        echo '<br>arbitre : $_SESSION = ';
        var_dump($_SESSION);

        // si session déjà commencée
        if (isset($_SESSION['state'])) {
            switch($_SESSION['state']) {
                case 0: // on ne fait rien
                case 1: // on reste sur la page
                    break;
                case 2: // Formulaire go
                    header("Location: arbitre2.php"); // formulaire go ou raz
                    break;
                case 3: // suivi course et stop
                    header("Location: arbitre3.php");  // suivi de la course et FIN
                    break;
            } // sw
        // Si première connexion
        } else {
           echo "<br>arbitre1 : state existe pas,  FIN";
           exit();
        }// if isset
    } else {
        header("Location: raz.php");
    }// if pas bon token
} else {
    header("Location: raz.php");
}// if cookie
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARBITRE : Configuration de la Course</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function coucou() {
            alert('coucou');
        }
         function saveRace() {
            let raceName = $('#raceName').val();
            let judgeCount = $('#judgeCount').val();
            alert('dans saveRace');
            $.post('save_race.php', { raceName: raceName, judgeCount: judgeCount }, function(response) {
                $('#status').text(response);
                waitForJudges();
            });
        } // saveRace

        function waitForJudges() {
            $('#raceName').val('<?php echo $_SESSION['raceName'];?>');
            $('#judgeCount').val('<?php echo $_SESSION['judgeCount'];?>');
            const boutonS = document.getElementById('bt-sauver');
            const textRN = document.getElementById('raceName');
            const numberJC = document.getElementById('judgeCount');
            boutonS.style.display = 'none'; // ou JQuery : $('#bt-sauver').prop('disabled', true);
            numberJC.disabled = true;
            textRN.disabled = true;
            $('#status').html('Attente des juges...');
            let interval = setInterval(function() {
                $.get('check_state.php', function(response) {
                    if (response.trim() === '2') { // tous les juges sont connectés
                        clearInterval(interval);
                        $('#status').html('Tous les juges sont connectés !<br>Vous pouvez lancer la course.');
                        window.location.href = 'arbitre2.php';
                    } // if juges connectés
                });
            }, 1000);
        } // waitForJudges
        </script>

</head>
<body>
    <header>
        ARBITRE : Configuration de la Course
    </header>
    <form id="configCourse">
        <label for="raceName">Nom course :</label>
        <input type="text" name="raceName" id="raceName" required>
        <br>
        <label for="judgeCount">Nombre de juge :</label>
        <input type="number" name="judgeCount" id="judgeCount" min="1" max="8" required>
        <br>
        <button type="submit" name="bt-sauver" id="bt-sauver" value="bt-sauver">Sauver et attendre les juges</button>
        <a href="raz.php"> RAZ</a>
    </form>
    <div id="status">Définissez les paramètres...</div>

<script>
    $('#configCourse').on('submit', function(e) {
      e.preventDefault();
      alert("avant saveRace");
      coucou();
      alert("après saveRace");
    });

    /*
    //    if (isset($_SESSION['raceName'])) { // si paramétrage déjà fait
    //        echo "<br>On attend directement les juges";
    //        die();
    //        echo "window.onload = waitForJudges();";
    //    } // if id
    //
    */
</script>

    <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>


