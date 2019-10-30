    <?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");

        $information_bar = new Template("skins/dtml/information_bar.html");

        if (isset($_SESSION['auth'])) {
            /* user already authenticated */
            $main = new Template("skins/dtml/frame_public_login.html");
        }
        else {
            /* User is not authenticated */
            $main = new Template("skins/dtml/frame_public.html");
        }

        // Query che conta elementi nel carrello
        $db->query("SELECT COUNT(id_watch) AS cart_obj
                    FROM cart 
                    WHERE cart.id_username = '{$_SESSION['auth']['username']}' ");

        if (!$db->isError()) {
            $data = $db->getResult();
            foreach($data[0] as $key => $value) {
                $main->setContent($key,$value);
            }
        }

        // Query per le categorie
        $db->query("SELECT name AS category
                                FROM category");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Query per i brand
        $db->query("SELECT DISTINCT brand
                        FROM watch");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Query per le categorie donne
        $db->query("SELECT name AS category_w
                                FROM category");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Query per i brand donna
        $db->query("SELECT DISTINCT brand AS brand_w
                                FROM watch");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $main->setContent($key,$value);
                }
            }
        }

        // Se non é settata la pagina di default carica la 0
        if (!isset($_REQUEST['page'])) {
            $_REQUEST['page'] = 0;
        }

        switch($_REQUEST['page']) {

            case 0:
                $body = new Template("skins/dtml/error.html");


            break;

            case 1: // Man case
                $body = new Template("skins/dtml/watch.html");

                // Select Men Category
                $db->query("SELECT * FROM watch
                            WHERE gender = 'male' OR gender = 'unisex' ");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

            break;

            case 2: // Female case
                $body = new Template("skins/dtml/watch.html");

                // Select Female Category
                $db->query("SELECT * FROM watch
                            WHERE gender = 'female' OR gender = 'unisex' ");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

            break;

            case 3: // Selezione orologi per categoria uomo

                // Controllo se ci sono orologi di questa categoria
                $db->query("SELECT COUNT(*) as tot 
                            FROM watch
                            WHERE product_line = '{$_REQUEST['category']}' AND gender = 'male' OR product_line = '{$_REQUEST['category']}' AND gender = 'unisex'");
                $temp = $db->getResult();

                if($temp[0]['tot'] == 0){
                    $body = new Template("skins/dtml/empty_watch.html");
                }
                else {
                    $body = new Template("skins/dtml/watch.html");
                }

                // Select Male for Category
                $db->query("SELECT * FROM watch
                            WHERE product_line = '{$_REQUEST['category']}' AND gender = 'male' OR product_line = '{$_REQUEST['category']}' AND gender = 'unisex'");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                break;

             case 4: // Selezione orologi per categoria donna

                 // Controllo se ci sono orologi di questa categoria
                 $db->query("SELECT COUNT(*) as tot 
                             FROM watch
                             WHERE product_line = '{$_REQUEST['category']}' AND gender = 'female' OR product_line = '{$_REQUEST['category']}' AND gender = 'unisex'");
                 $temp = $db->getResult();
                 //print_r($temp[0]['tot']);

                 if($temp[0]['tot'] == 0){
                     $body = new Template("skins/dtml/empty_watch.html");
                 }
                 else {
                     $body = new Template("skins/dtml/watch.html");
                 }

                 // Select Female for Category
                 $db->query("SELECT * FROM watch
                             WHERE product_line = '{$_REQUEST['category']}' AND gender = 'female' OR product_line = '{$_REQUEST['category']}' AND gender = 'unisex'");

                 $data = $db->getResult();
                 if (!$db->isError()) {
                     foreach ($data as $line) {
                         foreach ($line as $key => $value) {
                             $body->setContent($key,$value);
                         }
                     }
                 }

                break;

            case 5: // Selezione brand per uomo

                // Controllo se ci sono orologi di questo marchio
                $db->query("SELECT COUNT(*) as tot 
                            FROM watch
                            WHERE brand = '{$_REQUEST['brand']}' AND gender = 'male' OR brand = '{$_REQUEST['brand']}' AND gender = 'unisex'");
                $temp = $db->getResult();

                if($temp[0]['tot'] == 0){
                    $body = new Template("skins/dtml/empty_watch.html");
                }
                else {
                    $body = new Template("skins/dtml/watch.html");
                }

                // Select Female for Category
                $db->query("SELECT * FROM watch
                            WHERE brand = '{$_REQUEST['brand']}' AND gender = 'male' OR brand = '{$_REQUEST['brand']}' AND gender = 'unisex'");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                break;

            case 6: //Selezione brand donna

                // Controllo se ci sono orologi di questo marchio
                $db->query("SELECT COUNT(*) as tot 
                            FROM watch
                            WHERE brand = '{$_REQUEST['brand']}' AND gender = 'female' OR brand = '{$_REQUEST['brand']}' AND gender = 'unisex' ");
                $temp = $db->getResult();

                if($temp[0]['tot'] == 0){
                    $body = new Template("skins/dtml/empty_watch.html");
                }
                else {
                    $body = new Template("skins/dtml/watch.html");
                }

                // Select Female for Category
                $db->query("SELECT * FROM watch
                            WHERE brand = '{$_REQUEST['brand']}' AND gender = 'female' OR brand = '{$_REQUEST['brand']}' AND gender = 'unisex' ");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                break;


            default:
                header("");

                break;

        }

    $main->setContent("body", $body->get());
    $main->setContent("information_bar", $information_bar->get());

    $main->close();

    ?>
