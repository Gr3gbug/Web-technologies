<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");

    $information_bar = new Template("skins/dtml/information_bar.html");

        // Verifica se autenticato oppure no
        if (isset($_SESSION['auth'])) {
            /* user already authenticated */
            $main = new Template("skins/dtml/frame_public_login.html");

        }
        else {
            /* User is not authenticated */
            $main = new Template("skins/dtml/frame_public.html");
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

         // Se non é settata la pagina di default carica la 0
         if (!isset($_REQUEST['page'])) {
                $_REQUEST['page'] = 0;
            }

            switch($_REQUEST['page']) {

                case 0:// Select Case
                    $body = new Template("skins/dtml/single_product.html");

                    // Carico il prodotto selezionato
                    $db->query("SELECT * FROM watch WHERE id = {$_REQUEST['id']}");
                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    // Inserisco il Box Description
                    $db->sanitize();
                    $db->query("SELECT description AS description_box 
                                FROM blog
                                WHERE name = 'description' ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    $db->sanitize();
                    $db->query("SELECT description AS additional_info 
                                FROM blog
                                WHERE name = 'additional_info' ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    $db->sanitize();
                    $db->query("SELECT description AS helpfull 
                                FROM blog
                                WHERE name = 'helpfull' ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    $db->sanitize();
                    $db->query("SELECT description AS short_desc 
                                FROM blog
                                WHERE name = 'short_desc' ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    break;

                case 1: // Add item to cart
                    $body = new Template("skins/dtml/single_product.html");

                    if(isset($_SESSION['auth'])) {
                        /* user already authenticated */
                        $db->sanitize();

                        // Mi porto i prodotti dietro
                        $db->query("SELECT * 
                                    FROM watch
                                    WHERE id = {$_REQUEST['id']} ");
                        if (!$db->isError()) {
                            $data = $db->getResult();
                            foreach($data[0] as $key => $value) {
                                $body->setContent($key,$value);
                            }
                        }

                        $amount = $data[0]['amount'];

                        if ($amount >= $_REQUEST['amount']){

                            // Aggiungo l'elemento nel carrello
                            $db->query("INSERT INTO cart 
                                        VALUES( '{$_SESSION['auth']['username']}', 
                                                 {$_REQUEST['id']}, 
                                                 {$_REQUEST['amount']}
                                              )");

                            // Sottraggo dal prodotto la quantitá di elementi aggiunta al carrello
                            $db->query("UPDATE watch
                                        SET watch.amount = watch.amount-{$_REQUEST['amount']}
                                        WHERE watch.id = {$_POST['id']} ");

                            $body->setContent("message", ($db->isError()? "KO":"OK"));
                            header('Location: http://localhost/my_project_TdW/luxury_watch/index.php');
                        }
                        else {
                            $body = new Template("skins/dtml/single_product_alert.html");

                            $db->query("SELECT * 
                                        FROM watch
                                        WHERE id = {$_REQUEST['id']} ");
                            if (!$db->isError()) {
                                $data = $db->getResult();
                                foreach($data[0] as $key => $value) {
                                    $body->setContent($key,$value);
                                }
                            }
                            //print_r($data);
                        }

                    }
                    else {
                        /* User is not authenticated */
                        $body = new Template("skins/dtml/login_error.html");
                    }

                    break;

            }

         $main->setContent("body", $body->get());
         $main->setContent("information_bar", $information_bar->get());

         $main->close();

?>
