<?php

	//$db_using = "mysql:host=tecmatch.co;dbname=tecmatch_tecmatchdb","tecmatch_user","Tecmatch88";
  //BD real
/*
  $db_host = "tecmatch.co";
  $db_name = "tecmatch_tecmatchdb";
  $db_username = "tecmatch_user";
  $db_pass = "Tecmatch88";
*/

  //BD de testing

  $db_host = "localhost";
  $db_name = "test_TecMatch";
  $db_username = "root";
  $db_pass = "root";

  $url_using = "https://eb795afd.ngrok.io";

	# Autoload the required files
	require_once __DIR__ . '/vendor/autoload.php';
	//include '../index.php';

	$rid = $_GET['id'];
	echo "todo bien1";
	# Set the default parameters
	$fb = new Facebook\Facebook([
	  'app_id' => '585240351666649',
	  'app_secret' => '0c360663f24dec79e8428e58cc2069ee',
	  'default_graph_version' => 'v2.6',
	]);
	$redirect = "https://www.messenger.com/t/1827124694175123";
	# Create the login helper object
	$helper = $fb->getRedirectLoginHelper();

	
	$app_id = '585240351666649';
	$app_secret = '0c360663f24dec79e8428e58cc2069ee';
	$my_url = "https://eb795afd.ngrok.io/tecmatch/login/prueba.php?id=$rid";
	$code = $_GET['code'];
echo "esta es la id: ".$rid;
$token_url = "https://graph.facebook.com/oauth/access_token?"
. "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
. "&client_secret=" . $app_secret . "&code=" . $code;
echo $token_url;

$response = file_get_contents($token_url);
echo "estos es:".$response;
$params = null;
parse_str($response, $params);
$accessToken = $params['access_token'];

	  	// Logged in!
	 	// Now you can redirect to another page and use the
  		// access token from $_SESSION['facebook_access_token'] 
  		// But we shall we the same page
		// Sets the default fallback access token so 
		// we don't have to pass it to each request
    echo "estos es:".$response;
		$fb->setDefaultAccessToken($accessToken);

    echo "todo bien1.1";
		try {
		  $response = $fb->get('/me?fields=email,name,first_name, last_name, locale, gender');
		  $userNode = $response->getGraphUser();
		}catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
    echo "todo bien1.2";
		// Print the user Details
		/*
		$complete_name = $userNode->getName();
		$complete_name_array = explode (" ", $complete_name);
		$first_name = $complete_name_array[0];
		$last_name = $complete_name_array[1];*/


		$first_name = $userNode->getfirstname();
		$last_name = $userNode->getlastname();		
		$profile_pic = 'https://graph.facebook.com/'.$userNode->getId().'/picture?width=200';
		$gender = $userNode->getgender();
		//poner un int a los genders para que sea más fácil manejarlos 0=male, 1=female, 2=cualquier otro
		if ($gender == 'male')
		{
  			$genderInt = 0;
		} else if ($gender == "female")
		{
  		  $genderInt = 1;
		}else{
  			$genderInt = 2;
		}
		$fb_id = $userNode->getId();
		$locale = null;

		insertUser($first_name, $last_name, $profile_pic, $locale, $genderInt, $rid, $fb_id);
		
    echo "todo bien2";

		//sendGenericMessage ($rid, null);
    
    if ($genderInt == 1)
    {
      sendTextMessage($rid);
		  callSendApi ($rid);
    }

    if($genderInt == 0)
    {
     $reply = array ("Tú tranquilo, te avisaremos cuando alguna chica te contacte ;) ", "Ahora te toca esperar... ;) ","Ahora te toca esperar... ;) ");
     $messageData = "{
      'recipient': {
        'id': $rid
      },
      'message':{    
        'text': '".$reply[rand(0,2)]."'
      }
      }";
        $token ="EAAIUReNE8dkBACMj2EnYYr6RrTUjWeUddynExMbE2Rfs5McgtkUfZCa8knt8jpZCMpE3JeMOsg8UQmDdwrHaOhXfjEpYEQ7T75C3sKbtevJo3822ftohFP4doD2z3Wx587abqFBcOu1ANoONhTEol8KNuzl6p4QWBGYNg0ZCwZDZD";
        $url2 = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
        $ch = curl_init($url2);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);   
    }
		$url='https://www.messenger.com/t/1827124694175123';
  		echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';


function sendTextMessage($recipientId)
{

$messageText = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");  
$messageData = "{
    'recipient': {
      'id': $recipientId
    },
    'message':{    
      'text': '".$messageText[rand(0,2)]."'
  	}
	}";
    $token ="EAAIUReNE8dkBACMj2EnYYr6RrTUjWeUddynExMbE2Rfs5McgtkUfZCa8knt8jpZCMpE3JeMOsg8UQmDdwrHaOhXfjEpYEQ7T75C3sKbtevJo3822ftohFP4doD2z3Wx587abqFBcOu1ANoONhTEol8KNuzl6p4QWBGYNg0ZCwZDZD";
 	$url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
 	$ch = curl_init($url);
 	curl_setopt($ch, CURLOPT_POST, 1);
 	curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
 	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
 	$result = curl_exec($ch);
 	curl_close($ch);   
}

function callSendApi ($rid)
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
	} while ($num1 == $num2 || $rid == $results2[$num1]['fb_sender_id'] || $rid == $results2[$num2]['fb_sender_id']);

  $fb_id1 = $results2[$num1]['fb_id'];
  $first_name1 = $results2[$num1]['first_name'];
  $fg_sender_id1 = $results2[$num1]['fb_sender_id'];
  $profile_pic1 = $results2[$num1]['profile_pic'];

  $fb_id2 = $results2[$num2]['fb_id'];
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

    $token ="EAAIUReNE8dkBACMj2EnYYr6RrTUjWeUddynExMbE2Rfs5McgtkUfZCa8knt8jpZCMpE3JeMOsg8UQmDdwrHaOhXfjEpYEQ7T75C3sKbtevJo3822ftohFP4doD2z3Wx587abqFBcOu1ANoONhTEol8KNuzl6p4QWBGYNg0ZCwZDZD";
 	$url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
 	$ch = curl_init($url);
 	curl_setopt($ch, CURLOPT_POST, 1);
 	curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
 	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
 	$result = curl_exec($ch);
 	curl_close($ch);   
}

function insertUser ($first_name, $last_name, $profile_pic, $locale, $genderInt, $rid, $fb_id)
	{
  echo "todo bien3";
  global $db_host, $db_name, $db_username, $db_pass;
	try {
    	$pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
  	} catch (PDOException $e) {

    	echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
    	exit;
  	}
  	//select sí jala
  	$statement = $pdo->prepare('select * from Users where fb_id = '.$fb_id);
  	$statement-> execute();
  	$results = $statement->fetchAll(PDO::FETCH_OBJ);

	if ($results[0]==null)
	{
 	//Insert sí jala
    	$statement = $pdo->prepare("INSERT INTO Users(fb_id, first_name, last_name, fb_sender_id, profile_pic, locale, gender)
        	VALUES(?,?,?,?,?,?,?)");
    	//var_dump($statment);
    	$statement->execute(array($fb_id, $first_name, $last_name, $rid, $profile_pic, $locale, $genderInt)); 
	}
}

