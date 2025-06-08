<?php
session_start();
session_destroy();
header("Location: logincs.php");
exit();
?>
