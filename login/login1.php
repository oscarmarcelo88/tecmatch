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
    
    # Start the session 
    session_start();

    require '../config.php';

    $url_using = getenv("urlWebhook");
    $app_id = getenv("app_id");
    $app_secret = getenv("app_secret");
    $default_graph_version = getenv("default_graph_version");
    $rid = $_GET['id'];

    # Autoload the required files
    require_once __DIR__ . '/vendor/autoload.php';
    # Set the default parameters
    $fb = new Facebook\Facebook([
      'app_id' => $app_id,
      'app_secret' => $app_secret,
      'default_graph_version' => $default_graph_version,
    ]);
    $redirect = "".$url_using."/login/login2.php?id=$rid";
    # Create the login helper object
    $helper = $fb->getRedirectLoginHelper();
    # Get the access token and catch the exceptions if any
    
    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    
    # If the 
    if (isset($accessToken)) {
        echo ($accessToken);
        // Logged in!
        // Now you can redirect to another page and use the
        // access token from $_SESSION['facebook_access_token'] 
        // But we shall we the same page
        // Sets the default fallback access token so 
        // we don't have to pass it to each request
        $fb->setDefaultAccessToken($accessToken);
        try {
          $response = $fb->get('/me?fields=email,name,first_name');
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
        
    }else{
        $permissions  = ['email','user_location','user_education_history'];
        $loginUrl = $helper->getLoginUrl($redirect, $permissions);
      echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$loginUrl.'">';
      }
?>
        
