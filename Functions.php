<?php

class Functions
{
	
	public $rid;
	public $message;
	
	public function __construct ($rid, $message, $urlWebhook, $sexual_orientation, $channelUser, $first_name, $gender)
	{
		$this->rid = $rid;
		$this->message = $message; //Ando probando si lo necesito
		$this->urlWebhook = $urlWebhook;
		$this->sexual_orientation = $sexual_orientation;
		$this->channelUser = $channelUser;
		$this->first_name = $first_name;
		$this->gender = $gender;
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

	public function sendTextMessageToContact ($nickname, $reply)
	{
		$query2 = 'select ganadorId, jugadorId, nickname1, nickname2 from Games WHERE (ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid.') AND (nickname1 IS NOT NULL)';
	  	$results_contact2 = $this->connectiondb->Connection($query2);
	  	$results2 = json_decode(json_encode($results_contact2), true);
	  	$recipientId = null;
	  	
	  	foreach ($results2 as $value)
	  	{
	  		if ($value['jugadorId'] == $this->rid && $value['nickname2'] == $nickname)
	  		{
	  			$recipientId = $value['ganadorId'];
	  			$nickname_sender = $value['nickname1'];
	  		}
	  		if ($value['ganadorId'] == $this->rid && $value['nickname1'] == $nickname)
	  		{
	  			$recipientId = $value['jugadorId'];
	  			$nickname_sender = $value['nickname2'];
	  		}
	  	}
	  	if ($recipientId == null)
	  	{
	  		$recipientId = $this->rid;
	  		$reply = "Ese contacto no existe";
	  	}else{
		  	$replies = array ("".$nickname_sender." te escribió:".$reply."");
		  	$this->sendTextMessageNewUser($replies, $recipientId);
		  	$replies = array ("Para responderle escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. ".$nickname_sender.":MENSAJE)");
		  	$this->sendTextMessageNewUser($replies, $recipientId);
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
	          "text":"Para comenzar a jugar da click en hacer login. Por tu seguridad ninguna información será publicada ni compartida sin tu consentimiento.",
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

	public function preguntaMensaje($replies)
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
	          'title':'Jugar',
	          'payload':'nada'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function preguntaMensajePuntaje($replies)
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
	          'title':'Puntaje 🏆',
	          'payload':'puntaje'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function preguntaOrientacionSexual()
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'Te interesan:',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'Hombres 👨',
	          'payload':'sexhombres'
	        },
	        {
	          'content_type':'text',
	          'title':'Mujeres 👩',
	          'payload':'sexmujeres'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsInte1()
	{
	  if ($this->gender == 1)
	  {
		  $replies = array("".$this->first_name." Bienvenida al juego. Para apoyarte en tu decisiones contesta las siguientes 3 preguntas.");
		  $this->sendTextMessage($replies);
	  } else if ($this->gender == 0)
	  {
		  $replies = array("".$this->first_name." Bienvenido al juego. Primero me gustaría saber unas cosas de ti.");
		  $this->sendTextMessage($replies);
	  }
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'Juegas buscando?? 😏',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'Algo serio 💏',
	          'payload':'inte1/1'
	        },
	        {
	          'content_type':'text',
	          'title':'Casual 😘',
	          'payload':'inte1/2'
	        },
	        {
	          'content_type':'text',
	          'title':'Amigos 😃',
	          'payload':'inte1/3'
	        },
	        {
	          'content_type':'text',
	          'title':'Diversión 😜',
	          'payload':'inte1/4'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsInte2()
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'El fin de semana prefieres:',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'Antro 💃',
	          'payload':'inte2/1'
	        },
	        {
	          'content_type':'text',
	          'title':'Cine 🎬',
	          'payload':'inte2/2'
	        },
	        {
	          'content_type':'text',
	          'title':'Familia 🏡',
	          'payload':'inte2/3'
	        },
	        {
	          'content_type':'text',
	          'title':'Ejercicio 🚲',
	          'payload':'inte2/4'
	        },
	        {
	          'content_type':'text',
	          'title':'Netflix 📺',
	          'payload':'inte2/5'
	        },
	        {
	          'content_type':'text',
	          'title':'Leer 📖',
	          'payload':'inte2/6'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsInte3()
	{
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'¿Fumas? ',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'Sí 🚬',
	          'payload':'inte3/1'
	        },
	        {
	          'content_type':'text',
	          'title':'No 🚭',
	          'payload':'inte3/2'
	        }
	      ]
	    }
	  }";
	  $this->callSendApi($messageData);
	}

	public function questionsAssign ($code2, $interest1, $interest2, $interest3, $message, $answer)
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
		    	$replies = array ("Para estar claros, es Netflix sin chill... ☺");
		    	$this->sendTextMessage($replies);
		    }
		    $inte1 = 2;
		  break;
		    case 'inte3':
		    $this->changeInte($answer, 'inte3');
		    $replies = array ("Te encuentras en el canal ".$this->channelUser.", para cambiar de canal utiliza el menu en la parte inferior izquierda.");
		    $this->sendTextMessage($replies);
		   if ($this->gender == 0 && $this->sexual_orientation == 0)
		    {
				$replies = array ("Tú tranquilo, te avisaré cuando alguna chica te contacte 👌 ", "Ahora te toca esperar... 😉 ");			     
				$this->sendTextMessage($replies);
        	} else {
                $replies = array ("Perfecto, ya podemos comenzar 🎉", "Que te parece si empezamos ;)", "Estás lista?? 😉");
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


		    if (($interest1 == null && $inte1 == null) && $message != null)
		    {
		      $this->questionsInte1();
		    } else if (($interest2 == null && $inte1 <= 1) && $message != null)
		            { 
		              $this->questionsInte2(); 
		            }else if (($interest3 == null && $inte1 <= 2) && $message != null)
		                {
		                  $this->questionsInte3();
		                }
	}

	public function changeInte ($num, $type)
	{
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
	}

	public function saveGame ($ganadorId, $perdedorId)
	{
		$query = "select id from Games where ganadorId = ".$ganadorId." AND perdedorId = ".$perdedorId." AND jugadorId = ".$this->rid."";
		$results = $this->connectiondb->Connection($query);
		if ($results == null)
		{	
			$pdo = $this->connectiondb->ConnectionReturnPDO();
			//jugadorId is the fb_sender_id of the player, ganadorid and perdedorid is the fb_id
			$statement = $pdo->prepare("INSERT INTO Games(ganadorId, perdedorId, jugadorId) 
	        	VALUES(?,?,?)");
	    	$statement->execute(array($ganadorId, $perdedorId, $this->rid)); 
	    }
	}

	public function showContacts ($cont)
	{
		$query = 'select ganadorId, jugadorId, nickname1, nickname2 from Games WHERE (ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid.') AND (nickname1 IS NOT NULL)';
		$results_contacts = $this->connectiondb->Connection($query);
		$results = json_decode(json_encode($results_contacts), true);
		foreach ($results as $key=>$value)
		{
			echo "el key es: ".$key;
			if ($key >= $cont && $key < $cont+6)
			{
				if ($value['jugadorId'] == $this->rid)
				{
					$nickname = $value['nickname2'];
					$this->showContacts2($value['ganadorId'], $nickname);
				}
				if ($value['ganadorId'] == $this->rid)
				{
					$nickname = $value['nickname1'];
					$this->showContacts2($value['jugadorId'], $nickname);
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
			      'text':'Para hablar con un contacto escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. NOMBRE:MENSAJE)',
			      'quick_replies':[
			       	{
			          'content_type':'text',
			          'title':'Cargar más',
			          'payload':'contact/".$cont."'
			        }
			      ]
			    }
			  }";
			  $this->callSendApi($messageData);
		} else {
			
			if( $results != null)
			{
			$reply = array ("Para hablar con un contacto escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. NOMBRE:MENSAJE)");
			$this->sendTextMessage($reply);
			}else{
				$reply = array ("Por ahora no tienes contactos, te avisaremos cuando alguien te agregue 😉");
			$this->sendTextMessage($reply);
			}
			
		}
	}

	public function showContacts2 ($contactId, $nickname)
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
			             'subtitle':'Haz click para entrar a su perfil'
			          }
			          ]
			        }
			      }
			    }
			 }";
			 $this->callSendApi($messageData);
	}

	public function askContact ($reply, $ganadorId, $perdedorId)
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
	          'title':'Agregar a contactos',
	          'payload':'contacto/".$ganadorId."/".$perdedorId."'
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

	
	public function contact ($ganadorId)
	{
      $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id ='.$ganadorId;
	  $results_contact = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results_contact), true);

	  $fb_sender_id_ganador = $results3[0]['fb_sender_id'];
	  $first_name2 = $results3[0]['first_name'];

	  $query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id ='.$this->rid;
	  $results_contact2 = $this->connectiondb->Connection($query);
	  $results2 = json_decode(json_encode($results_contact2), true);

	  $query = 'select nickname1, nickname2, ganadorId, jugadorId from Games where ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid.'';
	  $results_contact3 = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results_contact3), true);

	  var_dump($results3);	
	  foreach ($results3 as $key => $value) 
	  {
	  	echo "aganador: ".$value['ganadorId'];
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
	  $replies = array ("Que onda ".$first_name2."! ".$first_name1." te agregó como contacto! Ella ya dio el primer paso te toca a ti! 😏 Para hablar con ella escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. ".$nickname.":MENSAJE)","Que onda ".$first_name2."! Te agregó ".$first_name1.". Deberías escribirle 😉 . Para hablar con ella escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. ".$nickname.":MENSAJE)", "Oye galán, andas con todo! 8| ".$first_name1." te agregó a sus contactos. Para hablar con ella escribe su nombre seguido de dos puntos y tu mensaje será enviado (Ej. ".$nickname.":MENSAJE)");
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
			echo "entras4 ";
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
			echo "entras5 ";
			$channel = "General";
		}
		return $channel;
	}

	public function changeChannel ($location, $education)
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

			//$echo "esdsta es: ".$channel_city;
			$messageData = "{
		    'recipient':{
		      'id': $this->rid
		    },
		    'message':{
		      'text':'A qué canal te quieres cambiar: ',
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

	public function newGame ()
	{	 
		if($this->sexual_orientation >= 1)
		{
			$query = "select fb_id, first_name, fb_sender_id, profile_pic, inte1, inte2, inte3 from Users where fb_id IS NOT NULL AND sexual_orientation = '".$this->sexual_orientation."' AND location = '".$this->channelUser."'";
		} else {
			$query = "select fb_id, first_name, fb_sender_id, profile_pic, inte1, inte2, inte3 from Users where fb_id IS NOT NULL AND gender = 0 AND sexual_orientation = '".$this->sexual_orientation."' AND location = '".$this->channelUser."'";
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
			            'subtitle':'Haz click para entrar a su perfil',
			            'buttons': [{
			              'type':'postback',
			              'title':'Ganador',
			              'payload': 'gano/".$fb_sender_id1."/".$fb_sender_id2."'
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
			              'payload': 'gano/".$fb_sender_id2."/".$fb_sender_id1."'
			            }
			            ]  
			          }
			          ]
			        }
			      }
			    }
			 }";
			 	$replies = array("A quién prefieres?? 😏", "Cena en tu casa, llevarías a: ", "Con quién saldrías?? 😜", "Quién se te hace más guapo?? 😍", "Quién te gusta más??", "Quién pasaría el filtro de tus amigas?? 😳");
		        $this->sendTextMessage($replies);

		        $this->displayBio($results[$num1]['inte1'], $results[$num1]['inte2'], $results[$num1]['inte3'], $results[$num1]['first_name']);
		        $this->displayBio($results[$num2]['inte1'], $results[$num2]['inte2'], $results[$num2]['inte3'], $results[$num2]['first_name']);

			 	$this->callSendApi($messageData);
			}else{
				$replies = array("Por el momento no se encuentra nadie en el canal ".$this->channelUser.", vuelve a intentar más tarde");
		        $this->sendTextMessage($replies);
			}
	}

	public function displayBio ($inte1, $inte2, $inte3, $firstnameUser)
	{
		$inte1_arr = array ("algo serio", "algo casual", "amigos", "diversion");
		$inte2_arr = array ("ir al antro", "ir al cine", "estar con la familia", "haver ejercicio", "ver netflix", "leer");
		$inte3_arr = array ("fuma 🚬", "no fuma 🚭");
		$replies = array ("".$firstnameUser." esta buscando ".$inte1_arr[($inte1-1)].", lo que más le gusta hacer en lo fines es ".$inte2_arr[($inte2-1)]." y ".$inte3_arr[($inte3-1)]);
	    $this->sendTextMessage($replies);
	}

	public function insertUser ()
	{
	  $query = 'select * from Users where fb_sender_id = '.$this->rid;
	  $results_insertUser = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_insertUser), true);

	  $token ="EAAQuAw8ZC2rMBAATnv6OJRU8YP60L8hGpJlUBpxXUOXBmMeJNszS2Gu3UWfCn2CYXauUFzS5ZAoTVGAtSYZAgusY6OZAiH3RoiZAY8sW0ECIWEt19UsIOwUW2AWeJNW59tz2hZC8anTnVbC0ZCzeawRmnJ1ZA28uLbPZBXAayJ8NA0AZDZD";
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

	public function score ()
	{
		$query = "select perdedorId from Games where ganadorId = ".$this->rid."";
		$results = $this->connectiondb->Connection($query);
		$results_perdedor = json_decode(json_encode($results), true);

		if ($results_perdedor[0] == null)
		{
			$replies = array ("Aun no hay resultados, intenta más tarde", "Es muy pronto para resultados, inténtalo más tarde");
			$this->sendTextMessage($replies);
		}else{
			$replies = array ("A ellos les has ganado: 💪");
			$this->sendTextMessage($replies);
		}

		if ($results_perdedor[0] != null)
		{
			$this->showScore($results_perdedor);
		}
	}

	public function showScore($results_perdedor) 
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
			             'subtitle':'Haz click para entrar a su perfil'
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
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'Haz click para entrar a su perfil'
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
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[2]."',          
			            'image_url':'".$profile_pic1[2]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[2]."',
			             'subtitle':'Haz click para entrar a su perfil'
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
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[2]."',          
			            'image_url':'".$profile_pic1[2]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[2]."',
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[3]."',          
			            'image_url':'".$profile_pic1[3]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[3]."',
			             'subtitle':'Haz click para entrar a su perfil'
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
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[1]."',          
			            'image_url':'".$profile_pic1[1]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[1]."',
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[2]."',          
			            'image_url':'".$profile_pic1[2]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[2]."',
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[3]."',          
			            'image_url':'".$profile_pic1[3]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[3]."',
			             'subtitle':'Haz click para entrar a su perfil'
			          },
			          {
			            'title':'".$first_name1[4]."',          
			            'image_url':'".$profile_pic1[4]."',
			            'item_url': 'https://www.facebook.com/".$fb_id1[4]."',
			             'subtitle':'Haz click para entrar a su perfil'
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
		 $token ="EAAQuAw8ZC2rMBAATnv6OJRU8YP60L8hGpJlUBpxXUOXBmMeJNszS2Gu3UWfCn2CYXauUFzS5ZAoTVGAtSYZAgusY6OZAiH3RoiZAY8sW0ECIWEt19UsIOwUW2AWeJNW59tz2hZC8anTnVbC0ZCzeawRmnJ1ZA28uLbPZBXAayJ8NA0AZDZD";
		 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
		 $ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		 $result = curl_exec($ch);
		 curl_close($ch);
	}
	
}