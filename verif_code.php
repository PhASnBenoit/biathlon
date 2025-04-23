<?php
require 'cbdd.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
        $code = $_POST['code'];
        $exists = $db->isGoodCode($code);
        if ($exists) {
            session_start();
            $_SESSION['id'] = $code;
            header("Location: arbitre2.php");
            exit();
        } else {
            echo "Code invalide.";
        }
    }
?>
