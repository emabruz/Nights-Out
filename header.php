<?php session_start(); 
  include 'conn_db.php';?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>NightsOut</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/jumbotron.css">
    <script type="text/javascript" src="fbapp/fb.js"></script>
    <!-- -->

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    
    <script type="text/javascript">
      function repeatAjax(){
        jQuery.ajax({
          type: "POST",
          url: 'send.php',
          dataType: 'json',
          success: function() {
            window.location.reload(false);
          }
        });
      }

      function repeatAjax1(){
        jQuery.ajax({
          type: "POST",
          url: 'find_event.php',
          dataType: 'json',
          success: function() {
            window.location.reload(false);
          },
          complete: function() {
            setTimeout(repeatAjax,24000000); //After completion of request, time to redo it after a second
          }
        });
      }
    </script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
   
    <!-- Pop-up -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
  </head>
  <body>

    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <?php if(isset($_SESSION['name'])){ ?>
              <li role="presentation"><a style="color:white;" href="eventi.php">Eventi</a></li>
              <li role="presentation"><a style="color:white;" href="locali.php">Locali</a></li>
              <li role="presentation"><a style="color:white;" href="receive.php">
                <img src="img/notif_icon.png" width="20px" height="20px">&nbsp;(<?php 
                  $non_lette = 'SELECT COUNT(*) as c FROM notifica WHERE id_utente = "'.$_SESSION['user_id'].'" AND letta = 0';
                  $result = $connessione->query($non_lette);
                  $row = mysqli_fetch_array($result);
                  echo $row['c'];
                ?>)</a></li>
              <li role="presentation"><a style="color:white;" href="logout.php">Esci</a></li>
            <?php }else{ ?>
              <li role="presentation"><a style="color:white;" href="index.php">Home</a></li>
            <?php } ?>
          </ul>
        </nav>
      <h3 class="text-muted">
      <?php
        if(isset($_SESSION['user_id'])){
          echo '<a href="eventi.php">';
        }else{
          echo '<a href="index.php">';
        }
      ?>
      <img src="img/title.png"></a></h3>
      </div>