<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");

    if (!isset($_REQUEST['page'])) {
        $_REQUEST['page'] = 0;
    }

    switch($_REQUEST['page']) {

        // Base case
        case 0:
            $body = new Template("skins/dtml/edit_category_rep.html");

            $db->query("SELECT * FROM category");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }
        break;

        // Select Case
        case 1:
            $body = new Template("skins/dtml/edit_category.html");

            $db->query("SELECT * FROM category WHERE id = {$_GET['id']}");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data[0] as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
        break;

        // Update Case
        case 2:
            $body = new Template("skins/dtml/edit_category.html");
            $db->sanitize();

            //se non è stata scelta una nuova cover
            if($_FILES['img']['name']==""){
                $image=$_POST['image']; //utilizzo la vecchia
            }
            else {  //altrimenti carico la nuova
                $image=$system->fileUpload("skins/images/product/","img");
            }

            //TODO qui, c'é qualcosa che non va! Quando aggiorno mi scoppia peró l'aggiornamento effettivamente lo fa
            $db->query("UPDATE category
                        SET    name = '{$_POST['name']}',
                               description = '{$_POST['description']}',
                               image = '{$image}'
                        WHERE  id = {$_REQUEST['id']}");

            $body->setContent("message", ($db->isError()? "KO":"OK"));


        break;

        // Delete Case
        case 3:
            $body = new Template("skins/dtml/edit_category_rep.html");

            $db->query("DELETE category FROM category WHERE id = {$_GET['id']}");
            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_category.php');
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
