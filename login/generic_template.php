<?


new GenericTemplate()

class GenericTemplate {


	function send() {
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
		 sendApi()
	}

}