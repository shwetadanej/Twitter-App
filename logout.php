<?php
session_start();

session_destroy();

header('Location: http://beta.technonic.in/TwitterApp/index.php'); 

?>