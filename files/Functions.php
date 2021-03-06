<?php

	


class Functions
{
	public $rid;
	public $message;
	
	public function __construct ($rid, $message, $urlWebhook, $sexual_orientation, $channelUser, $first_name, $gender, $token)
	{
		$this->rid = $rid;
		$this->message = $message; //Ando probando si lo necesito
		$this->urlWebhook = $urlWebhook;
		$this->sexual_orientation = $sexual_orientation;
		$this->channelUser = $channelUser;
		$this->first_name = $first_name;
		$this->gender = $gender;
		$this->connectiondb = $connectiondb = new ConnectionDb();
		$this->token = $token;
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

	public function sendTextMessageNewUser ($reply, $rid)
	{
		$numReplies = count ($reply);
		$messageData = "{
    	'recipient': {
      	'id': $rid
    	},
    	'message':{    
      	'text': '".$reply[rand(0,$numReplies-1)]."'
   		 }
    	}";
  		$this->callSendApi($messageData);
	}

	public function sendTextMessageToContact ($nickname, $reply, $text_NoContacts, $text_ChatWrote, $text_ChatReply, $text_ConfirmBlock)
	{
		$query2 = 'select ganadorId, jugadorId, nickname1, nickname2 from Games WHERE (ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid.') AND (nickname1 IS NOT NULL)';
	  	$results_contact2 = $this->connectiondb->Connection($query2);
	  	$results2 = json_decode(json_encode($results_contact2), true);
	  	$recipientId = null;
	  	var_dump($results2);
	  	foreach ($results2 as $value)
	  	{
	  		if ($value['jugadorId'] == $this->rid && $value['nickname2'] == $nickname)
	  		{
	  			$recipientId = $value['ganadorId'];
	  			$nickname_sender = $value['nickname1'];
	  			$nickname_receiver = $value['nickname2'];
	  		}
	  		if ($value['ganadorId'] == $this->rid && $value['nickname1'] == $nickname)
	  		{
	  			$recipientId = $value['jugadorId'];
	  			$nickname_sender = $value['nickname2'];
	  			$nickname_receiver = $value['nickname1'];
	  		}
	  	}
	  	if ($recipientId == null)
	  	{
	  		$recipientId = $this->rid;
	  		$this->sendTextMessageNewUser($text_NoContacts, $recipientId);
	  	}else{
		  	$replies = array ($nickname_sender.$text_ChatWrote.$reply);
		  	$this->sendTextMessageNewUser($replies, $recipientId);
		  	$replies = array ($text_ChatReply[0].$nickname_sender.$text_ChatReply[1]);
		  	$this->sendTextMessageNewUser($replies, $recipientId);

		  	//change the block to 2, which means "sending", once we received the delivery it will change to 1. If it still in 2 after 10 sec, they block the bot.
		  	$this->blockAndUnblockUser($recipientId, 2);

		  	sleep (10);
		  	//if it's block we send a message saying that the user is not available
		  	$this->confirmBlock($recipientId, $nickname_receiver, $text_ConfirmBlock);
	  	}
	}

	public function confirmBlock($userId, $nickname_receiver, $text_ConfirmBlock)
	{
		$query = "select block from Users where fb_sender_id ='".$userId."'";
		$result1 = $this->connectiondb->Connection($query);
		$results = json_decode(json_encode($result1), true);
		if ($results[0]['block'] == 2)
		{
			$replies = array ($nickname_receiver.$text_ConfirmBlock);
			$this->sendTextMessage($replies);
		}
	}

	public function blockAndUnblockUser($userId, $num)
	{
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE Users SET block = :block WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':fb_sender_id', $userId, PDO::PARAM_STR);
		$stmt->bindParam(':block', $num, PDO::PARAM_INT);
		$stmt->execute(); 	
	}

	public function checkBlockUser($num)
	{
		echo "es el id ".$this->rid." y ".$num;
	    if ($num == null or $num == 2)
		{
            echo "adenntro: es el id ".$this->rid." y ".$num;
			$this->blockAndUnblockUser($this->rid, 1);
		}
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

	public function sendLogin ($text_loginDescription, $text_loginOption)
	{
	  $urlLogin = "".$this->urlWebhook."/login/login1.php?id=$this->rid";
  	  $messageData = '{
	    "recipient":{
	      "id": '.$this->rid.'
	    },
	    "message":{
	      "attachment":{
	        "type":"template",
	        "payload":{
	          "template_type":"button",
	          "text":"'.$text_loginDescription.'",
	          "buttons":[
	            {
	              "type":"web_url",
	              "url":"'.$urlLogin.'",
	              "title":"'.$text_loginOption.'"
	            }
	          ]
	        }
	      }
	    }
	  }';
	  $this->callSendApi($messageData);
	}

	public function preguntaMensaje($replies, $text_title)
	{
	  $numReplies = count ($replies);
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$replies[rand(0,($numReplies-1))]."',
	      'quick_replies':[
	        {
	          'content_type':'text',
	          'title':'".$text_title."',
	          'payload':'Jugar'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function preguntaMensajePuntaje($replies, $text_optionScore)
	{
	  $numReplies = count ($reply);
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$replies[rand(0,($numReplies-1))]."',
	      'quick_replies':[
	        {
	          'content_type':'text',
	          'title':'".$text_optionScore."',
	          'payload':'puntaje'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function preguntaOrientacionSexual($text_askGender)
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$text_askGender[0]."',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'".$text_askGender[2]." 👨',
	          'payload':'sexhombres'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_askGender[3]." 👩',
	          'payload':'sexmujeres'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsInte1($text_Q1)
	{
	  if ($this->gender == 1)
	  {
		  $replies = array($text_Q1[0].$this->first_name.$text_Q1[1]);
		  $this->sendTextMessage($replies);
	  } else if ($this->gender == 0)
	  {
		  $replies = array($text_Q1[2].$this->first_name.$text_Q1[3]);
		  $this->sendTextMessage($replies);
	  }
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$text_Q1[4]."',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'".$text_Q1[5]."',
	          'payload':'inte1/1'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q1[6]."',
	          'payload':'inte1/2'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q1[7]."',
	          'payload':'inte1/3'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q1[8]."',
	          'payload':'inte1/4'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsInte2($text_Q2)
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$text_Q2[0]."',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'".$text_Q2[1]."',
	          'payload':'inte2/1'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q2[2]."',
	          'payload':'inte2/2'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q2[3]."',
	          'payload':'inte2/3'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q2[4]."',
	          'payload':'inte2/4'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q2[5]."',
	          'payload':'inte2/5'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q2[6]."',
	          'payload':'inte2/6'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsInte3($text_Q3)
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$text_Q3[0]."',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'".$text_Q3[1]."',
	          'payload':'inte3/1'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_Q3[2]."',
	          'payload':'inte3/2'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsAssign ($code2, $interest1, $interest2, $interest3, $message, $answer, $text_Q1, $text_Q2, $text_Q3, $text_QuestionsAssign)
	{
	//ask questionarie
		switch ($code2)
		{
		  case 'inte1':
		    $this->changeInte($answer, 'inte1');
		    $inte1 = 1;
		  break;
		    case 'inte2':
		    $this->changeInte($answer, 'inte2');
		    if ($answer == 5)
		    {
		    	$replies = array ($text_QuestionsAssign[0]);
		    	$this->sendTextMessage($replies);
		    }
		    $inte1 = 2;
		  break;
		    case 'inte3':
		    $this->changeInte($answer, 'inte3');
		    $replies = array ($text_QuestionsAssign[1].$this->channelUser.$text_QuestionsAssign[2]);
		    $this->sendTextMessage($replies);
		   if ($this->gender == 0 && $this->sexual_orientation == 0)
		    {
				$replies = array ($text_QuestionsAssign[3], $text_QuestionsAssign[4]);			     
				$this->sendTextMessage($replies);
        	} else {
                $replies = array ($text_QuestionsAssign[5], $text_QuestionsAssign[6], $text_QuestionsAssign[7]);
	        	$this->preguntaMensaje($replies);
        	}
		    $inte1 = 3;
		  break;
		}

		//validation if the type smt random on the questions
		if ($interest1 == null && $code2 == null)
		{
			$inte1 = null;
		} else if ($interest2 == null && $code2 == null)
			{
				$inte1 = 1;
			} else if ($interest3 == null && $code2 == null)
				{
					$inte1 = 2;
				}

				var_dump($code2, $interest1, $interest2, $interest3, $message, $answer);
		    if (($interest1 == null && $inte1 == null) && $message != null)
		    {
		      $this->questionsInte1($text_Q1);
		    } else if (($interest2 == null && $inte1 <= 1) && $message != null)
		            { 
		              $this->questionsInte2($text_Q2); 
		            }else if (($interest3 == null && $inte1 <= 2) && $message != null)
		                {
		                  $this->questionsInte3($text_Q3);
		                }
	}

	public function askGender ($text_askGender)
	{
		$messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$text_askGender[1]."',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'".$text_askGender[2]." 👨',
	          'payload':'generohombre'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_askGender[3]." 👩',
	          'payload':'generomujer'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function assignGender($gendercode)
	{
		if ($gendercode == "generomujer")
		{
			$gender = 1;
		} else {$gender = 0;}
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE Users SET $gender = :gender WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':fb_sender_id', $this->rid, PDO::PARAM_STR);
		$stmt->bindParam(':gender', $gender, PDO::PARAM_INT);
		$stmt->execute(); 
	}

	public function changeInte ($num, $type)
	{
		//set the interests, $type is the type of inte (e.i. inte1, inte2 or inte3)
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE Users SET $type = :type WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':fb_sender_id', $this->rid, PDO::PARAM_STR);
		$stmt->bindParam(':type', $num, PDO::PARAM_INT);
		$stmt->execute(); 
	}

	public function eraseInte ()
	{
		$num = null;
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE Users SET inte1 = :inte1, inte2 = :inte2, inte3 = :inte3 WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':inte1', $num, PDO::PARAM_INT);
		$stmt->bindParam(':inte2', $num, PDO::PARAM_INT);
		$stmt->bindParam(':inte3', $num, PDO::PARAM_INT);
		$stmt->bindParam(':fb_sender_id', $this->rid, PDO::PARAM_STR);
		$stmt->execute(); 
		echo "si pasa";
	}

	public function saveGame ($ganadorId, $perdedorId)
	{
		$query = "select id from Games where ganadorId = ".$ganadorId." AND perdedorId = ".$perdedorId." AND jugadorId = ".$this->rid."";
		$results = $this->connectiondb->Connection($query);
		if ($results == null)
		{	
			$pdo = $this->connectiondb->ConnectionReturnPDO();
			//jugadorId is the fb_sender_id of the player, ganadorid and perdedorid is the fb_id
			$statement = $pdo->prepare("INSERT INTO Games(ganadorId, perdedorId, jugadorId, updated_at) 
	        	VALUES(?,?,?,?)");
	    	date_default_timezone_set('America/Chicago'); // Set the time in CDT 
	    	$statement->execute(array($ganadorId, $perdedorId, $this->rid, strtotime("now"))); 
	    }
	}

	public function showContacts ($cont, $text_showContacts)
	{
		$query = 'select ganadorId, jugadorId, nickname1, nickname2 from Games WHERE (ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid.') AND (nickname1 IS NOT NULL)';
		$results_contacts = $this->connectiondb->Connection($query);
		$results = json_decode(json_encode($results_contacts), true);
		foreach ($results as $key=>$value)
		{
			if ($key >= $cont && $key < $cont+6)
			{
				if ($value['jugadorId'] == $this->rid)
				{
					$nickname = $value['nickname2'];
					$this->showContacts2($value['ganadorId'], $nickname, $text_showContacts);
				}
				if ($value['ganadorId'] == $this->rid)
				{
					$nickname = $value['nickname1'];
					$this->showContacts2($value['jugadorId'], $nickname, $text_showContacts);
				}
			}
			if ($key >= $cont+6)
			{
				$hayMas = 1;
			}else
			{
				$hayMas = 0;
			}
		}

		$cont = $cont + 6;
		if ($hayMas == 1)
		{
			$messageData = "{
			    'recipient':{
			      'id': $this->rid
			    },
			    'message':{
			      'text':'".$text_showContacts[0]."',
			      'quick_replies':[
			       	{
			          'content_type':'text',
			          'title':'".$text_showContacts[1]."',
			          'payload':'contact/".$cont."'
			        }
			      ]
			    }
			  }";
			  $this->callSendApi($messageData);
		} else {
			
			if( $results != null)
			{
			$reply = array ($text_showContacts[0]);
			$this->sendTextMessage($reply);
			}else{
				$reply = array ($text_showContacts[2]);
			$this->sendTextMessage($reply);
			}
			
		}
	}

	public function showContacts2 ($contactId, $nickname, $text_showContacts)
	{
		$query = "select profile_pic, fb_id from Users where fb_sender_id = ".$contactId."";
		$results_contacts = $this->connectiondb->Connection($query);
		$results = json_decode(json_encode($results_contacts), true);
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
				            'title':'".$nickname."',          
				            'image_url':'".$results[0]['profile_pic']."',
				            'item_url': 'https://www.facebook.com/".$results[0]['fb_id']."',
				             'subtitle':'".$text_showContacts[3]."'
				          }
				          ]
				        }
				      }
				    }
				 }";
				 $this->callSendApi($messageData);
	}

	public function askContact ($reply, $ganadorId, $perdedorId, $text_askPlayAdd)
	{
		$numReplies = count ($reply);
		$messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$reply[rand(0,($numReplies-1))]."',
	      'quick_replies':[
	        {
	          'content_type':'text',
	          'title':'".$text_askPlayAdd[0]."',
	          'payload':'addcontact/".$ganadorId."/".$perdedorId."'
	        },
	        {
	          'content_type':'text',
	          'title':'".$text_askPlayAdd[1]."',
	          'payload':'".$ganadorId."'
	        }
	      ]
	    }
	  }";

	  $this->callSendApi($messageData);
	}

	
	public function contact ($ganadorId, $text_contact)
	{
      $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id ='.$ganadorId;
	  $results_contact = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results_contact), true);

	  $fb_sender_id_ganador = $results3[0]['fb_sender_id'];
	  $first_name2 = $results3[0]['first_name'];

	  $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id ='.$this->rid;
	  $results_contact2 = $this->connectiondb->Connection($query);
	  $results2 = json_decode(json_encode($results_contact2), true);

	  $query = 'select nickname1, nickname2, ganadorId, jugadorId from Games where ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid;
	  $results_contact3 = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results_contact3), true);

	  foreach ($results3 as $key => $value)
	  {
	  	if($ganadorId == $value['ganadorId'])
	  	{
	  		$nickname = $value['nickname1'];
	  	}
	  }

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
	          }
	          ]
	        }
	      }
	    }
	 }";
	 //este if es para que no se loopee, no sé porque lo hace.
	 if ($first_name1 != null)
	 {
	  $replies = array ($text_contact[0].$first_name2.$text_contact[1].$first_name1.$text_contact[2].$nickname.$text_contact[3],$text_contact[4].$first_name2.$text_contact[5].$first_name1.$text_contact[6].$nickname.$text_contact[7], $text_contact[8].$first_name1.$text_contact[9].$nickname.$text_contact[10]);
	  $this->sendTextMessageContact ($replies, $fb_sender_id_ganador);
	 }
	  $this->callSendApi($messageData);
	}

	public function changeSexualOrientationDb ($changeSex)
	{
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE Users SET sexual_orientation = :sexual_orientation WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':fb_sender_id', $this->rid, PDO::PARAM_STR);
		$stmt->bindParam(':sexual_orientation', $changeSex, PDO::PARAM_INT);
		$stmt->execute(); 
	}

	public function changeRelationship ($ganadorId, $perdedorId)
	{
		//we need to assign the 2 nicknames based who is the winner and the player
		$nickname1 = $this->setNickname($ganadorId, "nickname1", $this->rid);
		$nickname2 = $this->setNickname($this->rid, "nickname2", $ganadorId);
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$cont = 0;
		$sql = "UPDATE Games SET contactar = :contactar, nickname1 = :nickname1, nickname2 = :nickname2 
        WHERE ganadorId = :ganadorId AND perdedorId = :perdedorId AND jugadorId = :jugadorId";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':contactar', $cont, PDO::PARAM_INT); 
		$stmt->bindParam(':nickname1', $nickname1, PDO::PARAM_STR);
		$stmt->bindParam(':nickname2', $nickname2, PDO::PARAM_STR);
		$stmt->bindParam(':ganadorId', $ganadorId, PDO::PARAM_STR);
		$stmt->bindParam(':perdedorId', $perdedorId, PDO::PARAM_STR);
		$stmt->bindParam(':jugadorId', $this->rid, PDO::PARAM_STR);
		$stmt->execute(); 
	}
	
	public function setChannel($location, $education)
	{		
		$edu_array = explode("/",$education);
		foreach($edu_array as $key=>$value)
		{
			$query = "select type, name from Locations where name = '".$value."'";

	  		$results = $this->connectiondb->Connection($query);
	  		$results2 = json_decode(json_encode($results), true);
	  		if ($results != null)
	  		{
	  			$results2 = json_decode(json_encode($results), true);
	  			$channel = $results2[0]['type'];
	  		}
		}
		if ($channel == null)
		{
			$query = "select type from Locations where name ='".$location."'";
	  		$results = $this->connectiondb->Connection($query);
	  		if ($results != null)
	  		{
	  			$results2 = json_decode(json_encode($results), true);
	  			$channel = $results2[0]['type'];
	  		}
		}
		if ($channel == null)
		{
			$channel = "General";
		}
		return $channel;
	}

	public function changeChannel ($location, $education, $text_changeChannel)
	{
		$edu_array = explode("/",$education);
		foreach($edu_array as $key=>$value)
		{
			$query = "select type, name from Locations where name = '".$value."'";
	  		$results = $this->connectiondb->Connection($query);
	  		$results2 = json_decode(json_encode($results), true);
	  		if ($results != null)
	  		{
	  			$results2 = json_decode(json_encode($results), true);
	  			$channel_university = $results2[0]['type'];
	  		}
		}
			$query = "select type from Locations where name ='".$location."'";
	  		$results = $this->connectiondb->Connection($query);
	  		if ($results != null)
	  		{
	  			$results2 = json_decode(json_encode($results), true);
	  			$channel_city = $results2[0]['type'];
	  		}
			$channel_general = "General";

			$messageData = "{
		    'recipient':{
		      'id': $this->rid
		    },
		    'message':{
		      'text':'".$text_changeChannel[0]."',
		      'quick_replies':[
		       	{
		          'content_type':'text',
		          'title':'".$channel_general."',
		          'payload':'channelChange/".$channel_general."'
		        },
		        {
		          'content_type':'text',
		          'title':'".$channel_city."',
		          'payload':'channelChange/".$channel_city."'
		        },
		        {
		          'content_type':'text',
		          'title':'".$channel_university."',
		          'payload':'channelChange/".$channel_university."'
		        }
		      ]
		    }
		  }";
			var_dump($messageData);
		  $this->callSendApi($messageData);
	}

	public function changeChannel2 ($channel)
	{
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE Users SET location = :location WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);                                  
		$stmt->bindParam(':location', $channel, PDO::PARAM_STR); 
		$stmt->bindParam(':fb_sender_id', $this->rid, PDO::PARAM_STR);
		$stmt->execute(); 
	}

	public function updateTime ($table)
	{
		$pdo = $this->connectiondb->ConnectionReturnPDO();
		$sql = "UPDATE $table SET updated_at = :updated_at WHERE fb_sender_id = :fb_sender_id";
		$stmt = $pdo->prepare($sql);   
		date_default_timezone_set('America/Chicago'); // Set the time in CDT                             
		$stmt->bindParam(':updated_at', strtotime("now"), PDO::PARAM_INT); 
		$stmt->bindParam(':fb_sender_id', $this->rid, PDO::PARAM_STR);
		$stmt->execute(); 
	}

	public function setNickname ($userId, $typeNick, $otherUserId)
	{
	  
	  $query = 'select first_name from Users where fb_sender_id ='.$otherUserId;
	  $results_contact = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_contact), true);
	  $jugador_firstname = $results[0]['first_name'];
	  
	  $query2 = 'select '.$typeNick.', contactar, ganadorId, jugadorId from Games where ganadorId ='.$userId.' OR jugadorId ='.$userId.'';
	  $results_contact2 = $this->connectiondb->Connection($query2);
	  $results2 = json_decode(json_encode($results_contact2), true);
	  $cont = 0;

	  foreach ($results2 as $value) {
	  	if ($jugador_firstname == $value[$typeNick] && ($value["ganadorId"] != $otherUserId && $value["jugadorId"] != $otherUserId))
	  	{
	  		if($value["contactar"] != 1)
	  		{
	  			$cont++;
	  		}
	  	}
	  }
	  if ($cont == 0)
	  {
	  	return $jugador_firstname;
	  }else{
	  	return $jugador_firstname."".$cont;
	  }
	 
	}

	public function newGame ($text_newGame, $text_bio)
	{	 
		if($this->sexual_orientation >= 1)
		{
			$query = "select fb_id, first_name, fb_sender_id, profile_pic, inte1, inte2, inte3 from Users where fb_id IS NOT NULL AND sexual_orientation = '".$this->sexual_orientation."' AND location = '".$this->channelUser."' AND (block IS null OR block = 1)";
		} else {
			$query = "select fb_id, first_name, fb_sender_id, profile_pic, inte1, inte2, inte3 from Users where fb_id IS NOT NULL AND gender = 0 AND sexual_orientation = '".$this->sexual_orientation."' AND location = '".$this->channelUser."' AND (block IS null OR block = 1)";
		}	
		  $results_newGame = $this->connectiondb->Connection($query);
		  $results = json_decode(json_encode($results_newGame), true);


		  $numcount = count ($results);

		 if (count($results) > 2) //So there are at least 3 ppl in channel 2 ppl and the user.
		 {
			  $num_results = count($results);
			  do{
			  $num1 = rand (0, ($num_results-1));
			  $num2 = rand (0, ($num_results-1));
			  } while ($num1 == $num2 || $this->rid == $results[$num1]['fb_sender_id'] || $this->rid == $results[$num2]['fb_sender_id']); //para que no se repitan y que no salga el usuario

			  $fb_id1 = $results[$num1]['fb_id'];

			  $first_name1 = $results[$num1]['first_name'];
			  $fb_sender_id1 = $results[$num1]['fb_sender_id'];
			  $profile_pic1 = $results[$num1]['profile_pic'];

			  $fb_id2 = $results[$num2]['fb_id'];
			  $first_name2 = $results[$num2]['first_name'];
			  $fb_sender_id2 = $results[$num2]['fb_sender_id'];
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
			            'subtitle':'".$text_newGame[0]."',
			            'buttons': [{
			              'type':'postback',
			              'title':'".$text_newGame[1]."',
			              'payload': 'gano/".$fb_sender_id1."/".$fb_sender_id2."'
			            }
			            ]  
			          },
			          {
			            'title':'".$first_name2."',
			          
			            'image_url':'".$profile_pic2."',
			            'item_url': 'https://www.facebook.com/".$fb_id2."',
			            'subtitle':'".$text_newGame[0]."',
			            'buttons': [{
			              'type':'postback',
			              'title':'".$text_newGame[1]."',
			              'payload': 'gano/".$fb_sender_id2."/".$fb_sender_id1."'
			            }
			            ]  
			          }
			          ]
			        }
			      }
			    }
			 }";
			 	$replies = array($text_newGame[2], $text_newGame[3], $text_newGame[4], $text_newGame[5], $text_newGame[6], $text_newGame[7]);
		        $this->sendTextMessage($replies);

		        $this->displayBio((int)$results[$num1]['inte1'], (int)$results[$num1]['inte2'], (int)$results[$num1]['inte3'], $results[$num1]['first_name'], $text_bio);
		        $this->displayBio((int)$results[$num2]['inte1'], (int)$results[$num2]['inte2'], (int)$results[$num2]['inte3'], $results[$num2]['first_name'], $text_bio);

			 	$this->callSendApi($messageData);
			}else{
				$replies = array($text_newGame[8].$this->channelUser.$text_newGame[9]);
		        $this->sendTextMessage($replies);
			}
	}

	public function displayBio ($inte1, $inte2, $inte3, $firstnameUser, $text_bio)
	{
	    $inte1_arr = array ($text_bio[0], $text_bio[1], $text_bio[2], $text_bio[3]);
		$inte2_arr = array ($text_bio[4], $text_bio[5], $text_bio[6], $text_bio[7], $text_bio[8], $text_bio[9]);
		$inte3_arr = array ($text_bio[10], $text_bio[11]);
		//send the whole text with interest together
        $replies = array($firstnameUser . $text_bio[12] . $inte1_arr[($inte1-1)] . $text_bio[13] . $inte2_arr[($inte2-1)] . $text_bio[14] . $inte3_arr[($inte3 -1)]);
        $this->sendTextMessage($replies);
	}

