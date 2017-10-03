<!DOCTYPE html>
<html>
<head>
	<title>Details</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>

<div class="DetailsTables">
<?php

include('lib/SQLEmailLog.php');
 
$query = new SQLEmailLog();

$data = $_GET;
$arr = $query->GET($data);


if($data['q_type'] == 'LD') {
echo 
	'<h2>Client:</h2>',
		'<table>',

			'<tr>',
		  		'<th>Message id:</th>',
		  		'<td>',$arr['client_mess_id'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Log id:</th>',
		  		'<td>',$arr['client_log_id'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Host:</th>',
		  		'<td>',$arr['client_host'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Instance:</th>',
		  		'<td>',$arr['client_inst'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Instance Process:</th>',
		  		'<td>',$arr['client_proc'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Connecting Client:</th>',
		  		'<td>',$arr['client'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Sasl Method:</th>',
		  		'<td>',$arr['client_sasl_method'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Sasl Username:</th>',
		  		'<td>',$arr['client_sasl_username'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Date:</th>',
		  		'<td>',$arr['client_date'],'</td>',
			'</tr>',

	  '</table>',

	  '<br>',

	  '<h2>From:</h2>',

	  '<table>',

			'<tr>',
		  		'<th>Message id:</th>',
		  		'<td>',$arr['from_mess_id'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Log id:</th>',
		  		'<td>',$arr['from_log_id'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Host:</th>',
		  		'<td>',$arr['from_host'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Instance:</th>',
		  		'<td>',$arr['from_inst'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Instance Process:</th>',
		  		'<td>',$arr['from_proc'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>From:</th>',
		  		'<td>',$arr['from_addr'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Size:</th>',
		  		'<td>',$arr['from_size'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Date:</th>',
		  		'<td>',$arr['from_date'],'</td>',
			'</tr>',

		'</table>',

		'<br>',

		'<h2>Delivery:</h2>',

		'<table>',

			'<tr>',
		  		'<th>Message id:</th>',
		  		'<td>',$arr['delivery_mess_id'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Log id:</th>',
		  		'<td>',$arr['delivery_log_id'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Host:</th>',
		  		'<td>',$arr['delivery_host'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Instance:</th>',
		  		'<td>',$arr['delivery_inst'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Instance Process:</th>',
		  		'<td>',$arr['delivery_proc'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>To:</th>',
		  		'<td>',$arr['delivery_to_addr'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Orig To:</th>',
		  		'<td>',$arr['delivery_orig_to'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Relay:</th>',
		  		'<td>',$arr['delivery_relay'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Delay:</th>',
		  		'<td>',$arr['delivery_delay'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Delays:</th>',
		  		'<td>',$arr['delivery_delays'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>DSN:</th>',
		  		'<td>',$arr['delivery_dsn'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Status:</th>',
		  		'<td>',$arr['delivery_status'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Delivery Details:</th>',
		  		'<td>',$arr['delivery_details'],'</td>',
			'</tr>',

			'<tr>',
		  		'<th>Date:</th>',
		  		'<td>',$arr['delivery_date'],'</td>',
			'</tr>',

		'</table>';
	}
	elseif($data['q_type'] == 'BD') {
		echo 	
			'<h2>Bounced Raport:</h2>',

			'<table>',

				'<tr>',
			  		'<th>Mail Box ID:</th>',
			  		'<td>',$arr['mbox_id'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Reporting MTA:</th>',
			  		'<td>',$arr['reporting-mta'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Message ID:</th>',
			  		'<td>',$arr['mess_id'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>From:</th>',
			  		'<td>',$arr['from_addr'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>To:</th>',
			  		'<td>',$arr['to_addr'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Orig TO:</th>',
			  		'<td>',$arr['orig_to'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Action:</th>',
			  		'<td>',$arr['action'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Status:</th>',
			  		'<td>',$arr['status'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Remote MTA:</th>',
			  		'<td>',$arr['remote-mta'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Diagnostic Code:</th>',
			  		'<td>',$arr['diagnostic-code'],'</td>',
				'</tr>',

				'<tr>',
			  		'<th>Date:</th>',
			  		'<td>',$arr['date'],'</td>',
				'</tr>',

			'</table>';
	}
?>
</div>

</body>
