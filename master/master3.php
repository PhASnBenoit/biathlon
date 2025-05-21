<?php
// master3.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON SUIVI COURSE MASTER';
$foot = 'Biathlon Supervision System';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_master_token'])) {
  echo "Cookie absent !";
    exit();
}

$token = $_COOKIE['biathlon_master_token'];
$tokenBdd = $db->getTokenmaster();

if ($token !== $tokenBdd) {
    echo "Session terminée !";
    exit();
} // if token

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt-fin'])) {
    require 'ccsv.php';
    $courses = $db->getRace();
    $csv->composeFile($_SESSION["raceName"], $_SESSION["judgeCount"], $courses);
    // réinitialiser les courses
    $db->setState(0);
    $_SESSION = array();
    header("Location: raz.php");
} // if post go

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 0:
        case 1:
            header("Location: /master/");
            exit();
        case 2:
            header("Location: master2.php");
            exit();
        // 3 : On reste sur la page
    } // sw
} // isset

//
// affichage maintenant du déroulement de la course avec gestion du bouton STOP COURSE
// Le bouton STOP provoquera la sauvegarde des données dans un fichier au format CSV, ce fichier sera accessible en téléchargement.
//
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
    <form action="master3.php" method="post">
        <button onclick="return confirm('Es-tu sûr de vouloir terminer la course ?\n(un fichier csv sera généré dans /biathlon/res)');" type="submit" name="bt-fin" id="bt-fin" value="bt-fin" >FIN</button>
        <a href="raz.php"> RAZ</a>
    </form>
    <div id="status">Course en cours !</div>

<?php $page->tablePublic(); ?>

  <script>
    async function loadData() {
      const response = await fetch('/public/refreshPublic.inc.php');
      const html = await response.text();
      document.getElementById('table-body').innerHTML = html;
    } // async
    loadData(); // Chargement initial
    setInterval(loadData, 3000); // Rafraîchissement toutes les 5 secondes
  </script>

<?php $page->footer($foot); ?>
