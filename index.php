<?php

session_start();

require_once "header.php";

$page = $_GET["page"]??"index";

// if ($_SESSION){

//   require_once "$page.php";    

// }

// else {

//   require_once "login.php";  

// }

require_once "$page.php";

require_once "footer.php";

?>