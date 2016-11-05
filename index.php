<?php 
  include 'conn_db.php';
  include 'header.php'; 

if(isset($_SESSION['name'])){
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
    echo '<script type="text/javascript">
      repeatAjax();
    </script>';
  }else{
     
  }
  
  echo "<script>
    window.location = 'eventi.php';
  </script>";
  
}else{
?>
      <div class="jumbotron">
      <!--ogni riga è divisa in colonne per un tot di lg=12, di larghezza lg specificata (lg-6 = 2 colonne)-->
      
      <div class="row marketing">
        <div class="col-lg-12">
          <p> NightsOut ti aiuta a cercare tutti gli eventi intorno a te! <br> Basta inserire una via per vedere gli eventi proposti dai locali in zona che si sono iscritti a NightsOut!</p><br>
          <h4>Possiedi un locale e vuoi inserire il tuo evento? <br><br> Registrati e accedi al nostro sito</h4>
          <p>
            <form method="post" action="login.php">
            <div class="form-group">
            <input type="text" class="form-control" name="email" placeholder="email">
            </div>
            <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="password">
            </div>
            <input type="submit" name="action" style="padding:10px 20px;" value="Accedi"> <!--class="btn btn-default"-->
          </form>
          <br>
          <span><a href="registrati.php">Registrati</a></span><br>
          <!-- <span><a href="lost_psw.php">Password dimenticata?</a></span><br><br> -->
          <h4>o accedi da Facebook!</h4><br>
          <center></center><fb:login-button data-size="xlarge" data-scope="public_profile,email" onlogin="checkLoginState();">
            Accedi
          </fb:login-button></center>

          <div id="status"></div>
            
          </p>
          <br>
          <h4>Oppure continua per cominciare subito a cercare</h4>
          <p><a href="home.php">continua >></a></p>
        </div>
      </div>
    </div>

<?php
}
include 'footer.php';
?>