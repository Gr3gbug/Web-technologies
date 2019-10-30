<?php

     require("include/dbms.inc.php");
     require("include/system.inc.php");
     require("include/template2.inc.php");

     $main = new Template("skins/dtml/frame-public.html");
     $navbar = new Template("skins/dtml/navbar-index.html");
     $sidebar = new Template("skins/dtml/sidebar-index.html");

     if (!isset($_REQUEST['page'])) {
         $_REQUEST['page'] = 0;
     }

     switch($_REQUEST['page']) {

         case 0: // Logout semplice
             $body = new Template("skins/dtml/index.html");
             break;

         case 1: // Permessi non autorizzati
             $body = new Template("skins/dtml/index_not_perm.html");
             break;
     }


     $main->setContent("navbar", $navbar->get());
     $main->setContent("sidebar", $sidebar->get());
     $main->setContent("body", $body->get());
     $main->close();

?>
