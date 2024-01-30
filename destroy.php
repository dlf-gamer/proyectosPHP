<?php

//
session_start();
session_unset();// Removemos session
session_destroy();// Destruimos session
header("Location: login.php");
exit();

?>