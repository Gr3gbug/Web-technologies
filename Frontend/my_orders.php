<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");

    $main = new Template("skins/dtml/frame_public_login.html");
    $information_bar = new Template("skins/dtml/information_bar.html");

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

                case 0: // Mostro tutti gli ordini
                    $body = new Template("skins/dtml/order_execute.html");

                    // Elenco tutti gli ordini dell'utente
                    $db->query("SELECT purchase.id AS purchase_id,
                                       purchase.id_user AS user_id,
                                       purchase.id_card AS card_id,
                                       purchase.id_address AS address_id,
                                       purchase.id_username AS username,
                                       purchase.confirm_date AS confirm_date,
                                       purchase.tot AS total,
                                       purchase.status AS status
                                FROM purchase
                                WHERE purchase.id_username = '{$_SESSION['auth']['username']}'
                                ORDER BY purchase.id DESC");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach ($data as $item) {
                            foreach($item as $key => $value) {
                                $body->setContent($key, $value);
                            }
                        }
                    }
                break;

                case 1: // Seleziono l'ordine specifico
                    $body = new Template("skins/dtml/view_order.html");

                    // Prelevo i dati di: Utente e Indirizzo
                    $db->query("SELECT purchase.id       AS id_purchase,
                                       address.country   AS country,
                                       address.region    AS region,
                                       address.city      AS city,
                                       address.zip_code  AS zip_code,
                                       address.address   AS address,
                                       users.username    AS username,
                                       users.email       AS email,
                                       users.name        AS name,
                                       users.surname     AS surname
                                FROM purchase LEFT JOIN address ON purchase.id_address=address.id
                                              LEFT JOIN users ON purchase.id_username = users.username
                                WHERE purchase.id = {$_REQUEST['purchase_id']} ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    // Prelevo i dati della carta utilizzata e stato spedizione
                    $db->query("SELECT purchase.id     AS id_purchase,
                                       purchase.status AS status,
                                       card.number     AS card_number,
                                       card.ccv        AS card_ccv,
                                       card.expire     AS card_expire,
                                       card.owner      AS card_owner
                                FROM  purchase LEFT JOIN card ON purchase.id_card=card.id
                                WHERE purchase.id = {$_REQUEST['purchase_id']} ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach($data[0] as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }

                    // Prelevo i singoli prodotti acquistati e le quantitá relative
                    $db->query("SELECT 
                                purchase.id AS id_purchase,
                                sold.id_watch AS watch_id, 
                                sold.amount AS quantity, 
                                sold.price AS prezzo, 
                                watch.name AS product_name,
                                watch.image AS image
                            FROM 
                                purchase 
                                LEFT JOIN sold ON sold.id_purchase = purchase.id
                                LEFT JOIN watch ON watch.id = sold.id_watch
                            WHERE 
                                purchase.id = {$_REQUEST['purchase_id']}  ");

                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach ($data as $item) {
                            foreach($item as $key => $value) {
                                $body->setContent($key, $value);
                            }
                        }
                    }

                break;


        }

    $main->setContent("body", $body->get());
    $main->setContent("information_bar", $information_bar->get());

    $main->close();

?>
