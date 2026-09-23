<?php
session_start();
require_once "../../include/runtime_config.php";
require_once "../../include/db_config.php";

// Clear Remember Me Token
if (isset($_SESSION['id'])) {
    $uid = $_SESSION['id'];
    // Clear token in DB (Using mysqli as per config)
    $sql = "UPDATE users SET remember_token=NULL WHERE id='$uid'";
    if (isset($GLOBALS["___mysqli_ston"])) {
         mysqli_query($GLOBALS["___mysqli_ston"], $sql);
    }
}

// Clear Cookie
if (isset($_COOKIE['remember_me'])) {
    set_remember_me_cookie('', time() - 3600);
}

session_destroy();
header('location:../../');//goto login page
?>
