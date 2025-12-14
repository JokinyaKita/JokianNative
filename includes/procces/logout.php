<?php
session_start();
session_destroy();
header("Location: ../../views/auth/login/login.html?sukses=logout");
exit;
?>
