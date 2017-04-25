	<?php



			$messageDataSend = "{
	    	'recipient': {
	      	'id': 1142279975821548
	    	},
	    	'message':{    
	      	'text': 'Prueba de super mensaje'
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

	
	