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
            $body = new Template("skins/dtml/edit_service_report.html");

            $db->query("SELECT * FROM services");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }
        break;

        // Select Case
        case 1:
            $body = new Template("skins/dtml/edit_service.html");

            $db->query("SELECT * FROM services WHERE id = {$_GET['id']}");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data[0] as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
        break;

        // Update Case
        case 2:
            $body = new Template("skins/dtml/edit_service.html");
            $db->sanitize();
            if ($_POST['position'] == "") {
                $_POST['position'] = 0;
            }

            $db->query("UPDATE services
                        SET    id = {$_POST['id']},
                               script = '{$_POST['script']}',
                               description = '{$_POST['description']}'
                        WHERE  id = {$_POST['id']}");

            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
            $body->setContent("message", ($db->isError()? "KO":"OK"));
        break;

        // Delete Case
        case 3:
            $body = new Template("skins/dtml/edit_service_report.html");
            // ci stava un echo davanti
            $db->query("DELETE services FROM services WHERE id = {$_GET['id']}");
            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_service.php');
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
