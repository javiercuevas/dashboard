<?php

    require_once '../models/auth.php';

    $auth = new Auth();
    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    $utilities = new Utilities();

    $survey_stats_summary_results = $dashboard->survey_stats_summary_get( $customer_id, $date_from_default, $date_to_default, $survey_id_selected_array, $surveyed_customer_name )->fetch();

    $percent_response = $survey_stats_summary_results['SurveysSent'] == 0 ? 0 : $survey_stats_summary_results['TotalResponses'] / $survey_stats_summary_results['SurveysSent'] * 100;

    $percent_checked_nosubmit = $survey_stats_summary_results['CheckedButNoSubmit'] == 0 ? 0 : $survey_stats_summary_results['CheckedButNoSubmit'] / $survey_stats_summary_results['TotalResponses'] * 100;

    $percent_opt_outs = $survey_stats_summary_results['OptOuts'] == 0 ? 0 : $survey_stats_summary_results['OptOuts'] / $survey_stats_summary_results['TotalResponses'] * 100;

    include 'dsp-charts-1.php';

?>

<div id="page-wrapper">

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="">
                    <?php echo isset($customer_detail_results['CustomerName']) ? $customer_detail_results['CustomerName'] : "Select Customer"; ?>
                    <br />
                    <span class="light-gray-md">
                        <?php echo isset($customer_detail_results['CustomerName']) ? 'Survey data history: ' . $min_date . " - " . $max_date : ''; ?>
                    </span><br />
                    <span class="light-gray-lg">
                        <?php echo isset($customer_detail_results['CustomerName']) ? 'Selected Date Range: ' . $date_from_default . ' and ' . $date_to_default : ''; ?>
                        <br />
                        <?php echo isset($customer_detail_results['CustomerName']) ? 'Selected Surveys: ' . $surveys_selected_list : ''; ?>
                        <br />
                        <?php $utilities->surveyed_customer_selected_display( $eauto_customer_name ); ?>
                    </span>
                </h1>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-lg-12">
                    <?php include 'dsp-form.php'; ?>
            </div>
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-1">
                                <i class="fa fa-envelope fa-2x"></i>
                            </div>
                            <div class="col-xs-10 text-center">
                                <div class="huge"><?php echo number_format( $survey_stats_summary_results['SurveysSent'] ); ?></div>
                                <div class="huge"></div>
                                <div><span class="white-md">Surveys sent</span></div>
                                <div><span class="white-md">&nbsp;</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="panel panel-gray">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-1">
                                <i class="fa fa-comments fa-2x"></i>
                            </div>
                            <div class="col-xs-10 text-center">
                                <div class="huge"><?php echo number_format( $survey_stats_summary_results['TotalResponses'] ); ?></div>
                                <div class="huge"></div>
                                <div><span class="white-md">Responses&nbsp;(<?php echo number_format( $percent_response, 2); ?>%)</span></div>
                                <div><span class="white-md">&nbsp;</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="panel panel-yellow">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-1">
                                <i class="fa fa-at fa-2x"></i>
                            </div>
                            <div class="col-xs-10 text-center">
                                <div class="huge"><?php echo number_format( $survey_stats_summary_results['UniqueEmails'] ); ?></div>
                                <div><a href=".?action=survey-count-by-email"><span class="white-md">Contacts&nbsp;<span class="glyphicon glyphicon-zoom-in" style="color:white"></span></span></a></div>
                                <div><span class="white-md">&nbsp;</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-1">
                                <i class="fa fa-times fa-2x"></i>
                            </div>
                            <div class="col-xs-10 text-center">
                                <div class="huge"><?php echo number_format( $survey_stats_summary_results['CheckedButNoSubmit'] ); ?></div>
                                <div><a href=".?action=clicked-but-not-submitted"><span class="white-md">Click,&nbsp;no&nbsp;submit&nbsp;<span class="glyphicon glyphicon-zoom-in" style="color:white"></span></span></a></div>
                                <div><span class="white-md">(<?php echo number_format( $percent_checked_nosubmit, 2); ?>%)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="panel panel-gray">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-1">
                                <i class="fa fa-comments-o fa-2x"></i>
                            </div>
                            <div class="col-xs-10 text-center">
                                <div class="huge"><?php echo number_format( $survey_stats_summary_results['NoResponses'] ); ?></div>
                                <div><a href=".?action=no-responses"><span class="white-md">No responses&nbsp;<span class="glyphicon glyphicon-zoom-in" style="color:white"></span></span></a></div>
                                <div><span class="white-md">&nbsp;</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="panel panel-yellow">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-1">
                                <i class="fa fa-sign-out fa-2x"></i>
                            </div>
                            <div class="col-xs-10 text-center">
                                <div class="huge"><?php echo number_format( $survey_stats_summary_results['OptOuts'] ); ?></div>
                                <div><a href=".?action=opt-outs"><span class="white-md">Opt Outs&nbsp;<span class="glyphicon glyphicon-zoom-in" style="color:white"></span></span></a></div>
                                <div><span class="white-md">(<?php echo number_format( $percent_opt_outs, 2); ?>%)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <div class="row">

            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> NPS by Customer</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive" style="overflow-y: scroll; height:320px">
                            <?php include 'dsp-detail-table-2.php'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-dashboard fa-fw"></i> Overall NPS Score</h3>
                    </div>
                    <div class="panel-body">
                        <div id="container-speed" style="height:300px">
                        </div>
                        <div class="text-right">
                            <a href=".?action=overall-nps-details">View Details <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> NPS Leaders - U.S. 2020</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive" style="height:300px">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>NPS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Costco</td>
                                        <td>74</td>
                                    </tr>
                                    <tr>
                                        <td>Southwest</td>
                                        <td>71</td>
                                    </tr>
                                    <tr>
                                        <td>USAA Bank</td>
                                        <td>68</td>
                                    </tr>
                                    <tr>
                                        <td>Ritz Carlton</td>
                                        <td>68</td>
                                    </tr>
                                    <tr>
                                        <td>Apple</td>
                                        <td>68</td>
                                    </tr>
                                    <tr>
                                        <td>Cricket Wireless</td>
                                        <td>55</td>
                                    </tr>
                                    <tr>
                                        <td>UPS</td>
                                        <td>36</td>
                                    </tr>
                                    <tr>
                                        <td>Dish</td>
                                        <td>17</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <a href="http://www.satmetrix.com/nps-score-model/" target="_blank">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Survey Responses</h3>
                    </div>
                    <div class="panel-body">
                        <div id="respondents-chart" style="height:<?php echo $question_chart_height; ?>px"></div>
                        <div class="text-right">
                            <a href=".?action=respondent-export">Export Data <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Survey Responses</h3>
                    </div>
                    <div class="panel-body">
                        <div id="average-scores-chart" style="height:<?php echo $question_chart_height; ?>px"></div>
                        <div class="text-right">
                            <a href=".?action=respondent-export">Export Data <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- /.row -->

        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Survey Responses</h3>
                    </div>
                    <div class="panel-body">
                        <div id="respondents-other-chart" style="height:<?php echo $question_chart_height_other; ?>px"></div>
                        <div class="text-right">
                            <a href=".?action=respondent-export">Export Data <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Survey Responses</h3>
                    </div>
                    <div class="panel-body">
                        <div id="average-scores-other-chart" style="height:<?php echo $question_chart_height_other; ?>px"></div>
                        <div class="text-right">
                            <a href=".?action=respondent-export">Export Data <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- /.row -->

        <div class="row">

            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-table fa-fw"></i> Response Breakout</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive" style="overflow-y: scroll; height:500px">
                            <?php include 'dsp-detail-table-1.php'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> NPS Score By Month</h3>
                    </div>
                    <div class="panel-body">
                        <div id="nps-monthly-chart" style="height:500px"></div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->
