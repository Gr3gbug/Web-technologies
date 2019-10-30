<?php

    // Login luxury_watch site
    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");


    $main = new Template("skins/dtml/frame_public_login.html");
    $body = new Template("skins/dtml/slider.html");
    $latest_arrive = new Template("skins/dtml/latest_arrive.html");
    $offer = new Template("skins/dtml/offer.html");
    $information_bar = new Template("skins/dtml/information_bar.html");

    $main->setContent("name", $data[0]['name']);
    header('Location: http://localhost/my_project_TdW/luxury_watch/index.php');

    $main->close();

?>
