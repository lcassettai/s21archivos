<?php
session_start();
$_SESSION["usuario"] = null;
$_SESSION["menu"] = null;
session_destroy();
header('Location: login.php');
?>