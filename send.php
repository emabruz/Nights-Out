<?php
session_start();
include 'conn_db.php';

require('vendor/autoload.php');
define('AMQP_DEBUG', false);
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
* Create a connection to RabbitMQ
*/

//$url = parse_url(getenv('amqp://atmmsjxq:d7tRabC3PZvOAjMBKY98X7jWfM6smw_e@spotted-monkey.rmq.cloudamqp.com/atmmsjxq'));
$conn = new AMQPConnection(
          'chicken.rmq.cloudamqp.com', //$url['host'], //host - CloudAMQP_URL 
          5672,         //port - port number of the service, 5672 is the default
          'cflqmkaw', //$url['user'], //user - username to connect to server
          '71Mp6-8weTRmzw02U9g9xj-9VfwZltKr', //$url['pass'], //password - password to connecto to the server
          'cflqmkaw' //substr($url['path'], 1) //vhost
);

$ch = $conn->channel();

$exchange = 'amq.direct';

$inviate = 'SELECT * FROM notifica WHERE id_utente = "'.$_SESSION['user_id'].'" AND inviata = 0';
$result_inv = $connessione->query($inviate);

while($row_inv = mysqli_fetch_array($result_inv)){

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

     $msg_body = 'Il tuo evento '.$row_inv['nome_evento'].' è stato eliminato poichè scaduto in data '.$row_inv['data_fine'];
     $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
     $ch->basic_publish($msg, $exchange, $key);

     $sql = 'UPDATE notifica SET testo_notifica = "'.$msg_body.'", inviata = 1 WHERE id_utente = "'.$_SESSION['user_id'].'" AND letta = 0 AND nome_evento = "'.$row_inv['nome_evento'].'"';
     $connessione->query($sql);

}

$ch->close();
$conn->close();

?>