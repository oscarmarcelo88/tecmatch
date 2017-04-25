	<?php
			$query = "select first_name, last_name, fb_sender_id from Users";
	  		$results = Connection($query);
	  		$results_tm = json_decode(json_encode($results), true);
	  		
	  		$cont = 0;

	  		foreach ($results_tm as $value) {
	  			//echo $value["fb_id"];
		  		//$query = "select fb_id, first_name from Users where fb_id = ".$value["fb_id"];
		  		//echo($value["first_name"]);
		  		$query = "select first_name, last_name from Users where first_name = '".$value["first_name"]."' and last_name = '".$value["last_name"]."'";
		  		//var_dump($query);
		  		$results = Connection2($query);
		  		$results_ha = json_decode(json_encode($results), true);
		  		//var_dump($results_ha);	
		  		if ($results_ha == null)
		  		{
		  					$fb_sender = $value["fb_sender_id"];
		  					$messageDataSend = "{
					    	'recipient': {
					      	'id': $fb_sender
					    	},
					    	'message':{    
					      	'text': 'Hola ".$value["first_name"]."! Te invito a probar Hi Alice 🤖 \\nEs como Tec Match pero mejorado 😏 \\nEntra a esta liga: https://www.messenger.com/t/403864166613488'
					   		 }
					    	}";
					  	
					  	$token = "EAAYzZBr1heJ8BAAfI0wXgvTMOS3ca3edpYY3j472LdcfBFOTtjm4eOwFo4ZAcAHOvlZCYVdiiEhZA5ebntMEODRqI4weNtQS4hRzILy3K6IuofnLYKD2PWItZAL1DtP1WynaWGJefBTrdq7hL7OcXsHZCJy7AW8EiDXqdBVx0quQZDZD";
						 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
						 $ch = curl_init($url);
						 curl_setopt($ch, CURLOPT_POST, 1);
						 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
						 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
						 $result = curl_exec($ch);
						 curl_close($ch);
		  		}
	  		}

			$messageDataSend = "{
	    	'recipient': {
	      	'id': 1142279975821548
	    	},
	    	'message':{    
	      	'text': 'Hola Oscar!'
	   		 }
	    	}";
	  	
	  	$token = "EAAIUReNE8dkBAMcdnW5Tgf3Ww6cZCpDzexUp8ZAB7xZB70cj89PtnI6lU6mWX2DG7M6CifA9wRmBhwAOwZBZCHXHlM3f9qQWRAV8XVrbusx8fJGuEeAzwtiWxJgzmcDIXFZAsSVdtWeIH5Np1QZCzd0si94eWfiJHFSpkpWrxHeVgZDZD";
		 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
		 $ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		 $result = curl_exec($ch);
		 curl_close($ch);

	function Connection ($query)
	{
		$db_host = "tecmatch.co";
		$db_name = "tecmatch_tecmatchdb";
		$db_username = "tecmatch_user";
		$db_pass = "Tecmatch88";
		try {
	    	$pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
	  	} catch (PDOException $e) {

	    	echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
	    	exit;
	  	}
	  	$statement = $pdo->prepare($query);
	  	$statement-> execute();
	  	$results = $statement->fetchAll(PDO::FETCH_OBJ);
	  	return $results;
   }

   	function Connection2 ($query)
	{
	 $db_host = "tecmatch.co";
	 $db_name = "tecmatch_alice";
	 $db_username = "tecmatch_alice";
	 $db_pass = "Tecmatch88";
		try {
	    	$pdo = new PDO ("mysql:host=".$db_host.";dbname=".$db_name."",$db_username,$db_pass);
	  	} catch (PDOException $e) {

	    	echo "Failed to get Base de Datos handle: " . $e->getMessage() . "\n";
	    	exit;
	  	}
	  	$statement = $pdo->prepare($query);
	  	$statement-> execute();
	  	$results = $statement->fetchAll(PDO::FETCH_OBJ);
	  	return $results;
   }	