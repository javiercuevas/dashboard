<?php

    require_once '../models/db.php';
    require_once '../models/dashboard.php';
    require_once '../models/auth.php';

    $dashboard = new Dashboard();
    $auth = new Auth();

    $utilities = new utilities();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    $customer_detail_results = $dashboard->customer_detail_get( $customer_id )->fetch();

    // nps overall yearly
    $nps_overall_yearly_results = $dashboard->nps_overall_yearly_get ( 
                                        $customer_id = $customer_id, 
                                        $date_from = $date_from_default, 
                                        $date_to = $date_to_default,
                                        $survey_id_array = $survey_id_selected_array,
                                        $eauto_customer_name = $surveyed_customer_name
                                        );
    $nps_overall_yearly_year = [];
    $nps_overall_yearly_score = [];

    // create arrays
    foreach( $nps_overall_yearly_results as $row ) 
    {
        array_push( $nps_overall_yearly_year, $row['CreateYear'] );
        array_push( $nps_overall_yearly_score, $row['NPSScore'] );
    }

?>

<script type="text/javascript">

/*******************************************
  overall nps scores (monthly)
 *******************************************/
$(function () {
    $('#nps-yearly-chart').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'NPS Score By Year'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( $nps_overall_yearly_year as $m ) { echo "'" . $m . "'" . ", "; } ?> ],
            title: {
                text: null
            },
            labels: {
                rotation: -90
            }
        },
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: 'NPS Score',
                align: 'middle'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Avg Score',
            data: [ <?php foreach( $nps_overall_yearly_score as $s ) { echo $s . ", "; } ?> ],
            color: '#3b8cd1'
        }]
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
                    Selected Surveys: <?php echo isset($customer_detail_results['CustomerName']) ? $surveys_selected_list : 'All'; ?> <br />
                    <?php $utilities->surveyed_customer_selected_display( $eauto_customer_name ); ?>
                </h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> NPS Score By Year</h3>
                    </div>
                    <div class="panel-body">
                        <div id="nps-yearly-chart" style="height:500px"></div>
                    </div>
                </div>
            </div>
        </div>
