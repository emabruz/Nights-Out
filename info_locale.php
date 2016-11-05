<?php 
  include 'header.php'; 
	include 'conn_db.php';
	include 'url.php';

	$id_locale = $_GET['locId'];

	$sql = 'SELECT * FROM locale WHERE id_locale = "'.$id_locale.'"';
	$result = $connessione->query($sql);

	if(mysqli_num_rows($result)){
		while($row = mysqli_fetch_array($result)){
	    echo '<div class="jumbotron">';
      echo '<p style="text-align:center; font-size:2em;"><b>'.nl2br($row['nomeLocale']).'</b></p>';
      $sql1 = 'SELECT * FROM foto_locale WHERE id_locale = "'.$id_locale.'"';
      $result1 = $connessione->query($sql1);

      if(mysqli_num_rows($result1) > 0){
        $row1 = mysqli_fetch_array($result1);
        $dir = "foto_locale";
        $handle = opendir($dir);
        echo '<br><center><img class="img-thumbnail-sm" src="' . $dir.'/'.$row1['nome_immagine'].$row1['estensione'].'" /></center>';
      }
      
      echo '<p style="text-align:left; font-size:1.4em"><br>';
      echo '<b>Indirizzo:&nbsp;&nbsp;</b>'.$row['indirizzo'].'<br>';
      echo '<b>Telefono:&nbsp;&nbsp;</b>'.$row['telefono'].'<br>';
    
      if($row['sitoWeb']!=NULL){
        echo '<b>Sito Web:&nbsp;&nbsp;</b>'.make_clickable($row['sitoWeb']).'<br>';
      }

      //echo '<b>Partita Iva:&nbsp;&nbsp;</b>'.$row['pIva'].'<br>';
      echo '<b>Descrizione locale:&nbsp;&nbsp;</b>'.make_clickable(nl2br($row['descrizione_loc']));
      echo '</p></div>';
		}
	}
	
	include 'footer.php'; 
?>