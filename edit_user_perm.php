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
            $body = new Template("skins/dtml/edit_user_perm_report.html");

            $db->query("SELECT * FROM users");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }
        break;

        // Select Case
        case 1:
            $body = new Template("skins/dtml/edit_user_perm.html");

            $db->query("SELECT
                                users.id AS userid,
                                users.username AS username,
                                groups.id AS groupid,
                                groups.name AS groupname
                        FROM
                            users
                            LEFT JOIN (usergroups, groups) ON (
                                usergroups.username = users.username
                                AND groups.id = usergroups.id_group
                            )
                        WHERE users.id = {$_GET['id']}"); // GET quando passi dalla barra

            if (!$db->isError()) {
                $data = $db->getResult();
                foreach ($data[0] as $key => $value) {
                    $body->setContent($key, $value);
                }
            }

            // Mostro tutti i gruppi
            $db->query("SELECT id AS id_group,
                               name AS group_name
                        FROM groups ");

            $data = $db->getResult();
            if (!$db->isError()) {
                foreach ($data as $line) {
                    foreach ($line as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }
            }

        break;

        // Update case
        case 2:
            $body = new Template("skins/dtml/edit_user_perm.html");
            $db->sanitize();

            //print_r($_POST);

            $db->query("UPDATE 
                            usergroups 
                        SET 
                            usergroups.id_group = {$_POST['groupid']}
                        WHERE 
                            usergroups.username = '{$_POST['username']}' ");

            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
            else echo "Internal Update Case Error!";

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            //TODO Mettere script per il messaggio
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
