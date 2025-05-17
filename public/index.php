<?php
// index.php profil public
session_start();
require '../cbdd.php';
require '../cpage.php';
$titre = 'BIATHLON SUIVI DE COURSE PUBLIC';
$foot = 'Biathlon Supervision System';

$page->entete($titre);
$page->finHeadBody();
$page->header($titre);
$page->tablePublic();
?>
  <script>
    async function loadData() {
      const response = await fetch('refreshPublic.inc.php');
      const html = await response.text();
      document.getElementById('table-body').innerHTML = html;
    } // async
    loadData(); // Chargement initial
    setInterval(loadData, 2000); // Rafraîchissement toutes les 5 secondes
  </script>

<?php $page->footer($foot);?>
