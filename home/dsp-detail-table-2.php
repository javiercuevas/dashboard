<?php

    require_once '../models/db.php';
    require_once '../models/dashboard.php';
    require_once '../models/auth.php';

    $dashboard = new Dashboard();
    $auth = new Auth();
    
    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    // query
    $nps_by_customer_result = $dashboard->nps_by_customer_get( 
                                    $customer_id, 
                                    $date_from = $date_from_default, 
                                    $date_to = $date_to_default,
                                    $survey_id_array = $survey_id_selected_array,
                                    $eauto_customer_name = $surveyed_customer_name
                                    );
?>

<table class="table-condensed table-bordered">
    <tr class="bg-active">
        <th>Customer</th>
        <th>NPS Score</th>
    </tr>
    <?php foreach($nps_by_customer_result as $row): ?>
        <?php
            if ( $row['NPSScore'] <= 50) 
            {
                $tr_class = "bg-warning";
            }
            else 
            {
                $tr_class = "";
            }
        ?>
        <tr class="<?php echo $tr_class ?>">
            <td><?php echo $row['EautoCustomer']; ?></td>
            <td><?php echo number_format( $row['NPSScore'] ); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
