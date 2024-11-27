<?php

    require_once '../models/auth.php';

    $auth = new Auth();

    $auth->pre_run_check( $_SERVER['PHP_SELF']  );

    $email = isset( $_SESSION['email'] ) == 1 ? $_SESSION['email'] : '';

    // debug url
    if ( strpos( $_SERVER["REQUEST_URI"], '?') > 0 )
    {
        $debug_url = $_SERVER["REQUEST_URI"] . '&debug';
    }
    else
    {
        $debug_url = $_SERVER["REQUEST_URI"] . '?debug';
    }

    // admin users
    $admins = ['javier@ceojuice.com', 'aj@ceojuice.com', 'todd@ceojuice.com', 'john@ceojuice.com', 'gary@ceojuice.com', 'deb@ceojuice.com', 'eric@ceojuice.com'];

    if ( in_array( $email, $admins ) || $allow_everyone = 1 ) 
    {
        $show_debug_link = true;
    }
    else
    {
        $show_debug_link = false;
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CEO Juice - Survey Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>


    <!-- Custom CSS -->
    <link href="../css/sb-admin.css" rel="stylesheet">

     <!-- Our CSS -->
    <link href="../css/style.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <!--<link href="../css/plugins/morris.css" rel="stylesheet">-->

    <!-- Custom Fonts -->
    <link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- jQuery -->
    <script src="../js/jquery-1.11.1.min.js"></script>

    <!-- jquery ui -->
    <script src="../js/jquery-ui-1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="../js/jquery-ui-1.11.1/jquery-ui.min.css" />

    <!-- Highcharts -->
    <script src="../js/plugins/highcharts/js/highcharts.js"></script>
    <script src="../js/plugins/highcharts/js/modules/exporting.js"></script>

    <script src="../js/plugins/highcharts/js/highcharts-more.js"></script>
    <script src="../js/plugins/highcharts/js/modules/solid-gauge.src.js"></script>

    <!-- Morris Charts JavaScript -->
    <!--
    <script src="../js/plugins/morris/raphael.min.js"></script>
    <script src="../js/plugins/morris/morris.min.js"></script>
    -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        $(window).load(function() {
          $('#dvLoading').fadeOut(1000);
        });
    </script>

    <script>
        function printFunction() {
            window.print();
        }
    </script>

</head>

<body>

<br />

<div id="dvLoading"></div>


<div id="wrapper">
    <div id="page-wrapper">

        <div class="container-fluid">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"><<body>/span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href=".">CEO Juice Survey Dashboard</a>
            </div>

            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $email; ?></b></a>
                </li>
            </ul>

            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li class="active">
                        <a href="."><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="."><i class="fa fa-fw fa-bar-chart-o"></i> Surveys and NPS</a>
                    </li>
                    <li>
                        <a href=".?action=nps-by-year"><i class="fa fa-fw fa-bar-chart-o"></i> NPS by Year</a>
                    </li>
                    <li>
                        <a href=".?action=nps-benchmarks"><i class="fa fa-fw fa-bar-chart-o"></i> NPS Benchmarks</a>
                    </li>
                    <li>
                        <a href=".?action=survey-scores-by-month"><i class="fa fa-fw fa-bar-chart-o"></i> Survey Scores by Month</a>
                    </li>
                    <li>
                        <a href=".?action=survey-scores-by-tech"><i class="fa fa-fw fa-bar-chart-o"></i> Survey Scores by Tech</a>
                    </li>
                    <li>
                        <a href=".?action=survey-comments"><i class="fa fa-comment"></i> Survey Comments</a>
                    </li>
                    <li>
                        <a href="" onclick="printFunction()" media="print"><i class="fa fa-print"></i> Print Page</a>
                    </li>
                    <?php if ( $show_debug_link == true ): ?>
                    <li>
                        <a href="<?= $debug_url ?>"><i class="fa fa-fw fa-wrench"></i> Debug</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>
