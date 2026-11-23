<?php
session_start();
session_destroy();
header("Location: /waterutility/customer/login.php");
exit();
?>