<?php
// juge3.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON SUIVI COURSE JUGE';
$foot = 'Biathlon Supervision System';

// Sécurité : vérifier l'authentification par cookie
if (!isset($_COOKIE['biathlon_juge_token'])) {
  echo "Cookie absent !";
    exit();
}

$token = $_COOKIE['biathlon_juge_token'];
$data = $db->getTokenJuge($token);
if ($token != $data['token']) {
    echo "Session terminée !";
    exit();
} // if token

// MACHINE A ETATS ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt-temps'])) {
    // TODO prévoir et sauver le nombre de pénalité
    $t = hrtime(true);
    $judgeState = $db->get_judgeState($_SESSION['no']);  // on récupère le numéro de séquence course.
    if ($judgeState < 9) {
      $t0 = $db->get_t0();
      $db->setTime($_SESSION['no'], $judgeState+1, $t-$t0);
    } // if
} // if post go

// Si la session existe, décider où aller
if (isset($_SESSION['state']))
    if ($_SESSION['state'] != 3)
         header("Location: /juge/");
//
// affichage maintenant du déroulement de la course avec gestion du bouton TEMPS INTERMEDIAIRE
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
    <form action="juge3.php" method="post">
        <button type="submit" name="bt-temps" id="bt-temps" value="bt-temps" >TEMPS</button>
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
    setInterval(loadData, 2000); // Rafraîchissement toutes les 2 secondes
  </script>

<?php $page->footer($foot);?>
