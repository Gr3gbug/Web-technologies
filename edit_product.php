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
            $body = new Template("skins/dtml/edit_product_report.html");

            $db->query("SELECT * FROM watch");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }
        break;

        // Select Case
        case 1:
            $body = new Template("skins/dtml/edit_product.html");

            $db->query("SELECT * FROM watch WHERE id = {$_GET['id']}");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data[0] as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
        break;

        // Update Case
        case 2:
            $body = new Template("skins/dtml/edit_product.html");
            $db->sanitize();

            //se non è stata scelta una nuova cover
            if($_FILES['img']['name']==""){
              $image = $_REQUEST['image']; //utilizzo la vecchia
            }
            else {  //altrimenti carico la nuova
                $image=$system->fileUpload("/Users/gregbug/MEGAsync/Progetti_Univaq/TdW/my_project_TdW/lux_backend/skins/images/product/", "img");
            }

            $db->query("UPDATE watch
                        SET    name =            '{$_POST['name']}',
                               product_line =    '{$_POST['product_line']}',
                               brand =           '{$_POST['brand']}',
                               year =             {$_POST['year']},
                               case_material =   '{$_POST['case_material']}',
                               strap_material =  '{$_POST['strap_material']}',
                               movement =        '{$_POST['movement']}',
                               water_resistance = {$_POST['water_resistance']},
                               functions =       '{$_POST['functions']}',
                               case_width =       {$_POST['case_width']},
                               case_height =      {$_POST['case_height']},
                               case_thickness =   {$_POST['case_thickness']},
                               date =            '{$_POST['date']}',
                               price =            {$_POST['price']},
                               amount =           {$_POST['amount']},
                               offer =           '{$_POST['offer']}',
                               percentage =       {$_POST['percentage']}, 
                               gender =          '{$_POST['gender']}',
                               image =           '{$image}'
                        WHERE  id = {$_REQUEST['id']}" );

            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
            else echo "Internal Update Case Error!";

            $_SESSION['message'] = ($db->isError()? "KO":"OK");
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_product.php');

        break;

        // Delete Case
        case 3:
            $body = new Template("skins/dtml/edit_product_del.html");
            // ci stava un echo davanti
            $db->query("DELETE watch FROM watch WHERE id = {$_GET['id']}");
            if (!$db->isError()) {
                foreach($_POST as $key => $value) {
                    $body->setContent($key,$value);
                }
            }

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_product.php');
            exit;
        break;

        default:
            header('Location: http://localhost/my_project_TdW/lux_backend/error.php');
            exit;

    }

    $main->setContent("navbar", $navbar->get());
    $main->setContent("sidebar", $sidebar->get());
    $main->setContent("body", $body->get());
    $body->setContent("message", $_SESSION['message']);
    $main->close();

    //$_SESSION['message']='';

?>
