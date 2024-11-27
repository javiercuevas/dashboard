<?php

    // timer
    $msc = microtime(true);

    $app_version = '20200116.01';

    require_once '../models/db.php';
    require_once '../models/dashboard.php';
    require_once '../models/utilities.php';
    require_once '../models/auth.php';

    $dashboard = new Dashboard();
    $utilities = new Utilities();
    $auth = new Auth();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    // params
    $customer_id = isset( $_SESSION['customer_id'] ) ? intval( $_SESSION['customer_id'] ) : -1;
    $survey_id_selected_array = isset( $_SESSION['survey_id_selected_array'] ) == 1 ? $_SESSION['survey_id_selected_array'] : [];
    $surveyed_customer_name = isset( $_SESSION['surveyed_customer_name'] ) == 1 ? $_SESSION['surveyed_customer_name'] : [''];
    $email = isset( $_SESSION['email'] ) == 1 ? $_SESSION['email'] : '';

    //passed in from index controller to avoid some overhead processing if not needed
    $slim_action = isset( $slim_action ) == true ? $slim_action : false;

    // user details$_SESSION['surveyed_customer_name'][0]
    $user_details_results = $auth->user_details_get( $email )->fetch();

    // customer details
    $customer_detail_results = $dashboard->customer_detail_get( $customer_id )->fetch();

    // set to 1 for dev only
    // never set to 1 in prod
    // should be 0 in prod
    if ( $_SERVER["COMPUTERNAME"] == 'JAVI-PC' )
    {
        $allow_everyone = 1;
    }
    else
    {
        $allow_everyone = 0;
    }

    if ( $allow_everyone != 1 )
    {
        // auth
        if ( $email == '' )
        {
            echo '<i class="fa fa-exclamation-triangle fa-5x"></i>' . "<h1>Sorry, you don't have access</h1>";
            die();
        }

        //check if active
        if ( $user_details_results['Active'] != 1 )
        {
            echo '<i class="fa fa-exclamation-triangle fa-5x"></i>' . "<h1>Sorry, this account is not active</h1>";
            die();
        }
    }

    if ( $allow_everyone == 1 && strpos( $action, 'export') == false )
    {
        echo '<i class="fa fa-exclamation-triangle fa-2x" style="color:red"></i> ' . '<span class="red-text-sm">Set to allow everyone to view dashboard</span><br />';
    }

    // flash message
    $session_flash_type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : "success";
    $session_flash_message = isset($_SESSION['flash_message']) ? $_SESSION['flash_message'] : "";

    // if statment to avoid unnecessary queries on certain actions
    if ( $slim_action == false )
    {
        // survey list selected display
        $surveys_selected_results = $dashboard->survey_list_get( $customer_id, $survey_id_array = $survey_id_selected_array )->fetchAll( PDO::FETCH_ASSOC);
        $surveys_selected_list = "";

        foreach ( $surveys_selected_results as $row)
        {
            if ($row['Survey'] == $row['Description'])
            {
                $surveys_selected_list .= $row['Survey'] . ', ';
            }
            else 
            {
                $surveys_selected_list .= $row['Survey'] . '  (' . $row['Description'] . ')' . ', ';
            }
        }

        $surveys_selected_list = substr($surveys_selected_list, 0, strlen($surveys_selected_list) - 2);

        // min/max dates for this customer
        $min_max_survey_dates = $dashboard->min_max_survey_dates_get( $customer_id )->fetch();
        $min_date = $min_max_survey_dates['MinDate'];
        $max_date = $min_max_survey_dates['MaxDate'];
    }

    // dashboard default dates
    $dashboard_defaults = $dashboard->dashboard_defaults_get()->fetch();

    // set default dates
    $date_from_default = isset( $_SESSION['date_from'] ) == 1 && trim( $_SESSION['date_from'] ) != '' ? $_SESSION['date_from'] : $dashboard_defaults['DateFrom'];
    $date_to_default = isset( $_SESSION['date_to'] ) == 1 && trim( $_SESSION['date_to'] ) != '' ? $_SESSION['date_to'] : $dashboard_defaults['DateTo'];

    //in case we have invalid dates in session
    $date_from_default = $utilities->validateDate($date_from_default) == 0 ? $dashboard_defaults['DateFrom'] : $date_from_default;
    $date_to_default = $utilities->validateDate($date_to_default) == 0 ? $dashboard_defaults['DateTo'] : $date_to_default;


    // flash message
    $utilities->flash_message_display($flash_type = $session_flash_type , $flash_message = $session_flash_message);

    flush();

?>
