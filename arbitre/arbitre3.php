<?php
// arbitre3.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON SUIVI COURSE ARBITRE';
$foot = 'Biathlon Supervision System';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_arbitre_token'])) {
  echo "Cookie absent !";
    exit();
}

$token = $_COOKIE['biathlon_arbitre_token'];
$tokenBdd = $db->getTokenArbitre();

if ($token !== $tokenBdd) {
    echo "Session terminée !";
    exit();
} // if token

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt-fin'])) {
    $db->setState(0);
    $_SESSION['state'] = 0;
    // sauver les données au format CSV
    // réinitialiser les tables
    header("Location: raz.php");
    exit();
} // if post go

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 0:
        case 1:
            header("Location: /arbitre/");
            exit();
        case 2:
            header("Location: arbitre2.php");
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
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['judgeName']." est le juge ".$row['num']." de ".$row['runnerName']."<br>";
            } // wh
?>
        </div>
    <form action="arbitre3.php" method="post">
        <button onclick="return confirm('Es-tu sûr de vouloir terminer la course ?');" type="submit" name="bt-fin" id="bt-fin" value="bt-fin" >FIN</button>
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
