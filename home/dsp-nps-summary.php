<?php
    require_once '../models/auth.php';

	$auth = new Auth();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );
?>

<!-- table sorter -->
<!--
<link rel="stylesheet" href="../js/tablesorter/themes/gray/style.css">
<script src="../js/tablesorter/jquery-latest.js"></script>
<script src="../js/tablesorter/jquery.tablesorter.js"></script>
-->

<?php

    require_once '../models/db.php';
    require_once '../models/dashboard.php';

    $dashboard = new Dashboard();

    //calculate date from
	$time = new DateTime('now');
	$period_from_default = $time->modify('-1 year')->format('Ym');
	$period_to_default = $time->modify('+1 year')->format('Ym');

    //params
	$period_from_default = isset( $_SESSION['nps_summary_period_from'] ) == 1 ? $_SESSION['nps_summary_period_from'] : $period_from_default;
	$period_to_default = isset( $_SESSION['nps_summary_period_to'] ) == 1 ? $_SESSION['nps_summary_period_to'] : $period_to_default;
	$eauto_license_group_default = isset( $_SESSION['nps_summary_eauto_license_group'] ) == 1 ? $_SESSION['nps_summary_eauto_license_group'] : '';
	$state_default = isset( $_SESSION['nps_state'] ) == 1 ? $_SESSION['nps_state'] : '';

    //data
    $customer_nps_results = $dashboard->nps_by_period_range_get(
										$customer_id = $customer_id,
										$period_from = $period_from_default,
										$period_to = $period_to_default,
										$group_by = null,
										$licenses_group = null,
										$state = null
										)->fetchAll();

	$overall_nps_results = $dashboard->nps_by_period_range_get(
										$customer_id = null,
										$period_from = $period_from_default,
										$period_to = $period_to_default,
										$group_by = null,
										$licenses_group = $eauto_license_group_default,
										$state = $state_default
										)->fetchAll();

	$country_nps_results = $dashboard->nps_by_period_range_get(
										$customer_id = null,
										$period_from = $period_from_default,
										$period_to = $period_to_default,
										$group_by = 'Country',
										$licenses_group = $eauto_license_group_default,
										$state = $state_default
										)->fetchAll();

		$state_nps_results = $dashboard->nps_by_period_range_get(
										$customer_id = null,
										$period_from = $period_from_default,
										$period_to = $period_to_default,
										$group_by = 'State_ID',
										$licenses_group = $eauto_license_group_default,
										$state = $state_default
										)->fetchAll();

		$license_group_summary = $dashboard->nps_by_period_range_get(
										$customer_id = null,
										$period_from = $period_from_default,
										$period_to = $period_to_default,
										$group_by = 'EautoLicensesGroup',
										$licenses_group = $eauto_license_group_default,
										$state = $state_default
										)->fetchAll();

	$periods = $dashboard->periods_get()->fetchAll();

	$nps_eauto_license_groups = $dashboard->nps_eauto_license_groups_get()->fetchAll();

	$nps_states = $dashboard->nps_states_get()->fetchAll();

	// period as textnps_eauto_license_groups_get
	$period_from_default_text = "";
	foreach ( $periods as $row )
	{
		if ( $row['Period'] == $period_from_default)
		{
			$period_from_default_text = $row['PeriodText'];
		}
	}

	$period_to_default_text = "";
	foreach ( $periods as $row )
	{
		if ( $row['Period'] == $period_to_default)
		{
			$period_to_default_text = $row['PeriodText'];
		}
	}

	// customer nps score
    $customer_nps_score = 0;
    $customer_surveys_sent = 0;
    $customer_response_count = 0;
  	foreach ( $customer_nps_results as $r )
  	{
  		$customer_nps_score = $r['NPSScore'];
  		$customer_surveys_sent = $r['TotalSurveysSent'];
  		$customer_response_count = $r['TotalResponseCount'];
  	}
  	$customer_nps_score *= 100;
  	$customer_response_percent = $customer_surveys_sent == 0 ? 0 : ($customer_response_count / $customer_surveys_sent) * 100;

  	// overall nps score
    $overall_nps_score = 0;
    $overall_company_count = 0;
    $overall_surveys_sent = 0;
    $overall_response_count = 0;
  	foreach ( $overall_nps_results as $r )
  	{
  		$overall_nps_score = $r['NPSScore'];
  		$overall_company_count = $r['CustomerCount'];
  		$overall_surveys_sent = $r['TotalSurveysSent'];
  		$overall_response_count = $r['TotalResponseCount'];
  	}
  	$overall_nps_score *= 100;
  	$overall_response_percent = $overall_surveys_sent == 0 ? 0 : ($overall_response_count / $overall_surveys_sent) * 100;

  	if ( $action == 'nps-benchmarks' )
  	{
  		$panel_title = 'Your NPS Score Compared To The Industry';
  	}
  	else
  	{
  		$panel_title = 'NPS Scores For The Industry';
  	}

