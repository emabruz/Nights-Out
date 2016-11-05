<?php
	//process client reuquest (VIA URL)
	header("Content-Type: application/json");
	include("../conn_db.php");
	
	if($_GET['locale']){
		if($_GET['locale'] != 'all'){
			
			$find_locale = $_GET['locale'];
		
  			$sqlfindlocale = 'SELECT * FROM locale l, evento e WHERE l.id_locale = e.id_locale AND nomeLocale = "'.$find_locale.'"';
  			$resultfindlocale = $connessione->query($sqlfindlocale);
  	
  			$eventijson = array();
  			$i = 0;
  	
  			if(mysqli_num_rows($resultfindlocale) > 0){
				while($row = mysqli_fetch_array($resultfindlocale)){
					$eventijson[$i] = array("nome evento"=>$row['titoloEvento'],
						"inizio"=>$row['inizio'],
						"fine"=>$row['fine']);
						$i++;
				}
  			}
  
  			$sqlfindlocale2 = 'SELECT * FROM locale WHERE nomeLocale = "'.$find_locale.'"';
  			$resultfindlocale2 = $connessione->query($sqlfindlocale2);
  	
  			if(mysqli_num_rows($resultfindlocale2) > 0){
				$row = mysqli_fetch_array($resultfindlocale2);
					$localejson = array("nome locale"=>$row['nomeLocale'],
						"telefono"=>$row['telefono'],
						"indirizzo"=>$row['indirizzo'],
						"eventi"=>$eventijson
					);
					deliver_response(200, "SUCCESS", $localejson);
  			}else{
  				deliver_response(404, "NOT FOUND",NULL);
  			}
			//return $localejson;
  			//echo json_encode($localejson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); //Restituisce il risultato
	
			
		}
		else {
		
			$sql_findall_locali = 'SELECT * FROM locale';
  			$result_findall_locali = $connessione->query($sql_findall_locali);
  			
  			$all_locali_json = array();
  			$i = 0;
  			
  			if(mysqli_num_rows($result_findall_locali) > 0){
				while($row = mysqli_fetch_array($result_findall_locali)){
					$all_locali_json[$i] = array("nome locale"=>$row['nomeLocale'],
						"telefono"=>$row['telefono'],
						"indirizzo"=>$row['indirizzo']);
						
						$i++;
				}
				deliver_response(200, "SUCCESS", $all_locali_json);
  			}else{
  				deliver_response(404, "NOT FOUND",NULL);
  			}
  			
			
		}
	}//chiuso get_locale
	
	else if($_GET['evento']){
		if($_GET['evento'] != 'all'){
			
			$find_evento = $_GET['evento'];
			
			$sql_find_evento = 'SELECT * FROM evento WHERE titoloEvento = "'.$find_evento.'"';
  			$result_find_evento = $connessione->query($sql_find_evento);
  	
  			$evento_json = array();
  	
  			if(mysqli_num_rows($result_find_evento) > 0){
				while($row = mysqli_fetch_array($result_find_evento)){
					$evento_json = array("nome evento"=>$row['titoloEvento'],
						"inizio"=>$row['inizio'],
						"fine"=>$row['fine']);
					
				}
				deliver_response(200, "SUCCESS",$evento_json);
				
  			}else{
  				deliver_response(404, "NOT FOUND",NULL);
  			}
		
		}
		else{
			
			$sql_findall_eventi = 'SELECT * FROM evento';
  			$result_findall_eventi = $connessione->query($sql_findall_eventi);
  			
  			$all_eventi_json = array();
  			$i = 0;
  			
  			if(mysqli_num_rows($result_findall_eventi) > 0){
				while($row = mysqli_fetch_array($result_findall_eventi)){
					$all_eventi_json[$i] = array("nome evento"=>$row['titoloEvento'],
						"inizio"=>$row['inizio'],
						"fine"=>$row['fine']);
						
						$i++;
				}
				deliver_response(200, "SUCCESS", $all_eventi_json);
  			}else{
  				deliver_response(404, "NOT FOUND",NULL);
  			}

		}
		
	}

	else{
		deliver_response(400, "INVALID REQUEST", NULL);
	}
  	
  	
	function deliver_response($status, $status_message, $data){
		header("HTTP/1.1 $status $status_message");

		$response['status'] = $status;
		$response['status_message'] = $status_message;
		$response['data'] = $data;

		$json_response = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		echo $json_response;

	}

?>