//aquí me quede en la traducción y también probar
	

	public function insertUser ()
	{
	  $query = 'select * from Users where fb_sender_id = '.$this->rid;
	  $results_insertUser = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_insertUser), true);

	  $token = $this->token;
	  $url = "https://graph.facebook.com/v2.6/$this->rid?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=$token";
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch, CURLOPT_URL,$url);
	  $infoUser = curl_exec($ch);
	  curl_close($ch);		

	  $infoUser2 = json_decode($infoUser, true); 
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

	public function score ($text_score)
	{
		$query = "select perdedorId from Games where ganadorId = ".$this->rid."";
		$results = $this->connectiondb->Connection($query);
		$results_perdedor = json_decode(json_encode($results), true);

		if ($results_perdedor[0] == null)
		{
			$replies = array ($text_score[0], $text_score[1]);
			$this->sendTextMessage($replies);
		}else{
			$replies = array ($text_score[2]);
			$this->sendTextMessage($replies);
		}

		if ($results_perdedor[0] != null)
		{
			$this->showScore($results_perdedor, $text_score);
		}
	}

	public function showScore($results_perdedor, $text_score)
	{
	    $fb_id1 = [];
		$first_name1 = [];
		$fg_sender_id1 = [];
		$profile_pic1 = [];

	  $numUsers = count ($results_perdedor);
	  $cont = 0;
	 while ($cont <= 5 && $cont != $numUsers)
	 {
		  $query = "select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id = ".$results_perdedor[$numUsers-1-$cont]['perdedorId'].""; 
	 	  $results = $this->connectiondb->Connection($query);
		  $results2 = json_decode(json_encode($results), true);
		  $fb_id1 [$cont] = $results2[0]['fb_id'];
		  $first_name1 [$cont] = $results2[0]['first_name'];
		  $fg_sender_id1 [$cont] = $results2[0]['fb_sender_id'];
		  $profile_pic1 [$cont] = $results2[0]['profile_pic'];

		  $cont++;
	 }
		 switch ($numUsers)
		  {
		  	case 1:
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
			            'title':'".$first_name1[0]."',          
			            'image_url':'".$profile_pic1[0]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[0]."',
			             'subtitle':'".$text_score[3]."'
			          }
			          ]
			        }
			      }
			    }
			 }";
			 break;
			 case 2:
			  $messageData = "{
			    'recipient': {
			      'id': $this->rid
			    },
			    'message':{
			      'attachment':{
			        'type':'template',
			        'payload':{
			          'template_type': 'generic',
			          'elements': [
			          {
			            'title':'".$first_name1[0]."',          
			            'image_url':'".$profile_pic1[0]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[0]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'".$text_score[3]."'
			          }
			          ]
			        }
			      }
			    }
			 }";
			 break;
			 case 3:
			 	$messageData = "{
			    'recipient': {
			      'id': $this->rid
			    },
			    'message':{
			      'attachment':{
			        'type':'template',
			        'payload':{
			          'template_type': 'generic',
			          'elements': [
			          {
			            'title':'".$first_name1[0]."',          
			            'image_url':'".$profile_pic1[0]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[0]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[2]."',          
			            'image_url':'".$profile_pic1[2]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[2]."',
			             'subtitle':'".$text_score[3]."'
			          }
			          ]
			        }
			      }
			    }
			 }";
			 break;
			 case 4:
			   $messageData = "{
			    'recipient': {
			      'id': $this->rid
			    },
			    'message':{
			      'attachment':{
			        'type':'template',
			        'payload':{
			          'template_type': 'generic',
			          'elements': [
			          {
			            'title':'".$first_name1[0]."',          
			            'image_url':'".$profile_pic1[0]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[0]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[2]."',          
			            'image_url':'".$profile_pic1[2]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[2]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[3]."',          
			            'image_url':'".$profile_pic1[3]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[3]."',
			             'subtitle':'".$text_score[3]."'
			          }
			          ]
			        }
			      }
			    }
			 }";
			 break;
			 default:
			 $messageData = "{
			    'recipient': {
			      'id': $this->rid
			    },
			    'message':{
			      'attachment':{
			        'type':'template',
			        'payload':{
			          'template_type': 'generic',
			          'elements': [
			          {
			            'title':'".$first_name1[0]."',          
			            'image_url':'".$profile_pic1[0]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[0]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[2]."',          
			            'image_url':'".$profile_pic1[2]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[2]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[3]."',          
			            'image_url':'".$profile_pic1[3]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[3]."',
			             'subtitle':'".$text_score[3]."'
			          },
			          {
			            'title':'".$first_name1[4]."',          
			            'image_url':'".$profile_pic1[4]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[4]."',
			             'subtitle':'".$text_score[3]."'
			          }
			          ]
			        }
			      }
			    }
			 }";
		  }

	 //vamos a mandarle los ´último que ha ganado
	 $this->callSendApi($messageData);
	}

	public function sendTyping ()
	{
	    $messageData = "{
	    'recipient':{
	      'id':$this->rid
	    },
	    'sender_action':'typing_on'
	  }";
	  $this->callSendApi($messageData);
	}

	public function callSendApi ($messageDataSend)
	{
		echo "aqui ando";
		var_dump($this->token);
	    $token = $this->token;
		 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
		 $ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		 $result = curl_exec($ch);
		 curl_close($ch);
	}
	
}