?>

<script type="text/javascript">

$(function () {
    $('#nps-pie-chart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,//null,
            plotShadow: false,
        },
        title: {
            text: 'Dealer Sizes'
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b> - {point.y} Companies'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            type: 'pie',
            name: 'Eauto Licenses Group',
            data: [

            	<?php foreach( $license_group_summary as $s ): ?>
            		<?php
            			$eautoLicensesGroup = $s['EautoLicensesGroup'] == '' ? 'N/A': $s['EautoLicensesGroup'];
            		?>

                	['<?= $eautoLicensesGroup ?>',   <?= $s['CustomerCount'] ?>],

                <?php endforeach; ?>
            ]
        }]
    });
});

</script>

<div class="row">
	<div class="col-lg-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> <?= $panel_title ?> <br /> </h3>
	        </div>
	        <div class="panel-body">

	        	<?php if ( $action != 'nps-benchmarks-link') : ?>
            		<div><a href="javascript:history.go(-1);" class="btn btn-default btn-sm" role="button" style="width:125px"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></div>
            		<br />
            	<?php endif; ?>


				<form role="form" name="myform" class="form-inline" method="post" action=".?action=set-nps-summary-periods">
					<div class="form-group">

						<label>From Period:</label>
						<select class="form-control input-md" name="period_from">
							<?php foreach( $periods as $row ): ?>
								<option value="<?php echo $row['Period']; ?>" <?php if ( $row['Period'] == $period_from_default ) { echo 'selected'; } ?> ><?php echo $row['PeriodText']; ?></option>
							<?php endforeach; ?>
						</select>

						<label>To Period:</label>
						<select class="form-control input-md" name="period_to">
							<?php foreach( $periods as $row ): ?>
								<option value="<?php echo $row['Period']; ?>" <?php if ( $row['Period'] == $period_to_default ) { echo 'selected'; } ?> ><?php echo $row['PeriodText']; ?></option>
							<?php endforeach; ?>
						</select>

						<label>Dealer Size:</label>
						<select class="form-control input-md" name="eauto_license_group">
							<option value="" <?php if ( $eauto_license_group_default == '' ) { echo 'selected'; } ?> >ALL</option>
							<?php foreach( $nps_eauto_license_groups as $row ): ?>
								<option value="<?php echo $row['EautoLicensesGroup']; ?>" <?php if ( $row['EautoLicensesGroup'] == $eauto_license_group_default ) { echo 'selected'; } ?> ><?php echo $row['EautoLicensesGroup']; ?></option>
							<?php endforeach; ?>
						</select>

						<label>State:</label>
						<select class="form-control input-md" name="state">
							<option value="" <?php if ( $state_default == '' ) { echo 'selected'; } ?> >ALL</option>
							<?php foreach( $nps_states as $row ): ?>
								<option value="<?php echo $row['State_ID']; ?>" <?php if ( $row['State_ID'] == $state_default ) { echo 'selected'; } ?> ><?php echo $row['State_ID']; ?></option>
							<?php endforeach; ?>
						</select>

						<button type="submit" class="btn btn-default btn-md" value="btnPeriods" name="btnPeriods">
							<span class="glyphicon glyphicon-search"></span> Apply
						</button>

						<br /><br />


					</div>
				</form>

	            <div class="table-responsive">

					<a href="https://support.ceojuice.com/hc/en-us/articles/115001148346?flash_digest=ce749387fe918b574e892951d91cd090a48aa789" target="_blank">More Info</a>

					<br />
					<a href="https://ceojuice.com/Identity/Account/Login" target="_top">Log In To See Your Results</a>

			        <table>
			        	<tr valign="top">
			        		<td>


								<h3>NPS Scores</h3>
								<h4>
									Periods:
									<?= str_replace( '-', '', $period_from_default_text ) ?> to
									<?= str_replace( '-', '', $period_to_default_text ) ?>
									<br />
									Dealer Size: <?= $eauto_license_group_default == '' ? 'ALL' : $eauto_license_group_default; ?>
									<br />
									State: <?= $state_default == '' ? 'ALL' : $state_default; ?>
                                    <br />
                                    <small>note: this benchmark summary data as of last night</small>
								</h4>
								<br />

								<?php if ( $action != 'nps-benchmarks-link') : ?>
									<h4>Your Score</h4>
									<table id="customer_nps_table" class="table-condensed table-bordered">
										<thead>
									    <tr class="bg-active">
									        <th>NPS Score</th>
									        <th>Surveys Sent</th>
									        <th>Survey Responses</th>
									        <th>% Response</th>
									    </tr>
										</thead>
										<tbody>
											<tr>
												<td><?= $customer_nps_score == 0 ? 'N/A' : number_format($customer_nps_score, 2) ?></td>
												<td><?= number_format($customer_surveys_sent) ?></td>
												<td><?= number_format($customer_response_count) ?></td>
												<td><?= number_format($customer_response_percent, 2) ?>%</td>
									        </tr>
										</tbody>
									</table>

									<br />
								<?php endif; ?>

								<h4>All CEO Juice Clients</h4>
								<table id="overall_nps_table" class="table-condensed table-bordered">
									<thead>
								    <tr class="bg-active">
								        <th>NPS Score</th>
								        <th>Companies</th>
								        <th>Surveys Sent</th>
								        <th>Survey Responses</th>
								        <th>% Response</th>
								    </tr>
									</thead>
									<tbody>
										<tr>
											<td><?= $overall_nps_score == 0 ? 'N/A' : number_format($overall_nps_score, 2) ?></td>
											<td><?= number_format($overall_company_count) ?></td>
											<td><?= number_format($overall_surveys_sent) ?></td>
											<td><?= number_format($overall_response_count) ?></td>
											<td><?= number_format($overall_response_percent, 2) ?>%</td>
								        </tr>
									</tbody>
								</table>

								<br />

								<h4>Broken out by Country</h4>
								<table id="country_nps_table" class="table-condensed table-bordered">
									<thead>
								    <tr class="bg-active">
								    	<th>Country</th>
								        <th>NPS Score</th>
								        <th>Companies Included</th>
								    </tr>
									</thead>
									<tbody>
										<?php foreach($country_nps_results as $row): ?>
										<tr>
											<td><?= $row['Country'] ?></td>
											<td>
												<?php
												if ($row['NPSScore']  == "" )
												{
													echo ("N/A");
												}
												else
												{
													echo ( number_format($row['NPSScore'] * 100, 2));
												}
												?>
											</td>
											<td><?= $row['CustomerCount'] ?></td>
								        </tr>
								    	<?php endforeach; ?>
									</tbody>
								</table>

								<br />

								<h4>Broken out by State</h4>
								<table id="state_nps_table" class="table-condensed table-bordered">
									<thead>
								    <tr class="bg-active">
								    	<th>State</th>
								        <th>NPS Score</th>
								        <th>Companies Included</th>
								    </tr>
									</thead>
									<tbody>
										<?php foreach($state_nps_results as $row): ?>
										<tr>
											<td>
												<?php
												if ($row['State_ID'] == '')
												{
													echo ("N/A");
												}
												else
												{
													echo $row['State_ID'];
												}
												?>
											</td>
											<td>
												<?php
												if ($row['NPSScore']  == '' || $row['NPSScore'] == 0)
												{
													echo ("N/A");
												}
												else
												{
													echo (number_format($row['NPSScore'] * 100, 2));
												}
												?>
											</td>
											<td><?= $row['CustomerCount'] ?></td>
								        </tr>
								    	<?php endforeach; ?>
									</tbody>
								</table>

			        		</td>

			        		<td>
			        			<div id="size-info" align="center"><a href="https://support.ceojuice.com/hc/en-us/articles/213943086-NPS-Benchmarks" target="_blank">Size Info</a></div>
								<div id="nps-pie-chart" style="min-width: 500px; height: 500px; max-width: 600px;"></div>
			        		</td>
			        	</tr>
			        </table>

	            </div>
	        </div>
	    </div>
	</div>
</div>
