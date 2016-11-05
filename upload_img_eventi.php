<?php
session_start();
require 'conn_db.php';

$album = (isset($_POST['option'])) ? $_POST['option'] : '' ;
$thumb = (isset($_POST['thumb'])) ? $_POST['thumb'] : '' ;
date_default_timezone_set('Europe/Paris');
$date = date("Y-m-d H:i:s");
$id_evento = (isset($_POST['id_evento'])) ? $_POST['id_evento'] : '';
$id_locale = (isset($_POST['id_locale'])) ? $_POST['id_locale'] : '';

$cartella = "$album/";
$thumbs = "$album/$thumb/";

if($_FILES['file']['name'][0] != ""){

	$sql = 'SELECT * FROM foto_evento WHERE id_evento = "'.$id_evento.'"';
	$result = $connessione->query($sql);

	if(mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_array($result);
		$dir = "foto_evento";
	    $dir1 = "foto_evento/thumb_evento";

		$handle = opendir($dir);
	    unlink($dir.'/'.$row['nome_immagine'].$row['estensione']);
	    closedir($handle);
	              
	    $handle1 = opendir($dir1);
	    unlink($dir1.'/'.$row['nome_immagine'].$row['estensione']);
	    closedir($handle1);

	    $sql1 = 'DELETE FROM foto_evento WHERE id_evento = "'.$id_evento.'"';
		$connessione->query($sql1);
	}

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_REQUEST['upload_img_e']) && $_REQUEST['upload_img_e'] == 'Carica immagine'){
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
						  
						  	echo "<br>File " . $i . " caricato ";
						
							$estensione = strstr($nome_upload,'.');
							$nome_completo =  str_replace($estensione,"",$nome_upload);
							
							$sql = 'INSERT INTO foto_evento (id_foto_evento, id_evento, id_locale, id_utente, data_inserimento, nome_galleria, nome_immagine, estensione) VALUES (NULL, "'.$id_evento.'", "'.$id_locale.'", "'.$_SESSION['user_id'].'", "'.$date.'", "'.$album.'", "'.$nome_completo.'","'.$estensione.'")';
							$connessione->query($sql);
						
							$srcPath = "$album/";
							$destPath = "$album/$thumb/";  
							
							$sql = 'SELECT UNIX_TIMESTAMP(data_inserimento) AS data, nome_immagine, estensione FROM foto_evento ORDER BY data DESC LIMIT 1';
							$result = $connessione->query($sql);
							
							$srcDir = opendir($srcPath);
							
							$max =360;
							//$newheight = 160;
							
							while($row = mysqli_fetch_assoc($result)){
								while($readFile = readdir($srcDir)){
									if($readFile == $row['nome_immagine'].$row['estensione']){
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
		} // Chiuso for
	} //Chiuso primo
	
	echo "<script>
		window.location.href = 'eventi.php';
	</script>";	

}else{
	include 'header.php';
	echo '<div class="jumbotron">';
	echo "<p>ATTENZIONE: DEVI SELEZIONARE UN'IMMAGINE!</p>";
	echo '<p><a href="javascript: window.history.go(-1)" class="show_a">Torna indietro</a></p>';
	echo '</div>';
	include 'footer.php';
}

?>