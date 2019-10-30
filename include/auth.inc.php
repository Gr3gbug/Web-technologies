<?php

    Class Login {

        function Login() {

            global $db, $data;

            if (isset($_SESSION['auth'])) {

                /* user already authenticated */

            } else {

                    $db->query("SELECT * FROM users WHERE username = '{$_POST['username']}' AND password = MD5('{$_POST['password']}')");

                    if (!$db->isError()) {

                        if ($db->getNumRows() == 1) {
                            $data = $db->getResult();

                            $_SESSION['auth'] = $data[0];

                            $db->query("SELECT users.username AS username, 
                                               groups.name,
                                               services.script
                                          FROM users
                                     LEFT JOIN usergroups
                                            ON users.username = usergroups.username
                                     LEFT JOIN groups
                                            ON groups.id = usergroups.id_group
                                     LEFT JOIN groupservices
                                            ON groupservices.id_group = groups.id
                                     LEFT JOIN services
                                            ON services.id = groupservices.id_service
                                         WHERE users.username = '{$_POST['username']}'");

                            if (!$db->isError()) {

                                $services = $db->getResult();

                                foreach ($services as $service) {
                                    $_SESSION['auth']['service'][$service['script']] = true;
                                }
                            }

                        } else {
                            Header("Location: error.php?error=login");
                            exit;
                        }

                } else {
                    Header("Location: error.php?error=system");
                    exit;
                }

            }

            /* control here if
             *      1. user already autheticated (already in session)
             *      2. user not in session but logged in with correct login data
             *
             */

            if (!isset($_SESSION['auth']['service'][basename($_SERVER['SCRIPT_NAME'])])) {
                //error.php?error=permission
                Header("Location: logout.php?page=1");
                exit;
            }
        }
    }

    new Login();

?>
