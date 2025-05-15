<?php
// juge3.php
session_start(); // Toujours ouvrir la session en début de script
require '../cbdd.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt-temps'])) {
  // TODO sauver le temps selon l'étape de la course
  // prévoir et sauver le nombre de pénalité
    echo "bouton temps appuyé !";
    exit();
} // if post go

// Si la session existe, décider où aller
if (isset($_SESSION['state'])) {
    switch ($_SESSION['state']) {
        case 0:
        case 1:
            header("Location: index.php");
            exit();
        case 2:
            header("Location: juge2.php");
            exit();
        // 3 : On reste sur la page
    } // sw
} // isset

//
// affichage maintenant du déroulement de la course avec gestion du bouton TEMPS INTERMEDIAIRE
//
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>BIATHLON SUIVI COURSE JUGE</title>
    <link rel="stylesheet" href="/biathlon/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        BIATHLON SUIVI COURSE JUGE
    </header>
    <?php
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

  <table>
    <thead>
      <tr>
        <th>Num</th>
        <th>Coureur</th>
        <?php
          //for ($i = 1; $i <= 9; $i++) {
          //    echo "<th>t$i</th>";
          //}
          echo "<th>2T1</th>";
          echo "<th>ST1</th>";
          echo "<th>TP1</th>";
          echo "<th>2T2</th>";
          echo "<th>ST2</th>";
          echo "<th>TP2</th>";
          echo "<th>2T3</th>";
          echo "<th>ST3</th>";
          echo "<th>TP3</th>";
          echo "<th>Total</th>";
        ?>
      </tr>
    </thead>
    <tbody id="table-body">
      <!-- Contenu chargé dynamiquement par AJAX -->
    </tbody>
    </table>
      <?php
        echo "Temps au format min:sec:cent.<br>";
        echo "2T : 2 tours de stade. ";
        echo "ST : Séquence de 5 tirs. ";
        echo "TP : Tours de pénalité. <br>";

        //if ($_POST['bt-go']) {
      ?>

  <script>
    async function loadData() {
      const response = await fetch('refreshPublic.inc.php');
      const html = await response.text();
      document.getElementById('table-body').innerHTML = html;
    } // async
    loadData(); // Chargement initial
    setInterval(loadData, 2000); // Rafraîchissement toutes les 2 secondes
  </script>

  <?php
        //} // fin go
  ?>

    <footer>
        © 2025 - Biathlon Supervision System
    </footer>
</body>
</html>
