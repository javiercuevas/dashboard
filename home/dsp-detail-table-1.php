<?php
    
    require_once '../models/db.php';
    require_once '../models/dashboard.php';
    require_once '../models/auth.php';

    $dashboard = new Dashboard();
    $auth = new Auth();
    
    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    // query
    $results_1 = $dashboard->question_results_rollup_get( 
        $customer_id = $customer_id, 
        $date_from = $date_from_default, 
        $date_to = $date_to_default,
        $survey_id_array = $survey_id_selected_array,
        $eauto_customer_name = $surveyed_customer_name
        )
?>

<table class="table-condensed table-bordered">
    <tr bgcolor="#f5f5f5">
        <th>Question Text</th>
        <th style="width:150px">Answer</th>
        <th>Respondents</th>
    </tr>
    <?php $respondents_total = 0; ?>
    <?php foreach($results_1 as $row): ?>
        <?php
            $respondents_total += $row['Respondents'];
        ?>
        <tr>
            <td><?php if ( $row['OccuranceDesc'] == 1 ) { echo $row['QuestionText'] . " (" . $row['QuestionType'] . ")"; } ?></td>
            <td><?php echo str_replace( '*', 'Not Applicable', $row['Answer']); ?></td>
            <td><?php echo number_format( $row['Respondents'] ); ?></td>
        </tr>
        <?php if ( $row['Occurance'] == 1 ): ?>
            <tr bgcolor="#f5f5f5">
                <td>Total</td>
                <td></td>
                <td><?php echo number_format( $respondents_total ); ?></td>
            </tr>
        <?php endif ?>
        <?php if ( $row['Occurance'] == 1 ) { $respondents_total = 0; } ?>
    <?php endforeach; ?>
</table>
