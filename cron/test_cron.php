	<?php

/*var_dump(__FILE__);

$myfile = fopen("fb.txt", "w") or die("Unable to open file!");
$txt = __FILE__;
fwrite($myfile, $txt);
fclose($myfile);*/

			$messageDataSend = "{
	    	'recipient': {
	      	'id': 1142279975821548
	    	},
	    	'message':{    
	      	'text': 'Prueba de super mensaje'
	   		 }
	    	}";

	  	
	  	$token = "EAAIUReNE8dkBAIGcJ8YN1JVT2tr4ojW6Yf2i8MWU1LST3ZBlmLerf7VVR8h0zzHGNyi8ycVZC4xkjBcEzkqBLvP8uvxvhQHhEZBemUfePKxPGvv29lQ5PNWemkxZCFdcl1l5A3r3WvbEvm6q2UFoSgi2ZBkHwN54itk7tZBAhZAnAZDZD";
		 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
		 $ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		 $result = curl_exec($ch);
		 curl_close($ch);

	
	