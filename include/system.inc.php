<?php

    Class System {
        var
            $table,
            $operation;

        function System() {

            if (preg_match("+^([a-z]{1,})_([a-z]{1,})\.php$+", basename($_SERVER['SCRIPT_NAME']), $matches)) {
                $this->table = $matches[1];
                $this->operation = $matches[2];

            } else {
                $this->table = "";
                $this->operation = "";
            }
        }

        function getTable() {
            return $this->table;
        }

        function getOperation() {
            return $this->operation;
        }

        function fileUpload($where, $name){
          $target_dir = $where;
          $return=basename($_FILES[$name]["name"]);
          $target_file = $target_dir . basename($_FILES[$name]["name"]);
          $uploadOk = 1;
          $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
          // Check if image file is a actual image or fake image
          if(isset($_POST["submit"])) {
              $check = getimagesize($_FILES[$name]["tmp_name"]);
              if($check !== false) { //ok
                  $uploadOk = 1;
              } else {//il file non è un'immagine
                  $uploadOk = 0;
              }
          }
          // controllo formato file
          if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
              && $imageFileType != "gif" ) {
              $uploadOk = 0;
          }
          //controllo flag
          if ($uploadOk == 0) {
          //se è tutto ok
          } else {
              //sposto file nella nuova destinazione
              if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
              } else {
                 //errore spostamento file
              }
          }
          return $return;
        }

    }

    $system = new System();


?>
