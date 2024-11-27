<?php

    require_once '../models/auth.php';

    $auth = new Auth();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );
    
    // queries
    $customers = $dashboard->customers_get();
    $survey_list = $dashboard->survey_list_get( $customer_id );
    $surveyed_eauto_customers_results = $dashboard->surveyed_eauto_customers_get ( $customer_id, $date_from = $date_from_default, $date_to = $date_to_default );
?> 

<script>
	$(function() {
		$( "#datepicker_from" ).datepicker();
	});

	$(function() {
		$( "#datepicker_to" ).datepicker();
	});
</script>

<!-- form -->
<div align="left">
<form role="form" name="myform" class="form-inline" method="post" action=".?action=set-filter">
	<div class="form-group">

		<?php if ( stripos( $user_details_results['Email'] , '@ceojuice.com' ) !== false /* && $user_details_results['SecurityID'] == 5 */ || $allow_everyone == 1 ) : ?>
			<label>Customer:</label><br />
				<select class="form-control input-md" name="customer_id" style="" onchange="myform.submit();">
					<?php foreach( $customers as $row ): ?>
						<option value="<?php echo $row['CustomerID']; ?>" <?php if ( $row['CustomerID'] == $customer_id ) { echo 'selected'; } ?> ><?php echo $row['CustomerName']; ?></option>
					<?php endforeach; ?>
				</select>
		<?php endif; ?>

		<br /><br />

		
			<label>Surveyed Customers: <span class='light-gray-sm'>(use SHIFT + DOWN ARROW KEY, COMMAND + DOWN ARROW KEY on mac, to select a range)</span></label><br />
			<select class="form-control input-md" name="surveyed_customer_name[]" multiple size="10">
				<option value="" <?php if ( implode(', ', $surveyed_customer_name) == '' ) { echo 'selected'; } ?> >ALL</option>

				<?php foreach( $surveyed_eauto_customers_results as $row ): ?>
					<option value="<?php echo $row['EAutoCustomer']; ?>" <?php if ( in_array( $row['EAutoCustomer'], $surveyed_customer_name ) ) { echo 'selected'; } ?> ><?php echo $row['EAutoCustomer']; ?></option>
				<?php endforeach; ?>
			</select>
		
		<br /><br />

		<label></label><input class="form-control input-md" type="text" name="date_from" id="datepicker_from" value="<?php echo $date_from_default ?>" size="10">

		<label>To &nbsp;</label><input class="form-control input-md" type="text" name="date_to" id="datepicker_to" value="<?php echo $date_to_default ?>" size="10">

		<br /><br />

		<label>Survey:</label><br />
			<?php foreach( $survey_list as $row): ?>
				<?php 
					if (in_array($row['SurveyID'], $survey_id_selected_array)) 
					{ 
						$survey_checked = 'checked'; 
					} 
					elseif (count($survey_id_selected_array) == 0) 
					{
						$survey_checked = 'checked';
					}
					else 
					{
						$survey_checked = '';
					}
				?>
				<div class="checkbox">
					<label class="control-label col-sm">
						<input type="checkbox" name="survey_id[]" value="<?php echo $row['SurveyID']; ?>" <?php echo $survey_checked; ?> >&nbsp;
							<small>
							<?php
								if ($row['Survey'] == $row['Description'])
								{
									echo $row['Survey'];
								}
								else
								{
									echo $row['Survey']; 
									echo ' (' . $row['Description'] . ')';
								}
							?>
							</small>
					</label>
				</div>
				<br />
			<?php endforeach; ?>

		<br />

		<button type="submit" class="btn btn-default btn-lg" value="btn-filter" name="btn-filter">
			<span class="glyphicon glyphicon-search"></span> Apply
		</button>


		<br /><br />

	</div>
</form>
</div>