<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");
    $body = new Template("skins/dtml/add_service.html");

    if (!isset($_REQUEST['page'])) {
        $_REQUEST['page'] = 0;
    }

    switch($_REQUEST['page']) {
        case 0: // emit form
            break;

        case 1: // transaction
            $db->sanitize();
            $db->query("INSERT INTO services VALUES (
                                                     NULL,
                                                    '{$_POST['script']}',
                                                    '{$_POST['description']}'
                                                    )");

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            break;

        default:
            header('Location: http://localhost/my_project_TdW/lux_backend/error.php');
            exit;
    }

    $main->setContent("navbar", $navbar->get());
    $main->setContent("sidebar", $sidebar->get());
    $main->setContent("body", $body->get());
    $main->close();
?>