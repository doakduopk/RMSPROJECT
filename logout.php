<?php
session_start();

$_SESSION = [];
session_destroy();

header("Location: /RMSPROJECT/login.php");
exit();
?>