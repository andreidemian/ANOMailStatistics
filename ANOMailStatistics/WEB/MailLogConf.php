<?php function MailLogConf() { ?>
<!DOCTYPE html>
<html>
<head>
<?php
	include('lib/SQLMailLog.php');
	$query = new SQLMailLog();
	$query->PUT($_POST);
?>
</head>
<body>

<?php function popup($name,$id,$btn,$conf) { ?>
<div>
  <!-- Trigger the modal with a button -->
  <button type="button" class="btn <?php echo $btn ?>" data-toggle="modal" data-target="#<?php echo $id ?>"><?php echo $name ?></button>
  <!-- Modal -->
  <div class="modal fade" id="<?php echo $id ?>" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo $name ?></h4>
        </div>
	        <div class="modal-body">
	          <form action="index.php?page=mlog" id="settings<?php echo $id; ?>" method="POST">
	          	<input type="hidden" name="formtype" value="<?php echo $name; ?>">
	          	<input type="hidden" name="id" value="<?php echo $conf['id']; ?>">
	          	<div class="form-group">
	          		<label class="checkbox-inline" for="logtype1"><b>Default Log Format:</b></label>
	          		<input type="checkbox" <?php if($conf['logtype'] == 1) { echo 'checked'; } ?> name="logtype1" value="1">
	          		<label class="checkbox-inline" for="logtype2"><b>ANO Log Format:</b></label>
	          		<input type="checkbox" <?php if($conf['logtype'] != 1) { echo 'checked'; } ?> name="logtype2" value="2">
	          	</div>
	          	<br>
	          	<br>
	          	<div class="form-group">
	          		<label for="log">Log Path:</label>
	          		<br>
	          		<input type="text" name="log" id="log" class="form-control" value="<?php echo $conf['log'] ?>" placeholder="/var/log/maillog">
	          	</div>
	          	<br>
	          	<br>
	          	<div class="form-group">
	          		<label for="log_des">Log Description:</label>
	          		<br>
	          		<input type="text" name="log_des" id="log_des" class="form-control" value="<?php echo $conf['log_description'] ?>" placeholder="Log Description">
	          	</div>
	          	<br>
	          	<br>
	            <div class="form-group">
	            	<label for="ITN">Number Of Iteration:</label>
	            	<br>
	            	<input type="number" name="ITN" id="ITN" class="form-control" value="<?php echo $conf['iteration_num'] ?>" placeholder="5000">
	            </div>
	            <br>
	            <br>
	            <div class="form-group">
	          		<label for="logrotate">Log Rotate:</label>
	          		<input type="checkbox" <?php if($conf['logrotate'] == 1) { echo 'checked'; } ?> name="logrotate" value="1">
	          	</div>
	          	<br>
	          	<br>
	            <div class="form-group">
	            	<label for="time">Daily Rotate Time</label>
	            	<br>
	            	<input type="text" name="time" class="form-control" value="<?php  if( (!empty($conf['R_H'])) || (!empty($conf['R_M']))) { echo $conf['R_H'].':'.$conf['R_M']; } ?>" placeholder="10:20">
	            </div>
	            <br>
	            <br>
	            <div class="form-group">
	            	<label for="week">Weekly Rotate Day</label>
	            	<br>
	            	<input type="number" name="week" min="0" max="7" class="form-control" value="<?php echo $conf['R_W']; ?>" placeholder="0 -> 7">
	            </div>
	            <br>
	            <br>
	            <div class="form-group">
	            	<label for="deldb">Del from DB records older then days</label>
	            	<br>
	            	<input type="number" name="deldb" class="form-control" value="<?php echo $conf['del_older_rows']; ?>" placeholder="60">
	            </div>
	            <br>
	            <br>
	            <div class="form-group">
	            	<label for="dellog">Del Logs older then days</label>
	            	<br>
	            	<input type="number" name="dellog" class="form-control" value="<?php echo $conf['del_older_logs']; ?>" placeholder="30">
	            </div>
	            <br>
	            <br>
	          	<div class="form-group">
	          		<label for="active">Active:</label>
	          		<input type="checkbox" <?php if($conf['active'] == 1) { echo 'checked'; } ?> name="active" value="1">
	          	</div>
	          	<br>
	          	<br>
          	  </form>
          	</div>
          <div class="modal-footer">
          	<button type="submit" form="settings<?php echo $id; ?>" class="btn btn-default">Save</button>
          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php function delete($id) { ?>
<form id="delete-<?php echo $id; ?>" action="index.php?page=mlog" method="POST">
	<input type="hidden" name="formtype" value="delete">
	<input type="hidden" name="delete_id" value="<?php echo $id; ?>">
</form>
	<button type="submit" form="delete-<?php echo $id; ?>" class="btn btn-danger btn-sm">Delete</button>
<?php } ?>

<div class="pull-right" style="margin-right: 2.5%;">
	<?php echo popup('Add','new','btn-primary btn-lg'); ?>
</div>

<div class="EmailTables container" style="margin-top: 70px; width: 100%;">

<table class="table-bordered" id="emailTable">
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
			<th>Action</th>
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
			<th>Action</th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ($query->GET() as $row) {
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
						'<td>','<div class="btn-group">',popup('Edit',$row['id'],'btn-warning btn-sm',$row),'</div>','<div class="btn-group">',delete($row['id']),'</div>','</td>',
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
   			"iDisplayLength": 25
   		});
	});
</script>
</body>
</html>
<?php } ?>