<?php 
  include 'conn_db.php';
  include 'header.php'; 
  include 'url.php';
?>

<script type="text/javascript">repeatAjax1();</script>
<script src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyAPrju4jKR4_YQJ22PRglSpUfeW9EHDASg&libraries=places&sensor=false"></script>
    
<script type="text/javascript">
  function initialize() {
    var input = document.getElementById('loc-search');
    new google.maps.places.Autocomplete(input);
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>

<?php 
  if (empty($_GET['location'])){ ?>
  <div class="jumbotron">
    <p>Inserisci la via o il luogo dove desideri effettuare la ricerca!</p><br>
    <form action="#" method="get">
      <input type="text" id="loc-search" size="40" name="location" placeholder="Inserisci indirizzo" autocomplete="off">
      <button type="submit">Cerca</button>
    </form><br>
    
<!--<script type="text/javascript">/*
    var input = document.getElementById('loc-search');
    var autocomplete = new google.maps.places.Autocomplete(input);*/
    /*
    function initialize() {
            var input = document.getElementById('loc-search');
            new google.maps.places.Autocomplete(input);
        }
        function loadScript()
            {
                var script = document.createElement("script");
                script.type = "text/javascript";
                script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyAPrju4jKR4_YQJ22PRglSpUfeW9EHDASg&libraries=places&sensor=false&callback=initialize";
                document.body.appendChild(script);    
            }
            window.onload = loadScript;*/
            
            //google.maps.event.addDomListener(window, 'load', initialize);
          
  </script>-->

  </div> <!-- chiuso jumbotron -->

<?php
  }else{  //if (!empty($_GET['location']))

    //Restituisce, dato un indirizzo, le rispettive coordinate in latitudine e longitudine.
    $maps_url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($_GET['location']);
    $maps_json = file_get_contents($maps_url);
    $maps_array = json_decode($maps_json, true);
    $lat = $maps_array['results'][0]['geometry']['location']['lat'];
    $lng = $maps_array['results'][0]['geometry']['location']['lng'];

    echo '<div class="jumbotron">';

?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPrju4jKR4_YQJ22PRglSpUfeW9EHDASg&callback=initMap" type="text/javascript"></script> 
    <!-- Mappa con le relative dimensioni -->
    <center><div id="map" style="width: 90%; height: 20em;"></div></center><br>
<?php 
    echo '<p style="font-size:18px;">Eventi vicino a: '.$_GET['location'].'</p><br>
    </div>';

    //Generazione connessione con db e selezione di tutti i campi della tabella evento.
    $eventi_vicini = FALSE;
    $i = 0;

    $sql = "SELECT * FROM evento";
    $result = $connessione->query($sql);
      if(mysqli_num_rows($result) > 0){
        echo "<script type='text/javascript'>
          var locations = new Array();
        </script>";
        
        while($row = mysqli_fetch_array($result)){
            extract($row);

            $data_i = explode("/", $row['inizio']);
            $mese_i = $data_i[1];
            $giorno_i = $data_i[0];
            $anno_i = $data_i[2];

            $data_f = explode("/", $row['fine']);
            $mese_f = $data_f[1];
            $giorno_f = $data_f[0];
            $anno_f = $data_f[2];
            
            if(confrontoData($mese_i, $giorno_i, date("m"), date("d")) == TRUE){

            $latitude = $row['latitudine'];
            $longitude = $row['longitudine'];
            $dist = getDistance($lat, $lng, $latitude, $longitude); 
            
            if($dist <= 3){ 
              $eventi_vicini = TRUE;
             
              $sql_1 = "SELECT * FROM locale WHERE id_locale = ".$row['id_locale'];
              $result_1 = $connessione->query($sql_1);

              $sql10 = 'SELECT * FROM foto_evento WHERE id_evento = "'.$row['id_evento'].'"';
              $result10 = $connessione->query($sql10);

              $row['ora_inizio'] = controlloOreMinuti($row['ora_inizio']);
              $row['ora_fine'] = controlloOreMinuti($row['ora_fine']);

              $row['minuti_inizio'] = controlloOreMinuti($row['minuti_inizio']);
              $row['minuti_fine'] = controlloOreMinuti($row['minuti_fine']);

              ?>
              <div class="jumbotron"> <!--<hr style="height:1px;border:none;color:black;background-color:black;" />-->
                <div class="row">
                  <div class="col-lg-8">
                    <p style="text-align:left; font-size:18px;">
                  
<?php
                      if(mysqli_num_rows($result_1) > 0){
                        $row_1 = mysqli_fetch_array($result_1);
                        echo '<b>Nome Locale: </b><a href="info_locale.php?locId='.$row_1['id_locale'].'" target="_blank">'.$row_1['nomeLocale'].'</a><br>';
                      }
                      echo '<a name="evento'.$i.'"></a>';
                      echo '<b>Evento: </b>'.stripslashes($row['titoloEvento']).'<br>';
                      echo '<b>Data: </b>'.$row['inizio'].' - '.$row['fine'].'<br>';
                      echo '<b>Orario: </b>'.$row['ora_inizio'].':'.$row['minuti_inizio'].' - '.$row['ora_fine'].':'.$row['minuti_fine'].'<br>';
                      echo '<b>Via: </b>'.$row_1['indirizzo'].'<br>';
                      echo "<b>Descrizione: </b>".stripslashes(make_clickable($row['descrizione_ev']));
?>                  </p>
                  </div> 
                  <div class="col-lg-4">
<?php
                    if(mysqli_num_rows($result10) > 0){
                      $row10 = mysqli_fetch_array($result10);
                      $dir = "foto_evento/thumb_evento";
                      $handle = opendir($dir);
                      echo '<img class="img-thumbnail" src="' . $dir.'/'.$row10['nome_immagine'].$row10['estensione'].'" />';
                    }
?>
                  </div>
                </div>
              </div>
              <!--<div class="row"> 
                <div class="col-lg-12"><p style="text-align:left; font-size:18px;"><?php
                echo "<b>Descrizione: </b>".stripslashes(make_clickable($row['descrizione_ev']));
              ?></p></div></div></div>-->
<?php
              echo "<script type='text/javascript'>
                locations['".$i."'] = new Array();
                locations['".$i."'][0] = '".$row["titoloEvento"]."';
                locations['".$i."'][1] = '".$latitude."';
                locations['".$i."'][2] = '".$longitude."';
                locations['".$i."'][3] = '';
                locations['".$i."'][4] = '".$giorno_i."';
                locations['".$i."'][5] = '".$giorno_f."';
                locations['".$i."'][6] = '".$mese_f."';
                locations['".$i."'][7] = '".$anno_f."';
                locations['".$i."'][8] = '".$row['ora_inizio']."';
                locations['".$i."'][9] = '".$row['minuti_inizio']."';
                locations['".$i."'][10] = '".$row['ora_fine']."';
                locations['".$i."'][11] = '".$row['minuti_fine']."';
                locations['".$i."'][12] = '".$row['id_evento']."';
                locations['".$i."'][13] = '".$row['id_locale']."';
                locations['".$i."'][14] = '".$row_1['nomeLocale']."';
              </script>";
              $i++;
            }
          }
        }
        echo '<div class="jumbotron"><p><a href="javascript: window.history.go(-1)">Nuova ricerca...</a></p></div>';
      }
      
      if($eventi_vicini == TRUE){

      }else{
        echo '<div class="jumbotron">';
        echo "<p>Non ci sono eventi nelle vicinanze!</p>";
        echo '<p><a href="javascript: window.history.go(-1)">Nuova ricerca...</a></p>';
        echo '</div>';
      }
      echo '</p>';
        
      echo "<script type='text/javascript'>
        var myCenter=new google.maps.LatLng('".$lat."','".$lng."');
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          center: myCenter,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var marker1=new google.maps.Marker({
          position:myCenter,
          animation:google.maps.Animation.BOUNCE,
        });
        marker1.setMap(map);
        var infowindow = new google.maps.InfoWindow();
        var marker, i;
        for (i = 0; i < locations.length; i++) {  
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
          });
          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent('<div>Locale: <a href=info_locale.php?locId='+locations[i][13]+' target=_blank>'+locations[i][14]+'</a><br><span>Evento: <a href=#evento'+i+'>'+locations[i][0]+'</a><br><small>'+locations[i][4]+'-'+locations[i][5]+'/'+locations[i][6]+'/'+locations[i][7]+' '+locations[i][8]+':'+locations[i][9]+'-'+locations[i][10]+':'+locations[i][11]+'</small></div>');
              infowindow.open(map, marker);
            }
          })(marker, i));
        }
      </script>"; 
}

function getDistance( $latitude1, $longitude1, $latitude2, $longitude2 ) {  
    $distance = (3958*3.1415926*sqrt(($latitude2-$latitude1)*($latitude2-$latitude1) + cos($latitude2/57.29578)*cos($latitude1/57.29578)*($longitude2-$longitude1)*($longitude2-$longitude1))/180);
    $distanza = $distance/0.62137;
    return $distanza;
}

include 'footer.php'; 

?>