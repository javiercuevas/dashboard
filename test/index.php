<?php	
		//$email = 'sjameson@conwayoffice.com';
		$email = 'javier@ceojuice.com';
		$secret_key = 'or@ng3';

		for( $i = 0; $i <= 15; $i++) {

			$date_string = date("YmdHi", strtotime("-{$i} minute"));
			$combined_string = $email . $date_string . $secret_key;
			$md5_string = md5($combined_string);

			echo($md5_string);
			echo('<br />');
		}	

?>