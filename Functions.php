<?php

class Functions
{
	
	public $rid;
	public $message;
	
	public function __construct ($rid, $message, $urlWebhook)
	{
		$this->rid = $rid;
		$this->message = $message;
		$this->urlWebhook = $urlWebhook;
		$this->connectiondb = $connectiondb = new ConnectionDb();
		
	}
	
	public function sendTextMessage ($reply)
	{
		$numReplies = count ($reply);
		$messageData = "{
    	'recipient': {
      	'id': $this->rid
    	},
    	'message':{    
      	'text': '".$reply[rand(0,$numReplies-1)]."'
   		 }
    	}";
  		$this->callSendApi($messageData);
	}

	public function sendTextMessageContact ($reply, $contactId)
	{
		$numReplies = count ($reply);
		$messageData = "{
    	'recipient': {
      	'id': $contactId
    	},
    	'message':{    
      	'text': '".$reply[rand(0,$numReplies-1)]."'
   		 }
    	}";
  		$this->callSendApi($messageData);
	}

	public function sendLogin ()
	{
	  $urlLogin = "".$this->urlWebhook."/login/LP_tecmatch.php?id=$this->rid";
  	  $messageData = '{
	    "recipient":{
	      "id": '.$this->rid.'
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
	              "url":"'.$urlLogin.'",
	              "title":"Hacer login"
	            }
	          ]
	        }
	      }
	    }
	  }';
	  $this->callSendApi($messageData);
	}

	public function preguntaMensaje($replies, $numReplies)
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$replies[rand(0,$numReplies)]."',
	      'quick_replies':[
	        {
	          'content_type':'text',
	          'title':'Jugar',
	          'payload':'nada'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function askContact ($reply, $numReplies, $ganadorId)
	{
		$messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$reply[rand(0,$numReplies)]."',
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
	  $this->callSendApi($messageData);
	}

	function sendGenericMessage($results, $ganadorId) 
	{
	  $pdo = $results[0];
	  $results2 = json_decode(json_encode($results[1]), true);

	  $num_results2 = count($results2);
	  do {
	  	$num1 = rand (0, ($num_results2-1));
	  } while ($results2[$num1]['fb_id'] == $ganadorId || $rid == $results2[$num1]['fb_sender_id']);

	  $fb_id1 = $results2[$num1]['fb_id'];
	  $first_name1 = $results2[$num1]['first_name'];
	  $fg_sender_id1 = $results2[$num1]['fb_sender_id'];
	  $profile_pic1 = $results2[$num1]['profile_pic'];

	  $ganadorId2 = (string)$ganadorId;

	  //código para acceder a la BD
	  $query = 'select first_name, fb_sender_id, profile_pic from Users where fb_id='.$ganadorId2;
	  $results_genericMsg = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results_genericMsg[1]), true);

	  $fb_id2 = $ganadorId;
	  $first_name2 = $results3[0]['first_name'];
	  $fg_sender_id2 = $results3[0]['fb_sender_id'];
	  $profile_pic2 = $results3[0]['profile_pic'];

	  $messageData = "{
	    'recipient': {
	      'id': $this->rid
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
	 $this->callSendApi($messageData);
	}
	
	public function contact ($ganadorId)
	{
      $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_id ='.$ganadorId;
	  $results_contact = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results_contact[1]), true);

	  $fb_sender_id_ganador = $results3[0]['fb_sender_id'];
	  $first_name2 = $results3[0]['first_name'];

	  $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id ='.$this->rid;
	  $results_contact2 = $this->connectiondb->Connection($query);
	  $results2 = json_decode(json_encode($results_contact2[1]), true);

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
	 //este if es para que no se loopee, no sé porque lo hace.
	 if ($first_name1 != null)
	 {
	  $replies = array ("Que onda ".$first_name2."! Mira, ".$first_name1." te quiere conocer! Ella ya dio el primer paso te toca a ti! Entra a su perfil y mándale un mensaje. Ella esta esperando tu mensaje. ;)","Que onda ".$first_name2."! Le interesas a ".$first_name1.". Ella quiere que le escribas. Entra a su perfil y mándale un mensaje. ;)", "Oye galán, andas con todo! ".$first_name1." quiere que le escribas. Entra a su perfil y mándale un mensaje. ;)");
	  $this->sendTextMessageContact ($replies, $fb_sender_id_ganador);
	 }
	  $this->callSendApi($messageData);
	}


	public function newGame ()
	{	 
	  $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where gender = 0 AND fb_id IS NOT NULL';
	  $results_newGame = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_newGame[1]), true);

	  $num_results = count($results);
	  do{
	  $num1 = rand (0, ($num_results-1));
	  $num2 = rand (0, ($num_results-1));
	  } while ($num1 == $num2 || $this->rid == $results[$num1]['fb_sender_id'] || $this->rid == $results[$num2]['fb_sender_id']); //para que no se repitan y que no salga el usuario

	  $fb_id1 = $results[$num1]['fb_id'];

	  $first_name1 = $results[$num1]['first_name'];
	  $fg_sender_id1 = $results[$num1]['fb_sender_id'];
	  $profile_pic1 = $results[$num1]['profile_pic'];

	  $fb_id2 = $results[$num2]['fb_id'];
	  $first_name2 = $results[$num2]['first_name'];
	  $fg_sender_id2 = $results[$num2]['fb_sender_id'];
	  $profile_pic2 = $results[$num2]['profile_pic'];
	 
	  $messageData = "{
	    'recipient': {
	      'id': $this->rid
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
	 $this->callSendApi($messageData);
	}

	public function insertUser ()
	{
	  $query = 'select * from Users where fb_sender_id = '.$this->rid;
	  $results_insertUser = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_insertUser[1]), true);
	  var_dump($results);

	  $token ="EAAIUReNE8dkBAMMYqXANPKSsiGvXQHSCIZA5UZAKB3pYtQK1l4MItZCcw4Ko4ipZB1qJxg7Uiabc6US77CboUezlvVtZBq7oFNRB1J3lIDgbrEfq3wHZBkNiMd1R1G5Xq9ojKB8UZCBHK0jjfXYQNZA6U9qzFY0QCD6iQZBsRqFJy9AZDZD";
	  $url = "https://graph.facebook.com/v2.6/$this->rid?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=$token";
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_URL,$url);
	  $infoUser = curl_exec($ch);
	  curl_close($ch);		

	  $infoUser2 = json_decode($infoUser, true); 
	  var_dump($infoUser2);
	  $first_name = $infoUser2['first_name'];
	  $last_name = $infoUser2['last_name'];
	  $profile_pic = $infoUser2['profile_pic'];
	  $locale = $infoUser2['locale'];
	  $gender = $infoUser2['gender'];

	  if ($gender == 'male')
	  {
	    $genderInt = 0;
	  } else if ($gender == "female")
	  {
	    $genderInt = 1;
	  }else{
	    $genderInt = 2;
	  }
	  if ($results[0]["fb_sender_id"]==null)
	  {
	  	  $pdo = $this->connectiondb->ConnectionReturnPDO();
	      $statement = $pdo->prepare("INSERT INTO Users(first_name, last_name, fb_sender_id, profile_pic, locale, gender)
	          VALUES(?,?,?,?,?,?)");
	      $statement->execute(array($first_name, $last_name, $this->rid, $profile_pic, $locale, $genderInt)); 
	  }
	}

	public function callSendApi ($messageDataSend)
	{
		 $token ="EAAIUReNE8dkBAMMYqXANPKSsiGvXQHSCIZA5UZAKB3pYtQK1l4MItZCcw4Ko4ipZB1qJxg7Uiabc6US77CboUezlvVtZBq7oFNRB1J3lIDgbrEfq3wHZBkNiMd1R1G5Xq9ojKB8UZCBHK0jjfXYQNZA6U9qzFY0QCD6iQZBsRqFJy9AZDZD";
		 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
		 $ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		 $result = curl_exec($ch);
		 curl_close($ch);
	}
	
}