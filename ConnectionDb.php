<?php

class ConnectionDb
{
	//BD real
	/*
	public $db_host = "tecmatch.co";
	public $db_name = "tecmatch_tecmatchdb";
	public $db_username = "tecmatch_user";
	public $db_pass = "Tecmatch88";
	*/

	//BD prueba
	public $db_host = "localhost";
	public $db_name = "test_TecMatch";
	public $db_username = "root";
	public $db_pass = "root";

	public function Connection ($query)
	{
		global $db_host, $db_name, $db_username, $db_pass;
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
}