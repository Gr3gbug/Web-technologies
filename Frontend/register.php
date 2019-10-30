<?php

        session_start();

         require("include/dbms.inc.php");
         require("include/system.inc.php");
         require("include/template2.inc.php");

         $main = new Template("skins/dtml/frame_public.html");
         $body = new Template("skins/dtml/register.html");
         $information_bar = new Template("skins/dtml/information_bar.html");

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

                    break;

                case 1: // transaction
                    $db->sanitize();
                    $db->query("INSERT INTO users VALUES (
                                                                '{$_POST['username']}',
                                                                 NULL,
                                                                '{$_POST['email']}',
                                                                '{$_POST['name']}',
                                                                '{$_POST['surname']}',
                                                                '{$_POST['sex']}',
                                                                MD5('{$_POST['password']}')
                                                                )");

                    $db->query("SELECT LAST_INSERT_ID() as id");
                    $id = $db->getResult();
                    //echo $id[0]['id'];
                    $db->query("INSERT INTO usergroups VALUES ( 4, '{$_POST['username']}')");

                    $body->setContent("message", ($db->isError()? "KO":"OK"));
                    break;

            }

         $main->setContent("body", $body->get());
         $main->setContent("information_bar", $information_bar->get());

         $main->close();

?>
