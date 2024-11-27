<?php

	require_once '../models/db.php';
	require_once '../models/auth.php';
	require_once '../models/utilities.php';

	$auth = new Auth();
	$utilities = new Utilities();

	$auth->pre_run_check( $_SERVER['PHP_SELF']  );

	$filename = 'nps-data.xls';

	// download file
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: application/vnd.ms-excel");

	// queries
    $nps_promoters_detractors_detail_results = $dashboard->nps_promoters_detractors_detail_get( 
    											$customer_id = $customer_id, 
    											$date_from = $date_from_default, 
    											$date_to = $date_to_default,
    											$survey_id_array = $survey_id_selected_array,
    											$eauto_customer_name = $surveyed_customer_name
    											)->fetchAll( PDO::FETCH_ASSOC );


	// write data to file
	$flag = false;
	foreach( $nps_promoters_detractors_detail_results as $row ) 
	{
	    if ( !$flag ) 
	    {
	        // display field/column names as first row
	        echo implode("\t", array_keys( $row )) . "\r\n";
	        $flag = true;
	    }
	    // call clean data function from utilities to clean for excel format
	    array_walk($row, array($utilities, 'cleanData'));
	    echo implode("\t", array_values( $row )) . "\r\n";
	}

?>