<?php function LogStatus() { ?>
<!DOCTYPE html>
<html>
<head>
<?php
	include('lib/QueryLogStatus.php');
	$query = new QueryLogStatus();
?>
</head>
<body>

<div class="EmailTables container" style="margin-top: 70px; width: 100%;">

<table class="table-bordered" id="emailTable">
	<thead>
		<tr>
			<th>Log ID</th>
			<th>LogDescription</th>
			<th>Line Number</th>
			<th>Date</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Log ID</th>
			<th>LogDescription</th>
			<th>Line Number</th>
			<th>Date</th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ($query->GET() as $row) {
				echo 
					'<tr>',
						'<td>',$row['log_id'],'</td>',
						'<td>',$row['log_description'],'</td>',
						'<td>',$row['line_num'],'</td>',
						'<td>',$row['date'],'</td>',
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
   		$('#emailTable').DataTable({
   			//"bFilter": false,
   			"iDisplayLength": 25,
   			"order": [3,'desc']
   		});
	});
</script>
</body>
</html>
<?php } ?>