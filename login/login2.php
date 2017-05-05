<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
          <title>Tec Match</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {background: #FBF1E7;}
img { position: absolute; left: 50%; top: 50%; margin: -320px 0 0 -320px}
</style>
</head>
<body>
  <div>
    <img src="img/cover_2.png" alt="cover">
  </div>
</body>
</html>

<?php
	# Autoload the required files
	require_once __DIR__ . '/vendor/autoload.php';
	require '../config.php';
	include '../files/Functions.php';
    include '../files/ConnectionDb.php';

	$db_host = getenv("db_host");
	$db_name = getenv("db_name");
	$db_username = getenv("db_username");
	$db_pass = getenv("db_pass");	
 	$url_using = getenv("urlWebhook");
	$rid = $_GET['id'];

	//DB access
	$app_id = getenv("app_id");
	$app_secret = getenv("app_secret");
	$default_graph_version = getenv("default_graph_version");
	$token = getenv("token");
	
	$my_url = "".$url_using."/login/login2.php?id=$rid";
	$code = $_GET['code'];

  $functions = new Functions($rid, null, $url_using, null, null, null, null, $token);
  $connectiondb = new ConnectionDb();

	# Set the default parameters
	$fb = new Facebook\Facebook([
	  'app_id' => $app_id,
	  'app_secret' => $app_secret,
	  'default_graph_version' => $default_graph_version,
	]);

  $redirect = "https://www.messenger.com/closeWindow/?image_url={".$url_using."/login/img/cover.png}&display_text={Gracias por registrarte}";
	# Create the login helper object
	$helper = $fb->getRedirectLoginHelper();

  $token_url = "https://graph.facebook.com/oauth/access_token?"
  . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
  . "&client_secret=" . $app_secret . "&code=" . $code;

  $response = file_get_contents($token_url); 
  $params = json_decode($response);
 
  $accessToken = $params->{'access_token'}; 

	$fb->setDefaultAccessToken($accessToken);

		try {
		  $response = $fb->get('/me?fields=email,name,first_name, last_name, locale, gender, location, education');
		  $userNode = $response->getGraphUser();
		}catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an errorr: ' . $e->getMessage();
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

		date_default_timezone_set('America/Chicago'); // Set the time in CDT
		$updated_at = strtotime("now");
		$created_at = strtotime("now");
	
		
		$cont = 0;

		//$education = null;
		//concatenate all the education in one string
		while ($userNode['education'][$cont]['school']['name'] != null)
		{
			$education = $education."/".$userNode['education'][$cont]['school']['name'];
			echo $education."/".$userNode['education'][$cont]['school']['name'];
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

    $query = ('select * from Users where fb_sender_id = '.$rid);
    $results = $connectiondb->Connection($query);
	insertUser($first_name, $last_name, $profile_pic, $email, $locale, $genderInt, $rid, $fb_id, $education, $location, $channel, $results, $connectiondb, $sexual_orientation, $updated_at, $created_at);
    
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

  		$url = "https://www.messenger.com/closeWindow/?image_url={".$url_using."/login/img/cover.png}&display_text={Gracias por registrarte}";
  		echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';

function insertUser ($first_name, $last_name, $profile_pic, $email, $locale, $genderInt, $rid, $fb_id, $education, $location, $channel, $results, $connectiondb, $sexual_orientation, $updated_at, $created_at)
	{
	$pdo = $connectiondb->ConnectionReturnPDO();
	if ($results[0]==null)
	{	
    	$statement = $pdo->prepare("INSERT INTO Users(fb_id, first_name, last_name, fb_sender_id, profile_pic, email, locale, sexual_orientation, studied_at, lives_in, location, gender, updated_at, created_at)
        	VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    	$statement->execute(array($fb_id, $first_name, $last_name, $rid, $profile_pic, $email, $locale, $sexual_orientation, $education, $location, $channel, $genderInt, $updated_at, $created_at)); 	
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
        gender = :gender, 
        updated_at = :updated_at,
        created_at = :created_at
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
		$stmt->bindParam(':updated_at', $updated_at, PDO::PARAM_INT);  
		$stmt->bindParam(':created_at', $created_at, PDO::PARAM_INT);     
		$stmt->execute(); 
	}
}
