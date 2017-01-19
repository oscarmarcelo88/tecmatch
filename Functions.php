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
	}
	
	public function sendTextMessage ($reply, $numReplies)
	{
		$messageData = "{
    	'recipient': {
      	'id': $this->rid
    	},
    	'message':{    
      	'text': '".$reply[rand(0,$numReplies)]."'
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

	function sendGenericMessage($results, $ganadorId, $pdo) 
	{

	  $results2 = json_decode(json_encode($results), true);
	  $num_results2 = count($results2);
	  do {
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
	 $this->callSendApi($messageData);*/
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

