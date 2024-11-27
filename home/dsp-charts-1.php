<?php

    require_once '../models/db.php';
    require_once '../models/dashboard.php';
    require_once '../models/auth.php';

    $dashboard = new Dashboard();
    $auth = new Auth();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    // query
    $results_2 = $dashboard->question_respondent_summary_get( 
        $customer_id = $customer_id, 
        $date_from = $date_from_default, 
        $date_to = $date_to_default, 
        $survey_id_array = $survey_id_selected_array,
        $eauto_customer_name = $surveyed_customer_name 
        );

    $question_text = [];
    $respondents = [];
    $avg_score = [];

    $question_text_other = [];
    $respondents_other = [];
    $avg_score_other = [];

    $question_count = 0;
    $question_count_other = 0;

    // create questions adn average score arrays
    foreach( $results_2 as $row ) 
    { 

        // 0-10 question type
        if ( $row['QuestionType'] == 'Scale 0-10') 
        {
            //note: replace single quotes
            $question_text_trimmed = strlen( $row['QuestionText'] ) > 255 ? substr( str_replace("'","\\'", $row['QuestionText'] ), 0, 255 ) . "..." : str_replace( "'","\\'", $row['QuestionText'] );

            array_push( $question_text, $question_text_trimmed); 
            array_push( $respondents, $row['Respondents'] ); 

            if ( $row['AvgScore'] == null ) { 
                $avg = 'null'; 
            } else { 
                $avg = $row['AvgScore']; 
            }

            array_push( $avg_score, $avg);

            $question_count++;
        }

        // other question type
        if ( $row['QuestionType'] != 'Scale 0-10') 
        {
            //note: replace single quotes
            $question_text_trimmed_other = strlen( $row['QuestionText'] ) > 255 ? substr( str_replace("'","\\'", $row['QuestionText'] ), 0, 255 ) . "..." : str_replace( "'","\\'", $row['QuestionText'] );

            array_push( $question_text_other, $question_text_trimmed_other); 
            array_push( $respondents_other, $row['Respondents'] ); 

            if ( $row['AvgScore'] == null ) 
            { 
                $avg = 'null'; 
            } 
            else 
            { 
                $avg = $row['AvgScore']; 
            }

            array_push( $avg_score_other, $avg);

            $question_count_other++;
        }

    } 

    // use question count to set the chart height
    $question_chart_height = $question_count >= 10  ? $question_count * 70 : 500;
    $question_chart_height_other = $question_count_other >= 10 ? $question_count_other * 70 : 500;

    // nps overall
    $nps_overall_score_results = $dashboard->nps_overall_score_get( 
                                    $customer_id = $customer_id, 
                                    $date_from = $date_from_default, 
                                    $date_to = $date_to_default,
                                    $survey_id_array = $survey_id_selected_array,
                                    $eauto_customer_name = $surveyed_customer_name
                                    )->fetch();

    // nps overall montly
    $nps_overall_monthly_results = $dashboard->nps_overall_monthly_get( 
                                        $customer_id = $customer_id, 
                                        $date_from = $date_from_default, 
                                        $date_to = $date_to_default,
                                        $survey_id_array = $survey_id_selected_array,
                                        $eauto_customer_name = $surveyed_customer_name
                                        );
    $nps_overall_monthly_month = [];
    $nps_overall_monthly_score = [];
    // create arrays
    foreach( $nps_overall_monthly_results as $row ) 
    {
        array_push( $nps_overall_monthly_month, $row['CreateMonthText'] );
        array_push( $nps_overall_monthly_score, $row['NPSScore'] );
    }

?>

<script type="text/javascript">

/*******************************************
  respondents
 *******************************************/
$(function () {
    $('#respondents-chart').highcharts({
        chart: {
            type: 'bar',
            marginLeft: 300
        },
        title: {
            text: 'Survey Responses By Question (0-10)'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( $question_text as $q ) { echo "'" . $q . "'" . ", "; } ?> ],
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    if(this.value.length > 100) {
                        return(this.value.substring(0,100) + "...");
                    }
                    else {
                        return this.value;
                    }
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Respondents',
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
            name: 'Respondents',
            data: [ <?php foreach( $respondents as $r ) { echo $r . ", "; } ?> ],
            color: '#3b8cd1'
        }]
    });
});

