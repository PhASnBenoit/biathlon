<?php
require 'cbdd.php';

// TODO contrôler que l'arbitre n'a pas annulé la course
// ou ne recommence pas ce script plusieurs fois
// peut être le faire dans juge2.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['runnerName'])) {
    $runnerName = $_POST['runnerName'];
    $judgeName = $_POST['judgeName'];
    // bloquer la table config
    $db->lockTable("config WRITE, race", "WRITE");  // BLOCAGE DES TABLES
        // lire la valeur de nb_juges
        $req = $db->getNbJuges();
        $row = $req->fetch(PDO::FETCH_ASSOC);
        $noJudge = $row['nb_juges'];
        $maxJudges = $row['max_juges'];
        if ($noJudge < $maxJudges) { // pas créer plus de juges que le max défini
            $noJudge++;
            // créer le cookie
            $token = bin2hex(random_bytes(16));
            setcookie('biathlon_token', $token, time() + (900 * 30), "/"); // 2h
            $_SESSION['no'] = $noJudge;
            $_SESSION['token'] = $token;
            $_SESSION['runnerName'] = $runnerName;
            $_SESSION['judgeName'] = $judgeName;
            $db->setNbJuges($noJudge);// créer l'enr dans race avec nb_juges+1
            // créer la ligne dans la table race
            $res = $db->saveJudge($runnerName, $judgeName, $noJudge, $token);
            // ouvrir session et sauver nojuge et les noms
            session_start();
            echo "paramètres sauvegardés.<br>Vous êtes le juge $noJudge<br>En attente du départ de la course...";
        } else // if pas plus de juge
            echo "Le nombre de juge est atteint ! impossible de continuer !";
    // débloquer la table
    $db->unlockTable();     //               DEBLOCAGE DES TABLES
} // if

?>
