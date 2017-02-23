<?php

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

  $url_using = "https://717d2ec6.ngrok.io";

	# Autoload the required files
	require_once __DIR__ . '/vendor/autoload.php';
  
  include '../Functions.php';
  include '../ConnectionDb.php';

	$rid = $_GET['id'];

  $functions = new Functions($rid, $url_using);
  $connectiondb = new ConnectionDb();

	# Set the default parameters
	$fb = new Facebook\Facebook([
	  'app_id' => '585240351666649',
	  'app_secret' => '0c360663f24dec79e8428e58cc2069ee',
	  'default_graph_version' => 'v2.6',
	]);

  $redirect = "https://www.messenger.com/closeWindow/?image_url={https://717d2ec6.ngrok.io/tecmatch/login/cover.png}&display_text={Gracias por registrarte}";
	# Create the login helper object
	$helper = $fb->getRedirectLoginHelper();

	$app_id = '585240351666649';
	$app_secret = '0c360663f24dec79e8428e58cc2069ee';
	$my_url = "https://717d2ec6.ngrok.io/tecmatch/login/prueba.php?id=$rid";
	$code = $_GET['code'];

  $token_url = "https://graph.facebook.com/oauth/access_token?"
  . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
  . "&client_secret=" . $app_secret . "&code=" . $code;

  $response = file_get_contents($token_url);
  $params = null;
  parse_str($response, $params);
  $accessToken = $params['access_token'];

	$fb->setDefaultAccessToken($accessToken);

		try {
		  $response = $fb->get('/me?fields=email,name,first_name, last_name, locale, gender, location, education');
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

		$first_name = $userNode->getfirstname();
		$last_name = $userNode->getlastname();		
		$profile_pic = 'https://graph.facebook.com/'.$userNode->getId().'/picture?width=200';
		$gender = $userNode->getgender();
		$locale = $userNode["locale"];
		$location = $userNode['location']['name'];
		$email = $userNode->getemail();
	
		
		$cont = 0;
		//concatenate all the education in one string
		while ($userNode['education'][$cont]['school']['name'] != null)
		{
			$education = $education."/".$userNode['education'][$cont]['school']['name'];
			$cont ++;
		}
		
		$channel = $functions->setChannel($location, $education);
	
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

    $sexual_orientation = 0;

    $query = ('select * from Users where fb_id = '.$fb_id);
    $results = $connectiondb->Connection($query);
	insertUser($first_name, $last_name, $profile_pic, $email, $locale, $genderInt, $rid, $fb_id, $education, $location, $channel, $results, $connectiondb, $sexual_orientation);
    
    if ($genderInt == 1)
    {
      $functions->eraseInte();
	  $functions->questionsAssign(null, null, null, null, "grr", null);

	  //$replies = array("A quién prefieres?? 😏😏", "Cena en tu casa, llevarías a: ", "Con quién saldrías?? 😜😜", "Quién se te hace más guapo?? 😍😍", "Quién te gusta más??", "Quién pasaría el filtro de tus amigas?? 😳😳");
      //$functions->sendTextMessage($replies);
      //$functions->newGame();
    }

    if($genderInt == 0)
    {
      $functions->eraseInte();
      $functions->questionsAssign(null, null, null, null, "grr", null);
     /*$replies = array ("Tú tranquilo, te avisaremos cuando alguna chica te contacte ;) ", "Ahora te toca esperar... ;)");
     $functions->sendTextMessage($replies);*/
    }

  		$url = "https://www.messenger.com/closeWindow/?image_url={https://717d2ec6.ngrok.io/tecmatch/login/cover.png}&display_text={Gracias por registrarte}";
  		echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';

function insertUser ($first_name, $last_name, $profile_pic, $email, $locale, $genderInt, $rid, $fb_id, $education, $location, $channel, $results, $connectiondb, $sexual_orientation)
	{
	$pdo = $connectiondb->ConnectionReturnPDO();
	if ($results[0]==null)
	{	
    	$statement = $pdo->prepare("INSERT INTO Users(fb_id, first_name, last_name, fb_sender_id, profile_pic, email, locale, sexual_orientation, studied_at, lives_in, location, gender)
        	VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
    	$statement->execute(array($fb_id, $first_name, $last_name, $rid, $profile_pic, $email, $locale, $sexual_orientation, $education, $location, $channel, $genderInt)); 	
	}else{
		$sql = "UPDATE Users SET fb_id = :fb_id, 
        first_name = :first_name, 
        last_name = :last_name,  
        fb_sender_id = :fb_sender_id,  
        profile_pic = :profile_pic,
        email = :email,
        locale = :locale,
        sexual_orientation = :sexual_orientation, 
        studied_at = :studied_at,  
        lives_in = :lives_in, 
        location = :location,  
        gender = :gender 
        WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':fb_id', $fb_id, PDO::PARAM_STR); 
		$stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);       
		$stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);    
		$stmt->bindParam(':fb_sender_id', $rid, PDO::PARAM_STR);
		$stmt->bindParam(':profile_pic', $profile_pic, PDO::PARAM_STR); 
		$stmt->bindParam(':email', $email, PDO::PARAM_STR); 
		$stmt->bindParam(':locale', $locale, PDO::PARAM_STR);   
		$stmt->bindParam(':sexual_orientation', $sexual_orientation, PDO::PARAM_INT);   
		$stmt->bindParam(':studied_at', $education, PDO::PARAM_STR);   
		$stmt->bindParam(':lives_in', $location, PDO::PARAM_STR);   
		$stmt->bindParam(':location', $channel, PDO::PARAM_STR);   
		$stmt->bindParam(':gender', $genderInt, PDO::PARAM_INT);   
		$stmt->execute(); 
	}
}
