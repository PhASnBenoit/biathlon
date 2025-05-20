<?php
// cpage.php

class CPage {
    public function entete($titre) {
        $entete = <<<EOT
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>$titre</title>
    <link rel="stylesheet" href="/biathlon/style.css">
    <script src="/biathlon/js/jquery.min.js"></script>
EOT;
    echo $entete;
    } // entete

    public function finHeadBody() {
        echo "</head>\n".
             "<body>\n";
    } // finHeadBody

    public function header($titre) {
        echo "<header>\n".
             "$titre\n".
             "</header>\n";
    } // header

    public function footer($titre) {
         echo "<footer>\n".
              "© 2025 - $titre\n".
              "</footer>\n".
              "</body>\n".
              "</html>\n";
    } // footer

    public function tablePublic() {
        $tp = <<<EOT
  <table>
    <thead>
      <tr>
        <th>Num</th>
        <th>Coureur</th>
        <th>2T1</th>
        <th>ST1</th>
        <th>TP1</th>
        <th>2T2</th>
        <th>ST2</th>
        <th>TP2</th>
        <th>2T3</th>
        <th>ST3</th>
        <th>TP3</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody id="table-body">
      <!-- Contenu chargé dynamiquement par AJAX -->
    </tbody>
    </table>
    Temps au format min:sec:cent. 2T : 2 tours de stade. ST : Séquence de 5 tirs. TP : Tours de pénalité.<br>
EOT;
        echo $tp;
    } // tablePublic
} // class
$page = new CPage();
