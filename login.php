<?php
	include 'conn_db.php';

	if(isset($_POST['email'])&&isset($_POST['password'])&&!empty($_POST['email'])&&!empty($_POST['password'])){
    	$email = $_POST['email'];
    	$password = $_POST['password'];
  
    	$sql = "SELECT * FROM utente WHERE email = '".$email."' and password = '".sha1(md5($password))."'";
    	$result = $connessione->query($sql);

		if($result->num_rows > 0) {
		    session_start();
		    while($row = $result->fetch_assoc()) {
		        $_SESSION['name'] = $row['nome'];
		        $_SESSION['user_id'] = $row['id_utente'];
		        $_SESSION['surname'] = $row['cognome'];
		        $_SESSION['email'] = $row['email'];
		    }
		    $sqlnotif = 'SELECT COUNT(*) as co FROM notifica WHERE id_utente = "'.$_SESSION['user_id'].'"';
			$resultnotif = $connessione->query($sqlnotif);
		    $rownotif= mysqli_fetch_array($resultnotif);
	        if($rownotif['co']==0){
	       		echo "<script>
					window.location.href = 'send_welcome.php';
				</script>";	
		     }else{
		       	echo "<script>
					window.location.href = 'eventi.php';
				</script>";	
	        }
		}else{
			echo "<script>
				window.location.href = 'error.php';
			</script>";		
		}
	}else if(empty($_POST['email']) || empty($_POST['password'])){
		include 'header.php';
		echo '<div class="jumbotron">';
		echo 'ATTENZIONE: COMPILARE I CAMPI<br>';
		echo '<a href="index.php">Torna indietro</a>';
		echo '</div>';
		include 'footer.php';
	}

?>