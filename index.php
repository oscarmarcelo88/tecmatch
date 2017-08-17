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
//include_once 'files/langES.php';

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
  $query = 'select fb_id, first_name, locale, gender, block, sexual_orientation, lives_in, studied_at, location, inte1, inte2, inte3, created_at from Users where fb_sender_id='.$rid;
  $results = $connectiondb->Connection($query);
  $results2 = json_decode(json_encode($results), true);

  //decide the language with locale
  if($results2[0]['locale'] == "es_LA" || $results2[0]['locale'] == "es_ES")
  {
      include_once 'files/langES.php';
  }else{
      include_once 'files/langEN.php';
  }

  $functions = new Functions($rid, $message, $urlWebhook, $results2[0]['sexual_orientation'], $results2[0]['location'], $results2[0]['first_name'], $results2[0]['gender'], $token);

  //unblock it if it's block, because they receive a message delivery
var_dump($results2);
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
        $replies = array ($lang['WELCOME_WOMEN_1_1/2'].$results2[0]['first_name'].$lang['WELCOME_WOMEN_1_2/2']);
        $functions->sendTextMessage($replies);
        $replies = array ($lang['WELCOME_WOMEN_2']);
        $functions->sendTextMessage($replies);
      }else {
        $functions->sendTyping();
        $replies = array ($lang['WELCOME_MALE_1_1/2'].$results2[0]['first_name'].$lang['WELCOME_MALE_1_2/2']);
        $functions->sendTextMessage($replies);
        $replies = array ($lang['WELCOME_MALE_2']);
        $functions->sendTextMessage($replies);
      }
    }

//if they don't have fb_id they need to do login
  if (($results2[0]['fb_id'] == null && $message != null) || $payload == "getstarted")
      {
        $functions->sendTyping();
        //$functions->sendTextMessage($results2[0]['first_name']);
        $functions->sendLogin($lang['LOGIN_DESCRIPTION'], $lang['LOGIN_OPTION']);
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
    $functions->changeChannel($results2[0]['lives_in'], $results2[0]['studied_at'], $lang['CHANGE_CHANNEL']);
  }
  if ($payload == "contactos")
  {
    $functions->sendTyping();
    $functions->showContacts(0, $lang['CONTACTS']);
  }
  
  if ($code2 == "contact")
  {
    $functions->sendTyping();
    $functions->showContacts($ganadorIdContacto, $lang['CONTACTS']);
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
      $replies = array ($lang['NOCHANNEL_AVAILABLE']);
      $functions->sendTextMessage($replies);
      $functions->changeChannel($results2[0]['lives_in'], $results2[0]['studied_at'], $lang['CHANGE_CHANNEL']);
    }

  }


if (($results2[0]['inte1'] == null || $results2[0]['inte2'] == null || $results2[0]['inte3'] == null || $payload == "borrar") && $results2[0]['fb_id'] != null)
  {
    $functions->questionsAssign($code2, $results2[0]['inte1'], $results2[0]['inte2'], $results2[0]['inte3'], $message, $ganadorIdContacto, $lang['QUESTIONARIE1'], $lang['QUESTIONARIE2'], $lang['QUESTIONARIE3'], $lang['QUESITON_ASSIGN']);
    $message = null;
  }

//if the don't have gender register on Facebook
$flagNoGender = false;
if ($results2[0]['gender'] == 2)
{
	$flagNoGender = true;
  if ($payloadParaContacto == null && $message != null) //means that I haven't ask them for the gender
  {
    $functions->askGender($lang['ASKGENDER']);
  }else{
    $functions->assignGender($payloadParaContacto);
    $flagNoGender = false;
  }
}

