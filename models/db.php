<?php

	if ( $_SERVER["COMPUTERNAME"] == 'JAVI-PC' ) 
	{
		$dsn = 'sqlsrv:server=LOCALHOST\SQL2016;database=CEOJuiceJuiceApp';
		$username = 'test';
		$password = 'test';	
	} 
	else 
	{
		$dsn = 'sqlsrv:server=JuiceSQL2;database=CEOJuice';
		$username = 'dashboard';
		$password = 'mPNDW8qqtz2mrhijaBVn;';	
	}
		
	try 
	{
		$db = new PDO($dsn, $username, $password);
	}
	catch (PDOException $e) 
	{
		$error_message = $e->getMessage();
		echo $error_message;
		exit();
	}


?>