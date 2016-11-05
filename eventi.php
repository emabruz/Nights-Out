<?php error_reporting(0); ?>

<script>
  function seiSicuro(){
    if(!confirm('Sei sicuro?')){ 
      window.history.back; return false;
    }
    return true;
  }
</script>

<?php  
  include 'conn_db.php';
  include 'header.php'; 
  include 'url.php';

  $sql = 'SELECT * FROM utente WHERE id_utente = "'.$_SESSION['user_id'].'"';
  $result = $connessione->query($sql);
  $row = mysqli_fetch_array($result);
  
  if($row['password'] == ''){
    echo "<script>
      window.location.href = 'complete_account.php';
    </script>";
  }

  //Controllo se esistono già notifiche dell'utente
  $sql3 = 'SELECT * FROM notifica WHERE id_utente = "'.$_SESSION['user_id'].'" AND inviata = 0 AND letta = 0';
  $result3 = $connessione->query($sql3);

  if(mysqli_num_rows($result3) > 0){
     ?>
     <script type="text/javascript">
        repeatAjax();
     </script>
     <?php
  }else{
    
  }

  if(isset($_SESSION['name'])){
 
    if(isset($_REQUEST['action']) && $_REQUEST['action']=='Aggiungi evento'){

      $titoloEvento = $_POST['nome_evento'];
      $inizio = $_POST['inizio'];
      $fine = $_POST['fine'];
      $ora_inizio = $_POST['ora_inizio'];
      $ora_fine = $_POST['ora_fine'];
      $minuti_inizio = $_POST['minuti_inizio'];
      $minuti_fine = $_POST['minuti_fine'];
      $descrizione_evento = $_POST['descrizione_evento'];
      $album = (isset($_POST['option'])) ? $_POST['option'] : '' ;
      $thumb = (isset($_POST['thumb'])) ? $_POST['thumb'] : '' ;
      date_default_timezone_set('Europe/Paris');
      $date = date("Y-m-d H:i:s");
      $data_i = explode("/", $inizio);
      $mese_i = $data_i[1];
      $giorno_i = $data_i[0];
      $anno_i = $data_i[2];
      $data_f = explode("/", $fine);
      $mese_f = $data_f[1];
      $giorno_f = $data_f[0];
      $anno_f = $data_f[2];
      $error = array();
      
      echo '<div class="jumbotron">';
      $id_locale = (isset($_POST['locale']) && is_array($_POST['locale'])) ? $_POST['locale'] : array();
              
      if(empty($_POST['nome_evento'])||empty($_POST['inizio'])||empty($_POST['fine'])||(empty($_POST['ora_inizio'])&&$_POST['ora_inizio']!=0&&$_POST['ora_inizio']!=00)||(empty($_POST['ora_fine'])&&$_POST['ora_fine']!=0&&$_POST['ora_fine']!=00)||(empty($_POST['minuti_inizio'])&&$_POST['minuti_inizio']!=0&&$_POST['minuti_inizio']!=00)||(empty($_POST['minuti_fine'])&&$_POST['minuti_fine']!=0&&$_POST['minuti_fine']!=00)||empty($_POST['descrizione_evento'])){
        $error[] = "Creazione evento fallita: bisogna riempire tutti i campi";
      }else{
        if($ora_inizio < 0 ||  $ora_inizio > 23){
          $error[] = "Attenzione, l'ora deve essere compresa tra 00 e 23!";
        } 
        if($ora_fine < 0 ||  $ora_fine > 23){
          $error[] = "Attenzione, l'ora deve essere compresa tra 00 e 23!";
        } 
        if($minuti_inizio < 0 ||  $minuti_inizio > 59){
          $error[] = "Attenzione, i minuti devono essere compresi tra 00 e 59!";
        } 
        if($minuti_inizio < 0 ||  $minuti_inizio > 59){
          $error[] = "Attenzione, i minuti devono essere compresi tra 00 e 59!";
        } 
        if($mese_i > $mese_f){
          $error[] = "Attenzione, la data finale non può precedere la data iniziale!";
        }else if($mese_i == $mese_f){
          if($giorno_i > $giorno_f){
            $error[] = "Attenzione, la data finale non può precedere la data iniziale!";
          }else if($giorno_i <= $giorno_f){
            if($ora_inizio > $ora_fine){
              $error[] = "Attenzione, la data finale non può precedere la data iniziale!";
            }
          }
        }
        if($mese_i < date("m")){
          $error[] = "Attenzione, il mese non può essere minore del mese corrente!";
        }
        if($mese_i >= date("m")){
          if($giorno_i < date("d")){
            $error[] = "Attenzione, il giorno non può essere minore del giorno corrente!";
          }
        }
      }
    
      if(!empty($error)){
        echo '<p>Per favore correggi i seguenti errori: <br>';
        foreach ($error as $errors){
            echo '<span style="color:red;">'.$errors.'</span><br>';
        }
        echo '</p>';
        echo '<a href="javascript: window.history.go(-1)"> Torna indietro</a>';
        echo '</div>';
      }else{
        $cartella = "$album/";
        $thumbs = "$album/$thumb/";
        $maxSize = 12000000;
        $acceptType = array(
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'JPG' => 'image/JPG',
        'jfif' => 'image/jfif'
        );
                
        $sql = "SELECT indirizzo FROM locale WHERE id_locale = '".mysqli_real_escape_string($connessione, join(',',$id_locale))."'";
        $result = $connessione->query($sql);
        
        if(mysqli_num_rows($result) == 1){
          $row = mysqli_fetch_array($result);
          extract($row);
          $luogo = $row['indirizzo'];
        }

        //Gestisco indirizzo da cui mi ricavo latitudine e longitudine
        $maps_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($luogo);
        $maps_json = file_get_contents($maps_url);
        $maps_array = json_decode($maps_json, true);
        $latitudine = $maps_array['results'][0]['geometry']['location']['lat'];
        $longitudine = $maps_array['results'][0]['geometry']['location']['lng'];

        $sql = "INSERT INTO evento (id_evento, id_locale, titoloEvento, inizio, fine, ora_inizio, ora_fine, minuti_inizio, minuti_fine, descrizione_ev, latitudine, longitudine)
        VALUES (NULL,'".mysqli_real_escape_string($connessione, join(',',$id_locale))."', '".$titoloEvento."', '".$inizio."','".$fine."','".$ora_inizio."','".$ora_fine."','".$minuti_inizio."','".$minuti_fine."','".$descrizione_evento."','".$latitudine."','".$longitudine."')";

        if ($connessione->query($sql) === TRUE){

          $sql_foto = 'SELECT id_evento FROM evento e, locale l WHERE l.id_utente = '.$_SESSION['user_id'].' AND l.id_locale = e.id_locale ORDER BY id_evento DESC LIMIT 1';
          $ideventofoto = $connessione->query($sql_foto);           
          $rowfoto = mysqli_fetch_assoc($ideventofoto);

          for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            
            if ($_FILES['file']['name'][$i] != "") { 
              $nome_upload = uniqid('gallery_').strrchr($_FILES['file']['name'][$i], '.');
              $destinazione = $cartella.$nome_upload;
              $thu = $thumbs.$nome_upload;
                
              if ($_FILES['file']['size'][$i] <= $maxSize){ 
                if (in_array($_FILES['file']['type'][$i], $acceptType)){
                  if (@move_uploaded_file($_FILES['file']['tmp_name'][$i], $destinazione)){
                    $maxdimminiatura=800;
                    $fileminiatura = $cartella.$nome_upload;
                    $immagine = imagecreatefromjpeg($cartella.$nome_upload);
                    $altezza=imagesy($immagine);
                    $larghezza=imagesx($immagine);

                    if($altezza > $larghezza) $larghezzaminiatura=$larghezza/$altezza*$maxdimminiatura;
                    else $larghezzaminiatura=$maxdimminiatura;
                          
                    $altezzaminiatura=floor($altezza/($larghezza/$larghezzaminiatura));
                    $miniatura=imagecreatetruecolor($larghezzaminiatura,$altezzaminiatura);
                    imagecopyresized($miniatura,$immagine,0,0,0,0,$larghezzaminiatura,$altezzaminiatura,$larghezza,$altezza);
                    imagejpeg ($miniatura, $fileminiatura); // registra la miniatura
                    imagedestroy($immagine);
                    imagedestroy($miniatura);
                    $estensione = strstr($nome_upload,'.');
                    $nome_completo = str_replace($estensione,"",$nome_upload);
                        
                    $sql11 = 'INSERT INTO foto_evento (id_foto_evento, id_locale, id_evento, id_utente, data_inserimento, nome_galleria, nome_immagine, estensione) VALUES (NULL,"'.mysqli_real_escape_string($connessione, join(',',$id_locale)).'","'.$rowfoto['id_evento'].'", "'.$_SESSION['user_id'].'", "'.$date.'", "'.$album.'", "'.$nome_completo.'","'.$estensione.'")';
                    $connessione->query($sql11);
                      
                    $srcPath = "$album/";
                    $destPath = "$album/$thumb/";  
                        
                    $sql12 = 'SELECT UNIX_TIMESTAMP(data_inserimento) AS data, nome_immagine, estensione FROM foto_evento ORDER BY data DESC LIMIT 1';
                    $result12 = $connessione->query($sql12);
                        
                    $srcDir = opendir($srcPath);
                    $max =360;
                    
                    while($row12 = mysqli_fetch_assoc($result12)){
                      while($readFile = readdir($srcDir)){
                        if($readFile == $row12['nome_immagine'].$row12['estensione']){
                          if($readFile != '.' && $readFile != '..' && $readFile != $thumb){
                            if (!file_exists($readFile)){
                              if(copy($srcPath . $readFile, $destPath . $readFile)){
                                $imm = imagecreatefromjpeg($destPath . $readFile);
                                $alt=imagesy($imm);
                                $largh=imagesx($imm);
                      
                                if($al > $largh) $new_width=$largh/$alt*$max;
                                else $new_width=$max;
                                      
                                $new_height=floor($alt/($largh/$new_width));
                                $mini=imagecreatetruecolor($new_width,$new_height);
                                imagecopyresized($mini,$imm,0,0,0,0,$new_width,$new_height,$largh,$alt);
                                imagejpeg ($mini, $destPath.$readFile);
                                imagedestroy($imm);
                                imagedestroy($mini);
                              }else{
                                echo "Cannot copy file";
                              }
                            } 
                          }
                        } //chiuso if uguaglianza
                      }
                      closedir($srcDir); // good idea to always close your handles
                    }
                  }else{
                    echo "Errore nell'upload";
                  }
                }else{ echo 'Tipo file non valido';}
              }else{ echo 'File troppo grande'; }
            }//else if($_POST['upload_img']) { echo 'Non ci sono file da inserire'; }
          } // Chiuso for
?>
          <p style="color:green;">Evento inserito con successo!</p>
          <a href="eventi.php">Torna indietro</a>
          </div> 
<?php     
        }else{ ?>
          <p style="color:red;">Errore durante la creazione dell'evento</p>
          <a href="javascript: window.history.go(-1)"> Torna indietro</a>
          </div>
<?php
        }//$connessione->close();
      }//chiuso if(empty(error))
    } //Chiuso if isset
