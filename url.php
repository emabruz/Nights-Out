<?php 
include 'conn_db.php';
function _make_url_clickable_cb($matches) {
  $ret = '';
  $url = $matches[2];
 
  if ( empty($url) )
    return $matches[0];
  // removed trailing [.,;:] from URL
  if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
    $ret = substr($url, -1);
    $url = substr($url, 0, strlen($url)-1);
  }
  return $matches[1] . "<a target='_blank' href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
}
 
function _make_web_ftp_clickable_cb($matches) {
  $ret = '';
  $dest = $matches[2];
  $dest = 'http://' . $dest;
 
  if ( empty($dest) )
    return $matches[0];
  // removed trailing [,;:] from URL
  if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
    $ret = substr($dest, -1);
    $dest = substr($dest, 0, strlen($dest)-1);
  }
  return $matches[1] . "<a target='_blank' href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
}
 
function _make_email_clickable_cb($matches) {
  $email = $matches[2] . '@' . $matches[3];
  return $matches[1] . "<a target='_blank' href=\"mailto:$email\">$email</a>";
}

function make_clickable($ret) {
  $ret = ' ' . $ret;
  // in testing, using arrays here was found to be faster
  $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
  $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
  $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
 
  // this one is not in an array because we need it to run last, for cleanup of accidental links within links
  $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
  $ret = trim($ret);
  return $ret;
}
/*
function controllaPIVA($variabile){

  if($variabile=='')
    return false;
  //la p.iva deve essere lunga 11 caratteri
  if(strlen($variabile)!=11)
    return false;

  //la p.iva deve avere solo cifre
  //if(preg_match("^[0-9]+$", $variabile)===false)
    //return false;
  $primo=0;
  for($i=0; $i<=9; $i+=2)
          $primo+= ord($variabile[$i])-ord('0');

  for($i=1; $i<=9; $i+=2 ){
    $secondo=2*( ord($variabile[$i])-ord('0') );
    if($secondo>9)
      $secondo=$secondo-9;
    $primo+=$secondo;

  }
  if( (10-$primo%10)%10 != ord($variabile[10])-ord('0') )
    return false;

  return true;
}*/

function confrontoData($mese_i, $giorno_i, $mese_att, $giorno_att){

  $var = 0;

  if(date("m") == 01){ $var = 31; }
  else if(date("m") == 02) { $var = 28; }
  else if(date("m") == 03) { $var = 31; }
  else if(date("m") == 04) { $var = 30; }
  else if(date("m") == 05) { $var = 31; }
  else if(date("m") == 06) { $var = 30; }
  else if(date("m") == 07) { $var = 31; }
  else if(date("m") == 08) { $var = 31; }
  else if(date("m") == 09) { $var = 30; }
  else if(date("m") == 10) { $var = 31; }
  else if(date("m") == 11) { $var = 30; }
  else if(date("m") == 12) { $var = 31; }


  if($mese_att < $mese_i){
    if(($giorno_att+7) > $var){
      if((($giorno_att+7)-$var) >= $giorno_i){
        return true;
      }
    }
  }

  if($mese_att == $mese_i){
    if(($giorno_i-$giorno_att) < 7){
      return true;
    }
  }

  return false;
}

function TagliaStringa($stringa, $max_char){
    if(strlen($stringa)>$max_char){
      $stringa_tagliata=substr($stringa, 0,$max_char);
      $last_space=strrpos($stringa_tagliata," ");
      $stringa_ok=substr($stringa_tagliata, 0,$last_space);
      return $stringa_ok."...";
    }else{
      return $stringa;
    }
  }

function controlloOreMinuti($OraMinuti){
  if($OraMinuti == 0){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 1){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 2){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 3){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 4){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 5){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 6){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 7){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 8){
    $OraMinuti = "0".$OraMinuti;
  }
  else if($OraMinuti == 9){
    $OraMinuti = "0".$OraMinuti;
  }
  return $OraMinuti;
}



?>
