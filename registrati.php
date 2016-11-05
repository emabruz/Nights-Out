<?php 
  include 'header.php'; 
  require 'conn_db.php';
  session_start();

  echo '<div class="jumbotron">';
      
  //controllare se l'email è già esistente.
  //In caso affermativo rimandare errore!
  if(isset($_POST['nome'])&&isset($_POST['cognome'])&&isset($_POST['email'])&&isset($_POST['password'])&&isset($_POST['conf_passw'])){
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $conf_passw = $_POST['conf_passw'];

    if(empty($_POST['nome'])||empty($_POST['cognome'])||empty($_POST['email'])||empty($_POST['password'])||empty($_POST['conf_passw'])){
      ?> 
      <p style="color:red;">Registrazione fallita: bisogna riempire tutti i campi</p>
      <a href="javascript: window.history.go(-1)">Riprova</a> <?php
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      ?> 
      <p style="color:red;">Registrazione fallita: formato email non valido</p>
      <a href="javascript: window.history.go(-1)">Riprova</a><?php
    }
    else if($password != $conf_passw){
      ?> 
      <p style="color:red;">Registrazione fallita: password non corrispondenti</p>
      <a href="javascript: window.history.go(-1)">Riprova</a> <?php
    }else{
      
      $sql = "INSERT INTO utente (id_utente, nome, cognome, email, password) VALUES (NULL,'".$nome."', '".$cognome."', '".$email."','".sha1(md5($password))."')";

      if ($connessione->query($sql) === TRUE) {
              ?>
        <p style="color:green;">Registrazione effettuata con successo!</p>
        <a href="index.php">Torna indietro</a>
        <?php
        }else{
            ?>
          <p style="color:red;">Email gi&agrave; esistente</p>
          <a href="javascript: window.history.go(-1)">Riprova</a>
          <?php
        }
      }
    }else{
?>

        <div class="row marketing">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
        <h2>Registrati</h2><br>
        <p class="lead">
        <form method="post" action="registrati.php">
          <input type="text" class="form-control" name="nome" placeholder="nome" required><br>
          <input type="text" class="form-control" name="cognome" placeholder="cognome" required><br>
          <input type="text" class="form-control" name="email" placeholder="email" required><br>
          <input type="password" class="form-control" name="password" placeholder="password" required><br>
          <input type="password" class="form-control" name="conf_passw" placeholder="conferma password" required><br><br>
          <input type="submit" value="Registrati">
        </form>
        </p>
        </div><div class="col-lg-3"></div></div>

        <a href="index.php">Torna alla pagina di login</a><br><br><br>
        <?php } ?>
</div>

<?php include 'footer.php'; ?>
