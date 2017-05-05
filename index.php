<?php

$challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        if ($verify_token === 'Oscar') {
            echo $challenge;
        }

require 'config.php';

$db_host = getenv("db_host");
$db_name = getenv("db_name");
$db_username = getenv("db_username");
$db_pass = getenv("db_pass");
$token = getenv("token");

require 'files/Functions.php';
require 'files/ConnectionDb.php';

$data = json_decode(file_get_contents('php://input'), true);
$rid = $data['entry'][0]['messaging'][0]['sender']['id'];
$message = $data['entry'][0]['messaging'][0]['message']['text'];
$lat = $data['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['lat'];
$long = $data['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['long'];
$payload = $data['entry'][0]['messaging'][0]['postback']['payload'];
$payloadParaContacto = $data['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];

$urlWebhook = getenv("urlWebhook");

$connectiondb = new ConnectionDb();

list ($code, $ganadorId, $perdedorId) = split ('/',$payload);
list ($code2, $ganadorIdContacto, $perdedorIdContacto) = split ('/',$payloadParaContacto);
list ($nickname, $messageToContact) = split (':',$message);


//Para saber si ponemos el login y el getstarted msg
  $query = 'select fb_id, first_name, gender, block, sexual_orientation, lives_in, studied_at, location, inte1, inte2, inte3, created_at from Users where fb_sender_id='.$rid;
  $results = $connectiondb->Connection($query);
  $results2 = json_decode(json_encode($results), true);

  $functions = new Functions($rid, $message, $urlWebhook, $results2[0]['sexual_orientation'], $results2[0]['location'], $results2[0]['first_name'], $results2[0]['gender'], $token);

  //unblock it if it's block, because they receive a message delivery
  $functions->checkBlockUser($results2[0]['block']);


  if ($results2 == null)
  {
    $functions->insertUser();
    $query = 'select fb_id, first_name, gender, sexual_orientation, lives_in, studied_at, location, inte1, inte2, inte3 from Users where fb_sender_id='.$rid;
    $results = $connectiondb->Connection($query);
    $results2 = json_decode(json_encode($results), true);
  }

     //Update the updated_at of Users
    $functions->updateTime("Users");

  //para probarlo: $payload = "getstarted";
    if ($payload == "getstarted")
    {
      if ($results2[0]["gender"] == 1)
      {
        $functions->sendTyping();
        $replies = array ("Hola ".$results2[0]['first_name']."! Mi nombre es Alice 🤖 y bienvenida a mi juego. Te voy a mostrar dos fotos de chavos y tú decidirás cuál te gusta más. Una vez tomada la decisión podrás elegir entre, agregarlo a tus contactos o seguir jugando 😏");
        $functions->sendTextMessage($replies);
        $replies = array ("No te preocupes, solo los hombres que agregues podrán contactarte 👌");
        $functions->sendTextMessage($replies);
      }else {
        $functions->sendTyping();
        $replies = array ("Hola ".$results2[0]['first_name']."! Mi nombre es Alice 🤖 y bienvenido a mi juego. A las mujeres les muestro fotos de dos chavos y ellas deciden cuál les gusta más. Una vez tomada la decisión deciden si lo agregan como contacto o no. Te toca esperar a que una chava te agregue como contacto para empezar la conversación 👌");
        $functions->sendTextMessage($replies);
        $replies = array ("No toda la diversión es para las mujeres, mientras los hombres esperan podrán ver a que chavos les han ganado 😜");
        $functions->sendTextMessage($replies);
      }
    }

//if they don't have fb_id they need to do login
  if (($results2[0]['fb_id'] == null && $message != null) || $payload == "getstarted")
      {
        $functions->sendTyping();
        $functions->sendLogin();
      }

  //Persistent menu options:
  //erase questionarie  
  if ($payload == "borrar")
  {
    $functions->sendTyping();
    $functions->eraseInte();
    //we set the inte1 to null so the code knows that we already erase the other inte1.
    $results2[0]['inte1'] = null;
    $message = "borrar";
  }
  if ($payload == "canal")
  {
    $functions->sendTyping();
    $functions->changeChannel($results2[0]['lives_in'], $results2[0]['studied_at']);
  }
  if ($payload == "contactos")
  {
    $functions->sendTyping();
    $functions->showContacts(0);
  }
  
  if ($code2 == "contact")
  {
    $functions->sendTyping();
    $functions->showContacts($ganadorIdContacto);
  }
  if ($code2 == "channelChange")
  {
    //if they choose a null channel:
    if ($ganadorIdContacto != null)
    {
      //is ganadorIdContacto is the new channel
      $functions->sendTyping();
      $functions->changeChannel2($ganadorIdContacto);    
    } else{
      $functions->sendTyping();
      $replies = array ("Esa opción no es valida. Elije otro canal.");
      $functions->sendTextMessage($replies);
      $functions->changeChannel($results2[0]['lives_in'], $results2[0]['studied_at']);
    }

  }


if (($results2[0]['inte1'] == null || $results2[0]['inte2'] == null || $results2[0]['inte3'] == null || $payload == "borrar") && $results2[0]['fb_id'] != null)
  {
    $functions->questionsAssign($code2, $results2[0]['inte1'], $results2[0]['inte2'], $results2[0]['inte3'], $message, $ganadorIdContacto);
    $message = null;
  }

//if the don't have gender register on Facebook
$flagNoGender = false;
if ($results2[0]['gender'] == 2)
{
	$flagNoGender = true;
  if ($payloadParaContacto == null && $message != null) //means that I haven't ask them for the gender
  {
    $functions->askGender();
  }else{
    $functions->assignGender($payloadParaContacto);
    $flagNoGender = false;
  }
}

//gays = 1, lesbianas=2, heter=0, pero lo pongo como 0 para probar
  if (($results2[0]['gender'] == 1 || $results2[0]['sexual_orientation'] == 1 || $results2[0]['sexual_orientation'] == 2) && $results2[0]['fb_id'] != null && $messageToContact == null && $payload != "cambiarsex" && $payloadParaContacto != "sexhombres" && $payloadParaContacto != "sexmujeres" && $results2[0]['inte3'] != null && $code2 != "contact")
  {
      //universal response whenever isn't another key message
      if ($message != null && $message != "Seguir Jugando" && $message != "Jugar" && $message != "Agregar a contactos" && $message != "Empezar" && $message != "Get Started ") 
      {
        $functions->sendTyping();
        $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
        $functions->preguntaMensaje($replies);
        //$functions->sendLogin();
      } 
      //choose the winner and we ask what to do
      if ($code == "gano") 
      {
        $functions->sendTyping();
        $replies = array ("¡Buena elección! Qué quieres hacer:", "Ese era mi preferido! Ahora qué hacemos:", "¡Tienes buenos gustos! Lo agregamos a tus contactos?");
        $functions->askContact($replies, $ganadorId, $perdedorId);
        $functions->saveGame($ganadorId, $perdedorId);
      }
      //send the 2 photos with a winner choosen before
      if ($message == "Seguir Jugando")
      {
        $functions->sendTyping();
        $functions->newGame();
      }
      //play a new game
      if ($message == "Jugar" || $payload == "jugar")
      {
        $functions->sendTyping();
        $functions->newGame();
      }
      //Contact the user 
      if ($message == "Agregar a contactos") 
      {
        $functions->sendTyping();
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
    if($message != null && $results2[0]['fb_id'] != null && $message != "Puntaje 🏆" && $messageToContact == null && $payload != "cambiarsex" && $payloadParaContacto != "sexhombres" && $payloadParaContacto != "sexmujeres" && $results2[0]['inte3'] != null && $code2 != "contact" && $flagNoGender == false)
    {
      $functions->sendTyping();
      $replies = array ("Tú tranquilo, te avisaré cuando alguna chica te contacte 👌 ", "Ahora te toca esperar... 😉");
      $functions->sendTextMessage($replies);
      $replies = array ("Puedes revisar como vas aquí: ");
      $functions->preguntaMensajePuntaje($replies);
      $functions->sendLogin();
    }
    if ($message == "Puntaje 🏆" || $payload == "puntaje")
    {
      $functions->sendTyping();
      $functions->score();
     
    }
  }
//change sexual orientation
  if ($payload == "cambiarsex")
  {
      $functions->sendTyping();
      $functions->preguntaOrientacionSexual();
  }

  if ($payloadParaContacto == "sexhombres" || $payloadParaContacto == "sexmujeres")
  {
      if($results2[0]["gender"] == 0 && $payloadParaContacto == "sexhombres")
        {
          $functions->sendTyping();
          $replies = array ("Ahora las reglas cambian 😱 Vas a poder ver a hombres que también le interesan hombres y ellos también te van a poder ver a ti.");
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(1);
          $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
          $functions->preguntaMensaje($replies);
          
        }
        if($results2[0]["gender"] == 0 && $payloadParaContacto == "sexmujeres")
        {
          $functions->sendTyping();
          $replies = array ("Tú tranquilo, te avisaré cuando alguna chica te contacte 👌 ", "Ahora te toca esperar... 😉 ");          
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(0);
        }
        if($results2[0]["gender"] == 1 && $payloadParaContacto == "sexmujeres")
        {
          $functions->sendTyping();
          $replies = array ("Ahora las reglas cambian 😱 Vas a poder ver a mujeres que también le interesan mujeres y ellas también te van a poder ver a ti.");
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(2);
          $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
          $functions->preguntaMensaje($replies);
        }
        if($results2[0]["gender"] == 1 && $payloadParaContacto == "sexhombres")
        {
          $functions->sendTyping();
          $replies = array ("Ahora nadie podrá ver tus fotos y veras a hombres que les interesan las mujeres 😏");
          $functions->sendTextMessage($replies); 
          $functions->changeSexualOrientationDb(0);
          $replies = array ("Que onda ".$results2[0]["first_name"].", ya podemos comenzar 🎉", "".$results2[0]["first_name"].", que te parece si empezamos ;)", "Estás lista?? 😉");
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
   $functions->sendTyping();
   $replies = array ("Cuidado con esa boquita", "Con esa boca saludas a tu mamá?");
   $functions->sendTextMessage($replies);
  }
