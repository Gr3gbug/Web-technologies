<?php

    session_start();

    require("include/dbms.inc.php");
    require("include/system.inc.php");
    require("include/template2.inc.php");
    require("include/auth.inc.php");

    $main = new Template("skins/dtml/frame-public.html");
    $navbar = new Template("skins/dtml/navbar-login.html");
    $sidebar = new Template("skins/dtml/sidebar-login.html");

    if (!isset($_REQUEST['page'])) {
        $_REQUEST['page'] = 0;
    }

    switch($_REQUEST['page']) {

        // Base case
        case 0:
            $body = new Template("skins/dtml/edit_order_report.html");

            $db->query("SELECT * FROM purchase ORDER BY confirm_date DESC");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }

        break;

        case 1: // Select case for category
          # code...
          $body = new Template("skins/dtml/edit_order_report_del.html");

          $db->query("SELECT * FROM purchase WHERE status='delivered' ORDER BY confirm_date DESC ");
          if (!$db->isError()) {
              $data = $db->getResult();
              foreach($data as $line) {
                  $body->setContent($line);
              }
          }

          break;

          case 2: // Select case for category
            # code...
            $body = new Template("skins/dtml/edit_order_report_work.html");

            $db->query("SELECT * FROM purchase WHERE status='working' ORDER BY confirm_date DESC ");
            if (!$db->isError()) {
                $data = $db->getResult();
                foreach($data as $line) {
                    $body->setContent($line);
                }
            }

            break;

            case 3: // Select case for category
              # code...
              $body = new Template("skins/dtml/edit_order_report_canc.html");

              $db->query("SELECT * FROM purchase WHERE status='canceled' ORDER BY confirm_date DESC ");
              if (!$db->isError()) {
                  $data = $db->getResult();
                  foreach($data as $line) {
                      $body->setContent($line);
                  }
              }

              break;

              case 4: // Select case for single order
                $body = new Template("skins/dtml/edit_order.html");

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
                            WHERE purchase.id = {$_GET['id']} ");

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
                              WHERE purchase.id = {$_GET['id']} ");

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
                                watch.name AS product_name
                            FROM 
                                purchase 
                                LEFT JOIN sold ON sold.id_purchase = purchase.id
                                LEFT JOIN watch ON watch.id = sold.id_watch
                            WHERE 
                                purchase.id = {$_GET['id']}  ");

                if (!$db->isError()) {
                    $data = $db->getResult();
                    foreach ($data as $item) {
                        foreach($item as $key => $value) {
                            $body->setContent($key, $value);
                        }
                    }
                }

                // Calcolo il totale della spedizione
                $db->query("SELECT 
                                SUM(sold.amount * sold.price) AS totale
                            FROM 
                                sold
                            WHERE 
                                sold.id_purchase = '{$_GET['id']}' ");
                  if (!$db->isError()) {
                      $data = $db->getResult();
                      foreach($data[0] as $key => $value) {
                          $body->setContent($key,$value);
                      }
                  }

              break;

              case 5: // Update status order
                  $body = new Template("skins/dtml/edit_order.html");

                  $db->query("UPDATE purchase SET status = '{$_REQUEST['status']}' WHERE id = {$_REQUEST['id_purchase']}");

                  if (!$db->isError()) {
                      $data = $db->getResult();
                      foreach($data[0] as $key => $value) {
                          $body->setContent($key,$value);
                      }
                  }

                  $main->setContent("message", ($db->isError()? "KO":"OK"));
                  $_SESSION['msg'] = "tutt appost' fraté";
                  header('Location: http://localhost/my_project_TdW/lux_backend/edit_order.php');

              break;

        default:
            header('Location: http://localhost/my_project_TdW/lux_backend/error.php');
            exit;
    }

    $main->setContent("navbar", $navbar->get());
    $main->setContent("sidebar", $sidebar->get());
    $main->setContent("body", $body->get());
    $main->setContent("message", $_SESSION['msg']);
    $main->close();
    $_SESSION['msg']="";

?>
