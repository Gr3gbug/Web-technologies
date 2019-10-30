<?php

        session_start();

         require("include/dbms.inc.php");
         require("include/system.inc.php");
         require("include/template2.inc.php");

         $body = new Template("skins/dtml/contact.html");
         $information_bar = new Template("skins/dtml/information_bar.html");

            if (isset($_SESSION['auth'])) {
                /* user already authenticated */
                $main = new Template("skins/dtml/frame_public_login.html");
                // Informazioni utente
                $main->setContent("name", $data[0]['name']);

                /* Prelevo il nome utente */
                $db->sanitize();
                $db->query("SELECT * FROM users WHERE username = '{$_REQUEST['username']}' ");
                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $main->setContent($key, $value);
                    }
                }
                print_r($data);

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


         $main->setContent("body", $body->get());
         $main->setContent("information_bar", $information_bar->get());

         $main->close();

?>
