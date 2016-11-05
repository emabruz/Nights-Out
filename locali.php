<?php error_reporting(0); ?>

<script src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAPrju4jKR4_YQJ22PRglSpUfeW9EHDASg&libraries=places&sensor=false"></script>
    
<script>
  function seiSicuro(){
    if(!confirm('Sei sicuro?')){ 
      window.history.back; return false;
    }
    return true;
  }
  
  function initialize_locale() {
    var input2 = document.getElementById('loc-addr');
    new google.maps.places.Autocomplete(input2);
  }
  google.maps.event.addDomListener(window, 'load', initialize_locale);
</script>

<?php  
  include 'conn_db.php';
  include 'header.php'; 
  include 'url.php';

  if(isset($_SESSION['name'])){
    if(isset($_REQUEST['action']) && $_REQUEST['action']=='Aggiungi locale'){
      $nome_locale = (isset($_POST['nome_locale'])) ? mysqli_real_escape_string($connessione,$_POST['nome_locale']) : '';
      $telefono = (isset($_POST['telefono'])) ? mysqli_real_escape_string($connessione,$_POST['telefono']) : '';
      $indirizzo = (isset($_POST['indirizzo'])) ? mysqli_real_escape_string($connessione,$_POST['indirizzo']) : '';
      $sito_web = (isset($_POST['sito_web'])) ? mysqli_real_escape_string($connessione,$_POST['sito_web']) : '';
      $partitaIva = (isset($_POST['pIva'])) ? mysqli_real_escape_string($connessione,$_POST['pIva']) : '';
      $descrizione = (isset($_POST['descrizione'])) ? mysqli_real_escape_string($connessione,$_POST['descrizione']) : '';
      
      $album = (isset($_POST['option'])) ? $_POST['option'] : '' ;
      $thumb = (isset($_POST['thumb'])) ? $_POST['thumb'] : '' ;
      date_default_timezone_set('Europe/Paris');
      $date = date("Y-m-d H:i:s");

      $error = array();
      echo '<div class="jumbotron">';

      /*if(!controllaPIVA($partitaIva)){
          $error[] = "Attenzione: Formato errato per la Partita Iva!";
        }*/

      if(!is_numeric($telefono)){
        $error[] = "Attenzione: Il telefono deve contenere solamente cifre numeriche!";
      }

      if(empty($nome_locale)||empty($telefono)||empty($indirizzo)||empty($partitaIva)||empty($descrizione)){
        $error[] = "Creazione locale fallita: Riempire i campi obbligatori!";
      }

      if(!empty($error)){
        echo '<p>Per favore correggi i seguenti errori: <br>';
        foreach ($error as $errors) {
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

        //Gestisco indirizzo da cui mi ricavo latitudine e longitudine
        $maps_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($indirizzo);
        $maps_json = file_get_contents($maps_url);
        $maps_array = json_decode($maps_json, true);
        $latitudine = $maps_array['results'][0]['geometry']['location']['lat'];
        $longitudine = $maps_array['results'][0]['geometry']['location']['lng'];

        $sql = "INSERT INTO locale (id_locale, id_utente, nomeLocale, descrizione_loc, telefono, indirizzo, sitoWeb, pIva, latitudine, longitudine)
          VALUES (NULL,'".$_SESSION['user_id']."', '".$nome_locale."', '".$descrizione."','".$telefono."','".$indirizzo."','".$sito_web."','".$partitaIva."','".$latitudine."','".$longitudine."')";

        if ($connessione->query($sql) === TRUE) {
          $sql_foto = 'SELECT * FROM locale WHERE id_utente = '.$_SESSION['user_id'].' ORDER BY id_locale DESC LIMIT 1';

          $idlocalefoto = $connessione->query($sql_foto);           
          $rowfoto = mysqli_fetch_assoc($idlocalefoto);

          for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if ($_FILES['file']['name'][$i] != "") { 
              $nome_upload = uniqid('gallery_') . strrchr($_FILES['file']['name'][$i], '.');
              $destinazione = $cartella.$nome_upload;
              $thu = $thumbs.$nome_upload;
              
              if($_FILES['file']['size'][$i] <= $maxSize){ 
                if (in_array($_FILES['file']['type'][$i], $acceptType)){
                  if (@move_uploaded_file($_FILES['file']['tmp_name'][$i], $destinazione)){
                          
                    //CARICO L'IMMAGINE
                    $maxdimminiatura=800;
                    $fileminiatura = $cartella.$nome_upload;
                    $immagine = imagecreatefromjpeg($cartella.$nome_upload);
                    $altezza=imagesy($immagine);
                    $larghezza=imagesx($immagine);

                    if($altezza > $larghezza) $larghezzaminiatura=$larghezza/$altezza*$maxdimminiatura;
                    else $larghezzaminiatura=$maxdimminiatura;
                      
                    //dimensione della larghezza l'altezza viene fatta in proporzione
                    $altezzaminiatura=floor($altezza/($larghezza/$larghezzaminiatura));
                    $miniatura=imagecreatetruecolor($larghezzaminiatura,$altezzaminiatura);
                    imagecopyresized($miniatura,$immagine,0,0,0,0,$larghezzaminiatura,$altezzaminiatura,$larghezza,$altezza);
                    imagejpeg ($miniatura, $fileminiatura); // registra la miniatura
                    imagedestroy($immagine);
                    imagedestroy($miniatura);
                    $estensione = strstr($nome_upload,'.');
                    $nome_completo = str_replace($estensione,"",$nome_upload);
                
                    $sql12 = 'INSERT INTO foto_locale (id_foto_locale, id_locale, id_utente, data_inserimento, nome_galleria, nome_immagine, estensione) VALUES (NULL, "'.$rowfoto['id_locale'].'", "'.$_SESSION['user_id'].'", "'.$date.'", "'.$album.'", "'.$nome_completo.'","'.$estensione.'")';
                    $connessione->query($sql12);
                    
                    $srcPath = "$album/";
                    $destPath = "$album/$thumb/";  
                      
                    $sql13 = 'SELECT UNIX_TIMESTAMP(data_inserimento) AS data, nome_immagine, estensione FROM foto_locale ORDER BY data DESC LIMIT 1';
                    $result13 = $connessione->query($sql13);
                      
                    $srcDir = opendir($srcPath);
                    $max =360;
                    
                    while($row13 = mysqli_fetch_assoc($result13)){
                      while($readFile = readdir($srcDir)){
                        if($readFile == $row13['nome_immagine'].$row13['estensione']){
                          if($readFile != '.' && $readFile != '..' && $readFile != $thumb){
                            if (!file_exists($readFile)){
                              if(copy($srcPath . $readFile, $destPath . $readFile)){
                                $imm = imagecreatefromjpeg($destPath . $readFile);
                                $alt=imagesy($imm);
                                $largh=imagesx($imm);
                    
                                if($al > $largh)$new_width=$largh/$alt*$max;
                                else $new_width=$max;
                                    
                                $new_height=floor($alt/($largh/$new_width));
                                $mini=imagecreatetruecolor($new_width,$new_height);
                                imagecopyresized($mini,$imm,0,0,0,0,$new_width,$new_height,$largh,$alt);
                                imagejpeg ($mini, $destPath.$readFile);
                                   
                                imagedestroy($imm);
                                imagedestroy($mini);
                              }else{
                                echo "Cannot Copy file";
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
                } else { echo 'Tipo file non valido';}
              } else { echo 'File troppo grande'; }
            }
          } // Chiuso for
        ?>
          
          <p style="color:green;">Locale inserito con successo!</p>
          <a href="locali.php">Torna indietro</a>
          </div>
          
          <?php

          } else {
          echo '<p style="color:red;">Errore durante la creazione del locale</p>';
          echo '<a href="javascript: window.history.go(-1)">Torna indietro</a>';
          echo '</div>';
          
          }
      }
  } //else{

  ?>

    <div class="jumbotron">
      <p style='text-align:center; font-size:30px;'><b>Aggiungi locale</b></p>
    </div>

  <div class="jumbotron">
    <form method="post" action="locali.php" enctype="multipart/form-data">
  
      <p style="text-align:left; font-size:1.4em;"><br>
      Nome locale: <input type="text" style="position:relative;left:41px" name="nome_locale">
      <br><br>
      Telefono: <input type="text" style="position:relative;left:76px" name="telefono">
      <br><br>
      Indirizzo: <input type="text" style="position:relative;left:80px" name="indirizzo" id="loc-addr" autocomplete="off" placeholder="">
      <br><br>
      Sito Web: <input type="text" style="position:relative;left:69px" name="sito_web">
      <br><br>
      Partita Iva: <input type="text" style="position:relative;left:67px" name="pIva">
      <br><br>
      Descrizione locale: <br><br><textarea cols="38" rows="5" name="descrizione"></textarea>
      <br><br>

      <input type="file" name="file[]" id="idfile" onchange="alert('Foto caricata correttamente');" style="width:0.1px; height:0.1px; opacity:0; overflow:hidden; position:absolute; z-index:-1;">
      <label for="idfile" style="color:blue; cursor:pointer; font-family:inherit; font-size=1.2em; font-weight:inherit;"><u>Scegli una foto..</u></label>
      <br>
            
      <input type="hidden" name="option" value="foto_locale">
      <input type="hidden" name="thumb" value="thumb_locale">
      <br>
      <input type="submit" name="action" value="Aggiungi locale">
    
    </form>
  </p>
</div> <!-- Chiuso Jumbotron inserimento -->
<?php

  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Modifica locale'){
    $nome_loc = (isset($_POST['mod_nome_locale'])) ? mysqli_real_escape_string($connessione,$_POST['mod_nome_locale']) : '';
    $descr_loc = (isset($_POST['mod_descr_locale'])) ? mysqli_real_escape_string($connessione,$_POST['mod_descr_locale']) : '';
    $telefono_mod = (isset($_POST['mod_tel'])) ? mysqli_real_escape_string($connessione,$_POST['mod_tel']) : '';
    $indirizzo_mod = (isset($_POST['mod_indirizzo'])) ? mysqli_real_escape_string($connessione,$_POST['mod_indirizzo']) : '';
    $sitoWeb_mod = (isset($_POST['mod_sito'])) ? mysqli_real_escape_string($connessione,$_POST['mod_sito']) : '';
    $pIva_mod = (isset($_POST['mod_pIva'])) ? mysqli_real_escape_string($connessione,$_POST['mod_pIva']) : '';

    $id_loc = (isset($_POST['id_locale'])) ? $_POST['id_locale'] : '';

    $album = (isset($_POST['option'])) ? $_POST['option'] : '' ;
    $thumb = (isset($_POST['thumb'])) ? $_POST['thumb'] : '' ;
    date_default_timezone_set('Europe/Paris');
    $date = date("Y-m-d H:i:s");

    $error_mod = array();
    
        /*if(!controllaPIVA($pIva_mod)){
          $error_mod[] = "Attenzione: Formato errato per la Partita Iva!";
        }*/

    if(!is_numeric($telefono_mod)){
      $error_mod[] = "Attenzione: Il telefono deve contenere solamente cifre numeriche!";
    }
    if(empty($nome_loc) || empty($indirizzo_mod) || empty($pIva_mod)){
      $error_mod[] = "Attenzione: Riempire i campi obbligatori!";
    }
    $cartella = "$album/";
    $thumbs = "$album/$thumb/";

    if($_FILES['file']['name'][0] != ""){
      $sql = 'SELECT * FROM foto_locale WHERE id_locale = "'.$id_loc.'"';
      $result = $connessione->query($sql);
          
      if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_array($result);
        $dir = "foto_locale";
        $dir1 = "foto_locale/thumb_locale";
        $handle = opendir($dir);
        unlink($dir.'/'.$row['nome_immagine'].$row['estensione']);
        closedir($handle);
        $handle1 = opendir($dir1);
        unlink($dir1.'/'.$row['nome_immagine'].$row['estensione']);
        closedir($handle1);
        
        $sql1 = 'DELETE FROM foto_locale WHERE id_locale = "'.$id_loc.'"';
        $connessione->query($sql1);
      }
    
      if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_REQUEST['action']) && $_REQUEST['action'] == 'Modifica locale'){
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
                  //CARICO L'IMMAGINE
                  $maxdimminiatura=800;
                  $fileminiatura = $cartella.$nome_upload;
                  $immagine = imagecreatefromjpeg($cartella . $nome_upload);
                  $altezza=imagesy($immagine);
                  $larghezza=imagesx($immagine);

                  if($altezza > $larghezza) $larghezzaminiatura=$larghezza/$altezza*$maxdimminiatura;
                  else $larghezzaminiatura=$maxdimminiatura;
                          
                  //dimensione della larghezza l'altezza viene fatta in proporzione
                  $altezzaminiatura=floor($altezza/($larghezza/$larghezzaminiatura));
                  $miniatura=imagecreatetruecolor($larghezzaminiatura,$altezzaminiatura);
                  imagecopyresized($miniatura,$immagine,0,0,0,0,$larghezzaminiatura,$altezzaminiatura,$larghezza,$altezza);
                  imagejpeg ($miniatura, $fileminiatura); // registra la miniatura
                  imagedestroy($immagine);
                  imagedestroy($miniatura);
                  $estensione = strstr($nome_upload,'.');
                  $nome_completo =  str_replace($estensione,"",$nome_upload);
                        
                  $sql = 'INSERT INTO foto_locale (id_foto_locale, id_locale, id_utente, data_inserimento, nome_galleria, nome_immagine, estensione) VALUES (NULL, "'.$id_loc.'", "'.$_SESSION['user_id'].'", "'.$date.'", "'.$album.'", "'.$nome_completo.'","'.$estensione.'")';
                  $connessione->query($sql);
                  
                  $srcPath = "$album/";
                  $destPath = "$album/$thumb/";  
                    
                  $sql = 'SELECT UNIX_TIMESTAMP(data_inserimento) AS data, nome_immagine, estensione FROM foto_locale ORDER BY data DESC LIMIT 1';
                  $result = $connessione->query($sql);
                    
                  $srcDir = opendir($srcPath);
                  $max =360;
                  
                  while($row = mysqli_fetch_assoc($result)){
                    while($readFile = readdir($srcDir)){
                      if($readFile == $row['nome_immagine'].$row['estensione']){
                        if($readFile != '.' && $readFile != '..' && $readFile != $thumb){
                          if (!file_exists($readFile)){
                            if(copy($srcPath . $readFile, $destPath . $readFile)){
                              $imm = imagecreatefromjpeg($destPath . $readFile);
                              $alt=imagesy($imm);
                              $largh=imagesx($imm);
                  
                              if($al > $largh)$new_width=$largh/$alt*$max;
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
              } else { echo 'Tipo file non valido';}
            } else { echo 'File troppo grande'; }
          }else if ($_POST['upload_img']) { echo 'Non ci sono file da inserire'; }
        } // Chiuso for
      } //Chiuso primo
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
      $maps_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($indirizzo_mod);
      $maps_json = file_get_contents($maps_url);
      $maps_array = json_decode($maps_json, true);
      $latitudine = $maps_array['results'][0]['geometry']['location']['lat'];
      $longitudine = $maps_array['results'][0]['geometry']['location']['lng'];

      $sql = 'UPDATE locale SET nomeLocale = "'.$nome_loc.'", descrizione_loc = "'.$descr_loc.'", telefono="'.$telefono_mod.'", indirizzo="'.$indirizzo_mod.'", sitoWeb="'.$sitoWeb_mod.'", pIva="'.$pIva_mod.'", latitudine="'.$latitudine.'", longitudine="'.$longitudine.'" WHERE id_locale = "'.$id_loc.'"';
      $result = $connessione->query($sql);

      $sql1 = 'UPDATE evento SET latitudine="'.$latitudine.'", longitudine="'.$longitudine.'" WHERE id_locale = "'.$id_loc.'"';
      $result1 = $connessione->query($sql1);
    }
  }

  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Elimina locale'){
    $id_loc = (isset($_POST['id_locale'])) ? $_POST['id_locale'] : '';
    $dir2 = "foto_evento";
    $dir3 = "foto_evento/thumb_evento";
    
    $sql4 = 'SELECT * FROM foto_evento WHERE id_locale = "'.$id_loc.'"';
    $result4 = $connessione->query($sql4);

    if(mysqli_num_rows($result4) > 0){
      while($row4 = mysqli_fetch_array($result4)){
        $handle2 = opendir($dir2);
        unlink($dir2.'/'.$row4['nome_immagine'].$row4['estensione']);
        closedir($handle2);
        $handle3 = opendir($dir2);
        unlink($dir3.'/'.$row4['nome_immagine'].$row4['estensione']);
        closedir($handle3);
      }
      $sql5 = 'DELETE FROM foto_evento WHERE id_locale = "'.$id_loc.'"';
      $connessione->query($sql5);
    }
    $sql_del_evento = 'DELETE FROM evento WHERE id_locale = "'.$id_loc.'"';
    $connessione->query($sql_del_evento);

    $dir = "foto_locale";
    $dir1 = "foto_locale/thumb_locale";

    $sql_locale = 'SELECT * FROM foto_locale WHERE id_locale = "'.$id_loc.'"';
    $result_locale = $connessione->query($sql_locale);

    if(mysqli_num_rows($result_locale) > 0){
      $row_locale = mysqli_fetch_array($result_locale);
      $handle = opendir($dir);
      unlink($dir.'/'.$row_locale['nome_immagine'].$row_locale['estensione']);
      closedir($handle);
      $handle1 = opendir($dir1);
      unlink($dir1.'/'.$row_locale['nome_immagine'].$row_locale['estensione']);
      closedir($handle1);

      $sql_del_foto_locale = 'DELETE FROM foto_locale WHERE id_locale = "'.$id_loc.'"';
      $connessione->query($sql_del_foto_locale);
    }
    $sql_del_locale = 'DELETE FROM locale WHERE id_locale = "'.$id_loc.'"';
    $connessione->query($sql_del_locale);
  }
  
  echo '<div class="jumbotron">';
  echo "<p style='text-align:center; font-size:30px;'><b>Locali inseriti</b></p></div>";

  $sql = "SELECT * FROM locale WHERE id_utente = ".$_SESSION['user_id'];
  $result = $connessione->query($sql);
  if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_array($result)){
      echo '<div class="jumbotron">';
      echo '<form method="post" action="locali.php" enctype="multipart/form-data" onsubmit="seiSicuro();">';
      $sql1 = 'SELECT * FROM foto_locale WHERE id_locale = "'.$row['id_locale'].'"';
      $result1 = $connessione->query($sql1);
      echo '<div class="row"><div class="col-lg-4">';
      
      if(mysqli_num_rows($result1) == 0){
        echo '<img class="img-thumbnail" src="img/no-photo.jpg" />';
      }else{
        $row1 = mysqli_fetch_array($result1);
        //Visualizza ed elimina immagine
        $thumb = "foto_locale/thumb_locale";
        $handle = opendir($thumb);
  
        echo '<img class="img-thumbnail" src="' . $thumb.'/'.$row1['nome_immagine'].$row1['estensione'].'" />';
      }
      //Inserisci l'immagine del locale
      echo '<br><br><input type="file" name="file[]">';
      echo '</div>';
      echo '<div class="col-lg-8">';
      echo '<p style="text-align:left; font-size:1.2em;">';
      echo '&nbsp;&nbsp;&nbsp;Nome Locale:<input type="text" style="position:relative;left:30px" name="mod_nome_locale" value="'.$row['nomeLocale'].'"/> <br>';
      echo '&nbsp;&nbsp;&nbsp;Telefono:<input type="text" style="position:relative;left:65px" name="mod_tel" value="'.$row['telefono'].'"><br>';
      echo '&nbsp;&nbsp;&nbsp;Indirizzo:<input type="text" style="position:relative;left:69px" name="mod_indirizzo" value="'.$row['indirizzo'].'"><br>';
      echo '&nbsp;&nbsp;&nbsp;Sito Web:<input type="text" style="position:relative;left:60px" name="mod_sito" value="'.$row['sitoWeb'].'"><br>';
      echo '&nbsp;&nbsp;&nbsp;Partita Iva:<input type="text" style="position:relative;left:57px" name="mod_pIva" value="'.$row['pIva'].'"><br>';
      echo '</p></div></div>';
      echo '<div class="row">';
      echo '<div class="col-lg-12">';
      echo '<p style="text-align:left; font-size:1.2em;">';
      echo '<br>';
      echo 'Descrizione locale:<br> <textarea cols="40" rows="5" name="mod_descr_locale">'.$row['descrizione_loc'].'</textarea><br>';
      echo "<span><br><input type='submit' name='action' value='Modifica locale'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      echo '<input type="hidden" name="option" value="foto_locale">';
      echo '<input type="hidden" name="thumb" value="thumb_locale">';
      echo '<input type="submit" name="action" value="Elimina locale"></span>';
      echo '<input type="hidden" name="id_locale" value = "'.$row['id_locale'].'">';
      echo '</form>';
      echo '</p>';
      echo '</div>';
      echo '</div>';
      echo '</div>'; //Chiuso Jumbotron
    }
  }else{
    echo '<div class="jumbotron">';
    echo '<p>Nessun locale inserito</p>';
    echo '</div>';
  }
  $connessione->close();
}else{
  echo '<div class="jumbotron">';
  echo '<p>Attenzione, effettua il <a href="index.php">login</a> per accedere!</p>';
  echo '</div>';
} 

include 'footer.php'; 
?>