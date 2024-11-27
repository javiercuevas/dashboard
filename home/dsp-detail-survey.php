<!DOCTYPE html>
<html lang="en">
<head>
    <title>Survey Details</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- table sorter -->
    <link rel="stylesheet" href="../js/tablesorter/themes/gray/style.css">
    <script src="../js/tablesorter/jquery-latest.js"></script>
    <script src="../js/tablesorter/jquery.tablesorter.js"></script>


    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

     <!-- Our CSS -->
    <link href="../css/style.css" rel="stylesheet">

    <script>
        function printFunction() {
            window.print();
        }
    </script>

    <style>
        body {
            padding:10px;
        }
    </style>

</head>

<?php

    require_once '../models/db.php';
    require_once '../models/dashboard.php';

    $dashboard = new Dashboard();

    $authentication_id = isset( $_GET['id'] ) == 1 ? $_GET['id'] : 0;

    $survey_response_results = $dashboard->survey_response_get( $authentication_id )->fetchAll();
    $survey_response_results_first = $survey_response_results[0];

    // if session customer_id doesn't match this detail, abort
    if ( $survey_response_results_first['customerid'] != $customer_id )
    {
        echo "Sorry, you don't have rights to view this record";
    }

?>

<body>

<button class="btn btn-md btn-default" onclick="printFunction()" name="" value="">
    <span class="glyphicon glyphicon-print"></span> Print
</button>
<br /><br />

<div style="width:98%">

<b>Suvey Title:</b> <?php echo $survey_response_results_first['SurveyTitle']; ?>
<br />
<b>Survey Sent To:</b> <?php echo $survey_response_results_first['SurveySentTo']; ?>
<br />
<b>Survey Start Date:</b> <?php echo date_format(date_create($survey_response_results_first['SurveyStartdate']), 'm/d/Y'); ?>
<br />
<b>Create Date:</b> <?php echo date_format(date_create($survey_response_results_first['CreateDate']), 'm/d/Y'); ?>
<br />
<b>Completed Date:</b> <?php echo date_format(date_create($survey_response_results_first['completeddate']), 'm/d/Y'); ?>
<br />
<b>Reference Name:</b> <?php echo $survey_response_results_first['referencename']; ?>
<br />
<b>Eauto Reference:</b> <?php echo $survey_response_results_first['EAutoReference']; ?>
<br />

<h3>Details:</h3>
<table id="myTable" class="table table-condensed table-bordered tablesorter">
    <thead>
    <tr class="bg-active">
        <th>Row</th>
        <th>QuestionText</th>
        <th>QuestionType</th>
        <th>Answer</th>
        <th>Target</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php $row_number = 1; ?>
    <?php foreach( $survey_response_results as $row ): ?>

        <tr bgcolor="CCCCCC !important">
            <td><?php echo $row_number; ?></td>
            <td>
                <?php echo $row['Questiontext']; ?>
                <?php
                    if ( strlen( $row['Comment' ]) > 0 ) {
                        echo '<br />' . '<span class="text-danger">' . 'Comment: ' . $row['Comment'] . '</span>';
                    }
                ?>
            </td>

            <td><?php echo $row['questiontype']; ?></td>
            <td><?php echo $row['Answer']; ?></td>
            <td><?php echo $row['TargetRank']; ?></td>
            <td>
                <?php
                    if ( is_numeric($row['rank']) && is_numeric($row['TargetRank']) && $row['rank'] < $row['TargetRank']) {
                        echo '<i class="fa fa-exclamation-triangle fa-2x" style="color:red"></i> ';
                    }
                    else {
                        echo '';
                    }
                ?>
            </td>
        </tr>
        <?php $row_number++; ?>
    <?php endforeach; ?>
    </tbody>
</table>

</div>

</body>
</html>