?>
    <div class="jumbotron">
    <p style='text-align:center; font-size:30px;'><b>Aggiungi evento</b></p>
    </div>
<?php
    echo '<div class="jumbotron">';
    $sql = "SELECT id_locale, nomeLocale FROM locale WHERE id_utente = '".$_SESSION['user_id']."'";
    $result = $connessione->query($sql);

    if(mysqli_num_rows($result) == 0){
      echo "Devi prima aggiungere un locale.";
    }else{
?>
      <form method="post" action="eventi.php" enctype="multipart/form-data">
      <p style="text-align:left; font-size:1.4em;">
      Locale: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
      echo '<select name="locale[]" id="locale">';
      while($row = mysqli_fetch_array($result)){
        extract($row);
        echo '<option value="'.$row['id_locale'].'">'.$row['nomeLocale'].'</option>';
      }
      echo '</select>';
?>
      <br><br>
      Nome evento: <input type="text" size="30" style="position:relative;left:12px" name="nome_evento">
      <br><br>
      Data inizio: <input type="text" size="9" id="datepicker" style="position:relative;left:40px" name="inizio">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <br><br>Data fine: <input type="text" size="9" id="datepickerf" style="position:relative;left:51px" name="fine">

      <script>
      $("#datepicker" ).datepicker();
      $("#datepickerf").datepicker();
      $.datepicker.setDefaults(
        $.extend(
          {'dateFormat':'dd/mm/yy'},
          $.datepicker.regional['it']
        )
      );
      $.datepickerf.setDefaults(
        $.extend(
          {'dateFormat':'dd/mm/yy'},
          $.datepicker.regional['it']
        )
      );
      </script>
      
      <br><br>
      Orario inizio:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="number" name="ora_inizio" min="0" max="23" size="5"> : <input type="number" name="minuti_inizio" min="0" max="59">
      <br><br>
      Orario fine:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="number" name="ora_fine" min="0" max="23" size="5"> : <input type="number" name="minuti_fine" min="0" max="59">
      <br><br>
      Descrizione: <br><br><textarea cols="38" rows="5" name="descrizione_evento"></textarea>
      <br><br>
      
      <input type="file" name="file[]" id="idfile" onchange="alert('Foto caricata correttamente');" style="width:0.1px; height:0.1px; opacity:0; overflow:hidden; position:absolute; z-index:-1;">
      <label for="idfile" style="color:blue; cursor:pointer; font-family:inherit; font-size=1.2em; font-weight:inherit;"><u>Scegli una foto..</u></label>
      <br>

      <input type="hidden" name="option" value="foto_evento">
      <input type="hidden" name="thumb" value="thumb_evento">
          
      <br>
        <input type="submit" name="action" value="Aggiungi evento">
      </p>
        </form>
<?php
    }//chiuso else
    echo '</div>'; //Chiuso il Jumbotron dell'inserimento
  
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Modifica evento'){   
      $titolo_ev = (isset($_POST['modifica_titolo_evento'])) ? mysqli_real_escape_string($connessione,$_POST['modifica_titolo_evento']) : '';
      $descr_ev = (isset($_POST['modifica_descrizione_evento'])) ? mysqli_real_escape_string($connessione,$_POST['modifica_descrizione_evento']) : '';
      $id_ev = (isset($_POST['id_evento'])) ? $_POST['id_evento'] : '';

      $album = (isset($_POST['option'])) ? $_POST['option'] : '' ;
      $thumb = (isset($_POST['thumb'])) ? $_POST['thumb'] : '' ;
      date_default_timezone_set('Europe/Paris');
      $date = date("Y-m-d H:i:s");

      $error_mod = array();

      $inizio_mod = $_POST['inizio_mod'];
      $fine_mod = $_POST['fine_mod'];
      $ora_inizio_mod = $_POST['ora_inizio_mod'];
      $ora_fine_mod = $_POST['ora_fine_mod'];
      $minuti_inizio_mod = $_POST['minuti_inizio_mod'];
      $minuti_fine_mod = $_POST['minuti_fine_mod'];
      $data_i = explode("/", $inizio_mod);
      $mese_i = $data_i[1];
      $giorno_i = $data_i[0];
      $anno_i = $data_i[2];
      $data_f = explode("/", $fine_mod);
      $mese_f = $data_f[1];
      $giorno_f = $data_f[0];
      $anno_f = $data_f[2];

      $id_local = (isset($_POST['locale_n']) && is_array($_POST['locale_n'])) ? $_POST['locale_n'] : array();
     
      if(empty($titolo_ev) || empty($inizio_mod) || empty($fine_mod) || (empty($ora_inizio_mod)&&$ora_inizio_mod!=0&&$ora_inizio_mod!=00) || (empty($ora_fine_mod)&&$ora_fine_mod!=0&&$ora_fine_mod!=00) || (empty($minuti_inizio_mod)&&$minuti_inizio_mod!=0&&$minuti_inizio_mod!=00) || (empty($minuti_fine_mod)&&$minuti_fine_mod!=0&&$minuti_fine_mod!=00)){
        $error_mod[] = "Devi compilare tutti i campi obbligatori!";
      }
      if($ora_inizio < 0 ||  $ora_inizio > 23){
        $error_mod[] = "Attenzione, l'ora deve essere compresa tra 00 e 23!";
      } 
      if($ora_fine < 0 ||  $ora_fine > 23){
        $error_mod[] = "Attenzione, l'ora deve essere compresa tra 00 e 23!";
      }
      if($minuti_inizio < 0 ||  $minuti_inizio > 59){
        $error_mod[] = "Attenzione, i minuti devono essere compresi tra 00 e 59!";
      }
      if($minuti_inizio < 0 ||  $minuti_inizio > 59){
        $error_mod[] = "Attenzione, i minuti devono essere compresi tra 00 e 59!";
      } 
      if($mese_i > $mese_f){
        $error_mod[] = "Attenzione, la data finale non può precedere la data iniziale!";
      }else if($mese_i == $mese_f){
        if($giorno_i > $giorno_f){
          $error_mod[] = "Attenzione, la data finale non può precedere la data iniziale!";
        }else if($giorno_i <= $giorno_f){
          if($ora_inizio > $ora_fine){
            $error_mod[] = "Attenzione, la data finale non può precedere la data iniziale!";
          }
        }
      }
      if($mese_i < date("m")){
        $error_mod[] = "Attenzione, il mese non può essere minore del mese corrente!";
      }
      if($mese_i >= date("m")){
        if($giorno_i < date("d")){
          $error_mod[] = "Attenzione, il giorno non può essere minore del giorno corrente!";
        }
      }

      $cartella = "$album/";
      $thumbs = "$album/$thumb/";

      if($_FILES['file']['name'][0] != ""){
        $sql8 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$id_ev.'"';
        $result8 = $connessione->query($sql8);

        if(mysqli_num_rows($result8) > 0){
          $row8 = mysqli_fetch_array($result8);
          $dir = "foto_evento";
          $dir1 = "foto_evento/thumb_evento";
          $handle = opendir($dir);
          unlink($dir.'/'.$row8['nome_immagine'].$row8['estensione']);
          closedir($handle);
          $handle1 = opendir($dir1);
          unlink($dir1.'/'.$row8['nome_immagine'].$row8['estensione']);
          closedir($handle1);

          $sql1 = 'DELETE FROM foto_evento WHERE id_evento = "'.$id_ev.'"';
          $connessione->query($sql1);
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_REQUEST['action']) && $_REQUEST['action'] == 'Modifica evento'){
          $maxSize = 12000000;
          $acceptType = array(
          'jpeg' => 'image/jpeg',
          'jpg' => 'image/jpeg',
          'png' => 'image/png',
          'JPG' => 'image/JPG',
          'jfif' => 'image/jfif'
          );
          
          for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if ($_FILES['file']['name'][$i] != "") { 
              $nome_upload = uniqid('gallery_') . strrchr($_FILES['file']['name'][$i], '.');
              $destinazione = $cartella.$nome_upload;
              $thu = $thumbs.$nome_upload;
            
              if ($_FILES['file']['size'][$i] <= $maxSize){ 
                if (in_array($_FILES['file']['type'][$i], $acceptType)){
                  if (@move_uploaded_file($_FILES['file']['tmp_name'][$i], $destinazione)){
                    $maxdimminiatura=800;
                    $fileminiatura = $cartella.$nome_upload;
                    $immagine = imagecreatefromjpeg($cartella . $nome_upload);
                    $altezza=imagesy($immagine);
                    $larghezza=imagesx($immagine);

                    if($altezza > $larghezza) $larghezzaminiatura=$larghezza/$altezza*$maxdimminiatura;
                    else $larghezzaminiatura=$maxdimminiatura;
                      
                    $altezzaminiatura=floor($altezza/($larghezza/$larghezzaminiatura));
                    $miniatura=imagecreatetruecolor($larghezzaminiatura,$altezzaminiatura);
                    imagecopyresized($miniatura,$immagine,0,0,0,0,$larghezzaminiatura,$altezzaminiatura,$larghezza,$altezza);
                    imagejpeg ($miniatura, $fileminiatura); // registra la miniatura
                    imagedestroy($immagine);
                    imagedestroy($miniatura);
                    $estensione = strstr($nome_upload,'.');
                    $nome_completo =  str_replace($estensione,"",$nome_upload);
                    
                    $sql = 'INSERT INTO foto_evento (id_foto_evento, id_evento, id_locale, id_utente, data_inserimento, nome_galleria, nome_immagine, estensione) VALUES (NULL, "'.$id_ev.'", "'.mysqli_real_escape_string($connessione, join(',',$id_local)).'", "'.$_SESSION['user_id'].'", "'.$date.'", "'.$album.'", "'.$nome_completo.'","'.$estensione.'")';
                    $connessione->query($sql);
                  
                    $srcPath = "$album/";
                    $destPath = "$album/$thumb/";  
                    
                    $sql2 = 'SELECT UNIX_TIMESTAMP(data_inserimento) AS data, nome_immagine, estensione FROM foto_evento ORDER BY data DESC LIMIT 1';
                    $result2 = $connessione->query($sql2);
                    
                    $srcDir = opendir($srcPath);
                    
                    $max =360;
                    
                    while($row2 = mysqli_fetch_assoc($result2)){
                      while($readFile = readdir($srcDir)){
                        if($readFile == $row2['nome_immagine'].$row2['estensione']){
                          if($readFile != '.' && $readFile != '..' && $readFile != $thumb){
                            if (!file_exists($readFile)){
                              if(copy($srcPath . $readFile, $destPath . $readFile)){
                                $imm = imagecreatefromjpeg($destPath . $readFile);
                                $alt=imagesy($imm);
                                $largh=imagesx($imm);
                  
                                if($al > $largh) $new_width=$largh/$alt*$max;
                                else $new_width=$max;
                                  
                                $new_height=floor($alt/($largh/$new_width));
                                $mini=imagecreatetruecolor($new_width,$new_height);
                                imagecopyresized($mini,$imm,0,0,0,0,$new_width,$new_height,$largh,$alt);
                                imagejpeg ($mini, $destPath.$readFile);
                                imagedestroy($imm);
                                imagedestroy($mini);
                              }else{
                                echo "Canot Copy file";
                              }
                            } 
                          }
                        } //chiuso if uguaglianza
                      }
                      closedir($srcDir); // good idea to always close your handles
                    }
                  }else{
                    echo "Errore nell'upload";
                  }
                }else{ echo 'Tipo file non valido';}
              }else{ echo 'File troppo grande'; }
            }else if($_POST['upload_img']) { echo 'Non ci sono file da inserire'; }
          } 
        }
      }
    
      if(!empty($error_mod)){
        echo '<div class="jumbotron">';
        echo '<p>Per favore correggi i seguenti errori: <br>';
        foreach ($error_mod as $error) {
          echo '<span style="color:red;">'.$error.'</span><br>';
        }
        echo '</p>';
        echo '<a href="javascript: window.history.go(-1)"> Torna indietro</a>';
        echo '</div>';
      }else{
        $sql3 = 'UPDATE evento SET id_locale = "'.mysqli_real_escape_string($connessione, join(',',$id_local)).'", titoloEvento = "'.$titolo_ev.'", inizio = "'.$inizio_mod.'", fine = "'.$fine_mod.'", ora_inizio = "'.$ora_inizio_mod.'", ora_fine = "'.$ora_fine_mod.'", minuti_inizio = "'.$minuti_inizio_mod.'", minuti_fine = "'.$minuti_fine_mod.'", descrizione_ev = "'.$descr_ev.'" WHERE id_evento = "'.$id_ev.'"';
        $result3 = $connessione->query($sql3);
      }
    } //Chiuso Modifica Evento

    if(isset($_REQUEST['action']) && $_REQUEST['action']=='Elimina evento'){ 
      $id_ev = (isset($_POST['id_evento'])) ? $_POST['id_evento'] : '';
      $dir = "foto_evento";
      $dir1 = "foto_evento/thumb_evento";
      $sql2 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$id_ev.'"';
      $result2 = $connessione->query($sql2);

      if(mysqli_num_rows($result2) > 0){
        $row2 = mysqli_fetch_array($result2);
        $handle = opendir($dir);
        unlink($dir.'/'.$row2['nome_immagine'].$row2['estensione']);
        closedir($handle);
        $handle1 = opendir($dir1);
        unlink($dir1.'/'.$row2['nome_immagine'].$row2['estensione']);
        closedir($handle1);

        $sql3 = 'DELETE FROM foto_evento WHERE id_evento = "'.$id_ev.'"';
        $connessione->query($sql3);
      }

      $sql1 = 'DELETE FROM evento WHERE id_evento = "'.$id_ev.'"';
      $connessione->query($sql1);
    } //Chiuso elimina evento

    echo '<div class="jumbotron">';
    echo "<p style='text-align:center; font-size:30px;'><b>Eventi inseriti</b></p></div>";
    $c = 0;
    $d = 2;

    $sql = "SELECT * FROM evento e, locale l WHERE l.id_utente = ".$_SESSION['user_id']." AND l.id_locale = e.id_locale";
    $result = $connessione->query($sql);

    if(mysqli_num_rows($result)>0){
      while($row = mysqli_fetch_array($result)){
        echo '<div class="jumbotron">';
        echo '<form method="post" action="eventi.php" enctype="multipart/form-data" onsubmit="seiSicuro();">';
        
        $sql2 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$row['id_evento'].'"';
        $result2 = $connessione->query($sql2);
    
        echo '<div class="row"><div class="col-lg-4">';
    
        if(mysqli_num_rows($result2) == 0){
          echo '<img class="img-thumbnail" src="img/no-photo.jpg" />';
        }else{
          $row2 = mysqli_fetch_array($result2);
          $thumb = "foto_evento/thumb_evento";
          $handle = opendir($thumb);
          echo '<img class="img-thumbnail" src="' . $thumb.'/'.$row2['nome_immagine'].$row2['estensione'].'" />';
        }
        echo '<br><br><input type="file" name="file[]">';
        echo '</div>';
        echo '<div class="col-lg-8">';
    
        $sql1 = 'SELECT id_locale, nomeLocale FROM locale WHERE id_utente = "'.$_SESSION['user_id'].'"';
        $result1 = $connessione->query($sql1);

        if(mysqli_num_rows($result1) == 0){
          echo 'Devi prima creare una locale!';
        }else{
          echo '<p style="text-align:left; font-size:1.2em;">';
          echo '&nbsp;&nbsp;&nbsp;Locale:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="locale_n[]" id="locale_n">';
          while($row1 = mysqli_fetch_array($result1)){
            if($row['nomeLocale'] == $row1['nomeLocale']){
              echo '<option selected="selected" value="'.$row1['id_locale'].'">'.$row1['nomeLocale'].'</option>';
            }else{
              echo '<option value="'.$row1['id_locale'].'">'.$row1['nomeLocale'].'</option>';
            }
          }
          echo '</select>';
        }
        echo '<br>';
        echo '&nbsp;&nbsp;&nbsp;Nome evento:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="modifica_titolo_evento" size="15" value="'.$row['titoloEvento'].'"/><br> ';
        echo '&nbsp;&nbsp;&nbsp;Data inizio:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="9" id="datepicker'.$c.'" name="inizio_mod" value="'.$row['inizio'].'">';
        echo '<br>&nbsp;&nbsp;&nbsp;Data fine:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="9" id="datepicker'.$d.'" name="fine_mod" value="'.$row['fine'].'">';
      
        echo '<script>
          $("#datepicker'.$c.'").datepicker();
          $("#datepicker'.$d.'").datepicker();
          $.datepicker'.$c.'.setDefaults(
            $.extend(
              {"dateFormat":"dd/mm/yy"},
              $.datepicker.regional["it"]
            )
          );

          $.datepicker'.$d.'.setDefaults(
            $.extend(
              {"dateFormat":"dd/mm/yy"},
              $.datepicker.regional["it"]
            )
          );
        </script>';
      
        $c++;
        $d++;
        $row['ora_inizio'] = controlloOreMinuti($row['ora_inizio']);
        $row['ora_fine'] = controlloOreMinuti($row['ora_fine']);
        $row['minuti_inizio'] = controlloOreMinuti($row['minuti_inizio']);
        $row['minuti_fine'] = controlloOreMinuti($row['minuti_fine']);

        echo '<br>&nbsp;&nbsp;&nbsp;Orario inizio:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="number" name="ora_inizio_mod" min="0" max="23" size="5" value="'.$row['ora_inizio'].'"> : <input type="number" name="minuti_inizio_mod" min="0" max="59" value="'.$row['minuti_inizio'].'">';
        echo '<br>&nbsp;&nbsp;&nbsp;Orario fine:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="number" name="ora_fine_mod" min="0" max="23" value="'.$row['ora_fine'].'"> : <input type="number" name="minuti_fine_mod" min="0" max="59" value="'.$row['minuti_fine'].'">';
        echo '</p></div></div><div class="row"><div class="col-lg-12"><p style="text-align:left; font-size:1.2em;">';
        echo "<br>Descrizione evento:<br> <textarea cols='40' rows='5' name='modifica_descrizione_evento'>".$row['descrizione_ev']."</textarea><br><br>";
        echo "<span><br><input type='submit' name='action' value='Modifica evento'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        echo '<input type="hidden" name="option" value="foto_evento">';
        echo '<input type="hidden" name="thumb" value="thumb_evento">';
        echo '<input type="submit" name="action" value="Elimina evento"></span>';
        echo '<input type="hidden" name="id_evento" value = "'.$row['id_evento'].'">';
        echo '</form></p></div></div></div>';
      }//Chiuso primo while
    }else{ 
      echo '<div class="jumbotron">';
      echo "Nessun evento inserito!";
      echo '</div>';
    }
    $connessione->close();
  }//chiuso if (_SESSION)
  else{
    echo '<div class="jumbotron">';
    echo '<p>Attenzione, effettua il <a href="index.php">login</a> per accedere!</p>';
    echo '</div>';
  }

include 'footer.php'; 

?>