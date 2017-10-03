<?php function MailBoxConf() { ?>
<!DOCTYPE html>
<html>
<head>
<?php
	include('lib/SQLMailBox.php');
	$query = new SQLMailBox();
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
	          <form action="index.php?page=mbox" id="settings<?php echo $id; ?>" method="POST">
	          	<input type="hidden" name="formtype" value="<?php echo $name; ?>">
	          	<input type="hidden" name="id" value="<?php echo $conf['id']; ?>">
	          	<br>
	          	<br>
	          	<div class="form-group">
	          		<label for="host">Host:</label>
	          		<br>
	          		<input type="text" name="host" class="form-control" value="<?php echo $conf['host'] ?>" placeholder="localhost">
	          	</div>
	          	<br>
	          	<br>
	            <div class="form-group">
	            	<label for="port">Port:</label>
	            	<br>
	            	<input type="number" name="port" class="form-control" value="<?php echo $conf['port'] ?>" placeholder="110">
	            </div>
	            <br>
	            <br>
	            <div class="form-group">
	          		<label for="ssl">SSL:</label>
	          		<input type="checkbox" <?php if($conf['ssl'] == 1) { echo 'checked'; } ?> name="ssl" value="1">
	          	</div>
	          	<br>
	          	<br>
	          	<div class="form-group">
	          		<label for="iteration_num">Number Of Iteration:</label>
	          		<br>
	          		<input type="number" class="form-control" name="iteration_num" value="<?php echo $conf['iteration_num'] ?>" placeholder="500">
	          	</div>
	          	<br>
	          	<br>
	            <div class="form-group">
	            	<label for="account">Account:</label>
	            	<br>
	            	<input type="text" name="account" class="form-control" value="<?php echo $conf['account']; ?>" placeholder="user@example.com">
	            </div>
	            <br>
	            <br>
	            <div class="form-group">
	            	<label for="password">Password:</label>
	            	<br>
	            	<input type="text" name="password" class="form-control" value="<?php echo $conf['password']; ?>" placeholder="POP_password">
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
<form id="delete-<?php echo $id; ?>" action="index.php?page=mbox" method="POST">
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
			<th>Host</th>
			<th>Port</th>
			<th>SSL</th>
			<th>Account</th>
			<th>Password</th>
			<th>Number of Iteration</th>
			<th>Del from DB records older then days</th>
			<th>Active</th>
			<th>Action</th>
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
			<th>Action</th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ($query->GET() as $row) {
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