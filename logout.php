<?php
    session_start();
    session_destroy();

    if (!isset($_REQUEST['page'])) {
        $_REQUEST['page'] = 0;
    }

    switch($_REQUEST['page']) {

        case 0: // Logout semplice
            Header("Location: index.php?page=0");
            break;

        case 1: // Permessi non autorizzati
            Header("Location: index.php?page=1");
            break;
    }

?>