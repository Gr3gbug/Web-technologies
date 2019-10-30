<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");
    $body = new Template("skins/dtml/add_slider.html");

        if (!isset($_REQUEST['page'])) {
            $_REQUEST['page'] = 0;
        }

        switch($_REQUEST['page']) {
            case 0: // emit form
                break;

            case 1: // transaction
                $db->sanitize();
                $image=$system->fileUpload("skins/images/slider/", "img");

                $db->query("INSERT INTO slider VALUES (
                                                             NULL,
                                                            '{$_POST['name']}',
                                                            '{$image}'
                                                            )");

                if (!$db->isError()) {
                    foreach($_POST as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                $body->setContent("message", ($db->isError()? "KO":"OK"));
                break;

            case 2: // View
                $body = new Template("skins/dtml/manage_slider.html");

                $db->query("SELECT * 
                            FROM slider");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data as $line) {
                        $body->setContent($line);
                    }
                }

                break;

            case 3:
                $body = new Template("skins/dtml/edit_slider.html");

                $db->query("SELECT * 
                            FROM slider
                            WHERE  id = {$_REQUEST['id']}");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data as $line) {
                        $body->setContent($line);
                    }
                }

                break;

            case 4:

                $db->sanitize();

                //se non è stata scelta una nuova cover
                if($_FILES['img']['name']==""){
                    $image=$_POST['image']; //utilizzo la vecchia
                }
                else {  //altrimenti carico la nuova
                    $image=$system->fileUpload("skins/images/slider/","img");
                }

                $db->query("UPDATE slider
                            SET name  = '{$_POST['name']}', 
                                image = '{$image}'
                            WHERE  id = {$_POST['id']} ");

                $_SESSION['message'] = ($db->isError()? "KO":"OK");
                header('Location: http://localhost/my_project_TdW/lux_backend/slider.php?page=2');

                break;

            case 5:

                $db->sanitize();
                $db->query("DELETE FROM slider WHERE id = {$_REQUEST['id']}");

                if (!$db->isError()) {
                    foreach($_POST as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                $body->setContent("message", ($db->isError()? "KO":"OK"));
                header('Location: http://localhost/my_project_TdW/lux_backend/slider.php?page=2');
                exit;

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
