<?php
//header("Location: https://de1430db.ngrok.io/tecmatch/index.php",TRUE,301);
$challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];
        // Set this Verify Token Value on your Facebook App
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

$urlWebhook = "https://eb795afd.ngrok.io/tecmatch/";
$functions = new Functions($rid, $message, $urlWebhook);
$connectiondb = new ConnectionDb();
$replies = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");

//para hacer pruebas
if ($message != null)
{
  //$functions->sendLogin();
  $query = "select fb_id, first_name, fb_sender_id, profile_pic from Users where gender = 0 AND fb_id IS NOT NULL";
  $results = $connectiondb->Connection($query);
  $functions->sendGenericMessage($results, 10158034253445612);
  
}


/*
echo $payloadParaContacto;
list ($code, $ganadorId) = split ('/',$payload);
list ($code2, $ganadorIdContacto) = split ('/',$payloadParaContacto);
echo $ganadorIdContacto;

//Para saber si ponemos el login
 try {
    $pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
  } catch (PDOException $e) {

    echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
    exit;
  }
  $statement = $pdo->prepare('select fb_id, first_name, gender from Users where fb_sender_id='.$rid);
  $statement-> execute();
  $results_login = $statement->fetchAll(PDO::FETCH_OBJ);
  $results2 = json_decode(json_encode($results_login), true);

  if ($payload == "getstarted")
  {
    $messageData = "{
    'recipient': {
      'id': $rid
    },
    'message':{    
      'text': 'Tec Match esta basado en la inversión de roles. Las mujeres entran a un juego donde deciden cual les gusta más entre 2 hombres, después de eso ellas deciden si quieren contactarlo o no. A los hombres les toca esperar a ser contactados.'
    }
    }";
    sendTyping($rid);
    callSendApi($messageData);
    mandarloLogin($rid);
  }

  if ($results2[0]['gender'] == 1)
  {
      if ($message != null && $message != "Seguir Jugando" && $message != "Jugar" && $message != "Contactarlo" && $message != "Empezar" && $message != "Get Started ") {
        $respuesta = 6;
      } 
      if ($code == "gano") //eligio al ganador
      {
        $respuesta = 1;
      }
      if ($message == "Seguir Jugando") //eligio que quiere seguir jugando
      {
        $ganadorId = $data['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
       
        $respuesta = 2;
      }
      if (strpos($message, 'Empezar') || strpos($message, 'Get Started'))
      {
        $respuesta = 3;
      }
      if ($results2[0]['fb_id'] == null && ($message != null || $message == "Empezar" || $message == "Get Started ")) //después utilizar el payload que te manda el get started
      {
         
        if ($rid != 1389633457747909)
        {
         $respuesta = 3;
        }

      }
      if ($message == "Jugar")
      {
        $respuesta = 4;
      }
      if ($message == "Contactarlo") //tengo que validar esto en el futuro, porque si el usuario pone solo "contactarlo" ser hara un desmadre
      {
        $respuesta = 5;
      }
  }else{
    if($message != null)
    {
      $respuesta = 8;
    }
  }

  if (strpos($message, 'puto') || strpos($message, 'pendeja') || strpos($message, 'puta') || strpos($message, 'pinche') || strpos($message, 'cabron') || strpos($message, 'pendejo') || strpos($message, 'culo') || strpos($message, 'mames'))
{
  $respuesta = 7;
}
  if ($results2[0]['fb_id'] == null && ($message != null || $message == "Empezar" || $message == "Get Started ")) //después utilizar el payload que te manda el get started
      {
         
        if ($rid != 1389633457747909)
        {
         $respuesta = 3;
        }

      }

switch ($respuesta) {
  case 1:
    sendTyping($rid);
    $messageText = array ("¡Buena elección! Qué quieres hacer:", "Ese era mi preferido! Ahora qué hacemos:", "¡Tienes buenos gustos! Lo contactamos?");
    preguntaVolver($rid, $ganadorId, $messageText);
    break;
  case 2:
    sendTyping($rid);
    $messageText = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");
    sendTextMessage($rid, $messageText);
    sendGenericMessage($rid, $ganadorId);
  break;
  case 3:
    sendTyping($rid);
    mandarloLogin($rid);
  break;
  case 4:
    sendTyping($rid);
    $messageText = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");
    sendTextMessage($rid, $messageText);
    jugarNuevo($rid);
  break;
  case 5:
    sendTyping($rid);
    contactar ($rid, $ganadorIdContacto);
  break;
  case 6:
    sendTyping($rid);
    //mandarloLogin($rid);
    preguntaMensaje($rid, $results2[0]['first_name']);
  break;
  case 7:
    sendTyping($rid);
    $messageText = array ("Cuidado con esa boquita", "Con esa boca saludas a tu mamá?", "Cuidado con esa boquita");
    echo $rid;
    echo "esta vacio";
    sendTextMessage($rid, $messageText);
  break;
  case 8:
    sendTyping($rid);
    $messageText = array ("Tú tranquilo, te avisaremos cuando alguna chica te contacte ;) ", "Ahora te toca esperar... ;) ","Ahora te toca esperar... ;) ");
    sendTextMessage($rid, $messageText);
    //mandarloLogin($rid); //lo uso a veces para probar el login
  break;
  default:
    # code...
    break;
}

//insertUser($first_name, $last_name, $profile_pic, $locale, $genderInt, $rid);


function mandarloLogin ($rid)
{
  echo $rid;
  $url = "https://c0050472.ngrok.io/tecmatch/login/LP_tecmatch.php?id=$rid";
  $messageData = '{
    "recipient":{
      "id": '.$rid.'
    },
    "message":{
      "attachment":{
        "type":"template",
        "payload":{
          "template_type":"button",
          "text":"Necesitamos que te registres por Facebook. No te preocupes no publicaremos nada ni compartiremos tu información sin tu consentimiento.",
          "buttons":[
            {
              "type":"web_url",
              "url":"'.$url.'",
              "title":"Hacer login"
            }
          ]
        }
      }
    }
  }';
  callSendApi($messageData);
}


function insertUser ($first_name, $last_name, $profile_pic, $locale, $genderInt, $rid)
{
  global $db_host, $db_name, $db_username, $db_pass;
  try {
      $pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
    } catch (PDOException $e) {

      echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
      exit;
    }
    //select sí jala
    $statement = $pdo->prepare('select * from Users where fb_sender_id = '.$rid);
    $statement-> execute();
    $results = $statement->fetchAll(PDO::FETCH_OBJ);

  if ($results[0]==null)
  {
   //Insert sí jala
      $statement = $pdo->prepare("INSERT INTO Users(first_name, last_name, fb_sender_id, profile_pic, locale, gender)
          VALUES(?,?,?,?,?,?)");
      //var_dump($statment);
      $statement->execute(array($first_name, $last_name, $rid, $profile_pic, $locale, $genderInt)); 
  }
}


function preguntaVolver ($rid, $ganadorId, $reply)
{
  $messageData = "{
    'recipient':{
      'id': $rid
    },
    'message':{
      'text':'".$reply[rand(0,2)]."',
      'quick_replies':[
        {
          'content_type':'text',
          'title':'Contactarlo',
          'payload':'contacto/".$ganadorId."'
        },
        {
          'content_type':'text',
          'title':'Seguir Jugando',
          'payload':'".$ganadorId."'
        }
      ]
    }
  }";
  callSendApi($messageData);
}


function contactar ($rid, $ganadorId)
{
  global $db_host, $db_name, $db_username, $db_pass;
  $messageText = array ("Ya lo contacté, te aviso si me dice algo de ti. Mientras tú sigue jugando!", "Le mandé un mensaje, veamos a ver si contesta. Vamos a seguir jugando!", "Ya le mandé un mensaje, si vale la pena el te va a contactar.");
  sendTextMessage ($rid, $messageText);
  $messageText2 = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "A cuál de estos le tomarías screenshot a sus conversaciones?");
  sendTextMessage($rid, $messageText2);
  jugarNuevo($rid);

  //Lo que se le va a mandar al ganador:
  try {
    $pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);

  } catch (PDOException $e) {

    echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
    exit;
  }
  $statement = $pdo->prepare('select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_id = '.$ganadorId);
  $statement-> execute();
  $results = $statement->fetchAll(PDO::FETCH_OBJ);
  $results3 = json_decode(json_encode($results), true);

  $fb_sender_id_ganador = $results3[0]['fb_sender_id'];
  $first_name2 = $results3[0]['first_name'];

  try {
    $pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
  } catch (PDOException $e) {

    echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
    exit;
  }
  $statement = $pdo->prepare('select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id = '.$rid);
  $statement-> execute();
  $results = $statement->fetchAll(PDO::FETCH_OBJ);
  $results2 = json_decode(json_encode($results), true);

  $fb_id1 = $results2[0]['fb_id'];
  $first_name1 = $results2[0]['first_name'];
  $fb_sender_id1 = $results2[0]['fb_sender_id'];
  $profile_pic1 = $results2[0]['profile_pic'];


  $messageData = "{
    'recipient': {
      'id': $fb_sender_id_ganador
    },
    'message':{
      'attachment':{
        'type':'template',
        'payload':{
          'template_type': 'generic',
          'elements': [{
            'title': '".$first_name1."',
          
            'image_url':'".$profile_pic1."',
            'item_url': 'https://www.facebook.com/".$fb_id1."',
            'buttons': [{
                'type':'web_url',
                'url':'https://facebook.com/".$fb_id1."',
                'title':'Contactar!'
            }
            ]  
          }
          ]
        }
      }
    }
 }";

  $messageText = array ("Que onda ".$first_name2."! Mira, ".$first_name1." te quiere conocer! Ella ya dio el primer paso te toca a ti! Entra a su perfil y mándale un mensaje. Ella esta esperando tu mensaje. ;)","Que onda ".$first_name2."! Le interesas a ".$first_name1.". Ella quiere que le escribas. Entra a su perfil y mándale un mensaje. ;)", "Oye galán, andas con todo! ".$first_name1." quiere que le escribas. Entra a su perfil y mándale un mensaje. ;)");
  sendTextMessage($fb_sender_id_ganador, $messageText); 
  callSendApi($messageData);
}


function preguntaMensaje ($rid, $first_name)
{  
  $messageText = array ("Que onda ".$first_name.", esto es lo que puedo hacer:", "".$first_name.", que te parece si empezamos");
  $messageData = "{
    'recipient':{
      'id': $rid
    },
    'message':{
      'text':'".$messageText[rand(0,1)]."',
      'quick_replies':[
        {
          'content_type':'text',
          'title':'Jugar',
          'payload':'nada'
        }
      ]
    }
  }";
  callSendApi($messageData);
}

function jugarNuevo ($rid)
{
  global $db_host, $db_name, $db_username, $db_pass;
  try {
    $pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
  } catch (PDOException $e) {

    echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
    exit;
  }
  $statement = $pdo->prepare('select fb_id, first_name, fb_sender_id, profile_pic from Users where gender = 0 AND fb_id IS NOT NULL');
  $statement-> execute();
  $results = $statement->fetchAll(PDO::FETCH_OBJ);
  $results2 = json_decode(json_encode($results), true);

  $num_results2 = count($results2);
  do{
  $num1 = rand (0, ($num_results2-1));
  $num2 = rand (0, ($num_results2-1));
  } while ($num1 == $num2 || $rid == $results2[$num1]['fb_sender_id'] || $rid == $results2[$num2]['fb_sender_id']); //para que no se repitan y que no salga el usuario

  $fb_id1 = $results2[$num1]['fb_id'];
  echo "antes antes";
  echo $results2[$num1]['fb_id'];
  echo "aquí estass";
  $first_name1 = $results2[$num1]['first_name'];
  $fg_sender_id1 = $results2[$num1]['fb_sender_id'];
  $profile_pic1 = $results2[$num1]['profile_pic'];

  $fb_id2 = $results2[$num2]['fb_id'];
  echo $results2[$num2]['fb_id'];
  $first_name2 = $results2[$num2]['first_name'];
  $fg_sender_id2 = $results2[$num2]['fb_sender_id'];
  $profile_pic2 = $results2[$num2]['profile_pic'];
 
  $messageData = "{
    'recipient': {
      'id': $rid
    },
    'message':{
      'attachment':{
        'type':'template',
        'payload':{
          'template_type': 'generic',
          'elements': [{
            'title': '".$first_name1."',
          
            'image_url':'".$profile_pic1."',
            'item_url': 'https://www.facebook.com/".$fb_id1."',
            'subtitle':'Haz click para entrar a su perfil',
            'buttons': [{
              'type':'postback',
              'title':'Ganador',
              'payload': 'gano/".$fb_id1."'
            }
            ]  
          },
          {
            'title':'".$first_name2."',
          
            'image_url':'".$profile_pic2."',
            'item_url': 'https://www.facebook.com/".$fb_id2."',
            'subtitle':'Haz click para entrar a su perfil',
            'buttons': [{
              'type':'postback',
              'title':'Ganador',
              'payload': 'gano/".$fb_id2."'
            }
            ]  
          }
          ]
        }
      }
    }
 }";
 callSendApi($messageData);
}


function sendGenericMessage($rid, $ganadorId) {
  global $db_host, $db_name, $db_username, $db_pass;
  try {
      $pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
    } catch (PDOException $e) {

  echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
  exit;
  }
  $statement = $pdo->prepare('select fb_id, first_name, fb_sender_id, profile_pic from Users where gender = 0 AND fb_id IS NOT NULL');
  $statement-> execute();
  $results = $statement->fetchAll(PDO::FETCH_OBJ);
  $results2 = json_decode(json_encode($results), true);

  $num_results2 = count($results2);
  do{
  $num1 = rand (0, ($num_results2-1));
  } while ($results2[$num1]['fb_id'] == $ganadorId || $rid == $results2[$num1]['fb_sender_id']);

  $fb_id1 = $results2[$num1]['fb_id'];
  $first_name1 = $results2[$num1]['first_name'];
  $fg_sender_id1 = $results2[$num1]['fb_sender_id'];
  $profile_pic1 = $results2[$num1]['profile_pic'];

  $ganadorId2 = (string)$ganadorId;
 $statement = $pdo->prepare('select first_name, fb_sender_id, profile_pic from Users where fb_id ='.$ganadorId2);
  $statement-> execute();
  $results = $statement->fetchAll(PDO::FETCH_OBJ);
  $results3 = json_decode(json_encode($results), true);

  $fb_id2 = $ganadorId;
  $first_name2 = $results3[0]['first_name'];
  $fg_sender_id2 = $results3[0]['fb_sender_id'];
  $profile_pic2 = $results3[0]['profile_pic'];

  $messageData = "{
    'recipient': {
      'id': $rid
    },
    'message':{
      'attachment':{
        'type':'template',
        'payload':{
          'template_type': 'generic',
          'elements': [{
            'title': '".$first_name1."',
          
            'image_url':'".$profile_pic1."',
            'item_url': 'https://www.facebook.com/".$fb_id1."',
            'subtitle':'Haz click para entrar a su perfil',
            'buttons': [{
              'type':'postback',
              'title':'Ganador',
              'payload': 'gano/".$fb_id1."'
            }
            ]  
          },
          {
            'title':'".$first_name2."',
          
            'image_url':'".$profile_pic2."',
            'item_url': 'https://www.facebook.com/".$fb_id2."',
             'subtitle':'Haz click para entrar a su perfil',
            'buttons': [{
              'type':'postback',
              'title':'Ganador',
              'payload': 'gano/".$fb_id2."'
            }
            ]  
          }
          ]
        }
      }
    }
 }";
callSendApi($messageData);
}


function sendTextMessage($recipientId, $reply)
{
$messageData = "{
    'recipient': {
      'id': $recipientId
    },
    'message':{    
      'text': '".$reply[rand(0,2)]."'
    }
    }";
  callSendApi($messageData);
}

function sendTyping ($rid)
{
    $messageData = "{
    'recipient':{
      'id':$rid
    },
    'sender_action':'typing_on'
  }";
  callSendApi($messageData);
}

function callSendApi ($messageData)
{
  $token ="EAAIUReNE8dkBAMMYqXANPKSsiGvXQHSCIZA5UZAKB3pYtQK1l4MItZCcw4Ko4ipZB1qJxg7Uiabc6US77CboUezlvVtZBq7oFNRB1J3lIDgbrEfq3wHZBkNiMd1R1G5Xq9ojKB8UZCBHK0jjfXYQNZA6U9qzFY0QCD6iQZBsRqFJy9AZDZD";
	//$context = stream_context_create($options);
	//file_get_contents("https://graph.facebook.com/v2.6/me/messages?access_token=$token",false, $context);
 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
 $result = curl_exec($ch);
 curl_close($ch);
    
}