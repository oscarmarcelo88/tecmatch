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


//echo $_SERVER['DOCUMENT_ROOT'];

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

$urlWebhook = "https://d3fc1717.ngrok.io/tecmatch/";

$connectiondb = new ConnectionDb();

list ($code, $ganadorId, $perdedorId) = split ('/',$payload);
list ($code2, $ganadorIdContacto, $perdedorIdContacto) = split ('/',$payloadParaContacto);

list ($nickname, $messageToContact) = split (':',$message);

//Para saber si ponemos el login y el getstarted msg
  $query = 'select fb_id, first_name, gender, sexual_orientation, studied_at, location, inte1, inte2, inte3 from Users where fb_sender_id='.$rid;
  $results = $connectiondb->Connection($query);
  $results2 = json_decode(json_encode($results), true);

  $functions = new Functions($rid, $message, $urlWebhook, $results2[0]['sexual_orientation'], $results2[0]['location'], $results2[0]['first_name'], $results2[0]['gender']);

  //para probarlo: $payload = "getstarted";
    if ($payload == "getstarted")
    {
      if ($results2[0]["gender"] == 1)
      {
        $replies = array ("Hola ! NOMBREBOT es un juego basado en la inversión de roles. Te voy a mostrar dos fotos de chavos y tú decidirás cual te gusta más. Una vez tomada la decisión podrás elegir entre dos opciones, agregarlo a tus contactos o seguir jugando. No te preocupes, solo los hombres que agregues podrán contactarte.");
        $functions->sendTextMessage($replies);
      }else{
        $replies = array ("Que onda! NOMBREBOT es un juego basado en la inversión de roles. A las mujeres les muestro fotos de dos chavos y ellas deciden cual les gusta más. Una vez tomada la decisión deciden si lo agregan como contacto o no. A ti te va a tocar esperar a que una chava te agregue como contacto para empezar la conversación. No te preocupes, no toda la diversión es para las mujeres, mientras esperas podrás ver a que chavos les has ganado 8|");
        $functions->sendTextMessage($replies);
      }
    }

//if they don't have fb_id they need to do login
  if (($results2[0]['fb_id'] == null && $message != null) || $payload == "getstarted")
      {
        $functions->sendLogin();
      }

  //Persistent menu options:
  //erase questionarie  
  if ($payload == "borrar")
  {
    $functions->eraseInte();
    $message = "borrar";
  }
  if ($payload == "canal")
  {
    $functions->changeChannel($results2[0]['location'], $results2[0]['studied_at']);
  }
  if ($code2 == "channelChange")
  {
    //is ganadorIdContacto is the new channel
    $functions->changeChannel2($ganadorIdContacto);
  }


if (($results2[0]['inte1'] == null || $results2[0]['inte2'] == null || $results2[0]['inte3'] == null || $payload == "borrar") && $results2[0]['fb_id'] != null)
  {
    $functions->questionsAssign($code2, $results2[0]['inte1'], $results2[0]['inte2'], $results2[0]['inte3'], $message, $ganadorIdContacto);
    $message = null;
  }


