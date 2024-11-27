<?php
    require_once '../models/auth.php';

	$auth = new Auth();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );
?>

<!-- table sorter -->
<link rel="stylesheet" href="../js/tablesorter/themes/gray/style.css">
<script src="../js/tablesorter/jquery-latest.js"></script>
<script src="../js/tablesorter/jquery.tablesorter.js"></script> 

</style>

<?php

    require_once '../models/db.php';
    require_once '../models/dashboard.php';

    $dashboard = new Dashboard();

    // queries
    $survey_count_by_email = $dashboard->survey_count_by_email_get(
								$customer_id = $customer_id, 
								$date_from = $date_from_default, 
								$date_to = $date_to_default,
								$survey_id_array = $survey_id_selected_array,
								$eauto_customer_name = $surveyed_customer_name
								);


?>

<div class="row">
	<div class="col-lg-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> Surveys by email</h3>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">

	            	<div><a href="javascript:history.go(-1);" class="btn btn-default btn-sm" role="button" style="width:125px"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></div>
	            	<br />

					<h3>Surveys by email detail:</h3>
					<span class="light-gray-sm">List of contacts who've responded and the number of times they've done so</span>
					<table id="myTable" class="table-condensed table-bordered tablesorter"> 
						<thead>
					    <tr class="bg-active">
					    	<th>Row</th>
					    	<th>Customer</th>
					        <th>Email</th>
					        <th>Survey Responses</th>
					    </tr>
						</thead>
						<tbody>
						<?php $row_number = 1; ?>
					    <?php foreach($survey_count_by_email as $row): ?>
					        <tr>
					        	<td><?php echo $row_number; ?></td>
					        	<td><?php echo $row['SurveyCustomerName'] ?></td>
					            <td><?php echo $row['AppCustomerEmail'] ?></td>
					            <td><?php echo $row['SurveyResponses']; ?></td>
					        </tr>
					        <?php $row_number++; ?>
					    <?php endforeach; ?>
						</tbody>
					</table>

	            </div>
	        </div>
	    </div>
	</div>
</div>