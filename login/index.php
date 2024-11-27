<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CEO Juice - Survey Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<?php

 	session_start();

	require_once '../models/db.php';
	require_once '../models/auth.php';

    $auth = new Auth();

    // convert get variables to lowercase
    $_GET_lower = array_change_key_case($_GET, CASE_LOWER);

    $email = isset( $_GET_lower['email'] ) == 1 ? $_GET_lower['email'] : '';

    $encrypted_string = isset( $_GET_lower['token'] ) == 1 ? $_GET_lower['token'] : '';

    $auth_info_results = $auth->auth_info_get( $email, $encrypted_string );

    if ( $auth_info_results == true ) 
    {
    	$_SESSION['email'] = $email;

        // set the user default company
        $user_details_results = $auth->user_details_get( $email )->fetch();

        $_SESSION['customer_id'] = $user_details_results['CustomerID'];
        //redirect to home page
        header( 'location: ../home' );
    }
    else 
    {
        echo '<i class="fa fa-exclamation-triangle fa-5x"></i>' . "<h1>Sorry, you don't have access</h1>";

        die();
    }

?>

