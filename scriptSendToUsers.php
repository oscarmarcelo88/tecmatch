	<?php

			$messageDataSend = "{
	    	'recipient': {
	      	'id': 10154159479161406
	    	},
	    	'message':{    
	      	'text': 'Hola'
	   		 }
	    	}";
		 $token ="EAAIUReNE8dkBAMMYqXANPKSsiGvXQHSCIZA5UZAKB3pYtQK1l4MItZCcw4Ko4ipZB1qJxg7Uiabc6US77CboUezlvVtZBq7oFNRB1J3lIDgbrEfq3wHZBkNiMd1R1G5Xq9ojKB8UZCBHK0jjfXYQNZA6U9qzFY0QCD6iQZBsRqFJy9AZDZD";
		 $url = "https://graph.facebook.com/v2.6/me/messages?access_token=$token";
		 $ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $messageDataSend);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		 $result = curl_exec($ch);
		 curl_close($ch);
	}