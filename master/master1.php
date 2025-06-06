<?php
// master1.php
session_start(); // Toujours ouvrir la session en début de script
//print_r($_SESSION);

require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON PARAMETRES MASTER';
$foot = 'Biathlon Supervision System';

$state = $db->getState();
if ($state == -1) {
    header("Location: /master/");
    exit();
} // if state

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_master_token'])) {
    header("Location: /master/");
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
        case -1:
            header("Location: /master/");
            exit();
        case 2:
            header("Location: master2.php");
            exit();
        case 3:
            header("Location: master3.php");
            exit();
        // 0 et 1 : On reste sur la page
    } // sw
} // if session

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['raceName'])) {
    $_SESSION['raceName'] = $_POST['raceName'];
    $_SESSION['judgeCount'] = $_POST['judgeCount'];
    $_SESSION['dist2T'] = $_POST['dist2T'];
    $_SESSION['distPen'] = $_POST['distPen'];
    $_SESSION['distSansPen'] = $_POST['distSansPen'];
    $_SESSION['dist1T'] = $_POST['dist1T'];
    $res = $db->saveParamsRace($_SESSION['raceName'], $_SESSION['judgeCount'], $_SESSION['dist2T'], $_SESSION['distPen'],$_SESSION['dist1T'], $_SESSION['distSansPen']);
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

<?php
    $result = $db->getParamsCourse();
    $row = $result->fetch(PDO::FETCH_ASSOC);
?>
    <form id="configCourse" method="post">
        <label for="raceName">Nom course :</label>
        <input type="text" name="raceName" id="raceName" required><br>
        <label for="judgeCount">Nombre de juges :</label>
        <input type="number" name="judgeCount" id="judgeCount" min="1" max="8" required><br>
        <label for="dist1T">Distance d'un tours en m (1T) :</label>
        <input type="number" name="dist1T" id="dist1T" min="100" max="500" value="<?php echo $row['dist1T'];?>" required><br>
        <label for="dist2T">Distance 2 tours en m (2T) :</label>
        <input type="number" name="dist2T" id="dist2T" min="200" max="999" value="<?php echo $row['dist2T'];?>" required><br>
        <label for="distSansPen">Distance SANS pénalité en m (TP) :</label>
        <input type="number" name="distSansPen" id="distSansPen" min="1" max="100" value="<?php echo $row['distSansPen'];?>" required><br>
        <label for="distPen">Distance du tour de pénalité en m (TP) :</label>
        <input type="number" name="distPen" id="distPen" min="30" max="200" value="<?php echo $row['distPen'];?>" required><br>
        <a href="chpwd.php">Changer le code</a>&nbsp;&nbsp;&nbsp;
        <button type="submit" id="bt-sauver" value="bt-sauver">Sauver et attendre les juges</button>
        &nbsp;&nbsp;&nbsp;<a href="raz.php">RAZ</a>
    </form>

    <div id="status">Définissez les paramètres...</div>

<?php $page->footer($foot);?>
