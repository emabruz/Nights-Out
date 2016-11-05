<?php
session_start();
require_once '../conn_db.php';
require_once __DIR__ . '/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '1602740183357651',
  'app_secret' => '79f5a63f22178adb2b4e7c851b1b9eeb',
  'default_graph_version' => 'v2.7'
]);

$helper = $fb->getJavaScriptHelper();

try {
  $accessToken = $helper->getAccessToken();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
}



if (isset($accessToken)) {
   $fb->setDefaultAccessToken($accessToken);
    //getting basic info about user
  try {
    $requestProfile = $fb->get("/me?fields=name,first_name,last_name,email");
    $profile = $requestProfile->getGraphNode()->asArray();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
  }



  $_SESSION['name'] = $profile['first_name'];
  $_SESSION['surname'] = $profile['last_name'];
  $_SESSION['email'] = $profile['email'];

  $nome = $profile['first_name'];
  $cognome = $profile['last_name'];
  $email = $profile['email'];


  $sql2 = "SELECT * FROM utente WHERE nome = '".$nome."' and cognome = '".$cognome."' and email = '".$email."'";
  $result2 = $connessione->query($sql2);
  
  if ($result2->num_rows > 0) {
    // l'utente è già stato inserito nel database
    while($row2 = $result2->fetch_assoc()) {
      //recupero l'id utente che mi servirà per fare in modo che inserisca i locali e gli eventi.
      $_SESSION['user_id'] = $row2['id_utente'];
    }

    header('location: ../eventi.php');
  } else {

    // l'utente è la prima volta che si logga con facebook, dobbiamo aggiungerlo
      $sql = "INSERT INTO utente (id_utente, nome, cognome, email, password)
      VALUES (NULL,'".$nome."', '".$cognome."', '".$email."','')";

      if ($connessione->query($sql) === TRUE) {
            //Crea il nuovo utente
            //Dobbiamo rimediare il suo user_id

            $sql1 = "SELECT * FROM utente WHERE nome = '".$nome."' and cognome = '".$cognome."' and email = '".$email."'";
            $result1 = $connessione->query($sql1);
            
            if (mysqli_num_rows($result1) > 0) {
              // l'utente è già stato inserito nel database
              while($row1 = mysqli_fetch_array($result1)) {
                $_SESSION['user_id'] = $row1['id_utente'];
              }
            }
            

      } else {
          echo "Error: " . $sql . "<br>" . $connessione->error;
      }

      header('location: ../complete_account.php');
    }

  //setcookie(name, $profile['name'], time() + (3600 * 2), "/");  
  //header('location: ../index.php');
  exit;
} else {
    echo "Unauthorized access!!!";
    exit;
}
