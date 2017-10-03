<?php function main() { 
	include('lib/SQLStatus.php');
	$status = new SQLStatus;
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>

<div class="EmailTables container" style="margin-top: 70px; width: 100%;">
<table class="table-bordered" id="mlog_status">
	<thead>
		<tr>
			<th>Status</th>
			<th>LogRotate</th>
			<th>LogDescription</th>
			<th>LogPath</th>
			<th>LogType</th>
			<th>Number of Iteration</th>
			<th>Daily Rotate Time</th>
			<th>Weekly Rotate Day</th>
			<th>Del from DB records older then days</th>
			<th>Del Logs older then days</th>
			<th>Active</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Status</th>
			<th>LogRotate</th>
			<th>LogDescription</th>
			<th>LogPath</th>
			<th>LogType</th>
			<th>Number of Iteration</th>
			<th>Daily Rotate Time</th>
			<th>Weekly Rotate Day</th>
			<th>Del from DB records older then days</th>
			<th>Del Logs older then days</th>
			<th>Active</th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ($status->GET('mlog') as $row) {
				$column = array();
				if($row['active'] == 1) { $column['active'] = 'ON'; } else { $column['active'] = 'OFF'; }
				if($row['logrotate'] == 1) { $column['logrotate'] = 'YES'; } else { $column['logrotate'] = 'NO'; }
				if($row['logtype'] == 1) { $column['logtype'] = 'Default Log Type'; } else { $column['logtype'] = 'ANO Log Type'; }
				if($row['status'] == 'on') { $column['status'] = '<p class="btn btn-success btn-sm">Online</p>'; }
				if($row['status'] == 'off') { $column['status'] = '<p class="btn btn-danger btn-sm">Offline</p>'; }
				echo 
					'<tr>',
						'<td>',$column['status'],'</td>',
						'<td>',$column['logrotate'],'</td>',
						'<td>',$row['log_description'],'</td>',
						'<td>',$row['log'],'</td>',
						'<td>',$column['logtype'],'</td>',
						'<td>',$row['iteration_num'],'</td>',
						'<td>',$row['R_H'].':'.$row['R_M'],'</td>',
						'<td>',$row['R_W'],'</td>',
						'<td>',$row['del_older_rows'],'</td>',
						'<td>',$row['del_older_logs'],'</td>',
						'<td>',$column['active'],'</td>',
					'</tr>';
			}
		?>
	</tbody>
</table>
</div>

<div class="EmailTables container" style="margin-top: 70px; width: 100%;">
<table class="table-bordered" id="mbox_status">
	<thead>
		<tr>
			<th>Status</th>
			<th>Host</th>
			<th>Port</th>
			<th>SSL</th>
			<th>Account</th>
			<th>Password</th>
			<th>Number of Iteration</th>
			<th>Del from DB records older then days</th>
			<th>Active</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Status</th>
			<th>Host</th>
			<th>Port</th>
			<th>SSL</th>
			<th>Account</th>
			<th>Password</th>
			<th>Number of Iteration</th>
			<th>Del from DB records older then days</th>
			<th>Active</th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ($status->GET('mbox') as $row) {
				$column = array();
				if($row['active'] == 1) { $column['active'] = 'ON'; } else { $column['active'] = 'OFF'; }
				if($row['ssl'] == 1) { $column['ssl'] = 'ON'; } else { $column['ssl'] = 'OFF'; }
				if($row['status'] == 'on') { $column['status'] = '<p class="btn btn-success btn-sm">Online</p>'; }
				if($row['status'] == 'off') { $column['status'] = '<p class="btn btn-danger btn-sm">Offline</p>'; }
				echo 
					'<tr>',
						'<td>',$column['status'],'</td>',
						'<td>',$row['host'],'</td>',
						'<td>',$row['port'],'</td>',
						'<td>',$column['ssl'],'</td>',
						'<td>',$row['account'],'</td>',
						'<td>',$row['password'],'</td>',
						'<td>',$row['iteration_num'],'</td>',
						'<td>',$row['del_older_rows'],'</td>',
						'<td>',$column['active'],'</td>',
					'</tr>';
			}
		?>
	</tbody>
</table>
</div>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
   	$(document).ready(function(){
   		$('#mlog_status').DataTable({
   			"bFilter": false,
   			"iDisplayLength": 10
   		});
   		$('#mbox_status').DataTable({
   			"bFilter": false,
   			"iDisplayLength": 10
   		});
	});
</script>
</body>
</html>
<?php } ?>