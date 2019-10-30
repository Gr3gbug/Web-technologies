<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");

        $main = new Template("skins/dtml/frame_public.html");

        $information_bar = new Template("skins/dtml/information_bar.html");

        // Query per le categorie
        $db->query("SELECT name AS category
                    FROM category");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Query per i brand
        $db->query("SELECT DISTINCT brand
                            FROM watch");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Query per le categorie donne
        $db->query("SELECT name AS category_w
                    FROM category");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Query per i brand donna
        $db->query("SELECT DISTINCT brand AS brand_w
                    FROM watch");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        if (!isset($_REQUEST['page'])) {
            $_REQUEST['page'] = 0;
        }

        switch($_REQUEST['page']) {

            case 0: // Login default
                $body = new Template("skins/dtml/login.html");
                break;

            case 1: // Credenziali errate
                $body = new Template("skins/dtml/err_login.html");
                break;

            case 2: // Non sei loggato
                $body = new Template("skins/dtml/no_login.html");
                break;
        }

        $main->setContent("body", $body->get());
        $main->setContent("information_bar", $information_bar->get());

        $main->close();

?>
