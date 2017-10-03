<!DOCTYPE html>
<html>
<head>
	<title>ANO Mail Server Statistics</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/moment.min.js"></script>
  <!--<script type="text/javascript" src="js/jquery.table2excel.js"></script>-->

  <script type="text/javascript" src="js/loader.js"></script>

  <link rel="stylesheet" href="css/font-awesome.css" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />

  <script src="js/bootstrap.min.js"></script>

  <script type="text/javascript" src="js/select2.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/select2.min.css" />

  <script type="text/javascript" src="js/daterangepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />

  <link rel="stylesheet" type="text/css" href="css/style.css" />

 <?php
 	$today = date('Y-m-d');
 	$week = array('start'=>date('Y-m-d', strtotime("-3 days")),'end'=>date('Y-m-d'));
 ?>
 </head>
 <body>
 	
 <!-- Menu Bar -->
	<nav class="navbar navbar-inverse">
	  <div class="container-fluid">
	    <div class="navbar-header">
	      <a class="navbar-brand" href="index.php">ANO Mail Server Statistics</a>
	    </div>
	    <ul class="nav navbar-nav">
	      <li <?php if($_GET['page'] == 'mlist') { echo 'class="active"'; } ?>><a href="index.php?page=mlist&numOFrows=100&daterange=<?php echo $today;?>+-+<?php echo $today;?>"><span class="fa fa-file-text"></span> MailLog</a></li>
	      <li <?php if($_GET['page'] == 'mailbox') { echo 'class="active"'; } ?>><a href="index.php?page=mailbox&numOFrows=100&daterange=<?php echo $today;?>+-+<?php echo $today;?>"><span class="fa fa-envelope"></span> Bounced MailBox</a></li>
	      <li <?php if(($_GET['page'] == 'stats') || ($_GET['page'] == 'bounced')) { echo 'class="dropdown active"'; } else { echo 'class="dropdown"'; } ?> ><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-bar-chart"></span> Charts<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li <?php if($_GET['page'] == 'stats') { echo 'class="active"'; } ?> ><a href="index.php?page=stats&daterange=<?php echo $week['start'];?>+-+<?php echo $week['end'];?>"><span class="fa fa-bar-chart"></span> Delivery Chart</a></li>
              <li <?php if($_GET['page'] == 'bounced') { echo 'class="active"'; } ?> ><a href="index.php?page=bounced&daterange=<?php echo $today;?>+-+<?php echo $today;?>"><span class="fa fa-pie-chart"></span> Bounced Chart</a></li>
            </ul>
          </li>
	      <li <?php if($_GET['btn'] == 'conf') { echo 'class="active dropdown"'; } else { echo 'class="dropdown"'; } ?> ><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-gears"></span> Settings<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li <?php if($_GET['page'] == 'logst') { echo 'class="active"'; } ?> ><a href="index.php?page=logst&btn=conf"><span class="fa fa-wrench"></span> Log Status</a></li>
              <li <?php if($_GET['page'] == 'mlog') { echo 'class="active"'; } ?> ><a href="index.php?page=mlog&btn=conf"><span class="fa fa-wrench"></span> Mail Log</a></li>
              <li <?php if($_GET['page'] == 'mbox') { echo 'class="active"'; } ?> ><a href="index.php?page=mbox&btn=conf"><span class="fa fa-wrench"></span> Bounce Mail Box</a></li>
              <li <?php if($_GET['page'] == 'inout') { echo 'class="active"'; } ?> ><a href="index.php?page=inout&btn=conf"><span class="fa fa-wrench"></span> SMTP IN/OUT</a></li>
              <li <?php if($_GET['page'] == 'codes') { echo 'class="active"'; } ?> ><a href="index.php?page=codes&btn=conf"><span class="fa fa-wrench"></span> Bounce Codes</a></li>
              <li <?php if($_GET['page'] == 'dmcfg') { echo 'class="active"'; } ?> ><a href="index.php?page=dmcfg&btn=conf"><span class="fa fa-wrench"></span> Domains</a></li>
            </ul>
          </li>
	    </ul>
	  </div>
	</nav>


<?php

	if(empty($_GET['page'])) {
		include('main.php');
		main();
	}	
	elseif($_GET['page'] == 'bounced') {
		include('PieChart.php');
		PieChart();
	}
	elseif ($_GET['page'] == 'stats') {
		include('SentChart.php');
		SentChart();
	}
	elseif($_GET['page'] == 'mlist') {
		include('emails.php');
		EmailList();
	}
	elseif($_GET['page'] == 'mlog') {
		include('MailLogConf.php');
		MailLogConf();
	}
	elseif ($_GET['page'] == 'mbox') {
		include('MailBoxConf.php');
		MailBoxConf();
	}
	elseif ($_GET['page'] == 'logst') {
		include('logstatus.php');
		LogStatus();
	}
	elseif ($_GET['page'] == 'inout') {
		include('SmtpInOutConf.php');
		SmtpInOutConf();
	}
	elseif ($_GET['page'] == 'codes') {
		include('BounceCodesConf.php');
		BounceCodesConf();
	}
	elseif ($_GET['page'] == 'mailbox') {
		include('BounceMailBox.php');
		BounceMailBox();
	}
	elseif ($_GET['page'] == 'dmcfg') {
		include('DomainsConf.php');
		DomainsConf();
	}
?>
 </body>