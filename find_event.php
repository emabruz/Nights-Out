<?php
	include 'conn_db.php';
	include 'url.php';
	
	date_default_timezone_set('Europe/Rome');

  $sql9 = 'SELECT * FROM evento';
  $result9 = $connessione->query($sql9);

  if(mysqli_num_rows($result9)>0){
    while($row9 = mysqli_fetch_array($result9)){
      $sql1 = 'SELECT id_utente FROM locale WHERE id_locale = "'.$row9['id_locale'].'"';
      $result1 = $connessione->query($sql1);

      if(mysqli_num_rows($result1) > 0){
        $row1 = mysqli_fetch_array($result1);
        $id_ut = $row1['id_utente'];         
      }
      
      $data_f = explode("/", $row9['fine']);
      $mese_f = $data_f[1];
      $giorno_f = $data_f[0];
      $anno_f = $data_f[2];
      
      if($mese_f == date("m")){
        if($giorno_f == date("d")){
          if(controlloOreMinuti($row9['ora_fine']) == date("H")){
            if(controlloOreMinuti($row9['minuti_fine']) <= date("i")){ //se l'ora è la stessa, vado a controllare i minuti
              $dir = "foto_evento";
              $dir1 = "foto_evento/thumb_evento";
              
              $sql2 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
              $result2 = $connessione->query($sql2);
              
              if(mysqli_num_rows($result2) > 0){
                $row2 = mysqli_fetch_array($result2);
                $handle = opendir($dir);
                unlink($dir.'/'.$row2['nome_immagine'].$row2['estensione']);
                closedir($handle);
                $handle1 = opendir($dir1);
                unlink($dir1.'/'.$row2['nome_immagine'].$row2['estensione']);
                closedir($handle1);

                $sql3 = 'DELETE FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
                $connessione->query($sql3);
              }
              $sql3 = 'INSERT INTO notifica VALUES(NULL,"'.$id_ut.'", NULL ,0,0,"'.$row9['titoloEvento'].'","'.$row9['fine'].'")';
              $connessione->query($sql3);
              
              $sql4 = 'DELETE FROM evento WHERE id_evento = "'.$row9['id_evento'].'"';
              $connessione->query($sql4);
            }
          }else if($row9['ora_fine'] < date("H")){
            $dir = "foto_evento";
            $dir1 = "foto_evento/thumb_evento";

            $sql2 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
            $result2 = $connessione->query($sql2);

            if(mysqli_num_rows($result2) > 0){
              $row2 = mysqli_fetch_array($result2);
              $handle = opendir($dir);
              unlink($dir.'/'.$row2['nome_immagine'].$row2['estensione']);
              closedir($handle);
              $handle1 = opendir($dir1);
              unlink($dir1.'/'.$row2['nome_immagine'].$row2['estensione']);
              closedir($handle1);

              $sql3 = 'DELETE FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
              $connessione->query($sql3);
            }
            $sql3 = 'INSERT INTO notifica VALUES(NULL,"'.$id_ut.'", NULL ,0,0,"'.$row9['titoloEvento'].'","'.$row9['fine'].'")';
            $connessione->query($sql3);
            
            $sql4 = 'DELETE FROM evento WHERE id_evento = "'.$row9['id_evento'].'"';
            $connessione->query($sql4);
          }
        }
      }else if($mese_f < date("m")){
        $dir = "foto_evento";
        $dir1 = "foto_evento/thumb_evento";

        $sql2 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
        $result2 = $connessione->query($sql2);

        if(mysqli_num_rows($result2) > 0){
          $row2 = mysqli_fetch_array($result2);
          $handle = opendir($dir);
          unlink($dir.'/'.$row2['nome_immagine'].$row2['estensione']);
          closedir($handle);
          $handle1 = opendir($dir1);
          unlink($dir1.'/'.$row2['nome_immagine'].$row2['estensione']);
          closedir($handle1);

          $sql3 = 'DELETE FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
          $connessione->query($sql3);
        }
        $sql3 = 'INSERT INTO notifica VALUES(NULL,"'.$id_ut.'", NULL ,0,0,"'.$row9['titoloEvento'].'","'.$row9['fine'].'")';
        $connessione->query($sql3);
  
        $sql4 = 'DELETE FROM evento WHERE id_evento = "'.$row9['id_evento'].'"';
        $connessione->query($sql4);
      }else if($mese_f == date("m")){
        if($giorno_f < date("d")){
          $dir = "foto_evento";
          $dir1 = "foto_evento/thumb_evento";

          $sql2 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
          $result2 = $connessione->query($sql2);

          if(mysqli_num_rows($result2) > 0){
            $row2 = mysqli_fetch_array($result2);
            $handle = opendir($dir);
            unlink($dir.'/'.$row2['nome_immagine'].$row2['estensione']);
            closedir($handle);
                          
            $handle1 = opendir($dir1);
            unlink($dir1.'/'.$row2['nome_immagine'].$row2['estensione']);
            closedir($handle1);

            $sql3 = 'DELETE FROM foto_evento WHERE id_evento = "'.$row9['id_evento'].'"';
            $connessione->query($sql3);
          }
          $sql3 = 'INSERT INTO notifica VALUES(NULL,"'.$id_ut.'", NULL ,0,0,"'.$row9['titoloEvento'].'","'.$row9['fine'].'")';
          $connessione->query($sql3);
            
          $sql4 = 'DELETE FROM evento WHERE id_evento = "'.$row9['id_evento'].'"';
          $connessione->query($sql4);
        }
      }
    }
  }
?>