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

  $url_using = "https://55258982.ngrok.io";

	# Autoload the required files
	require_once __DIR__ . '/vendor/autoload.php';
  

	//$rid = $_GET['id'];

  //$functions = new Functions($rid, $url_using);
  //$connectiondb = new ConnectionDb();

	# Set the default parameters
	$fb = new Facebook\Facebook([
	  'app_id' => '585240351666649',
	  'app_secret' => '0c360663f24dec79e8428e58cc2069ee',
	  'default_graph_version' => 'v2.6',
	]);

$url = "https://55258982.ngrok.io/tecmatch/login/graciasview.html";
	# Create the login helper object
	$helper = $fb->getRedirectLoginHelper();

	$app_id = '585240351666649';
	$app_secret = '0c360663f24dec79e8428e58cc2069ee';
	$my_url = "https://55258982.ngrok.io/tecmatch/login/prueba2.php";
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
		  $response = $fb->get('/me?fields=email');
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


  		$url = "https://55258982.ngrok.io/tecmatch/login/graciasview.html";
  		echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';

