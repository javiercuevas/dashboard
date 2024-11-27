
<?php

class Utilities
{

	public function flash_message_display ( $flash_type = "success", $flash_message ) 
	{
		$msg = "";

		// fix in case of blank
		if ($flash_type == "") 
		{
			$flash_type = "success";
		}
		// ifs
		if ($flash_type == "success" && strlen($flash_message) > 0) 
		{
			$msg = '<b><p class="bg-success"><span class="glyphicon glyphicon-ok-sign">&nbsp;</span>' . $flash_message . '</p></b>';
		}
		elseif ($flash_type == "danger") 
		{
			$msg = '<b><p class="bg-danger"><span class="glyphicon glyphicon-exclamation-sign">&nbsp;</span>' . $flash_message . '</p></b>';
		}
		elseif ($flash_type == "warning") 
		{
			$msg = '<b><p class="bg-warning"><span class="glyphicon glyphicon-question-sign">&nbsp;</span>' . $flash_message . '</p></b>';
		}
		elseif ($flash_type == "info") 
		{
			$msg = '<b><p class="bg-info"><span class="glyphicon glyphicon-info-sign">&nbsp;</span>' . $flash_message . '</p></b>';
		}
		elseif ($flash_type == "primary") 
		{
			$msg = '<b><p class="bg-primary"><span class="glyphicon glyphicon-ok-sign">&nbsp;</span>' . $flash_message . '</p></b>';
		}

		echo $msg;
	}

	public function cleanData ( &$str )
	{
		// escape tab characters
		$str = preg_replace("/\t/", "\\t", $str);

		// escape new lines
		$str = preg_replace("/\r?\n/", "\\n", $str);

		// convert 't' and 'f' to boolean values
		if($str == 't') $str = 'TRUE';
		if($str == 'f') $str = 'FALSE';

		// force certain number/date formats to be imported as strings
		/*
		if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
		  $str = "'$str";
		}
		*/

		// escape fields that include double quotes
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
	}

	public function validateDate( $date )
	{
		$arr  = explode('/', $date);
		if (count($arr) == 3) 
		{
		    if (checkdate($arr[0], $arr[1], $arr[2])) 
		    {
		        return true;
		    } 
		    else 
		    {
		        // problem with dates ...
		        return false;
		    }
		} 
		else 
		{
		    // problem with input ...
		    return false;
		}
	}

	public function surveyed_customer_selected_display ( $surveyed_customer_name )
	{
		$message = implode(', ', $surveyed_customer_name) == '' ? 
        	'Surveyed Customer: ALL' : 
            'Surveyed Customer: ' . count($surveyed_customer_name) . ' Selected' . '<br />' . "<span class='light-gray-md'>" . implode(', ', $surveyed_customer_name) . "</span>";

        echo $message;
	}

}

?>