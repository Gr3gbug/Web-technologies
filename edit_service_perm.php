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


        case 0: // Base case
            $body = new Template("skins/dtml/edit_service_rep_perm.html");

            $db->query("SELECT * FROM services");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }
        break;

        case 1: // View Permission
        $body = new Template("skins/dtml/edit_service_perm.html");

          $db->query("SELECT services.id AS id,
                             services.script AS script,
                             groups.name AS group_name,
                        	 groupservices.id_group AS id_group
                      FROM   services
                        	LEFT JOIN groupservices ON groupservices.id_service = services.id
                        	LEFT JOIN groups ON groupservices.id_group = groups.id
                        WHERE
                        	services.id = {$_GET['id']} ");

            if (!$db->isError()) {
                $data = $db->getResult();
                foreach ($data as $item) {
                    foreach($item as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }
            }

        break;


        case 2: // Delete Case
            $body = new Template("skins/dtml/edit_service_perm.html");

            $db->query("DELETE 
                        FROM groupservices 
                        WHERE groupservices.id_service = {$_REQUEST['id']} AND groupservices.id_group = {$_POST['id_group']} ");

            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_service_perm.php');
            exit;

        break;

        case 3: // Select Case
            $body = new Template("skins/dtml/add_service_perm.html");


            // Query per selezione script e id
            $db->query("SELECT services.id AS id_service,
	                           services.script AS script
                        FROM services 
                        WHERE services.id = {$_GET['id']} ");

            if (!$db->isError()) {
                $data = $db->getResult();
                foreach ($data as $item) {
                    foreach($item as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }
            }

            $db->query("SELECT id AS id_group, 
                               name AS group_name 
                        FROM groups ");

            if (!$db->isError()) {
                $data = $db->getResult();
                foreach ($data as $item) {
                    foreach($item as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }
            }


        break;

        case 4: // Add Permission Group
            $body = new Template("skins/dtml/edit_service_rep_perm.html");
            $db->sanitize();

            $db->query("INSERT INTO groupservices 
                        VALUES ({$_REQUEST['id_group']} ,
                                {$_REQUEST['id_service']}) ");

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_service_perm.php');
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
