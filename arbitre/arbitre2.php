<?php
// arbitre2.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON LANCEMENT COURSE ARBITRE';
$foot = 'Biathlon Supervision System';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_arbitre_token'])) {
    echo "pas de cookie !";
    exit();
    header("Location: /arbitre/");
} // if cookie

$cookieToken = $_COOKIE['biathlon_arbitre_token'];
$tokenBdd = $db->getTokenArbitre();

if ($cookieToken !== $tokenBdd) {
    echo "mauvais token !";
    exit();
    header("Location: /arbitre/");
} // if token

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 0:
        case 1:
            header("Location: /arbitre/");
            exit();
        case 3:
            header("Location: arbitre3.php");
            exit();
        // 2 : On reste sur la page
    } // sw
} // isset

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt-go'])) {
    $db->setState(3);
    $db->set_t0();
    $_SESSION['state'] = 3;
    header("Location: arbitre3.php");
} // if post go

$page->entete($titre);
$page->finHeadBody();
$page->header($titre);

// afficher les paramètres de la course
$stmt = $db->getParamsCourse();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
        <div id="params">
            Nom de la course : <?php echo $row['nom_course']; ?><br>
            Nombre de coureurs/juges : <?php echo $row['max_juges']; ?><br>
            -----<br>
<?php
        $stmt = $db->getRace();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['judgeName']." est le juge ".$row['num']." de ".$row['runnerName']."<br>";
            } // wh
?>
        </div>
    <form action="arbitre2.php" method="post">
        <button type="submit" name="bt-go" id="bt-go" value="bt-go" >GO</button>
        <a href="raz.php"> RAZ</a>

    </form>
    <div id="status">Cliquez pour démarrer la course !</div>
<?php $page->footer($foot);?>
