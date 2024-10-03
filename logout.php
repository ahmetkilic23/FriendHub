<?php
   session_start() ;

   session_destroy() ; // deletes session file
   setcookie("PHPSESSID", "", 1, "/") ;

   header("Location: login.php") ; 