/*******************************************
  respondents other
 *******************************************/
$(function () {
    $('#respondents-other-chart').highcharts({
        chart: {
            type: 'bar',
            marginLeft: 300
        },
        title: {
            text: 'Survey Responses By Question (Yes/No or Comments)'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( $question_text_other as $q ) { echo "'" . $q . "'" . ", "; } ?> ],
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    if(this.value.length > 100) {
                        return(this.value.substring(0,100) + "...");
                    }
                    else {
                        return this.value;
                    }
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number of Respondents',
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
            name: 'Respondents',
            data: [ <?php foreach( $respondents_other as $r ) { echo $r . ", "; } ?> ],
            color: '#3b8cd1'
        }]
    });
});

/*******************************************
  average scores
 *******************************************/
$(function () {
    $('#average-scores-chart').highcharts({
        chart: {
            type: 'bar',
            marginLeft: 300
        },
        title: {
            text: 'Average Score By Question (0-10)'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( $question_text as $q ) { echo "'" . $q . "'" . ", "; } ?> ],
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    if(this.value.length > 100) {
                        return(this.value.substring(0,100) + "...");
                    }
                    else {
                        return this.value;
                    }
                }
            }
        },
        yAxis: {
            min: 0,
            max:10,
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
            data: [ <?php foreach( $avg_score as $a ) { echo $a . ", "; } ?> ],
            color: '#3b8cd1'
        }]
    });
});

/*******************************************
  average scores other
 *******************************************/
$(function () {
    $('#average-scores-other-chart').highcharts({
        chart: {
            type: 'bar',
            marginLeft: 300
        },
        title: {
            text: 'Average Score By Question (Yes/No or Comments, No = 0, Yes = 1)'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( $question_text_other as $q ) { echo "'" . $q . "'" . ", "; } ?> ],
            title: {
                text: null
            },
            labels: {
                formatter: function() {
                    if(this.value.length > 100) {
                        return(this.value.substring(0,100) + "...");
                    }
                    else {
                        return this.value;
                    }
                }
            }
        },
        yAxis: {
            min: 0,
            max:1,
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
            data: [ <?php foreach( $avg_score_other as $a ) { echo $a . ", "; } ?> ],
            color: '#3b8cd1'
        }]
    });
});

/*******************************************
  overall nps scores (monthly)
 *******************************************/
$(function () {
    $('#nps-monthly-chart').highcharts({
        chart: {
            type: 'areaspline'
        },
        title: {
            text: 'NPS Score By Month'
        },
        /*
        subtitle: {
            text: 'Source: Wikipedia.org'
        },
        */
        xAxis: {
            categories: [ <?php foreach( $nps_overall_monthly_month as $m ) { echo "'" . $m . "'" . ", "; } ?> ],
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
            data: [ <?php foreach( $nps_overall_monthly_score as $s ) { echo $s . ", "; } ?> ],
            color: '#3b8cd1'
        }]
    });
});


/*******************************************
  overall nps
 *******************************************/
$(function () {

    var gaugeOptions = {

        chart: {
            type: 'solidgauge'
        },

        title: {
            text: 'Overall NPS Score'
        },

        pane: {
            center: ['50%', '85%'],
            size: '100%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: false
        },

        // the value axis
        yAxis: {
            stops: [
                [0.50, '#DF5353'], // red
                [0.70, '#DDDF0D'], // yellow
                [0.80, '#55BF3B'] //  green
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 400,
            tickWidth: 0,
            title: {
                y: -70
            },
            labels: {
                y: 16
            }
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    // The speed gauge
    $('#container-speed').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: {
            min: 0,
            max: 100
        },

        credits: {
            enabled: false
        },

        series: [{
            name: 'Speed',
            data: [<?php echo number_format($nps_overall_score_results['NPSScore'], 2 ); ?>],
            dataLabels: {
                format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                    ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                       '<span style="font-size:12px;color:silver">NPS Score</span></div>'
            },
            tooltip: {
                valueSuffix: ' NPS Score'
            }
        }]

    }));
});

</script>