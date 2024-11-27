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

    //params
	$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
	$dir = isset($_GET['dir']) ? $_GET['dir'] : 'asc';

    // queries
    $survey_comments = $dashboard->survey_comments_get(
    											$customer_id = $customer_id, 
    											$date_from = $date_from_default, 
    											$date_to = $date_to_default,
    											$sort_by = $sort,
    											$sort_dir = $dir
    											)->fetchAll();

   	$liked_count = 0;
   	foreach ($survey_comments as $c)
   	{
   		if ($c['Liked'] == 1) 
   		{
   			$liked_count++;
   		}
   	}

    $disliked_count = 0;
   	foreach ($survey_comments as $c)
   	{
   		if ($c['Liked'] == -1) 
   		{
   			$disliked_count++;
   		}
   	}

    $include_question_count = 0;
   	foreach ($survey_comments as $c)
   	{
   		if ($c['LikedIncludeQuestion'] == 1) 
   		{
   			$include_question_count++;
   		}
   	}


    //last updated record
    $answer_id_updated = isset($_SESSION['answer_id_updated']) ? $_SESSION['answer_id_updated'] : 0;

?>

<div class="row">
	<div class="col-lg-12">
	    <div class="panel panel-default">
	        <div class="panel-heading">
	            <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> Survey Comments <br /> </h3>
	        </div>
	        <div class="panel-body">
	            <div class="table-responsive">

	            	<div><a href="javascript:history.go(-1);" class="btn btn-default btn-sm" role="button" style="width:125px"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></div>
	            	<br />

					<h4>Survey Comments Created Between <?= $date_from_default; ?> and <?= $date_to_default; ?>:</h4>
					<h4><?= count($survey_comments) ?> Records ( <?= $liked_count ?> likes, <?= $disliked_count ?> Dislikes, <?= $include_question_count ?> Include Questions )</h4>
					<h4>Sorted by: <?= $sort ?> <?php if ($dir == 'desc') { echo $dir; } ?></h4>
					<span class="light-gray-sm"></span>

					<table id="comments_table" class="table-condensed table-bordered"> 
						<thead>
					    <tr class="bg-active">

					    	<?php if ($sort == 'survey-title' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=survey-title{$sort_dir_string}"; ?>">Survey&nbsp;Title</a></th>

					        <?php if ($sort == 'customer' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=customer{$sort_dir_string}"; ?>">Customer</a></th>

					        <?php if ($sort == 'question' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=question{$sort_dir_string}"; ?>">Question</a></th>

					        <?php if ($sort == 'comment' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=comment{$sort_dir_string}"; ?>">Comment</a></th>

					        <?php if ($sort == 'answer' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=answer{$sort_dir_string}"; ?>">Answer</a></th>

					        <?php if ($sort == 'target' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=target{$sort_dir_string}"; ?>">Target</a></th>

					        <?php if ($sort == 'create-date' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=create-date{$sort_dir_string}"; ?>">Create&nbsp;Date</a></th>

					        <?php if ($sort == 'ok-to-share-comments' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=ok-to-share-comments{$sort_dir_string}"; ?>">OK&nbsp;To&nbsp;Share&nbsp;Comments</a></th>

					        <?php if ($sort == 'like' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=like{$sort_dir_string}"; ?>">Like</a></th>

					        <?php if ($sort == 'dislike' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=dislike{$sort_dir_string}"; ?>">Dislike</a></th>

					        <?php if ($sort == 'include-question' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=include-question{$sort_dir_string}"; ?>">Include Question Alongside Comment</a></th>

					        <?php if ($sort == 'liked-date' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=liked-date{$sort_dir_string}"; ?>">Liked&nbsp;Date</a></th>

					        <?php if ($sort == 'liked-by' && $dir == 'asc') { $sort_dir_string = '&dir=desc'; } else { $sort_dir_string = ''; } ?>
					        <th><a href="<?= "./?action=survey-comments&sort=liked-by{$sort_dir_string}"; ?>">Liked&nbsp;By</a></th>

					        <th></th>

					    </tr>
						</thead>
						<tbody>
						<?php $row_number = 1; ?>
					    <?php foreach($survey_comments as $row): ?>	
					    	<?php
					    		//row_class
			    				if ($row['AnswerID'] == $answer_id_updated)
					    		{
					    			$row_class = 'bg-success';
					    		}
					    		else
					    		{
					    			$row_class = '';
					    		}

					    		//ok to share?
								if ($row['OkToShareComments'] == '1')
								{
									$ok_to_share_comments_string = 'Yes';
								}
								else if ($row['OkToShareComments'] == '0')
								{
									$ok_to_share_comments_string = 'No';
								}	
								else 
								{
									$ok_to_share_comments_string = 'Not Answered';
								}

							?>
					        <tr class="<?= $row_class ?>">
					            <td>
					            	<a name="<?= $row['AnswerID']; ?>" style="padding-top:200px"></a>
					            	<?= $row['SurveyTitle']; ?>
					            </td>
					            <td><?= $row['EAutoCustomer']; ?></td>
					            <!--<td><?= $row['SurveySentTo']; ?></td>-->
					            <td><?= $row['QuestionText']; ?></td>
					            <td><?= $row['Comment']; ?></td>
					            <td><?= $row['Answer']; ?></td>
					            <td><?= $row['TargetRank']; ?></td>
					            <td><?= date_format(date_create($row['CreateDate']), 'm/d/Y'); ?></td>
					            <td><?= $ok_to_share_comments_string; ?></td>
					            <td>
								<form role="form" class="form-inline" method="post" action=".?action=comment-vote">
									<div class="form-group">

										<?php
											if ($row['Liked'] == 1)
											{
												$thumb_style = "color:red";
											}
											else 
											{
												$thumb_style = "color:silver";
											}
										?>

										<button class="btn btn-default btn-md" name="btnLike" value="Like">
											<span class="glyphicon glyphicon-thumbs-up" style="<?= $thumb_style; ?>"></span>
										</button>
										<input type="hidden" name="answer_id" value="<?php echo $row['AnswerID']; ?>">
										<input type="hidden" name="question_id" value="<?php echo $row['QuestionID']; ?>">
										<input type="hidden" name="score" value="1">
									</div>
								</form>
					            </td>
					            <td>
								<form role="form" class="form-inline" method="post" action=".?action=comment-vote">
									<div class="form-group">

										<?php
											if ($row['Liked'] == -1)
											{
												$thumb_style = "color:red";
											}
											else 
											{
												$thumb_style = "color:silver";
											}
										?>

										<button class="btn btn-default btn-md" name="btnDislike" value="Dislike">
											<span class="glyphicon glyphicon-thumbs-down" style="<?= $thumb_style; ?>"</span>
										</button>
										<input type="hidden" name="answer_id" value="<?php echo $row['AnswerID']; ?>">
										<input type="hidden" name="question_id" value="<?php echo $row['QuestionID']; ?>">
										<input type="hidden" name="score" value="-1">
									</div>
								</form>
								</td>
								<td>
								<form role="form" class="form-inline" method="post" action=".?action=comment-include-question-update">
									<div class="form-group">

										<?php
											// checkmark button color
											if ($row['LikedIncludeQuestion'] == 1)
											{
												$thumb_style = "color:red";
											}
											else 
											{
												$thumb_style = "color:silver";
											}
	
							    		?>

										<button class="btn btn-default btn-md" name="btnIncludeQuestion" value="">
											<span class="glyphicon glyphicon-ok" style="<?= $thumb_style; ?>"</span>
										</button>

										<?php
											// flash message
								    		if ($row['AnswerID'] == $answer_id_updated)
								    		{
								    			$flash_message_include_question = 
								    				isset($_SESSION['flash_message_include_question']) ? $_SESSION['flash_message_include_question'] : '';
								    			echo "<span class='text-danger'><br />{$flash_message_include_question}</span>";

								    			// clear after displaying
								    			$_SESSION['flash_message_include_question'] = '';
								    		}
								    		else
								    		{
								    			// nothing
								    		}
										?>
										<input type="hidden" name="answer_id" value="<?php echo $row['AnswerID']; ?>">
										<input type="hidden" name="question_id" value="<?php echo $row['QuestionID']; ?>">
									</div>
								</form>
					            </td>

					            <td>
					            	<?php 
					            		if ( $row['LikedDate'] != "")
					            		{
					            			echo date_format(date_create($row['LikedDate']), 'm/d/Y');
					            		} 
					            	?>

						            </td>

						            <td><?= $row['LikedBy']; ?></td>

						        	<td>
						        		<a href=".?action=survey-detail&id=<?php echo $row['AuthenticationID']; ?>" 
						            	onclick="window.open(this.href, 'newwindow', 'width=900, height=800, scrollbars=yes, resizable=yes, titlebar=no, menubar=no, location=no').focus(); return false;"> 
						            	<span class="glyphicon glyphicon-zoom-in"></span></a>
					            	</td>

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

<?php
	// clear after page load
	$_SESSION['answer_id_updated'] = 0;
?>