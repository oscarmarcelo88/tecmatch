	<?php

    require '../config.php';

    $db_host = getenv("db_host");
    $db_name = getenv("db_name");
    $db_username = getenv("db_username");
    $db_pass = getenv("db_pass");
    $token = getenv("token");


require '../files/Functions.php';
require '../files/ConnectionDb.php';

	 $connectiondb = new ConnectionDb();

	 //for testing:
    $query = "select first_name, fb_id, profile_pic, fb_sender_id, locale, sexual_orientation, gender, inte1, inte2, inte3, studied_at, lives_in from Users Where fb_sender_id = '1142279975821548'";

   // $query = "select first_name, fb_id, profile_pic, fb_sender_id, sexual_orientation, gender, inte1, inte2, inte3, studied_at, lives_in from Users";
		  		$results = $connectiondb->Connection($query);
		  		$results_ha = json_decode(json_encode($results), true);



	foreach ($results_ha as $key => $value) {
		if ($value["gender"] == 1) //always send the notif to the girls
		{
			sendNotifcation($results_ha, $value["first_name"], $value["fb_sender_id"], $value["sexual_orientation"], $value["locale"], $value["gender"], $value["inte1"], $value["inte2"], $value["inte3"], $value["studied_at"], $value["lives_in"]);
		} else if ($value["gender"] == 0){
			$numRandom = rand (0, 5);
			if ($numRandom > 0) //notify just a 50% of the males
			{
				sendNotifcation($results_ha, $value["first_name"], $value["fb_sender_id"], $value["sexual_orientation"], $value["locale"], $value["gender"], $value["inte1"], $value["inte2"], $value["inte3"], $value["studied_at"], $value["lives_in"]);
			}
		}	
	}

	function sendNotifcation ($results_ha, $first_name, $fb_sender_id, $sexual_orientation, $locale, $gender, $inte1, $inte2, $inte3, $studied_at, $lives_in)
	{
        //decide the language with locale
        if($locale == "es_LA" || $locale == "es_ES")
        {
            include_once '../files/langES.php';
        }else{
            include_once '../files/langEN.php';
        }

	    do {
			//This will be the number of the option to choose
			$num_option = rand(1,5);
			switch ($num_option)
			{
				case 1:
					$sent = matchWithInterviews($results_ha, $first_name, $fb_sender_id, $sexual_orientation, $gender, $inte1, $inte2, $inte3, $lang['CRON_MATCHINTERVIEWS']);
					break;
				case 2:
					$sent = proposeNewChannel($first_name, $fb_sender_id, $lives_in, $studied_at, $lang['CRON_NEWCHANNEL'], $lang['CHANGE_CHANNEL']);
					break;
				case 3:
					$sent = newPeopleMessage($first_name, $fb_sender_id, $sexual_orientation, $gender, $lang['CRON_NEWPEOPLE']);
					break;
				case 4:
					$sent = checkScoreMessage($first_name, $fb_sender_id, $sexual_orientation, $gender, $lang['CRON_SCOREMSG']);
					break;
				case 5:
					$sent = recommendContact($first_name, $fb_sender_id, $lang['CRON_RECOMMENDCONTACT']);

					break;
			}
		}while ($sent == false);

	}

    function matchWithInterviews($results_ha, $first_name, $fb_sender_id, $sexual_orientation, $gender, $inte1, $inte2, $inte3, $text_MatchInt)
	{
        global $token;
		$array_winners = array();

		if ($sexual_orientation != 0 || $gender != 0) //we exclude the hetero males.
		{
			foreach ($results_ha as $key => $value) {
				if ($gender == 1 && $sexual_orientation == 0) //confirm if they are hetero girls so the search will be only heter guys.
					{
						$gender_type = 0;
					}else{
						$gender_type = $gender;
					}

				if (($value["fb_sender_id"] != $fb_sender_id) && ($sexual_orientation == $value["sexual_orientation"]) && $gender_type == $value["gender"]) //it have to be different person and with the same sexual orientation
				{
					$puntaje = 0;
					if ($value["inte1"] == $inte1)
					{
						$puntaje ++;
					}
					if ($value["inte2"] == $inte2)
					{
						$puntaje ++;
					}
					if ($value["inte1"] == $inte1)
					{
						$puntaje ++;
					}
					if ($puntaje > 1)
					{
						array_push($array_winners, $value);
					}

				}
			}
				$max = sizeof($array_winners);

			//if ($fb_sender_id == 1109242549184441 && $max > 0) //esto es solo para testing
			if ($max > 0)
			{
				do{
					$choosenNumber1 = rand (0, $max-1);
					$choosenNumber2 = rand (0, $max-1);
				} while ($choosenNumber1 == $choosenNumber2);
				//var_dump($array_winners);
				$functions = new Functions($fb_sender_id, null, null, null, null, null, null, $token);

				  $fb_id1 = $array_winners[$choosenNumber1]['fb_id'];

				  $first_name1 = $array_winners[$choosenNumber1]['first_name'];
				  $fb_sender_id1 = $array_winners[$choosenNumber1]['fb_sender_id'];
				  $profile_pic1 = $array_winners[$choosenNumber1]['profile_pic'];

				  $fb_id2 = $array_winners[$choosenNumber2]['fb_id'];
				  $first_name2 = $array_winners[$choosenNumber2]['first_name'];
				  $fb_sender_id2 = $array_winners[$choosenNumber2]['fb_sender_id'];
				  $profile_pic2 = $array_winners[$choosenNumber2]['profile_pic'];
				 
				  $messageData = "{
				    'recipient': {
				      'id': $fb_sender_id
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
				            'subtitle':'".$text_MatchInt[0]."',
				            'buttons': [{
				              'type':'postback',
				              'title':'".$text_MatchInt[1]."',
				              'payload': 'gano/".$fb_sender_id1."/".$fb_sender_id2."'
				            }
				            ]  
				          },
				          {
				            'title':'".$first_name2."',
				          
				            'image_url':'".$profile_pic2."',
				            'item_url': 'https://www.facebook.com/".$fb_id2."',
				            'subtitle':'".$text_MatchInt[0]."',
				            'buttons': [{
				              'type':'postback',
				              'title':'".$text_MatchInt[1]."',
				              'payload': 'gano/".$fb_sender_id2."/".$fb_sender_id1."'
				            }
				            ]  
				          }
				          ]
				        }
				      }
				    }
				 }";
				 	$replies = array($text_MatchInt[2].$first_name.$text_MatchInt[3]);
			       	$functions->sendTextMessage($replies);
				 	$functions->callSendApi($messageData);
				 	return true;
			//}else{return false;
			} else {return false;}

		}else{
			return false;
		}

	}


	function proposeNewChannel ($first_name, $fb_sender_id, $lives_in, $studied_at, $text_ProposeCh, $text_ChangeChannel)
	{
		global $connectiondb, $token;
		$functions = new Functions($fb_sender_id, null, null, null, null, null, null, $token);
		//code to see how many channels they are
			$channelAvailables = false; //means that there is more than General channel available
			$edu_array = explode("/",$studied_at);
			foreach($edu_array as $key=>$value)
			{
				$query = "select type, name from Locations where name = '".$value."'";
		  		$results = $connectiondb->Connection($query);
		  		$results2 = json_decode(json_encode($results), true);
		  	
		  		if ($results != null)
		  		{
		  			$channelAvailables = true;
		  		}
			}
				$query = "select type from Locations where name = '$lives_in'";
		  		$results = $connectiondb->Connection($query);

		  		if ($results != null)
		  		{
		  			$channelAvailables = true;
		  		}
				
		//if ($fb_sender_id == 1109242549184441) //para testing
		//{
			if ($channelAvailables)
			{
				$replies = array($first_name.$text_ProposeCh[0]);
				$functions->sendTextMessage($replies);
				$functions->changeChannel($lives_in, $studied_at, $text_ChangeChannel);
				return true;
			} else {
				return false;
			}
		//} else {return true;}
	}

	function newPeopleMessage ($first_name, $fb_sender_id, $sexual_orientation, $gender, $text_NewPpl)
	{
		global $token;
	    $functions = new Functions($fb_sender_id, null, null, null, null, null, null, $token);
		//if ($fb_sender_id == 1109242549184441) //para testing
		//{
			if (($sexual_orientation != 0 || $gender != 0) && $sexual_orientation != 1 && $sexual_orientation != 2)  //no hetero males, gays males, no lesbians
			{
				$replies = array($text_NewPpl[0].$first_name.$text_NewPpl[1]);
				$functions->preguntaMensaje($replies);
				return true;
		    }else{return false;}
		//} else {return true;}
	}

	function checkScoreMessage ($first_name, $fb_sender_id, $sexual_orientation, $gender, $text_CheckScore)
	{
		global $connectiondb, $token;
		$functions = new Functions($fb_sender_id, null, null, null, null, null, null, $token);
		$newScores = false;
		//if ($fb_sender_id == 880478048722246) //para testing
		//{
			if ($sexual_orientation == 0 && $gender == 0)
			{
				//code to know if they have new score in the last 2 days
				$query = "select updated_at from Games where ganadorId = '".$fb_sender_id."'";
			  	$results = $connectiondb->Connection($query);
			  	$results2 = json_decode(json_encode($results), true);

			  	foreach ($results2 as $key => $value) {
			  		date_default_timezone_set('America/Chicago'); // Set the time in CDT                             						
			  		$timeNow = strtotime("now");
					if (($timeNow - 172800) < $value["updated_at"])
						{
							$newScores = true;
						}
				  	}
				if ($newScores)
				{
					$replies = array ($text_CheckScore[0]);
      				$functions->preguntaMensajePuntaje($replies);
					return true;
				}else {return false;}
				
		    }else{return false;}
		//} else {return true;}
	}

	function recommendContact($first_name, $fb_sender_id, $text_RecommendContact)
	{
		global $connectiondb, $token;
		$functions = new Functions($fb_sender_id, null, null, null, null, null, null, $token);

		//if ($fb_sender_id == 880478048722246) //para testing
		//{
			//query to get their contacts
			$query = "select ganadorId, jugadorId, nickname1, nickname2 from Games where (ganadorId = '".$fb_sender_id."' OR jugadorId = '".$fb_sender_id."') AND nickname1 IS NOT NULL";
			$results = $connectiondb->Connection($query);
			$results2 = json_decode(json_encode($results), true);
		if ($results2 != null)
		{
			$numUser = rand(0, sizeof($results2)-1);

			//we need to know if it's the jugadorId or ganadorId
				if ($results2[$numUser]["jugadorId"] == $fb_sender_id)
				{
					$idRecommendedUser = $results2[$numUser]["ganadorId"];
					$nicknameUser = $results2[$numUser]["nickname2"];
				}else{
					$idRecommendedUser = $results2[$numUser]["jugadorId"];
					$nicknameUser = $results2[$numUser]["nickname1"];
				}
			

			//query to get the info of the user that we are going to show
			$query = "select fb_id, first_name, profile_pic from Users where fb_sender_id = '".$idRecommendedUser."'";
			$results = $connectiondb->Connection($query);
			$results2 = json_decode(json_encode($results), true);

				
				  $fb_id1 = $results2[0]['fb_id'];
				  $first_name1 = $results2[0]['first_name'];
				  $fb_sender_id1 = $results2[0]['fb_sender_id'];
				  $profile_pic1 = $results2[0]['profile_pic'];

				  $messageData = "{
				    'recipient': {
				      'id': $fb_sender_id
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

				$functions->callSendApi($messageData);

				$replies = array ($text_RecommendContact[0].$first_name.$text_RecommendContact[1].$first_name1.$text_RecommendContact[2].$nicknameUser.$text_RecommendContact[3]);
				$functions->sendTextMessage($replies);
				return true;
			} else {return false;}
		//} else {return true;}

	}
	