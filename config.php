<?php

	//BD  alice
/*
 $db_host = "tecmatch.co";
 $db_name = "tecmatch_alice";
 $db_username = "tecmatch_alice";
 $db_pass = "Tecmatch88";
	*/

  //BD real
/*
  $db_host = "tecmatch.co";
  $db_name = "tecmatch_tecmatchdb";
  $db_username = "tecmatch_user";
  $db_pass = "Tecmatch88";
*/

	//BD prueba alice
  /*
$db_host = "tecmatch.co";
 $db_name = "tecmatch_alice";
 $db_username = "tecmatch_alice";
 $db_pass = "Tecmatch88";
	*/

//Testing
 $db_host = "localhost";
 $db_name = "test_TecMatch";
 $db_username = "root";
 $db_pass = "root";
 $urlWebhook = "https://31a3f70a.ngrok.io/tecmatch/";
 $app_id = '585240351666649';
 $app_secret = '0c360663f24dec79e8428e58cc2069ee';
 $default_graph_version ='v2.8';
 $token = "EAAIUReNE8dkBAIGcJ8YN1JVT2tr4ojW6Yf2i8MWU1LST3ZBlmLerf7VVR8h0zzHGNyi8ycVZC4xkjBcEzkqBLvP8uvxvhQHhEZBemUfePKxPGvv29lQ5PNWemkxZCFdcl1l5A3r3WvbEvm6q2UFoSgi2ZBkHwN54itk7tZBAhZAnAZDZD";

	putenv("db_host=$db_host");
	putenv("db_name=$db_name");
	putenv("db_username=$db_username");
	putenv("db_pass=$db_pass");
	putenv("urlWebhook=$urlWebhook");
	putenv("app_id=$app_id");
	putenv("app_secret=$app_secret");
	putenv("default_graph_version=$default_graph_version");
	putenv("token=$token");

?>