<?php

     session_start();

     require("include/dbms.inc.php");
     require("include/system.inc.php");
     require("include/template2.inc.php");
     require("include/auth.inc.php");

     $main = new Template("skins/dtml/frame-public.html");
     $navbar = new Template("skins/dtml/navbar-login.html");
     $sidebar = new Template("skins/dtml/sidebar-login.html");
     $body = new Template("skins/dtml/login.html");

     $body->setContent("name", $data[0]['name']);

        /* Controllo l'utente a quale gruppo appartiene */
        $db->query("SELECT      users.username, 
                                usergroups.id_group, 
                                groups.id, 
                                groups.name AS group_name
                            FROM 
                                users 
                                LEFT JOIN usergroups ON users.username = usergroups.username 
                                LEFT JOIN groups ON usergroups.id_group = groups.id 
                            WHERE 
                                users.username = '{$_REQUEST['username']}' OR users.username = '{$_SESSION['auth']['username']}' ");

        if (!$db->isError()) {
            $data = $db->getResult();
            foreach ($data[0] as $key => $value) {
                $body->setContent($key, $value);
            }
        }

         /* Prelevo il nome utente */
         $db->sanitize();
         $db->query("SELECT * FROM users WHERE username = '{$_SESSION['auth']['username']}'");
         if (!$db->isError()) {
             $data = $db->getResult();
             foreach ($data[0] as $key => $value) {
                 $body->setContent($key, $value);
             }
         }

         // Recupero gli ultimi prodotti
        $db->query("SELECT id AS watch_id,
                                   name AS watch_name,
                                   image
                            FROM watch
                            ORDER BY date DESC
                            LIMIT 5");

        $data = $db->getResult();
        if (!$db->isError()) {
            foreach ($data as $line) {
                foreach ($line as $key => $value) {
                    $body->setContent($key,$value);
                }
            }
        }
         
     $main->setContent("navbar", $navbar->get());
     $main->setContent("sidebar", $sidebar->get());
     $main->setContent("body", $body->get());
     $main->close();

?>