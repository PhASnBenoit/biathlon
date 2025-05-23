<?php
// master2.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON LANCEMENT COURSE MASTER';
$foot = 'Biathlon Supervision System';

$state = $db->getState();
if ($state == -1) {
    header("Location: /master/");
    exit();
} // if state

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_master_token'])) {
    echo "pas de cookie !";
    exit();
    header("Location: /master/");
} // if cookie

$cookieToken = $_COOKIE['biathlon_master_token'];
$tokenBdd = $db->getTokenmaster();
if ($cookieToken !== $tokenBdd) {
    echo "mauvais token !";
    exit();
    header("Location: /master/");
} // if token

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 0:
        case 1:
            header("Location: /master/");
            exit();
        case 3:
            header("Location: master3.php");
            exit();
        // 2 : On reste sur la page
    } // sw
} // isset

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt-go'])) {
    $t0 = hrtime(true);
    $db->set_t0($t0);
    $db->setState(3);
    $_SESSION['state'] = 3;
    header("Location: master3.php");
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
        foreach ($stmt as $row) {
            echo $row['judgeName']." est le juge ".$row['num']." de ".$row['runnerName']."<br>";
        } // foreach
?>
        </div>
    <form action="master2.php" method="post">
        <button type="submit" name="bt-go" id="bt-go" value="bt-go" >GO</button>
        <a href="raz.php"> RAZ</a>

    </form>
    <div id="status">Cliquez pour démarrer la course !</div>
<?php $page->footer($foot);?>
