<?php function PieChart() { ?>
<!DOCTYPE html>
<html>
<head>

<?php
  $date = explode(' - ',$_GET['daterange']);
  $param['domain_id'] = $_GET['domains'];
  $param['StartDate'] = $date[0];
  $param['EndDate'] = $date[1];
?>

<script type="text/javascript">
  $(document).ready(function() {
    var $eventSelect=$(".PieChartSelect").select2({
        placeholder: "Select Sending Domain \"example.com\""
    });
    $eventSelect.on("select2:select", function() {
       //alert("select2:unselecting");
       $(this).parents('form').submit();
    });
    $eventSelect.on("select2:unselect", function() {
       //alert("select2:unselecting");
       $(this).parents('form').submit();
    });
  });
</script>

<script type="text/javascript">
  $(function() {
    $('input[name="daterange"]').daterangepicker({
      locale: {
        format: 'YYYY-MM-DD'
      },
      startDate: <?php if(!empty($_GET['daterange'])) { echo "'".$date[0]."'"; } else { echo date("Y-m-d"); } ?>,
      endDate: <?php if(!empty($_GET['daterange'])) { echo "'".$date[1]."'"; } else { echo date("Y-m-d"); } ?>
    });
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
      $('#daterange').val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      $('#form1').submit();
    });
  });
</script>

  <?php
    include('lib/SQLPieChart.php');

    $query = new SQLPieChart();
    $data = $query->GET('chart',$param);
  ?>

  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['BounceCode', 'No'],
          <?php
            foreach($data as $i) {
              echo "['".$i['b_code']."',".$i['b_count']."],";
            }
         ?>
        ]);

        var options = {
          title: "Bounces: <?php if($date[0] == $date[1]) { echo $date[0]; } else { echo $date[0],' -> ',$date[1]; } ?>",
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>

</head>
<body>

  <div>
  <form id="form1" class="form-inline" action="index.php">
    <div class="pull-right" style="margin-right: 10px">
      <input type="hidden" name="page" value="bounced">
  	  <div class="input-group">
     	  <input id="daterange" type="txt" name="daterange" class="form-control" style="width: 190px;" />
      
		  <div class="input-group-btn">
        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-calendar"></i></button>
      </div>
      </div>
    </div>
     <div class="form-inline" style="margin-left: 10px">
      <select class="PieChartSelect" multiple="multiple" style="width: 30%" name="domains[]">
        <?php
        foreach($query->GET('domains') as $domain) { ?>
         <option <?php foreach($_GET['domains'] as $id ) { if($domain['id'] == $id) { echo 'selected="selected"'; } } ?> value="<?php echo $domain['id']; ?>"><?php echo $domain['domain']; ?></option>
        <?php } ?>
      </select>
    </div>
  </form>
  </div>


<div style="margin-top: 100px;">
<div id="piechart"></div>

<div id="BounceTable">
<table style="width: 570px;">
	<tr>
		<th>Bounce Code</th><th>Bounce Type</th><th>Bounce Details</th>
	</tr>
<?php

	foreach($data as $d) {
		$codes = $query->GET('bcode',$d['b_code']);
         echo
         	'<tr>',
                 '<td>'.$codes['b_code'].'</td>',
                 '<td>'.$codes['b_type'].'</td>',
                 '<td>'.$codes['message'].'</td>',
            '</tr>';
    }
?>
</table>
</div>
</div>
</body>
<?php } ?>