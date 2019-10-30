<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");
    $body = new Template("skins/dtml/add_product.html");

    if (!isset($_REQUEST['page'])) {
        $_REQUEST['page'] = 0;
    }

    switch($_REQUEST['page']) {
        case 0: // emit form

            // Recupero le categorie
            $db->query("SELECT name AS product_line
                        FROM category");

            $data = $db->getResult();
            if (!$db->isError()) {
                foreach ($data as $line) {
                    foreach ($line as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }
            }

            break;

        case 1: // transaction
            // Pulizia del db
            $db->sanitize();
            // Carico l'immagine
            $image=$system->fileUpload("/Users/gregbug/MEGAsync/Progetti_Univaq/TdW/my_project_TdW/lux_backend/skins/images/product/", "img");

            $sql="INSERT INTO watch VALUES (
                                                           NULL,
                                                          '{$_POST['name']}',
                                                          '{$_POST['product_line']}',
                                                          '{$_POST['brand']}',
                                                           {$_POST['year']},
                                                          '{$_POST['case_material']}',
                                                          '{$_POST['strap_material']}',
                                                          '{$_POST['movement']}',
                                                           {$_POST['water_resistance']},
                                                          '{$_POST['functions']}',
                                                           {$_POST['case_width']},
                                                           {$_POST['case_height']},
                                                           {$_POST['case_thickness']},
                                                          '{$_POST['date']}',
                                                           {$_POST['price']},
                                                           {$_POST['amount']},
                                                           NULL,
                                                           NULL,
                                                          '{$_POST['gender']}',
                                                          '{$image}'
                                                          )";

            $db->query($sql);

            $body->setContent("message", ($db->isError()? "KO":"OK"));
            header('Location: http://localhost/my_project_TdW/lux_backend/edit_product.php');
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
