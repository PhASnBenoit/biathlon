<?php
require '../cbdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['runnerName'])) {
    $runnerName = $_POST['runnerName'];
    $judgeName = $_POST['judgeName'];
    // bloquer la table config
    $db->lockTable("activity WRITE, config WRITE, race ", "WRITE");  // BLOCAGE DES TABLES
        // lire la valeur de nb_juges
        $req = $db->getNbJuges();
        $row = $req->fetch(PDO::FETCH_ASSOC);
        $noJudge = $row['nb_juges'];
        $maxJudges = $row['max_juges'];
        if ($noJudge < $maxJudges) { // pas créer plus de juges que le max défini
            // Suppression des cookies existants
            foreach ($_COOKIE as $name => $value) {
                setcookie($name, '', time() - 3600, '/');
            }
            $noJudge++;
            // créer le cookie
            $token = bin2hex(random_bytes(16));
            setcookie('biathlon_juge_token', $token, time() + 1800, "/"); //
            session_start();
            $_SESSION = array();
            $_SESSION['state'] = $db->getState();
            $_SESSION['no'] = $noJudge;
            $_SESSION['token'] = $token;
            $_SESSION['judgeState'] = 1;
            $_SESSION['runnerName'] = $runnerName;
            $_SESSION['judgeName'] = $judgeName;
            $_SESSION['maxJudges'] = $maxJudges;
            $db->setNbJuges($noJudge);// augmente nb_juges+1
            if ($noJudge == $maxJudges) {
                $db->setState(2); // tous les juges sont présents.
                $_SESSION['state'] = 2;
            } // if
            // créer la ligne dans la table race
            $res = $db->addJudge($runnerName, $judgeName, $noJudge, $token);
            // ouvrir session et sauver nojuge et les noms
            echo "paramètres sauvegardés.<br>Vous êtes le juge $noJudge<br>En attente du départ de la course...";
        } else {// if pas plus de juge
            echo "Le nombre de juge est atteint ! impossible de continuer !";
            $db->unlockTable();     //               DEBLOCAGE DES TABLES
            exit();
        } // else max juges
    // débloquer la table
    $db->unlockTable();     //               DEBLOCAGE DES TABLES
} // if

?>
