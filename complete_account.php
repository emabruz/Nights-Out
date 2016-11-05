<?php
	include 'header.php';
	include 'conn_db.php';

	if(isset($_SESSION['user_id'])){

		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Salva e continua'){
			$password = (isset($_POST['password'])) ? mysqli_real_escape_string($connessione,$_POST['password']) : '';
			$conf_password = (isset($_POST['conf_password'])) ? mysqli_real_escape_string($connessione,$_POST['conf_password']) : '';

			if(!empty($password) && !empty($conf_password)){

				if($password == $conf_password){
					$sql1 = 'UPDATE utente SET password = "'.sha1(md5($password)).'" WHERE id_utente = "'.$_SESSION['user_id'].'"';
					
					if($connessione->query($sql1) === TRUE){
					    $sqlnotif = 'SELECT COUNT(*) as co FROM notifica WHERE id_utente = "'.$_SESSION['user_id'].'"';
						$resultnotif = $connessione->query($sqlnotif);
					    $rownotif= mysqli_fetch_array($resultnotif);
				        
				        if($rownotif['co']==0){
				       		echo "<script>
								window.location.href = 'send_welcome.php';
							</script>";	
					     }
					}else{
						echo '<div class="jumbotron">';
						echo '<p style="color:red;">Attenzione, si &eagrave; verificato un errore!</p>';
		          		echo '<a href="javascript: window.history.go(-1)">Torna indietro</a>';
		          		echo '</div>';
					}
				}else{
					echo '<div class="jumbotron">';
					echo '<p style="color:red;">Attenzione: Le password non corrispondono!</p>';
			        echo '<a href="javascript: window.history.go(-1)">Torna indietro</a>';
		        	echo '</div>';
				}
			}else{
				echo '<div class="jumbotron">';
				echo '<p style="color:red;">Compilare tutti i campi!</p>';
			    echo '<a href="javascript: window.history.go(-1)">Torna indietro</a>';
		        echo '</div>';
			}
		}else{
			$sql = 'SELECT * FROM utente WHERE id_utente = "'.$_SESSION['user_id'].'"';
			$result = $connessione->query($sql);

			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_array($result);

				if($row['password'] == ""){
					echo '<div class="jumbotron">';
					echo '<p>Aggiungi una password per il tuo account</p>';
					echo '<form action="complete_account.php" method="post"  minlength="4" required>';
					echo '<input type="password" name="password" placeholder="Aggiungi password">';
					echo '<br><br><input type="password" name="conf_password" placeholder="Conferma password">';
					echo '<br><br><input type="submit" name="action" value="Salva e continua">';
					echo "</form>";
					echo '</div>';
				}else{
					echo '<div class="jumbotron">';
				    echo '<p style="color:red;">Attenzione, password gi&agrave impostata!<br> Torna all\' <a style="color:blue;" href="eventi.php">index</a>!</p>';
				    echo '</div>'; 
				}
			}
		}
	}else{
		echo '<div class="jumbotron">';
	    echo '<p>Attenzione, effettua il <a href="index.php">login</a> per accedere!</p>';
	    echo '</div>';
	}
	
	include 'footer.php';
?>
