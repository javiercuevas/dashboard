<?php

Class Auth
{
	public function auth_info_get ( $email, $encrypted_string )
	{
		$results = false;
		$secret_key = 'or@ng3';

		//loop to represent 15 different minutes
		//since each minute string is hashed to compare
		for( $i = 0; $i <= 15; $i++)
		{

			$date_string = date("YmdHi", strtotime("-{$i} minute"));
			$combined_string = $email . $date_string . $secret_key;
			$md5_string = md5($combined_string);

			if ( $encrypted_string == $md5_string )
			{
				$results = true;
			}
		}

		if ($results == false)
		{
			//log failed login
			$this->audit_log( $email, $encrypted_string );
		}

		/*
		//temp fix for this user
		if ( $email == 'sjameson@conwayoffice.com' )
		{
			$results = true;
		}
		*/

		return $results;
	}

	public function user_details_get( $email )
	{
		global $db;
		$sql = "
			select	top 1
					ContactID,
					FirstName,
					LastName,
					Email,
					SecurityID,
					Active,
					CustomerID
			from	dbo.INF_Contacts with (nolock)
			where	Email = '{$email}'
		";
		$results = $db->query( $sql );
		return $results;
	}

	public function audit_log( $email, $encrypted_string )
	{
		global $db;
		$sql = "
			insert into SV_SurveyFailedLoginAudit (Email, Token, RemoteAddress)
			values ('{$email}', '{$encrypted_string}', '{$_SERVER['REMOTE_ADDR']}')
		";
		$results = $db->query( $sql );
		return $results;
	}

	public function pre_run_check ( $php_self )
	{
	    if ( stripos( $php_self , 'dsp-') == true or stripos( $php_self , 'inc-') == true)
	    {
	    	echo '<link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">';
	        echo '<i class="fa fa-exclamation-triangle fa-5x"></i>' . "<h1>Sorry, this page can't be called directly</h1>";
	        exit();
		}
	}

}

?>
