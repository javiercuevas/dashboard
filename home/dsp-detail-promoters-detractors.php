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

    // queries
    $nps_promoters_detractors_rollup_results = $dashboard->nps_promoters_detractors_rollup_get( 
    											$customer_id = $customer_id, 
    											$date_from = $date_from_default, 
    											$date_to = $date_to_default,
    											$survey_id_array = $survey_id_selected_array,
    											$eauto_customer_name = $surveyed_customer_name
    											)->fetchAll( PDO::FETCH_ASSOC);

    $nps_promoters_detractors_detail_results = $dashboard->nps_promoters_detractors_detail_get( 
    											$customer_id = $customer_id, 
    											$date_from = $date_from_default, 
    											$date_to = $date_to_default,
    											$survey_id_array = $survey_id_selected_array,
    											$eauto_customer_name = $surveyed_customer_name
    											)->fetchAll( PDO::FETCH_ASSOC);
?>

<div class="row">
	<div class="col-lg-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> NPS Details</h3>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">

	            	<div><a href="javascript:history.go(-1);" class="btn btn-default btn-sm" role="button" style="width:125px"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></div>
	            	<br />

	            	<div><a href=".?action=nps-export"><i class="fa fa-file-excel-o fa-1x"> Export NPS Details</i></a></div>

	            	<h3>Summary:</h3>
					<table class="table-condensed table-bordered">
					    <tr class="bg-active">
					        <th>Type</th>
					        <th style="width:150px">Count</th>
					    </tr>
					    <?php 
					    	$row_total = 0; 
					    	$promoter_count = 0;
					    	$detractor_count = 0;
					    	$passive_count = 0;
					    ?>
					    <?php foreach($nps_promoters_detractors_rollup_results as $row): ?>
					    	<?php
					    		if ( $row['Type'] == 'Promoter' ) { $promoter_count = $row['Rows']; }
					    		if ( $row['Type'] == 'Detractor' ) { $detractor_count = $row['Rows']; }
					    		if ( $row['Type'] == 'Passive' ) { $passive_count = $row['Rows']; }
					    	?>
					        <tr>
					            <td><?php echo $row['Type'] ?></td>
					            <td><?php echo number_format($row['Rows']); ?></td>
					        </tr>
					        <?php $row_total += $row['Rows']; ?>
					    <?php endforeach; ?>
					    <tr>
					    	<th>Total</th>
					    	<th><?php echo number_format($row_total); ?></th>
					    </tr>
					</table>

					<h3>NPS Score:</h3>
					<table class="table" style="width:50%">	
						<tr>
							<td align="center">(&nbsp;#Promoters</td>
							<td align="center">-</td>
							<td align="center">#Detractors&nbsp;)</td>
							<td align="center">/</td>
							<td align="center">(&nbsp;#Promoters</td>
							<td align="center">+</td>
							<td align="center">#Detractors</td>
							<td align="center">+</td>
							<td align="center">#Passive&nbsp;)</td>
							<td align="center">=</td>
							<td align="center">NPS Score</td>
						</tr>
						<tr>
							<td align="center">(&nbsp;<?= number_format($promoter_count); ?></td>
							<td align="center">-</td>
							<td align="center"><?= number_format($detractor_count); ?> )</td>
							<td align="center">/</td>
							<td align="center">(&nbsp;<?= number_format($promoter_count); ?></td>
							<td align="center">+</td>
							<td align="center"><?= number_format($detractor_count); ?></td>
							<td align="center">+</td>
							<td align="center"><?= number_format($passive_count); ?>&nbsp;)</td>
							<td align="center">=</td>
							<td align="center">
								<?php 
									$nps =  ( $promoter_count - $detractor_count ) / ( $promoter_count + $detractor_count + $passive_count ) * 100;
									echo '<b>' . number_format($nps, 1) . '</b>' . '%';
								?>
							</td>
						</tr>
					<table>

					<h3>Details:</h3>
					<table id="myTable" class="table-condensed table-bordered tablesorter"> 
						<thead>
					    <tr class="bg-active">
					    	<th>Row</th>
					        <th>Survey</th>
					        <th>Type</th>
					        <th>Customer</th>
					        <th>Tech</th>
					        <th>Survey Sent To</th>
					        <th>Answer</th>
					        <th>Create Date</th>
					        <th>Completed Date</th>
					        <th>Detail</th>
					    </tr>
						</thead>
						<tbody>
						<?php $row_number = 1; ?>
					    <?php foreach($nps_promoters_detractors_detail_results as $row): ?>
					        <tr>
					        	<td><?php echo $row_number; ?></td>
					            <td><?php echo $row['SurveyTitle'] ?></td>
					            <td><?php echo $row['Type']; ?></td>
					            <td><?php echo $row['EAutoCustomer']; ?></td>
					            <td><?php echo $row['EAutoTech']; ?></td>
					            <td><?php echo $row['SurveySentTo']; ?></td>
					            <td><?php echo $row['Answer']; ?></td>
					            <td><?php echo date_format(date_create($row['CreateDate']), 'm/d/Y'); ?></td>
					            <td><?php echo date_format(date_create($row['CompletedDate']), 'm/d/Y'); ?></td>
					            <td><a href=".?action=survey-detail&id=<?php echo $row['authenticationid']; ?>" 
					            	onclick="window.open(this.href, 'newwindow', 'width=900, height=800, scrollbars=yes, resizable=yes, titlebar=no, menubar=no, location=no').focus(); return false;"> 
					            	<span class="glyphicon glyphicon-zoom-in"></span></a></td>
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