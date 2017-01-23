<!DOCTYPE html>
<html>
<head>
	        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {background: #26438B;}

div.header1 {
	background-image:url("cover.png");
    background-position:center;
    width:640;
    height:360px;
    z-index: 1px;
    width: 100%;
}	

div.button{
      
}

img.logo
{
    width:100px;
    height:46px;
     display: block;
    margin-left: auto;
    margin-right: auto;

}

img {
	padding-top: 10px;
	  display: block;
    width: 100%;
    height: auto;
	}

h4  {
    color: white;
    font-family: Helvetica;
    font-size: 100%;
    text-align: center;
 
    padding-top: 100px;

        padding-left: 10px;
    padding-right: 10px;
}

</style>
</head>
<body>
<img src="logo.png" alt="" style="width:130px;height:37px;">
<div class="header1">
<h4>Necesitamos que te registres con Facebook para poder utilizar Tec Match. Tec Match nunca publicará nada sin tu concentimiento.</h4>
<div class="button" style="">
<?php
    
    # Start the session 
    session_start();
  

    $url_using = "https://7cb60b4c.ngrok.io/tecmatch";


    $rid = $_GET['id'];
    echo "el id de lp: ".$rid;
    
    # Autoload the required files
    require_once __DIR__ . '/vendor/autoload.php';
    # Set the default parameters
    $fb = new Facebook\Facebook([
      'app_id' => '585240351666649',
      'app_secret' => '0c360663f24dec79e8428e58cc2069ee',
      'default_graph_version' => 'v2.6',
    ]);
    $redirect = "https://7cb60b4c.ngrok.io/tecmatch/login/prueba.php?id=$rid";
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
        // Print the user Details
        echo "Welcome !<br><br>";
        echo 'Name: ' . $userNode->getName().'<br>';
        echo 'User ID: ' . $userNode->getId().'<br>';
        echo 'Email: ' . $userNode->getProperty('email').'<br><br>';
        $image = 'https://graph.facebook.com/'.$userNode->getId().'/picture?width=200';
        echo "Picture<br>";
        echo "<img src='$image' /><br><br>";
        
    }else{
        $permissions  = ['email'];
        $loginUrl = $helper->getLoginUrl($redirect,$permissions);
?>
        <a href=<?php echo $loginUrl; ?>>
            <img class="logo" src="fb_button.png" >
        </a>
        <?php
    }
?>
</div>
</div>

</body>
</html>