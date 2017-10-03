<?php function SentChart() { ?>
<!DOCTYPE html>
<html>
<head>
<?php
  include('lib/SQLDeliveryChart.php');

  $date = explode(' - ',$_GET['daterange']);
  $param['StartDate'] = $date[0];
  $param['EndDate'] = $date[1];
  $param['domain_id'] = $_GET['domains'];

  $query = new SQLDeliveryChart();
  $data = $query->GET('graph',$param);
?>

<script type="text/javascript">
  $(document).ready(function() {
    var $eventSelect=$(".SentDomainsSelect").select2({
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
        startDate: <?php if(!empty($_GET['daterange'])) { echo "'".$date[0]."'"; } else { echo "moment().subtract(6,'d')"; } ?>,
        endDate: <?php if(!empty($_GET['daterange'])) { echo "'".$date[1]."'"; } else { echo "moment()"; } ?>
      });
  
      $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $('#daterange').val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        $('#dateform').submit();
      });
    });
</script>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

        function drawVisualization() {
          // Some raw data (not necessarily accurate)
          var data = google.visualization.arrayToDataTable([
              ['Day', 'Sent'],
              <?php
                foreach($data as $i) {
                  echo "['".$i['date']."',".$i['sent']."],";
                }
              ?>
            ]);

          var options = {
            title : 'Sent Emails:',
            hAxis: {title: 'Daily Sent Emails'},
            seriesType: 'bars',
            series: {5: {type: 'line'}},
            colors:['green']
          };

          var chart = new google.visualization.ComboChart(document.getElementById('chart_sent'));
          chart.draw(data, options);
        }
</script>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

        function drawVisualization() {
          // Some raw data (not necessarily accurate)
          var data = google.visualization.arrayToDataTable([
              ['Day','Incoming'],
              <?php
                foreach($data as $i) {
                  echo "['".$i['date']."',".$i['incoming']."],";
                }
              ?>
            ]);

          var options = {
            title : 'Incoming Emails:',
            hAxis: {title: 'Daily Incoming Emails'},
            seriesType: 'bars',
            series: {5: {type: 'line'}},
            colors:['blue']
          };

          var chart = new google.visualization.ComboChart(document.getElementById('chart_incoming'));
          chart.draw(data, options);
        }	
</script>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

        function drawVisualization() {
          // Some raw data (not necessarily accurate)
          var data = google.visualization.arrayToDataTable([
              ['Day','Deferred'],
              <?php
                foreach($data as $i) {
                  echo "['".$i['date']."',".$i['deferred']."],";
                }
              ?>
            ]);

          var options = {
            title : 'Deferred Emails:',
            hAxis: {title: 'Daily Deferred Emails'},
            seriesType: 'bars',
            series: {5: {type: 'line'}},
            colors:['orange']
          };

          var chart = new google.visualization.ComboChart(document.getElementById('chart_deferred'));
          chart.draw(data, options);
        }	
</script>

<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

        function drawVisualization() {
          // Some raw data (not necessarily accurate)
          var data = google.visualization.arrayToDataTable([
              ['Day','Bounced'],
              <?php
                foreach($data as $i) {
                  echo "['".$i['date']."',".$i['bounced']."],";
                }
              ?>
            ]);

          var options = {
            title : 'Bounced Emails:',
            hAxis: {title: 'Daily Bounced Emails'},
            seriesType: 'bars',
            series: {5: {type: 'line'}},
            colors:['red']
          };

          var chart = new google.visualization.ComboChart(document.getElementById('chart_bounced'));
          chart.draw(data, options);
        }	
</script>

</head>
<body>

<div>
  <form id="dateform" class="form-inline" action="index.php" method="GET">
	  <div class="pull-right form-inline" style="margin-right: 10px">
	      	<input type="hidden" name="page" value="stats">
	    	<div class="input-group">
	      		<input id="daterange" type="txt" name="daterange" class="form-control" style="width: 190px;" />
		      	<div class="input-group-btn">
		          <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-calendar"></i></button>
		      	</div>
	    	</div>
	   </div>
     <div class="form-inline" style="margin-left: 10px">
      <select class="SentDomainsSelect" multiple="multiple" style="width: 30%" name="domains[]">
        <?php 
        foreach ($query->GET('domains') as $domain) { ?>
          <option <?php foreach($_GET['domains'] as $id ) { if($domain['id'] == $id) { echo 'selected="selected"'; } } ?> value="<?php echo $domain['id']; ?>"><?php echo $domain['domain']; ?></option>
        <?php } ?>
      </select>
    </div>
  </form>
</div>

<div id="chart_sent" style="width: 40%; height: 300px; margin: auto; margin-top: 100px; display: inline-block;"></div>
<div id="chart_incoming" style="width: 40%; height: 300px; margin: auto; margin-top: 100px; display: inline-block;"></div>
<div id="chart_deferred" style="width: 40%; height: 300px; margin: auto; margin-top: 100px; display: inline-block;"></div>
<div id="chart_bounced" style="width: 40%; height: 300px; margin: auto; margin-top: 100px; display: inline-block;"></div>

</body>
<?php } ?>