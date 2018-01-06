<?php

// Unset all session variables and destroy session
session_start();
session_regenerate_id(true);
session_unset();
session_destroy();
header('Location: index.php');
?>
