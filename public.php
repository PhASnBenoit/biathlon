<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>BIATHLON : TABLEAU D'AFFICHAGE</title>
    <link rel="stylesheet" href="style.css?v=1.2">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        BIATHLON SUIVI DE COURSE
    </header>
  <table>
    <thead>
      <tr>
        <th>Numéro</th>
        <th>Nom du coureur</th>
        <?php
          for ($i = 0; $i <= 9; $i++) {
              echo "<th>t$i</th>";
          }
        ?>
      </tr>
    </thead>
    <tbody id="table-body">
      <!-- Contenu chargé dynamiquement par AJAX -->
    </tbody>
  </table>

  <script>
    async function loadData() {
      const response = await fetch('refreshPublic.inc.php');
      const html = await response.text();
      document.getElementById('table-body').innerHTML = html;
    }
    loadData(); // Chargement initial
    setInterval(loadData, 15000); // Rafraîchissement toutes les 15 secondes
  </script>
</body>
</html>
