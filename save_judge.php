<?php
require 'cbdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['runnerName'])) {
    $runnerName = $_POST['runnerName'];
    $judgeName = $_POST['judgeName'];
    // lire la valeur de nb_juges
    $req = $db->getNbJuges();
    $row = $req->fetch(PDO::FETCH_ASSOC);
    $noJudge = $row['nb_juges'];
    // bloquer la table config
    $db->lockTable("config", "WRITE");
         // créer l'enr dans race avec nb_juges+1
         $db->setNbJuges($noJudge+1);
    // débloquer la table
    $db->unlockTable();

    // créer la ligne dans la table race
    $res = $db->saveJudge($runnerName, $judgeName, $noJudge);

    // ouvrir session et sauver nojuge et les noms
    session_start();
    $_SESSION['no'] = $noJudge;
    $_SESSION['runnerName'] = $runnerName;
    $_SESSION['judgeName'] = $judgeName;

    echo "paramètres sauvegardés. En attente du départ de la course...";
} // if

?>
