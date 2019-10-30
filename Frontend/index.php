<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");

    $body = new Template("skins/dtml/slider.html");
    $latest_arrive = new Template("skins/dtml/latest_arrive.html");
    $offer = new Template("skins/dtml/offer.html");
    $information_bar = new Template("skins/dtml/information_bar.html");

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

        /* Query per lo slider */
        $db->query("SELECT image
                    FROM slider
                    LIMIT 3");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
        }

        /* Query ultimi arrivi */
        $db->query("SELECT id AS watch_id,
                           name AS watch_name,
                           image
                    FROM watch
                    ORDER BY date DESC
                    LIMIT 3");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $latest_arrive->setContent($key,$value);
                }
            }
        }

        // Query per prodotti in offerta
        $db->query("SELECT     id AS watch_id,
                               name,
                               price,
                               image,
                               offer,
                               percentage
                        FROM watch 
                        WHERE offer = 'yes' 
                        ORDER BY rand()
                        LIMIT 8");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $offer->setContent($key,$value);
                }
            }
        }

        // Query che conta elementi nel carrello
        $db->query("SELECT COUNT(id_watch) AS cart_obj
                    FROM cart 
                    WHERE cart.id_username = '{$_SESSION['auth']['username']}' ");

        if (!$db->isError()) {
           $data2 = $db->getResult();
               foreach($data2[0] as $key => $value) {
                $main->setContent($key,$value);
               }
        }

        //print_r($data[0]);

    $main->setContent("body", $body->get());
    $main->setContent("latest_arrive", $latest_arrive->get());
    $main->setContent("offer", $offer->get());
    $main->setContent("information_bar", $information_bar->get());

    $main->close();

?>