//gays = 1, lesbianas=2, heter=0, pero lo pongo como 0 para probar
  if (($results2[0]['gender'] == 1 || $results2[0]['sexual_orientation'] == 1) && $results2[0]['fb_id'] != null && $messageToContact == null && $payload != "cambiarsex" && $payloadParaContacto != "sexhombres" && $payloadParaContacto != "sexmujeres" && $results2[0]['inte3'] != null)
  {
      //universal response whenever isn't another key message
      if ($message != null && $message != "Seguir Jugando" && $message != "Jugar" && $message != "Agregar a contactos" && $message != "Empezar" && $message != "Get Started ") 
      {
        $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
        $functions->preguntaMensaje($replies);
        //$functions->sendLogin();
      } 
      //choose the winner and we ask what to do
      if ($code == "gano") 
      {
        $functions->saveGame($ganadorId, $perdedorId);
        $replies = array ("¡Buena elección! Qué quieres hacer:", "Ese era mi preferido! Ahora qué hacemos:", "¡Tienes buenos gustos! Lo agregamos a tus contactos?");
        $functions->askContact($replies, $ganadorId, $perdedorId);
      }
      //send the 2 photos with a winner choosen before
      if ($message == "Seguir Jugando")
      {
        $functions->newGame();
      }
      //play a new game
      if ($message == "Jugar" || $payload == "jugar")
      {
        $functions->newGame();
      }
      //Contact the user 
      if ($message == "Agregar a contactos") 
      {
        $functions->changeRelationship($ganadorIdContacto, $perdedorIdContacto);
        $functions->contact($ganadorIdContacto); 
        $query = "select nickname2 from Games WHERE ganadorId =".$ganadorIdContacto." AND jugadorId =".$rid."";
        $results_contacto = $connectiondb->Connection($query);
        $results_contacto2 = json_decode(json_encode($results_contacto), true);
        $replies = array ("Ya lo agregué a tus contactos! Para hablar con el escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. ".$results_contacto2[0]['nickname2'].":MENSAJE)");
        $functions->sendTextMessage($replies);
        $functions->newGame();
      }
  }else{
    if($message != null && $results2[0]['fb_id'] != null && $message != "Puntaje" && $messageToContact == null && $payload != "cambiarsex" && $payloadParaContacto != "sexhombres" && $payloadParaContacto != "sexmujeres" && $results2[0]['inte3'] != null)
    {
      $replies = array ("Tú tranquilo, te avisaré cuando alguna chica te contacte 👌👌 ", "Ahora te toca esperar... 😉😉");
      $functions->sendTextMessage($replies);
      $replies = array ("Puedes revisar como vas aquí: ");
      $functions->preguntaMensajePuntaje($replies);
      //$functions->sendLogin();
    }
    if ($message == "Puntaje" || $payload == "puntaje")
    {
      $replies = array ("Tú tranquilo, te avisaré cuando alguna chica te contacte 👌👌 ", "Ahora te toca esperar... 😉😉 "); 
      $functions->sendTextMessage($replies);
      $functions->score();
     
    }
  }
//change sexual orientation
  if ($payload == "cambiarsex")
  {
      $functions->preguntaOrientacionSexual();
  }

  if ($payloadParaContacto == "sexhombres" || $payloadParaContacto == "sexmujeres")
  {
      if($results2[0]["gender"] == 0 && $payloadParaContacto == "sexhombres")
        {
          $replies = array ("Ahora las reglas cambian 😱😱 Vas a poder ver a hombres que también le interesan hombres y ellos también te van a poder ver a ti.");
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(1);
          $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
          $functions->preguntaMensaje($replies);
          
        }
        if($results2[0]["gender"] == 0 && $payloadParaContacto == "sexmujeres")
        {
          $replies = array ("Tú tranquilo, te avisaré cuando alguna chica te contacte 👌👌 ", "Ahora te toca esperar... 😉😉 ");          
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(0);
        }
        if($results2[0]["gender"] == 1 && $payloadParaContacto == "sexmujeres")
        {
          $replies = array ("Ahora las reglas cambian 😱😱 Vas a poder ver a mujeres que también le interesan mujeres y ellas también te van a poder ver a ti.");
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(2);
          $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
          $functions->preguntaMensaje($replies);
        }
        if($results2[0]["gender"] == 1 && $payloadParaContacto == "sexhombres")
        {
          $replies = array ("Ahora nadie podrá ver tus fotos y veras a hombres que les interesan las mujeres 😏😏");
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(0);
          $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
          $functions->preguntaMensaje($replies);
        }
  }

  //send message to contact
  if ($messageToContact != null)
  {
    $functions->sendTextMessageToContact($nickname, $messageToContact);
  }

  if (strpos($message, 'puto') || strpos($message, 'pendeja') || strpos($message, 'puta') || strpos($message, 'pinche') || strpos($message, 'cabron') || strpos($message, 'pendejo') || strpos($message, 'culo') || strpos($message, 'mames'))
  {
   $replies = array ("Cuidado con esa boquita", "Con esa boca saludas a tu mamá?");
   $functions->sendTextMessage($replies);
  }



      

