<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");
    $body = new Template("skins/dtml/add_category.html");

    if (!isset($_REQUEST['page'])) {
        $_REQUEST['page'] = 0;
    }

    switch($_REQUEST['page']) {
        case 0: // emit form
            break;

        case 1: // transaction
            $db->sanitize();
            $image=$system->fileUpload("skins/images/product/", "img");

            $db->query("INSERT INTO category VALUES (
                                                     NULL,
                                                    '{$_POST['name']}',
                                                    '{$_POST['description']}',
                                                    '{$image}'
                                                    )");

             if (!$db->isError()) {
              foreach($_POST as $key => $value) {
               $body->setContent($key,$value);
              }
             }

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
