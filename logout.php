<?php
session_start();
session_destroy();
setcookie('login', '', time() - 3600);
setcookie('pass', '', time() - 3600);
header('Location: index.php');
exit;
?>