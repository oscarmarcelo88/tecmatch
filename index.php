<?php

$challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        if ($verify_token === 'Oscar') {
            echo $challenge;
        }

//BD real
/*
$db_host = "tecmatch.co";
$db_name = "tecmatch_tecmatchdb";
$db_username = "tecmatch_user";
$db_pass = "Tecmatch88";
*/

require 'Functions.php';
require 'ConnectionDb.php';

$db_host = "localhost";
$db_name = "test_TecMatch";
$db_username = "root";
$db_pass = "root";

$data = json_decode(file_get_contents('php://input'), true);
$rid = $data['entry'][0]['messaging'][0]['sender']['id'];
$message = $data['entry'][0]['messaging'][0]['message']['text'];
$lat = $data['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['lat'];
$long = $data['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['long'];
$payload = $data['entry'][0]['messaging'][0]['postback']['payload'];
$payloadParaContacto = $data['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];

$urlWebhook = "https://ec7de6d5.ngrok.io/tecmatch/";
$functions = new Functions($rid, $message, $urlWebhook);
$connectiondb = new ConnectionDb();
$replies = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");

list ($code, $ganadorId) = split ('/',$payload);
list ($code2, $ganadorIdContacto) = split ('/',$payloadParaContacto);

//Para saber si ponemos el login y el getstarted msg
  $query = 'select fb_id, first_name, gender from Users where fb_sender_id='.$rid;
  $results = $connectiondb->Connection($query);
  $results2 = json_decode(json_encode($results), true);
  //para probarlo: $payload = "getstarted";
  var_dump($results_GetStarted);
    if ($payload == "getstarted")
    {
      if ($results2[0]["gender"] == 1)
      {
        $replies = array ('MUJER: Tec Match esta basado en la inversión de roles. Las mujeres entran a un juego donde deciden cual les gusta más entre 2 hombres, después de eso ellas deciden si quieren contactarlo o no. A los hombres les toca esperar a ser contactados.');
        $functions->sendTextMessage($replies);
      }else{
        $replies = array ('Tec Match esta basado en la inversión de roles. Las mujeres entran a un juego donde deciden cual les gusta más entre 2 hombres, después de eso ellas deciden si quieren contactarlo o no. A los hombres les toca esperar a ser contactados.');
        $functions->sendTextMessage($replies);
      }
    }

  if ($results2[0]['gender'] == 1 && $results2[0]['fb_id'] != null)
  {
      //universal response whenever isn't another key message
      if ($message != null && $message != "Seguir Jugando" && $message != "Jugar" && $message != "Contactarlo" && $message != "Empezar" && $message != "Get Started ") 
      {
        $replies = array ("Que onda ".$results2[0]["first_name"].", esto es lo que puedo hacer:", "".$results2[0]["first_name"].", que te parece si empezamos");
        $functions->preguntaMensaje($replies);
      } 
      //choose the winner and we ask what to do
      if ($code == "gano") 
      {
        $replies = array ("¡Buena elección! Qué quieres hacer:", "Ese era mi preferido! Ahora qué hacemos:", "¡Tienes buenos gustos! Lo contactamos?");
        $functions->askContact($replies, $ganadorId);
      }
      //send the 2 photos with a winner choosen before
      if ($message == "Seguir Jugando")
      {
        $ganadorId = $data['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
        $replies = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");
        $functions->sendTextMessage($replies);
        $functions->sendGenericMessage($ganadorId);
      }
      //play a new game
      if ($message == "Jugar")
      {
        $replies = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");
        $functions->sendTextMessage($replies);
        $functions->newGame();
      }
      //Contact the user 
      if ($message == "Contactarlo") 
      {
        $replies = array ("Ya lo contacté, te aviso si me dice algo de ti. Mientras tú sigue jugando!", "Le mandé un mensaje, veamos a ver si contesta. Vamos a seguir jugando!", "Ya le mandé un mensaje, si vale la pena el te va a contactar.");
        $functions->sendTextMessage($replies);
        $replies = array ("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "A cuál de estos le tomarías screenshot a sus conversaciones?");
        $functions->sendTextMessage($replies);
        $functions->newGame();
        $functions->contact($ganadorIdContacto); 
      }
  }else{
    if($message != null && $results2[0]['fb_id'] != null)
    {
      $replies = array ("Tú tranquilo, te avisaremos cuando alguna chica te contacte ;) ", "Ahora te toca esperar... ;) ","Ahora te toca esperar... ;) ");
      $functions->sendTextMessage($replies);
    }
  }

  if (strpos($message, 'puto') || strpos($message, 'pendeja') || strpos($message, 'puta') || strpos($message, 'pinche') || strpos($message, 'cabron') || strpos($message, 'pendejo') || strpos($message, 'culo') || strpos($message, 'mames'))
{
   $replies = array ("Cuidado con esa boquita", "Con esa boca saludas a tu mamá?");
   $functions->sendTextMessage($replies);
}
//if they don't have fb_id they need to do login
  if ($results2[0]['fb_id'] == null && $message != null) 
      {
        $functions->sendLogin();
      }
