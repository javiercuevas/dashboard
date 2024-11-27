<?php

    // session stuff
    session_start();

    // page actions
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    switch ($action) 
    {  
        case 'overall-nps-details':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-detail-promoters-detractors.php';
            include 'dsp-footer.php';
            break;

        case 'nps-by-year':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-nps-by-year.php';
            include 'dsp-footer.php';
            break;

        case 'nps-benchmarks':
            $slim_action = true;
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-nps-summary.php';
            include 'dsp-footer.php';
            break;

        case 'nps-benchmarks-link':
            echo '<h1><span class="light-gray-lg">CEO Juice Survey Dashboard</span></h1>';
            include '../home/dsp-header-min.php';
            include '../home/dsp-nps-summary.php';
            include '../home/dsp-footer.php';
            break;

        case 'set-nps-summary-periods':
            // echo '<pre>';
            // print_r($_POST);

            if ( $_POST['period_from'] > $_POST['period_to'] ) 
            {
                $_SESSION['flash_type'] = "danger";
                $_SESSION['flash_message'] = "end period must be greater than start period";
                //redirect back
                $url = $_SERVER['HTTP_REFERER'];
                header( "location: {$url}" );
            }
            else
            {
                $_SESSION['nps_summary_period_from'] = $_POST['period_from'];
                $_SESSION['nps_summary_period_to'] = $_POST['period_to'];
                $_SESSION['nps_summary_eauto_license_group'] = $_POST['eauto_license_group'];
                $_SESSION['nps_state'] = $_POST['state'];

                //redirect back
                $url = $_SERVER['HTTP_REFERER'];
                header( "location: {$url}" );
            }

            break;

        case 'set-filter':

            require_once '../models/utilities.php';
            $utilities = new Utilities();

            //phpinfo();
            //exit();

            //echo '<pre>';
            //var_dump($_POST);
            //exit();

            // validations
            if (trim($_POST['date_from'] == "") || trim($_POST['date_to']) == "") 
            {
                $_SESSION['flash_type'] = "danger";
                $_SESSION['flash_message'] = "No empty values for dates alowed";
                //redirect back
                header( 'location: .' );
                exit();
            }

            if (strtotime(trim($_POST['date_from'])) > strtotime(trim($_POST['date_to']))) 
            {
                $_SESSION['flash_type'] = "danger";
                $_SESSION['flash_message'] = "Start date can't be greater than end date";
                //redirect back
                header( 'location: .' );
                exit();
            }

            if ($utilities->validateDate(trim($_POST['date_from'])) == 0 || $utilities->validateDate(trim($_POST['date_to'])) == 0 ) 
            {
                $_SESSION['flash_type'] = "danger";
                $_SESSION['flash_message'] = "An invalid date was entered";
                //redirect back
                header( 'location: .' );
                exit();
            }


            // set filter sessions
            if ( isset( $_POST['customer_id'] ) == 1 && $_POST['customer_id'] != $_SESSION['customer_id'] ) 
            {
                // reset if new customer id selected
                $_SESSION['survey_id_selected_array'] = [];
            }
            else 
            {
                $_SESSION['survey_id_selected_array'] = $_POST['survey_id'];
            }

            // only set if the customer id selected posted something
            // otherwise it's a user without admin rights
            // that should not be able to set the customerid
            if ( isset( $_POST['customer_id'] ) == 1 ) 
            {
                $_SESSION['customer_id'] = $_POST['customer_id'];
            }

            // surveyed customer filter
            if ( isset( $_POST['surveyed_customer_name'] ) == 1 && $_POST['surveyed_customer_name'][0] != '' ) 
            {
                $_SESSION['surveyed_customer_name'] = $_POST['surveyed_customer_name'];
            } 
            else 
            { 
                $_SESSION['surveyed_customer_name'] = [];
                $_SESSION['surveyed_customer_name'][0] = ''; 
            }

            $_SESSION['date_from'] = filter_input(INPUT_POST, date_from);
            $_SESSION['date_to'] = filter_input(INPUT_POST, date_to);

            //redirect back
            header( 'location: .' );

            break;  

        case 'survey-scores-by-month':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-survey-scores-by-month.php';
            include 'dsp-footer.php';
            break;

        case 'survey-scores-by-tech':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-survey-scores-by-tech.php';
            include 'dsp-footer.php';
            break;

        case 'survey-detail':
            include 'inc-logic.php';
            include 'dsp-detail-survey.php';
            break;

        case 'survey-count-by-email':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-detail-survey-count-by-email.php';
            include 'dsp-footer.php';
            break;

        case 'clicked-but-not-submitted':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-detail-clicked-but-not-submitted.php';
            include 'dsp-footer.php';
            break;    

        case 'no-responses':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-detail-no-responses.php';
            include 'dsp-footer.php';
            break;

        case 'opt-outs':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-detail-opt-outs.php';
            include 'dsp-footer.php';
            break;

        case 'nps-export':
            include 'inc-logic.php';
            include 'dsp-nps-export.php';
            break;

        case 'respondent-export':
            include 'inc-logic.php';
            include 'dsp-respondent-export.php';
            break;

        case 'survey-comments':
            $slim_action = true;
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-survey-comments.php';
            include 'dsp-footer.php';
            break;

        case 'comment-vote':
            $slim_action = true;
            include 'inc-logic.php';

            // update
            $comment_like = 
            $dashboard->comment_like_update(
                        $answer_id = $_POST['answer_id'], 
                        $question_id = $_POST['question_id'],
                        $score = $_POST['score'],
                        $email = $email
                        );

            //flash message
            //$_SESSION['flash_message'] = "Your vote was saved";

            //print_r($_POST);
            //phpinfo();
            //exit();

            $_SESSION['answer_id_updated'] = $_POST['answer_id'];

            //redirect back
            $url = $_SERVER['HTTP_REFERER'] . '#' . $_POST['answer_id'];
            header( "location: {$url}" );
            break;

        case 'comment-include-question-update':
            $slim_action = true;
            include 'inc-logic.php';

            // before
            $before = $dashboard->comment_include_question_get( $answer_id = $_POST['answer_id'] );

            // update
            $include_question = 
            $dashboard->comment_include_question_update(
                        $answer_id = $_POST['answer_id'], 
                        $question_id = $_POST['question_id']
                        )->fetchAll();


            // after
            $after = $dashboard->comment_include_question_get( $answer_id = $_POST['answer_id'] );

            //exit();

            //flash message

            if ($after === $before)
            {
                $_SESSION['flash_message_include_question'] = "Not saved. Can only check off likes/dislikes.";
            }
            else 
            {
                $_SESSION['flash_message_include_question'] = '';
            }
            
            $_SESSION['answer_id_updated'] = $_POST['answer_id'];

             //redirect back
            $url = $_SERVER['HTTP_REFERER'] . '#' . $_POST['answer_id'];
            header( "location: {$url}" );
            break;

        case 'test':
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'test.php';
            include 'dsp-footer.php';
            break;

        default:
            include 'dsp-header.php';
            include 'inc-logic.php';
            include 'dsp-main.php';
            include 'dsp-footer.php';
            break;
    }

    
?>

