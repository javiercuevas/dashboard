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
    $opt_outs_results = $dashboard->survey_opt_outs_detail_get(
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
	            <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> Opt Outs</h3>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">

	            	<div><a href="javascript:history.go(-1);" class="btn btn-default btn-sm" role="button" style="width:125px"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></div>
	            	<br />

					<h3>Opt Out Details:</h3>
					<span class="light-gray-sm">This is a list of the opt outs</span>
					<table id="myTable" class="table-condensed table-bordered tablesorter"> 
						<thead>
					    <tr class="bg-active">
					    	<th>Row</th>
					        <th>Survey</th>
					        <th>Customer</th>
					        <th>Email</th>
					        <th>AuthenticationID</th>
					        <th>Create Date</th>
					    </tr>
						</thead>
						<tbody>
						<?php $row_number = 1; ?>
					    <?php foreach($opt_outs_results as $row): ?>
					        <tr>
					        	<td><?php echo $row_number; ?></td>
					            <td><?php echo $row['Survey'] ?></td>
					            <td><?php echo $row['AppCustomerName']; ?></td>
					            <td><?php echo $row['AppCustomerEmail']; ?></td>
					            <td><?php echo $row['AuthenticationID']; ?></td>
					            <td><?php echo date_format(date_create($row['CreateDate']), 'm/d/Y'); ?></td>
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