<?php
// arbitre2.php
session_start(); // Toujours ouvrir la session en début de script
require 'cbdd.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>BIATHLON SUIVI DE COURSE</title>
    <link rel="stylesheet" href="style.css?v=1.3">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        BIATHLON SUIVI DE COURSE
    </header>
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
        echo "2T : 2 tours de stade.<br>";
        echo "ST : Séquence de 5 tirs.<br>";
        echo "TP : Tours de pénalité.<br>";
      ?>

  <script>
    async function loadData() {
      const response = await fetch('refreshPublic.inc.php');
      const html = await response.text();
      document.getElementById('table-body').innerHTML = html;
    } // async
    loadData(); // Chargement initial
    setInterval(loadData, 5000); // Rafraîchissement toutes les 5 secondes
  </script>
</body>
</html>
