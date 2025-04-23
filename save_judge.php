<?php
require 'cbdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['runnerName'])) {
    $raceName = $_POST['raceName'];
    $judgeName = $_POST['judgeName'];
    $res = $db->saveJudge($runnerName, $judgeName);
    echo "paramètres sauvegardés. En attente du départ de la course...";
} // if

?>
