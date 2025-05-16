<?php
    session_start();
    $_SESSION = array();  // détruit le tableau session
    // destruction du cookie de session
    setcookie('biathlon_arbitre_token', '', time() - 3600, '/');
    session_destroy();
    header("Location: /arbitre/");
?>
