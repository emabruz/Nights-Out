<?php

session_start();

include 'header.php';
require('vendor/autoload.php');
define('AMQP_DEBUG', false);
use PhpAmqpLib\Connection\AMQPConnection;

$conn = new AMQPConnection(
    'chicken.rmq.cloudamqp.com', //$url['host'], //host - CloudAMQP_URL 
    5672,         //port - port number of the service, 5672 is the default
    'cflqmkaw', //$url['user'], //user - username to connect to server
    '71Mp6-8weTRmzw02U9g9xj-9VfwZltKr', //$url['pass'], //password - password to connecto to the server
    'cflqmkaw' //substr($url['path'], 1) //vhost
);

$ch = $conn->channel();

$exchange = 'amq.direct';
$queue = 'queue'.$_SESSION['user_id'];
$key = 'key'.$_SESSION['user_id'];

$ch->queue_declare(
    $queue, //queue name 
    false,  //passive -  check whether an exchange exists without modifying server state
    true,   //durable - RabbitMQ will never lose the queue if a crash occurs
    false,  //exclusive - if queue only will be used by one connection
    true   //autodelete - queue is deleted when last consumer unsubscribes
);

$ch->exchange_declare($exchange, 'direct', true, true, false);
$ch->queue_bind($queue, $exchange,$key);

$sql4 = 'SELECT * FROM notifica WHERE id_utente = "'.$_SESSION['user_id'].'" AND letta = 1 ORDER BY id_notifica DESC LIMIT 5';
$result4 = $connessione->query($sql4);

$i=0;
for($i;$i<$row['c'];$i++){
     
     $retrived_msg = $ch->basic_get($queue);
     //var_dump($retrived_msg->body);

     $ch->basic_ack($retrived_msg->delivery_info['delivery_tag']);//get('delivery_tag')); 
     $mess_recv = $retrived_msg->body;
     
     echo '<div class="jumbotron"><p><img src="img/notif_icon_black.png" width="20px" height="20px">&nbsp;&nbsp;'.$mess_recv.'</p></div>';

     $sql3 = 'UPDATE notifica SET letta = 1 WHERE id_utente = "'.$_SESSION['user_id'].'" AND inviata = 1';
     $connessione->query($sql3);

}

if(mysqli_num_rows($result4)>0){
    while($row4 = mysqli_fetch_array($result4)){
          echo '<div class="jumbotron"><p>'.$row4['testo_notifica'].'</p></div>';
    }
}

//$ch->delete_queue($queue);
//msg_remove_queue($queue);

include 'footer.php'; 
//$ch->queue.delete($queue);
$ch->close();
$conn->close();
?>