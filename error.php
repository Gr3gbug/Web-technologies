<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $body = new Template("skins/dtml/error404.html");

    $main->setContent("body", $body->get());
    $main->close();

?>