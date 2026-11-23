<?php
session_start();
session_destroy();
header("Location: /waterutility/admin/login.php");
exit();
?>