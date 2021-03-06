<?php
require('parser.php');
define('BOT_TOKEN', '271051412:AAG0h6tJS0_LBIztxn_7D5kMFZEaBclH4mQ');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
function processMessage($message) {
  // processa a mensagem recebida
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    
    $text = $message['text'];//texto recebido na mensagem
    if (strpos($text, "/start") === 0) {
		//envia a mensagem ao usu�rio
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Ol�, '. $message['from']['first_name'].
		'! Eu sou um bot que informa o resultado do �ltimo sorteio da Mega Sena. Ser� que voc� ganhou dessa vez? Para come�ar, escolha qual loteria voc� deseja ver o resultado', 'reply_markup' => array(
        'keyboard' => array(array('Mega-Sena', 'Quina'),array('Lotof�cil','Lotomania')),
        'one_time_keyboard' => true)));
    } else if ($text === "Mega-Sena") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('megasena', $text)));
    } else if ($text === "Quina") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('quina', $text)));
    } else if ($text === "Lotomania") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('lotomania', $text)));
    } else if ($text === "Lotof�cil") {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('lotofacil', $text)));
    } else {
      sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas n�o entendi essa mensagem. :('));
    }
  } else {
    sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas s� compreendo mensagens em texto'));
  }
}
function sendMessage($method, $parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
$context  = stream_context_create( $options );
file_get_contents(API_URL.$method, false, $context );
}
/*Com o webhook setado, n�o precisamos mais obter as mensagens atrav�s do m�todo getUpdates.Em vez disso, 
* como o este arquivo ser� chamado automaticamente quando o bot receber uma mensagem, utilizamos "php://input"
* para obter o conte�do da �ltima mensagem enviada ao bot. 
*/
$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
if (isset($update["message"])) {
  processMessage($update["message"]);
}
?>