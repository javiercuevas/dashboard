<?php

    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    // query
    $results = $dashboard->question_respondent_summary_by_month_get( 
        $customer_id = $customer_id, 
        $date_from = $date_from_default, 
        $date_to = $date_to_default, 
        $survey_id_array = $survey_id_selected_array,
        $eauto_customer_name = $surveyed_customer_name 
        );

    $months = [];
    $questions_0_10 = [];
    $questions_yes_no = [];

    $months_dict = [];
    $questions_dict = [];

    foreach ( $results as $row ) 
    {

        $months[ $row['CreateMonth'] ] = $row['CreateMonthText'];

        if ( $row['QuestionType'] == 'Scale 0-10' ) 
        {
            array_push( $questions_0_10,  $row['QuestionText'] );
        }

        if ( $row['QuestionType'] == 'Yes/No' ) 
        {
            array_push( $questions_yes_no,  $row['QuestionText'] );
        }

        $questions_dict[ $row['QuestionText'] . $row['CreateMonth'] ] = $row['AvgScore'];
    }

    //$months = array_unique($months);
    ksort($months);

    $questions_0_10 = array_unique($questions_0_10);
    $questions_yes_no = array_unique($questions_yes_no);

?>

<script type="text/javascript">

$(function () {
    $('#survey-scores-1').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Average Survey Scores by Month (0-10)',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },
        xAxis: {
            categories: [ <?php foreach ($months as $k => $v) { echo "'" . $v . "'" . ", "; } ?> ]
        },
        yAxis: {
            max:10,
            title: {
                text: 'Avg Score'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ''
        },
        legend: {
            layout: 'vertical',
            align: 'center',
            verticalAlign: 'bottom',
            borderWidth: 0
        },
        credits: {
            enabled: false
        },
        series: [

        <?php foreach (str_replace("'", "\'", $questions_0_10) as $q) : ?>
            {
                name: '<?= substr($q, 0, 100) ?>',
                lineWidth: 5,
                data: [ 
                <?php 
                    foreach ($months as $k => $v) { 
                        if ( $questions_dict[$q . $k] == '' ) { 
                            echo 'null' . ", "; 
                        } else { 
                            echo $questions_dict[$q . $k] . ", "; 
                        } 
                    } 
                ?> 
                ]
            }, 
        <?php endforeach; ?>
        ]
    });
});



$(function () {
    $('#survey-scores-2').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Average Survey Scores by Month (Yes/No, No=0, Yes=1)',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },
        xAxis: {
            categories: [ <?php foreach ($months as $k => $v) { echo "'" . $v . "'" . ", "; } ?> ]
        },
        yAxis: {
            max:1,
            title: {
                text: 'Avg Score'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ''
        },
        legend: {
            layout: 'vertical',
            align: 'center',
            verticalAlign: 'bottom',
            borderWidth: 0
        },
        credits: {
            enabled: false
        },
        series: [

        <?php foreach (str_replace("'", "\'", $questions_yes_no) as $q) : ?>
            {
                name: '<?= substr($q, 0, 100) ?>',
                lineWidth: 5,
                data: [ 
                    <?php 
                        foreach ($months as $k => $v) 
                        { 
                            if ( $questions_dict[$q . $k] == '' ) 
                            { 
                                echo 'null' . ", "; 
                            } 
                            else 
                            { 
                                echo $questions_dict[$q . $k] . ", "; 
                            } 
                        } 
                    ?> 
                ]
            }, 
        <?php endforeach; ?>
        ]
    });
});


</script>


<div id="page-wrapper">
    <div class="container-fluid">

        <div><a href="javascript:history.go(-1);" class="btn btn-default btn-sm" role="button" style="width:125px"><span class="glyphicon glyphicon-chevron-left"></span> Back</a></div>

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h4 class="page-header">
                    Selected Date Range: <?= $date_from_default; ?> to <?= $date_to_default; ?> <br />
                    Selected Surveys: <?php echo isset($customer_detail_results['CustomerName']) ? $surveys_selected_list : 'All'; ?> <br />
                    <?php $utilities->surveyed_customer_selected_display( $eauto_customer_name ); ?>
                    <br />
                    <span class="light-gray-md">Click on question text to show/hide line charts</span>
                </h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Survey Scores by Month</h3>
                    </div>
                    <div class="panel-body">
                        <div id="survey-scores-1" style="height:600px"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Survey Scores by Month</h3>
                    </div>
                    <div class="panel-body">
                        <div id="survey-scores-2" style="height:600px"></div>
                    </div>
                </div>
            </div>
        </div>