//gays = 1, lesbianas=2, heter=0, pero lo pongo como 0 para probar
  if (($results2[0]['gender'] == 1 || $results2[0]['sexual_orientation'] == 1 || $results2[0]['sexual_orientation'] == 2) && $results2[0]['fb_id'] != null && $messageToContact == null && $payload != "cambiarsex" && $payloadParaContacto != "sexhombres" && $payloadParaContacto != "sexmujeres" && $results2[0]['inte3'] != null && $code2 != "contact")
  {
      //universal response whenever isn't another key message
      if ($message != null && $message != $lang['OPTION1'] && $message != $lang['OPTION2'] && $message != $lang['OPTION3'] && $message != $lang['OPTION4'] && $message != $lang['OPTION5']) 
      {
        $functions->sendTyping();
        $replies = array ($lang['LETS_START'][0].$results2[0]["first_name"].$lang['LETS_START'][1], "".$results2[0]["first_name"].$lang['LETS_START'][2], $lang['LETS_START'][3]);
        $functions->preguntaMensaje($replies, $lang['OPTION2']);
        //$functions->sendLogin($lang['LOGIN_DESCRIPTION'], $lang['LOGIN_OPTION']);

      } 
      //choose the winner and we ask what to do
      if ($code == "gano") 
      {
        $functions->sendTyping();
        $functions->askContact($lang['WINNER_MSG'], $ganadorId, $perdedorId, $lang['ASK_PLAY_ADD']);
        $functions->saveGame($ganadorId, $perdedorId);
      }
      //send the 2 photos with a winner choosen before
      if ($message == $lang['OPTION1'])
      {
        $functions->sendTyping();
        $functions->newGame($lang['NEWGAME'], $lang['NEWGAME_bio']);
      }
      //play a new game
      if ($payloadParaContacto == "Jugar")
      {
        $functions->sendTyping();
        $functions->newGame($lang['NEWGAME'], $lang['NEWGAME_bio']);
      }
      //Contact the user 
      if ($code2 == "addcontact")
      {
        $functions->sendTyping();
        $functions->changeRelationship($ganadorIdContacto, $perdedorIdContacto);
        $functions->contact($ganadorIdContacto, $lang['CONTACT_USER']); 
        $query = "select nickname2 from Games WHERE ganadorId =".$ganadorIdContacto." AND jugadorId =".$rid;
        $results_contacto = $connectiondb->Connection($query);
        $results_contacto2 = json_decode(json_encode($results_contacto), true);
        $replies = array ($lang['ADDCONTACT_1/2'].$results_contacto2[0]['nickname2'].$lang['ADDCONTACT_2/2']);
        $functions->sendTextMessage($replies);
        $functions->newGame($lang['NEWGAME'], $lang['NEWGAME_bio']);
      }
  }else{
    if($message != null && $results2[0]['fb_id'] != null && $payloadParaContacto != "puntaje" && $messageToContact == null && $payload != "cambiarsex" && $payloadParaContacto != "sexhombres" && $payloadParaContacto != "sexmujeres" && $results2[0]['inte3'] != null && $code2 != "contact" && $flagNoGender == false)
    {
      $functions->sendTyping();
      $functions->sendTextMessage($lang['WAITING_MSG']);
      $functions->preguntaMensajePuntaje($lang['CHECK_SCORE'], $lang['OPTION6']);
      //TESTING:
      //$functions->sendLogin($lang['LOGIN_DESCRIPTION'], $lang['LOGIN_OPTION']);
    }
    if ($payloadParaContacto == "puntaje")
    {
      $functions->sendTyping();
      $functions->score($lang['SCORE']);
     
    }
  }
//change sexual orientation
  if ($payload == "cambiarsex")
  {
      $functions->sendTyping();
      $functions->preguntaOrientacionSexual($lang['ASKGENDER']);
  }

  if ($payloadParaContacto == "sexhombres" || $payloadParaContacto == "sexmujeres")
  {
      if($results2[0]["gender"] == 0 && $payloadParaContacto == "sexhombres")
        {
          $functions->sendTyping();
          $functions->sendTextMessage($lang['CHANGESEX_GAY_MALE']); 
          $functions->changeSexualOrientationDb(1);
          $replies = array ($lang['LETS_START'][0].$results2[0]["first_name"].$lang['LETS_START'][1], "".$results2[0]["first_name"].$lang['LETS_START'][2], $lang['LETS_START'][3]);
          $functions->preguntaMensaje($replies,$lang['OPTION2']);
          
        }
        if($results2[0]["gender"] == 0 && $payloadParaContacto == "sexmujeres")
        {
          $functions->sendTyping();
          $functions->sendTextMessage($lang['CHANGESEX_HETERO_MALE']); 
          $functions->changeSexualOrientationDb(0);
        }
        if($results2[0]["gender"] == 1 && $payloadParaContacto == "sexmujeres")
        {
          $functions->sendTyping();
          $functions->sendTextMessage($lang['CHANGESEX_GAY_FEMALE']); 
          $functions->changeSexualOrientationDb(2);
          $replies = array ($lang['LETS_START'][0].$results2[0]["first_name"].$lang['LETS_START'][1], "".$results2[0]["first_name"].$lang['LETS_START'][2], $lang['LETS_START'][3]);
          $functions->preguntaMensaje($replies,$lang['OPTION2']);
        }
        if($results2[0]["gender"] == 1 && $payloadParaContacto == "sexhombres")
        {
          $functions->sendTyping();
          $functions->sendTextMessage($lang['CHANGESEX_HETERO_FEMALE']); 
          $functions->changeSexualOrientationDb(0);
          $replies = array ($lang['LETS_START'][0].$results2[0]["first_name"].$lang['LETS_START'][1], "".$results2[0]["first_name"].$lang['LETS_START'][2], $lang['LETS_START'][3]);
          $functions->preguntaMensaje($replies,$lang['OPTION2']);
        }
  }

  //send message to contact
  if ($messageToContact != null)
  {
      $functions->sendTextMessageToContact($nickname, $messageToContact, $lang['NOCONTACTS'], $lang['CHAT_WROTE'], $lang['CHAT_REPLY'], $lang['TEXT_CONFIRMBLOCK']);
  }

  if (strpos($message, 'puto') || strpos($message, 'pendeja') || strpos($message, 'puta') || strpos($message, 'pinche') || strpos($message, 'cabron') || strpos($message, 'pendejo') || strpos($message, 'culo') || strpos($message, 'mames'))
  {
   $functions->sendTyping();
   $functions->sendTextMessage($lang['BADWORDS']);
  }
