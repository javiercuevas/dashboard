<?php

    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    // query
    $results = $dashboard->question_respondent_summary_by_tech_get( 
        $customer_id = $customer_id, 
        $date_from = $date_from_default, 
        $date_to = $date_to_default, 
        $survey_id_array = $survey_id_selected_array,
        $eauto_customer_name = $surveyed_customer_name 
        )->fetchAll();

    $questions_included = $dashboard->questions_for_tech_score_get($customer_id = $customer_id)->fetchAll();

    // to hold our unique techs with related scores
    $technicians = [];
    $avg_scores = [];

    foreach( $results as $row ) 
    {
        if ( $row['CreateMonth'] == 'Total' && $row['TechnicianName'] != 'Total' )
        {
            array_push( $technicians, $row['TechnicianName'] );
            array_push( $avg_scores, $row['AvgScore'] );
        }
    }

    // dynamic chart height
    $chart_height = count($technicians) * 50 > 500 ? count($technicians)  * 50 : 500;

?>

<script type="text/javascript">

$(function () {
    $('#scores-by-tech').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Score by Technician'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( str_replace("'", "\'", $technicians) as $m ) { echo "'" . $m . "'" . ", "; } ?> ],
            title: {
                text: null
            },
            labels: {
                rotation: 0
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Avg Score',
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
            bar: {
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
            data: [ <?php foreach( $avg_scores as $s ) { echo $s . ", "; } ?> ],
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
                    Selected Date Range: <?= $date_from_default; ?> to <?= $date_to_default; ?> <br />
                    Selected Surveys: <?php echo isset($customer_detail_results['CustomerName']) ? $surveys_selected_list : 'All'; ?> <br />
                    <?php $utilities->surveyed_customer_selected_display( $eauto_customer_name ); ?>
                    <br /><br />

                    <span class="light-gray-lg">Questions Included in Scoring: </span><br />
                    <span class="light-gray-md">
                        <ul>
                        <?php foreach ( $questions_included as $q ) : ?>
                            <li><?= $q['QuestionText']; ?> </li>
                        <?php endforeach; ?>
                        </ul>
                    </span>
                </h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Survey Scores by Technician</h3>
                    </div>
                    <div class="panel-body">
                        <div id="scores-by-tech" style="height:<?= $chart_height; ?>px"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <table class="table table-condensed">
                    <tr class="active">
                        <th>Technician</th>
                        <th>Month</th>
                        <th>Respondents</th>
                        <th>Avg Score</th>
                    </tr>
                    <?php foreach ($results as $row) : ?>
                    <?php

                        // total row logic
                        if ( $row['TechnicianName'] == 'Total' ) 
                        {
                            $tech_name = "<b>Grand Total</b>";
                        }
                        elseif ( $row['CreateMonthText'] == 'Total' ) 
                        {
                            $tr_class = "active";
                            $tech_name = "<b>" . $row['TechnicianName'] . " - Total" . "</b>";
                        } 
                        else 
                        {
                            $tr_class = "";
                            $tech_name = $row['TechnicianName'];
                        }

                    ?>
                    <tr class="<?= $tr_class; ?>">
                        <td><?= $tech_name; ?></td>
                        <td><?= $row['CreateMonthText']; ?></td>
                        <td><?= $row['Respondents']; ?></td>
                        <td><?= number_format($row['AvgScore'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>


