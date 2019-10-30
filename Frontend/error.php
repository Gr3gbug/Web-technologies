<?php

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");

        $information_bar = new Template("skins/dtml/information_bar.html");
        $body = new Template("skins/dtml/login_error.html");


    $main->setContent("body", $body->get());
    $main->setContent("information_bar", $information_bar->get());

    $main->close();

?>
