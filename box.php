<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");
    $body = new Template("skins/dtml/add_text.html");

        if (!isset($_REQUEST['page'])) {
            $_REQUEST['page'] = 2;
        }

        switch($_REQUEST['page']) {
            case 0: // emit form
                break;

            case 1: // add transaction
                $db->sanitize();

                $db->query("INSERT INTO blog VALUES (
                                                             NULL,
                                                            '{$_POST['name']}',
                                                            '{$_POST['description']}'
                                                            )");

                $body->setContent("message", ($db->isError()? "KO":"OK"));
                break;

            case 2: // Select and report
                $body = new Template("skins/dtml/edit_text_rep.html");

                $db->query("SELECT *
                            FROM blog");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                break;

            case 3: // Select Single text box
                $body = new Template("skins/dtml/edit_text.html");

                $db->query("SELECT * FROM blog WHERE id = {$_GET['id']}");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                break;

            case 4: // Update
                $body = new Template("skins/dtml/edit_text.html");

                $db->sanitize();
                $db->query("UPDATE blog
                            SET    name = '{$_POST['name']}',
                                   description = '{$_POST['description']}'
                            WHERE  id = {$_REQUEST['id']}");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                header('Location: http://localhost/my_project_TdW/lux_backend/box.php?page=2.php');
                exit;

                break;

            case 5: // Delete case
                $db->sanitize();

                $db->query("DELETE blog
                            FROM blog
                            WHERE id = {$_REQUEST['id']} ");

                header('Location: http://localhost/my_project_TdW/lux_backend/box.php?page=2.php');

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
