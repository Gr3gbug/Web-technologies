<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");


    $main = new Template("skins/dtml/frame_public_login.html");
    $information_bar = new Template("skins/dtml/information_bar.html");

    // Query che conta elementi nel carrello
    $db->query("SELECT COUNT(id_watch) AS cart_obj
                FROM cart 
                WHERE cart.id_username = '{$_SESSION['auth']['username']}' ");

    // Assegno il risultato a $data
    $data = $db->getResult();

    $cart_var = $data[0]['cart_obj'];

    if ($cart_var == 0){
        $body = new Template("skins/dtml/empty_cart.html");
    }
    else {
        $body = new Template("skins/dtml/cart.html");

        // Se non é settata la pagina di default carica la 0
        if (!isset($_REQUEST['page'])) {
            $_REQUEST['page'] = 0;
        }

        switch($_REQUEST['page']) {

            case 0: // emit form

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
                            WHERE cart.id_username = '{$_SESSION['auth']['username']}'");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                        $main->setContent($key,$value);
                    }
                }

                // Prendi gli oggetti nel carrello
                $db->query("SELECT cart.id_watch AS watch_id,
                               cart.amount AS amount,
                               watch.id,
                               watch.image AS image,
                               watch.name AS watch_name,
                               watch.price AS watch_price,
                               watch.amount AS watch_amount
                        FROM cart 
                        LEFT JOIN watch ON watch.id = cart.id_watch
                        WHERE cart.id_username = '{$_SESSION['auth']['username']}'");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                // Calcolo del totale provvisorio
                $db->query("SELECT SUM(cart.amount * watch.price) AS totale
                         FROM cart
                         LEFT JOIN watch ON cart.id_watch = watch.id
                         WHERE cart.id_username = '{$_SESSION['auth']['username']}' ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                break;

            case 1: //Delete product from cart
                $body = new Template("skins/dtml/cart.html");

                // Prendi gli oggetti nel carrello
                $db->query("SELECT cart.id_watch AS watch_id,
                               cart.amount AS amount,
                               watch.id,
                               watch.image AS image,
                               watch.name AS watch_name,
                               watch.price AS watch_price,
                               watch.amount AS watch_amount
                        FROM cart 
                        LEFT JOIN watch ON watch.id = cart.id_watch
                        WHERE cart.id_username = '{$_SESSION['auth']['username']}'");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                $amount = $data[0]['amount'];

                $db->query("UPDATE watch
                            SET watch.amount = watch.amount + $amount
                            WHERE watch.id = {$_REQUEST['id']} ");

                $db->query("DELETE FROM cart 
                        WHERE id_username= '{$_SESSION['auth']['username']}' AND id_watch= {$_REQUEST['id']} ");

                if (!$db->isError()) {
                    foreach($_POST as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                $body->setContent("message", ($db->isError()? "KO":"OK"));
                header('Location: http://localhost/my_project_TdW/luxury_watch/cart.php?page=0');

                break;

            case 2: // Checkout
                $body = new Template("skins/dtml/checkout.html");

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

                // Prelevo le informazioni dell'indirizzo di spedizione
                $db->query("SELECT users.id,
                                   address.id AS address_id,
                                   address.id_user,
                                   address.country AS country,
                                   address.region AS region,
                                   address.city AS city,
                                   address.zip_code AS zip_code,
                                   address.address AS address,
                                   address.type AS type
                            FROM users
                            LEFT JOIN address ON users.id = address.id_user 
                            WHERE users.id = {$_SESSION['auth']['id']} AND address.type = 'default' ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                // Prelevo le info relative alle carte registrate dall'utente
                $db->query("SELECT users.id,
                                   card.id_user,
                                   card.id AS card_id,
                                   card.ccv AS ccv,
                                   card.expire AS expire,
                                   card.number AS card_number,
                                   card.owner AS owner,
                                   card.type AS type
                            FROM users
                            LEFT JOIN card ON users.id = card.id_user
                            WHERE users.id = {$_SESSION['auth']['id']} AND card.type = 'default' ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                // Query che conta elementi nel carrello
                $db->query("SELECT COUNT(id_watch) AS cart_obj
                    FROM cart ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $main->setContent($key,$value);
                    }
                }

                // Prendi gli oggetti nel carrello
                $db->query("SELECT cart.id_watch AS watch_id,
                                   cart.amount AS amount,
                                   watch.id,
                                   watch.image AS image,
                                   watch.name AS watch_name,
                                   watch.price AS watch_price,
                                   watch.amount AS watch_amount
                            FROM cart 
                            LEFT JOIN watch ON watch.id = cart.id_watch
                            WHERE cart.id_username = '{$_SESSION['auth']['username']}'");

                // Calcolo del totale provvisorio
                $db->query("SELECT SUM(cart.amount * watch.price) AS totale
                         FROM cart
                         LEFT JOIN watch ON cart.id_watch = watch.id
                         WHERE cart.id_username = '{$_SESSION['auth']['username']}' ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                // Prendi gli oggetti nel carrello
                $db->query("SELECT cart.id_watch AS watch_id,
                                   cart.amount AS amount,
                                   watch.id,
                                   watch.image AS image,
                                   watch.name AS watch_name,
                                   watch.price AS watch_price,
                                   watch.amount AS watch_amount
                            FROM cart 
                            LEFT JOIN watch ON watch.id = cart.id_watch
                            WHERE cart.id_username = '{$_SESSION['auth']['username']}'");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                $temp = $data[0]['watch_id'];

                break;

            case 3: // Emit Order and confirm purchase

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

                // Inserisco le info in Purchase
                $db->query("INSERT INTO purchase VALUES (NULL,
                                                     {$_SESSION['auth']['id']},
                                                     {$_REQUEST['card_id']},
                                                     {$_REQUEST['address_id']},
                                                     2,
                                                     '{$_SESSION['auth']['username']}',
                                                     CURRENT_DATE, 
                                                     {$_REQUEST['totale']},
                                                     'delivered'
                                                     )" );

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach($data[0] as $key => $value) {
                        $body->setContent($key,$value);
                    }
                }

                $db->query("SELECT LAST_INSERT_ID() AS id");
                $id = $db->getResult();
                $purchase_id = $id[0]['id'];

                // Prendi gli oggetti nel carrello
                $db->query("SELECT cart.id_watch AS watch_id,
                                   cart.amount AS amount,
                                   watch.id,
                                   watch.image AS image,
                                   watch.name AS watch_name,
                                   watch.price AS watch_price,
                                   watch.amount AS watch_amount
                            FROM cart 
                            LEFT JOIN watch ON watch.id = cart.id_watch
                            WHERE cart.id_username = '{$_SESSION['auth']['username']}'");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key,$value);
                        }
                    }
                }

                $temp = $data;

                $db->query("SELECT COUNT(*) FROM cart WHERE id_username = '{$_SESSION['auth']['username']}' ");
                $count = $db->getResult();
                $res = $count[0]['COUNT(*)'];
                //print_r($res);

                // Per ogni orologio metto nella tabella sold l'elemento venduto
                for ($i = 0, $c = 0; $i < $res; $i++, $c++){
                    $id_watch = $temp[$i]['watch_id'];
                    //print_r($id_watch);
                    $db->query("INSERT INTO sold VALUES (NULL,
                                                     $id_watch,
                                                     $purchase_id,
                                                     {$_REQUEST['amount']},
                                                     {$_REQUEST['watch_price']}
                                                     )");
                }

                // Svuota il carrello
                $db->query("DELETE FROM cart WHERE id_username = '{$_SESSION['auth']['username']}' ");

                $body->setContent("message", ($db->isError()? "KO":"OK"));
                header('Location: http://localhost/my_project_TdW/luxury_watch/my_orders.php');
                exit;

                break;

        }
    }

    $main->setContent("body", $body->get());
    $main->setContent("information_bar", $information_bar->get());

    $main->close();

?>

