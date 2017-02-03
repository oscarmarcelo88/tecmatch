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

	public function sendTextMessageToContact ($nickname, $reply)
	{
		$query2 = 'select ganadorId, jugadorId, nickname1, nickname2 from Games WHERE (ganadorId ='.$this->rid.' OR jugadorId ='.$this->rid.') AND (nickname1 IS NOT NULL)';
	  	$results_contact2 = $this->connectiondb->Connection($query2);
	  	$results2 = json_decode(json_encode($results_contact2), true);
	  	$recipientId = null;
	  	
	  	var_dump($results2);
	  	foreach ($results2 as $value)
	  	{
	  		echo $value['jugadorId']." y ".$value['nickname1'];
	  		if ($value['jugadorId'] == $this->rid && $value['nickname2'] == $nickname)
	  		{
	  			$recipientId = $value['ganadorId'];
	  			echo "entras1";
	  		}
	  		echo $value['ganadorId']." y ".$value['nickname2'];
	  		if ($value['ganadorId'] == $this->rid && $value['nickname1'] == $nickname)
	  		{
	  			$recipientId = $value['jugadorId'];
	  			echo "entras2";
	  		}
	  	}


	  	if ($recipientId == null)
	  	{
	  		$recipientId = $this->rid;
	  		$reply = "Ese contacto no existe";
	  		echo "entras3";
	  	}

		$messageData = "{
    	'recipient': {
      	'id': $recipientId
    	},
    	'message':{    
      	'text': '".$reply."'
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

	public function preguntaMensaje($replies)
	{
	  $numReplies = count ($reply);
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

	public function preguntaMensajePuntaje($replies)
	{
	  $numReplies = count ($reply);
	  $messageData = "{
	    'recipient':{
	      'id': $this->rid
	    },
	    'message':{
	      'text':'".$replies[rand(0,$numReplies)]."',
	      'quick_replies':[
	        {
	          'content_type':'text',
	          'title':'Puntaje',
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
	          'title':'Hombres',
	          'payload':'sexhombres'
	        },
	        {
	          'content_type':'text',
	          'title':'Mujeres',
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
	          'title':'Algo serio',
	          'payload':'inte1/1'
	        },
	        {
	          'content_type':'text',
	          'title':'Casual',
	          'payload':'inte1/2'
	        },
	        {
	          'content_type':'text',
	          'title':'Amigos',
	          'payload':'inte1/3'
	        },
	        {
	          'content_type':'text',
	          'title':'Diversión',
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
	          'title':'Antro',
	          'payload':'inte2/1'
	        },
	        {
	          'content_type':'text',
	          'title':'Cine',
	          'payload':'inte2/2'
	        },
	        {
	          'content_type':'text',
	          'title':'Familia',
	          'payload':'inte2/3'
	        },
	        {
	          'content_type':'text',
	          'title':'Ejercicio',
	          'payload':'inte2/4'
	        },
	        {
	          'content_type':'text',
	          'title':'Netflix',
	          'payload':'inte2/5'
	        },
	        {
	          'content_type':'text',
	          'title':'Leer',
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
	      'text':'¿Fumar? 🚬🚬',
	      'quick_replies':[
	       	{
	          'content_type':'text',
	          'title':'Sí',
	          'payload':'inte3/1'
	        },
	        {
	          'content_type':'text',
	          'title':'No',
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
		    $inte1 = 2;
		  break;
		    case 'inte3':
		    $this->changeInte($answer, 'inte3');
		    $inte1 = 3;
		  break;
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

	public function score ()
	{
		$query = "select perdedorId from Games where ganadorId = ".$results_ganador[0]['fb_sender_id']."";
		$results = $this->connectiondb->Connection($query);
		$results_perdedor = json_decode(json_encode($results), true);

		$cont = 0;

		if ($results_perdedor[0] == null)
		{
			$replies = array ("Aun no hay resultados, intenta más tarde", "Es muy pronto para resultados, inténtalo más tarde");
			$this->sendTextMessage($replies);
		}else{
			$replies = array ("A ellos les has ganado: ");
			$this->sendTextMessage($replies);
		}

		while ($results_perdedor[$cont] != null && $cont <= 5)
		{
			$this->showScore($results_ganador[0]['fb_sender_id'], $results_perdedor[$cont]['perdedorId']);
			$cont ++;
		}
	}

	public function showScore($ganadorId, $perdedorId) 
	{
	  $query = "select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id = ".$ganadorId.""; 
 	  $results = $this->connectiondb->Connection($query);
	  $results2 = json_decode(json_encode($results), true);

	  $fb_id1 = $results2[0]['fb_id'];
	  $first_name1 = $results2[0]['first_name'];
	  $fg_sender_id1 = $results2[0]['fb_sender_id'];
	  $profile_pic1 = $results2[0]['profile_pic'];

	  $query = "select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_sender_id = ".$perdedorId."";
 	  $results4 = $this->connectiondb->Connection($query);
	  $results3 = json_decode(json_encode($results4), true);

	  $fb_id2 = $results3[0]['fb_id'];
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
	            'title':'".$first_name2."',          
	            'image_url':'".$profile_pic2."',
	            'item_url': 'https://www.facebook.com/".$fb_id2."',
	             'subtitle':'Haz click para entrar a su perfil'
	          }
	          ]
	        }
	      }
	    }
	 }";

	 //vamos a mandarle los ´último que ha ganado
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
	      'text':'".$reply[rand(0,$numReplies)]."',
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

	  if ($results3[0]['ganadorId'] == $this->rid)
	  {
	  	$nickname = $results3[0]['nickname1'];
	  }else if ($results3[0]['jugadorId'] == $this->rid)
	  {
	  	$nickname = $results3[0]['nickname2'];
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
	  $replies = array ("Que onda ".$first_name2."! Mira, ".$first_name1." te agrego como contacto! Ella ya dio el primer paso te toca a ti! Para mandarle un mensaje tienes que tienes que escribir: ".$nickname.":MENSAJE","Que onda ".$first_name2."! Te agrego ".$first_name1.". Ella quiere que le escribas. Para mandarle un mensaje tienes que tienes que escribir: ".$nickname.":MENSAJE", "Oye galán, andas con todo! ".$first_name1." te agrego a sus contactos. Para mandarle un mensaje tienes que tienes que escribir: ".$nickname.":MENSAJE");
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
			echo "entras2 ".$value;
			$query = "select type, name from Locations where name = '".$value."'";

	  		$results = $this->connectiondb->Connection($query);
	  		$results2 = json_decode(json_encode($results), true);
	  		var_dump($results2);
	  		if ($results != null)
	  		{
	  			echo "entas chido";
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

	public function setNickname ($userId, $typeNick, $otherUserId)
	{
	  $query = 'select first_name from Users where fb_sender_id ='.$otherUserId;
	  $results_contact = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_contact), true);
	  $jugador_firstname = $results[0]['first_name'];
	  
	  $query2 = 'select '.$typeNick.', contactar from Games where ganadorId ='.$userId.' OR jugadorId ='.$userId.'';
	  $results_contact2 = $this->connectiondb->Connection($query2);
	  $results2 = json_decode(json_encode($results_contact2), true);
	  $cont = 0;

	  foreach ($results2 as $value) {
	  	if ($jugador_firstname == $value[$typeNick])
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
	  //$query = 'select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_id IS NOT NULL AND (gender = 0 OR sexual_orientation = 0) AND location = ITESM';

	  $query = "select fb_id, first_name, fb_sender_id, profile_pic from Users where fb_id IS NOT NULL AND (gender = 0 OR sexual_orientation = '".$this->sexual_orientation."') AND location = '".$this->channelUser."'";
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
		 	$replies = array("¿Quién esta más guapo?", "Mira, a quién le presentarías a tu mamá?", "¿A cuál invitarías a salir?");
	        $this->sendTextMessage($replies);
		 	$this->callSendApi($messageData);
		}else{
			$replies = array("Por el momento no se encuentra nadie en el canal ".$this->channelUser.", vuelve a intentar más tarde");
	        $this->sendTextMessage($replies);
		}
	}

	public function insertUser ()
	{
	  $query = 'select * from Users where fb_sender_id = '.$this->rid;
	  $results_insertUser = $this->connectiondb->Connection($query);
	  $results = json_decode(json_encode($results_insertUser), true);

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