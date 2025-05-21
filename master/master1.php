<?php
// master1.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON PARAMETRES MASTER';
$foot = 'Biathlon Supervision System';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_master_token'])) {
    exit();
} // if cookie

$token = $_COOKIE['biathlon_master_token'];
$tokenBdd = $db->getTokenmaster();
if ($token !== $tokenBdd) {
    exit();
} // if token

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 2:
            header("Location: master2.php");
            exit();
        case 3:
            header("Location: master3.php");
            exit();
        // 0 et 1 : On reste sur la page
    } // sw
} // if session

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purger'])) {
    require 'ccsv.php';
    $csv->purgerCsv();
} // if 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raceName'])) {
    $_SESSION['raceName'] = $_POST['raceName'];
    $_SESSION['judgeCount'] = $_POST['judgeCount'];
    $res = $db->saveParamsRace($_SESSION['raceName'], $_SESSION['judgeCount']);
    $db->setState(1);
    $_SESSION['state'] = 1;
} // if

$page->entete($titre);
?>
    <script>
        function waitForJudges() {
            // Ici on protège PHP avec isset pour éviter erreur JS si pas défini
<?php
            echo "let wraceName = '".(isset($_SESSION['raceName'])?$_SESSION['raceName']:'-')."';\n";
            echo "let wjudgeCount = ".(isset($_SESSION['judgeCount'])?$_SESSION['judgeCount']:0).";\n";
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
                        window.location.href = 'master2.php';
                    }
                });
            }, 1000);
        } // waitForJudges

        $(document).ready(function() {
<?php
            if (isset($_SESSION['raceName'])) { // si paramétrage déjà fait
                echo "window.onload = waitForJudges();";
            }
?>
        });
    </script>

<?php $page->finHeadBody();?>
<?php $page->header($titre);?>

    <form id="configCourse" method="post">
        <label for="raceName">Nom course :</label>
        <input type="text" name="raceName" id="raceName" required><br>

        <label for="judgeCount">Nombre de juge :</label>
        <input type="number" name="judgeCount" id="judgeCount" min="1" max="8" required><br>

        <button type="submit" id="bt-sauver" value="bt-sauver">Sauver et attendre les juges</button>
        <a href="raz.php">RAZ</a>
    </form>

    <div id="status">Définissez les paramètres...</div>

<?php
    $repertoire = __DIR__ . '/../res';  // dossier où sont stockés les fichiers CSV
    $baseUrl = '/biathlon/res/';               // chemin relatif depuis le navigateur
    $fichiers = glob($repertoire . '/*.csv');
    echo "<h2>Fichiers CSV disponibles</h2>";
    echo "<ul>";
    foreach ($fichiers as $cheminComplet) {
        $nomFichier = basename($cheminComplet);
        echo '<a href="' . $baseUrl . $nomFichier . '" target="_blank">' . $nomFichier . '</a><br>';
    } // foreach
    echo "</ul>";
    echo "<form method='post'><button type='submit' name='purger'>Purger les anciens fichiers (>1an)</button></form>";
?>

<?php $page->footer($foot);?>
