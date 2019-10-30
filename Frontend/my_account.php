<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    //require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame_public_login.html");
    $information_bar = new Template("skins/dtml/information_bar.html");

    // Query che conta elementi nel carrello
    $db->query("SELECT COUNT(id_watch) AS cart_obj
                FROM cart 
                WHERE cart.id_username = '{$_SESSION['auth']['username']}' ");

    if (!$db->isError()) {
        $data = $db->getResult();
        foreach ($data[0] as $key => $value) {
            $main->setContent($key, $value);
        }
    }

    // Query per le categorie
    $db->query("SELECT name AS category
                FROM category");

    $data = $db->getResult();
    if (!$db->isError()) {
        foreach ($data as $line) {
            foreach ($line as $key => $value) {
                $main->setContent($key, $value);
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
                $main->setContent($key, $value);
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
                $main->setContent($key, $value);
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
                $main->setContent($key, $value);
            }
        }
    }

    if (isset($_SESSION['auth'])) {

        // Se non é settata la pagina di default carica la 0
        if (!isset($_REQUEST['page'])) {
            $_REQUEST['page'] = 0;
        }

        switch ($_REQUEST['page']) {

            case 0: // Emit form
                $body = new Template("skins/dtml/my_account.html");

                // Prelevo le info relative all'utente loggato
                $db->query("SELECT * FROM users WHERE id = {$_SESSION['auth']['id']} ");
                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $body->setContent($key, $value);
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
                                   address.address AS address
                            FROM users
                            LEFT JOIN address ON users.id = address.id_user 
                            WHERE users.id = {$_SESSION['auth']['id']} ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }

                // Prelevo le info relative alle carte registrate dall'utente
                $db->query("SELECT users.id,
                                   card.id_user,
                                   card.id AS card_id,
                                   card.ccv AS ccv,
                                   card.expire AS expire,
                                   card.number AS card_number,
                                   card.owner AS owner
                            FROM users
                            LEFT JOIN card ON users.id = card.id_user
                            WHERE users.id = {$_SESSION['auth']['id']} ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }

                break;

            case 1: // Select account info
                $body = new Template("skins/dtml/my_account_edit.html");

                $db->query("SELECT username,
                                   name,
                                   surname,
                                   email,
                                   sex,
                                   password AS old_password
                            FROM users 
                            WHERE id = {$_SESSION['auth']['id']} ");
                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }

                break;

            case 2: // Update Account Info
                $body = new Template("skins/dtml/my_account_edit.html");

                $db->sanitize();
                $db->query("UPDATE users
                            SET    username = '{$_POST['username']}',
                                   email = '{$_POST['email']}',
                                   name = '{$_POST['name']}',
                                   surname = '{$_POST['surname']}',
                                   sex = '{$_POST['sex']}'
                            WHERE  id = {$_POST['id']}");

                if (!$db->isError()) {
                    foreach ($_POST as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }
                $body->setContent("message", ($db->isError() ? "KO" : "OK"));

                break;

            case 3: // Recupero informazioni indirizzo singolo
                $body = new Template("skins/dtml/my_address_edit.html");

                $db->query("SELECT users.id,
                                   address.id AS address_id,
                                   address.id_user,
                                   address.country AS country,
                                   address.region AS region,
                                   address.city AS city,
                                   address.zip_code AS zip_code,
                                   address.address AS address
                            FROM users
                            LEFT JOIN address ON users.id = address.id_user 
                            WHERE users.id = {$_SESSION['auth']['id']} AND address.id = {$_REQUEST['address_id']} ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }

                break;

            case 4: // Update indirizzo
                $body = new Template("skins/dtml/my_address_edit.html");

                $db->query("UPDATE address
                            SET    country = '{$_POST['country']}',
                                   region =  '{$_POST['region']}', 
                                   city =    '{$_POST['city']}',
                                   zip_code ='{$_POST['zip_code']}',
                                   address =  '{$_POST['address']}' 
                            WHERE  id = {$_POST['id']}");

                if (!$db->isError()) {
                    foreach ($_POST as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }
                $body->setContent("message", ($db->isError() ? "KO" : "OK"));

                break;

            case 5: // Emit add address form
                $body = new Template("skins/dtml/add_address.html");

                break;

            case 6: // Add new address
                $body = new Template("skins/dtml/add_address.html");

                $db->sanitize();

                $db->query("INSERT INTO address VALUES (
                                                     NULL,
                                                     {$_SESSION['auth']['id']},
                                                    '{$_POST['country']}',
                                                    '{$_POST['region']}',
                                                    '{$_POST['city']}',
                                                    '{$_POST['zip_code']}',
                                                    '{$_POST['address']}',
                                                    ''
                                                    )");

                $body->setContent("message", ($db->isError() ? "KO" : "OK"));

                break;

            case 7: // Recupero tutti gli indirizzi
                $body = new Template("skins/dtml/view_address.html");

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
                            WHERE users.id = {$_SESSION['auth']['id']}");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                break;

            case 8: // Cancellazione indirizzo

                $db->query("DELETE address FROM address WHERE id = {$_REQUEST['address_id']} ");
                if (!$db->isError()) {
                    foreach ($_POST as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }

                $body->setContent("message", ($db->isError() ? "KO" : "OK"));
                header('Location: http://localhost/my_project_TdW/luxury_watch/my_account.php?page=7');
                exit;

                break;

            case 9: // Add new credit card
                $body = new Template("skins/dtml/add_card.html");

                break;

            case 10: // Emit add card form
                $body = new Template("skins/dtml/add_card.html");

                $db->sanitize();
                $db->query("INSERT INTO card VALUES (
                                                     NULL,
                                                     {$_SESSION['auth']['id']},
                                                    '{$_SESSION['auth']['username']}',
                                                     {$_POST['number']},
                                                     {$_POST['ccv']},
                                                    '{$_POST['expire']}',
                                                    '{$_POST['owner']}',
                                                    ''
                                                    )");

                $body->setContent("message", ($db->isError() ? "KO" : "OK"));

                break;

            case 11: // View All Card
                $body = new Template("skins/dtml/view_card.html");

                $db->query("SELECT users.id,
                                   card.id_user,
                                   card.id AS card_id,
                                   card.ccv AS ccv,
                                   card.expire AS expire,
                                   card.number AS card_number,
                                   card.owner AS owner
                            FROM users
                            LEFT JOIN card ON users.id = card.id_user
                            WHERE users.id = {$_SESSION['auth']['id']} ");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                break;

            case 12: // Select Card Case
                $body = new Template("skins/dtml/my_card_edit.html");

                $db->sanitize();
                $db->query("SELECT users.id,
                                   card.id AS card_id,
                                   card.number AS card_number,
                                   card.owner AS owner,
                                   card.expire AS expire,
                                   card.ccv AS ccv
                            FROM users
                            LEFT JOIN card ON users.id = card.id_user 
                            WHERE users.id = {$_SESSION['auth']['id']} AND card.id = {$_REQUEST['card_id']} ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data[0] as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }

                break;

            case 13: // Update Card Case
                $body = new Template("skins/dtml/my_card_edit.html");

                $db->sanitize();
                $db->query("UPDATE card
                            SET    number = {$_POST['card_number']},
                                   ccv =    {$_POST['ccv']},
                                   expire = '{$_POST['expire']}',
                                   owner =  '{$_POST['owner']}'
                            WHERE  id = {$_POST['card_id']}");

                if (!$db->isError()) {
                    foreach ($_POST as $key => $value) {
                        $body->setContent($key, $value);
                    }
                }
                $body->setContent("message", ($db->isError() ? "KO" : "OK"));

                break;

            case 14:
                //TODO Gestire il caso di modifica della password
                $db->sanitize();

                $old = "{$_REQUEST['old_password']}";
                //print_r($old);

                $check = "{$_REQUEST['check_password']}";
                //print_r(md5($check));


                if (md5($check) == $old) {
                    // Aggiorno
                    $db->query("UPDATE users
                                SET    password = MD5('{$_POST['password']}')
                                WHERE  id = {$_SESSION['auth']['id']}") ;

                    header("Location: http://localhost/my_project_TdW/luxury_watch/my_account.php");

                } else {
                    // Segnalo che qualcosa non va
                    $body = new Template("skins/dtml/wrong_password.html");

                    $db->query("SELECT username,
                                       name,
                                       surname,
                                       email,
                                       sex,
                                       password AS old_password
                                FROM users 
                                WHERE id = {$_SESSION['auth']['id']} ");
                    if (!$db->isError()) {
                        $data = $db->getResult();
                        foreach ($data[0] as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                break;

            case 15: // List Address what you choose
                $body = new Template("skins/dtml/choose_address.html");

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
                            WHERE users.id = {$_SESSION['auth']['id']}");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                break;

            case 16: // Set default address
                $body = new Template("skins/dtml/choose_address.html");

                // Metti a secondary tutti
                $db->query("UPDATE address 
                            SET type = 'secondary' 
                            WHERE id_user = {$_SESSION['auth']['id']} ");

                // Setto a defalut quello che voglio
                $db->query("UPDATE address 
                            SET type = 'default' 
                            WHERE id_user = {$_SESSION['auth']['id']} AND id = {$_REQUEST['address_id']} ");

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
                            WHERE users.id = {$_SESSION['auth']['id']}");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                $body->setContent("message", ($db->isError() ? "KO" : "OK"));


                break;

            case 17:
                $body = new Template("skins/dtml/choose_card.html");

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
                            WHERE users.id = {$_SESSION['auth']['id']} ");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                break;

            case 18:
                $body = new Template("skins/dtml/choose_card.html");

                // Metti a secondary tutti
                $db->query("UPDATE card 
                            SET type = 'secondary' 
                            WHERE id_user = {$_SESSION['auth']['id']} ");

                // Setto a defalut quello che voglio
                $db->query("UPDATE card 
                            SET type = 'default' 
                            WHERE id_user = {$_SESSION['auth']['id']} AND id = {$_REQUEST['card_id']} ");

                // Rielenco le carte
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
                            WHERE users.id = {$_SESSION['auth']['id']} ");

                $data = $db->getResult();
                if (!$db->isError()) {
                    foreach ($data as $line) {
                        foreach ($line as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                $body->setContent("message", ($db->isError() ? "KO" : "OK"));

                break;

        }
    }
    else{
        header("Location: http://localhost/my_project_TdW/luxury_watch/login_page.php?page=2");
    }

    $main->setContent("body", $body->get());
    $main->setContent("information_bar", $information_bar->get());

    $main->close();

?>
