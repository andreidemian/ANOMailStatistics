<?php function SmtpInOutConf() { ?>
<!DOCTYPE html>
<html>
<head>
<?php
	include('lib/SQLSmtpInOut.php');
	$query = new SQLSmtpInOut();
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
	          <form action="index.php?page=inout" id="settings<?php echo $id; ?>" method="POST">
	          	<input type="hidden" name="formtype" value="<?php echo $name; ?>">
	          	<input type="hidden" name="id" value="<?php echo $conf['id']; ?>">
	          	<br>
	          	<br>
	          	<div class="form-group">
	          		<label for="include_relay">Include Relay:</label>
	          		<br>
	          		<input type="text" name="include_relay" class="form-control" value="<?php echo $conf['include_relay'] ?>" placeholder="Include in Relay for charts">
	          	</div>
	          	<br>
	          	<br>
	            <div class="form-group">
	            	<label for="exclude_relay">Exclude Relay:</label>
	            	<br>
	            	<input type="text" name="exclude_relay" class="form-control" value="<?php echo $conf['exclude_relay'] ?>" placeholder="Exclude in Relay for charts">
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
<form id="delete-<?php echo $id; ?>" action="index.php?page=inout" method="POST">
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
			<th>Include relay to Receive Graph</th>
			<th>Exclude relay from Sent Graph</th>
			<th>Action</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Include relay to Receive Graph</th>
			<th>Exclude relay from Sent Graph</th>
			<th>Action</th>
		</tr>
	</tfoot>
	<tbody>
		<?php
			foreach ($query->GET() as $row) {
				echo 
					'<tr>',
						'<td>',$row['include_relay'],'</td>',
						'<td>',$row['exclude_relay'],'</td>',
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