<?php

if (array_key_exists("logout", $_GET)) {
    session_start();
    unset($_SESSION['twitter_otoken']);
    unset($_SESSION['twitter_otoken_secret']);
    unset($_SESSION['twitter_username']);
    session_destroy();
    header("location: LoginTumblr.php");
}
?>
