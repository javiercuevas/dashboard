<?php
    // timer
    $msc = microtime(true);
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
        $(window).load(function(){
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

