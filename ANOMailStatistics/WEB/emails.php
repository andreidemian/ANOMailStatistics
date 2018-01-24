<?php
	function EmailList() {

	include('lib/SQLEmailLog.php');
	include('lib/SQLCSV.php');
	$searchType = array('FROM','TO','CLIENT','RELAY');
	$selectedType = 'null';
	$date = explode(' - ', $_GET['daterange']);
	$data = $_GET;
?>

<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
	$(function() {
  		$('input[name="daterange"]').daterangepicker({
      		locale: {
        		format: 'YYYY-MM-DD'
      		},
       		startDate: <?php if(!empty($_GET['daterange'])) { echo "'".$date[0]."'"; } else { echo date("Y-m-d"); } ?>,
       		endDate: <?php if(!empty($_GET['daterange'])) { echo "'".$date[1]."'"; } else { echo date("Y-m-d"); } ?>
  		});
	});
</script>
</head>
<body>
<!-- Search menu -->
	<div class="SEARCH ">
		<form action="index.php">

			<input type="hidden" name="page" value="mlist">

			<!-- date range bar -->
			<div class="pull-right">
				<input id="daterange" type="txt" name="daterange">
			</div>

			<div class="container form-inline pull-left" style="width: 400px;">
				<div>
	 				<label class="checkbox-inline" for="sent"><b>Sent:</b> </label>
	 				<input <?php if(($_GET['sent'] == '1') && ($_GET['mbox'] == false)){ echo "checked";}; ?> type="checkbox" id="sent" name="sent" value="1">
					<label class="checkbox-inline" for="bounced"><b>Bounced:</b> </label>
					<input <?php if(($_GET['bounced'] == '2') && ($_GET['mbox'] == false)){ echo "checked";}; ?> type="checkbox" id="bounced" name="bounced" value="2">
					<label class="checkbox-inline" for="deferred"><b>Deferred:</b> </label>
					<input <?php if(($_GET['deferred'] == '3') && ($_GET['mbox'] == false)){ echo "checked";}; ?> type="checkbox" id="deferred" name="deferred" value="3">
				</div>

				<br>
				<br>

				<div>
					<label for="numOFrows">Number Of Rows: </label>
					<input name="numOFrows" type="number" style="width: 70px;" value="<?php if(!empty($_GET['numOFrows'])) { echo $_GET['numOFrows']; } ?>">
				</div>

				<br>

				<div>
					<label for="order">Order Date: </label>
					<select name="order" size="1" class="form-inline">
						<option <?php if('1' == $_GET['order']) {echo 'selected="selected"';}?> value="1">DESC</option>
						<option <?php if('2' == $_GET['order']) {echo 'selected="selected"';}?> value="2">ASC</option>
					</select>
				</div>
			</div>

			<!-- search input -->
			<div class="container" style="width: 440px;">
				<select name="type" size="1" class="SEARCH_TYPE form-inline">
					<?php foreach($searchType as $type) { ?>
						<option <?php if($type == $_GET['type']) {echo 'selected="selected"';}?> value="<?php echo $type;?>"><?php echo $type;?></option>
					<?php } ?>
				</select>
				<input type="text" name="Search" placeholder="Search.." class="SEARCH_BOX form-inline" value="<?php if(!empty($_GET['Search'])) { echo $_GET['Search']; } ?>">
				<input type="submit" class="button form-inline">
			</div>
			<div class="pull-right form-inline" style="margin-right: 20px;">
				<input type="submit" name="export_log" class="btn-md btn-primary" value="Export to CSV">
			</div>
		</form>
	</div>

	<?php
		$exportLog = new SQLCSV();
		if($exportLog->download($data) == 'yes') { ?>
			<div class="pull-right form-inline" style="margin-right: 20px;">
				<button class="btn-md btn-success"><a href="uploads/maillog.csv" target="popup">Download CSV</a></button>
			</div>
<?php   } ?>

	<div class="EmailTables container" style="margin-top: 70px; width: 100%;">

	<?php
		$query = new SQLEmailLog();
	?>

<?php	if($_GET['mbox'] == false) { ?>
		<table class="table-bordered" id="emailTable">
			<thead>
				<tr>
					<th>Date</th>
					<th>From</th>
					<th>To</th>
					<th>Client</th>
					<th>Relay</th>
					<th>Status</th>
					<th>Details</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>Date</th>
					<th>From</th>
					<th>To</th>
					<th>Client</th>
					<th>Relay</th>
					<th>Status</th>
					<th>Details</th>
				</tr>
			</tfoot>
			<tbody>
	<?php
		$data['q_type'] = 'RL';
		$rows = $query->GET($data);
				foreach($rows as &$row) {
					echo
						'<tr>',
							'<td>',$row['date'],'</td>',
							'<td>',$row['from_addr'],'</a></td>',
							'<td>',$row['to_addr'],'</td>',
							'<td>',$row['client'],'</td>',
							'<td>',$row['relay'],'</td>',
							'<td>',$row['status'],'</td>',
							'<td><a href="details.php?mid='.$row['mess_id'].'&to_addr='.$row['to_addr'].'&status='.$row['status_b'].'&q_type=LD" target="_blank">Details</a></td>',
						'</tr>';
				}
	?>
			</tbody>
		</table>
<?php } ?>
</div>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
   	$(document).ready(function(){
   		$('#emailTable').DataTable({
   			"bFilter": false,
   			"iDisplayLength": 25,
   			"order": [0,'desc']
   		});
	});
</script>
</body>
</html>
<?php } ?>