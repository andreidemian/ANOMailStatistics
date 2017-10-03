<?php
class SQLCSV {

	function __construct() {
	}

	private function ConnectToDB() {
		$XML = file_get_contents(realpath(__DIR__ . '/../../DB/db.xml'));
		$conf = simplexml_load_string($XML);
		$connect = new mysqli($conf->host, $conf->user, $conf->password, $conf->db, intval($conf->port));
		if($connect->connect_error) {
    		die("Connection failed: " . $connect->connect_error);
		}
		return $connect;
	}

	private function ExportBounce($data) {

		$conditions;

		$query = "select * from bounce_report where";

		if(!empty($data['Search'])) {

			if($data['type'] == 'TO') {
				$conditions .= " to_addr like '%".$data['Search']."%' AND";
			}
			elseif($data['type'] == 'FROM') {
				$conditions .= " from_addr like '%".$data['Search']."%' AND";
			}
			elseif($data['type'] == 'MTA') {
				$conditions .= " `remote-mta` like '%".$data['Search']."%' AND";
			}
			elseif($data['type'] == 'STATUS') {
				$conditions .= " `status` like '%".$data['Search']."%' AND";
			}
		}

		$date = explode(' - ',$data['daterange']);

		if($date[0] == $date[1]) {
			$conditions .= " DATE(`date`) = '".$date[0]."'";
		}
		else {
			$conditions .= " (DATE(`date`) BETWEEN '".$date[0]."' AND '".$date[1]."')";
		}

		if($data['order'] == '1') {
			$conditions .= " order by `date` desc";
		}
		elseif($data['order'] == '2') {
			$conditions .= " order by `date` asc";
		}

		if(!empty($data['numOFrows'])) {
			$conditions .= " limit ".$data['numOFrows']."";
		}

		$query .= $conditions;

		$output = '';
		
		$conn = $this->ConnectToDB();

		$result = $conn->query($query);
		if($result->num_rows > 0) {
			$output .= "sep=|";
			$output .= "\n";
			$output .= '"Date"|"Message ID"|"Reporting MTA"|"From"|"To"|"Orig TO"|"Action"|"Status"|"Remote MTA"|"Diagnostic Code"';
			$output .= "\n";
			while($row = $result->fetch_assoc()) {
				$output .= '"'.$row['date'].'"|"'.$row['mess_id'].'"|"'.$row['reporting-mta'].'"|"'.$row['from_addr'].'"|"'.$row['to_addr'].'"|"'.$row['orig_to'].'"|"'.$row['action'].'"|"'.$row['status'].'"|"'.$row['remote-mta'].'"|"'.$row['diagnostic-code'].'"';
				$output .= "\n";
			}
		}
		$result->free();
    	$conn->close();
    	return $output;
	}

	private function ExportLog($search) {
		
		$conditions;
		$query = "select

					m_delivery.`srv` as `Host`,
					m_delivery.`log_id` as `Log_ID`,
					m_delivery.`mess_id` as `Queue_MessageID`,

					m_client.`inst` as `Connect_Instance`,
					m_client.`proc` as `Connect_Process`,
					m_client.`date` as `Connect_Date`,

					m_from.`inst` as `Queue_Instance`,
					m_from.`proc` as `Queue_Process`,
					m_from.`date` as `Queue_Date`,

					m_delivery.`inst` as `Delivery_Instance`,
					m_delivery.`proc` as `Delivery_Process`,
					m_delivery.`date` as `Delivery-Date`,
					
					m_from.`size` as `Mail_Size`,
					
					m_client.`sasl_method` as `sasl_method`,
					m_client.`sasl_username` as `sasl_username`,

					m_from.`from_addr` as `FROM`,
					m_delivery.`to_addr` as `TO`,
					m_delivery.`orig_to` as `Orig_TO`,
					
					m_client.`client` as `Connecting_Server_or_Host`,
					m_delivery.`relay` as `Relay`,

					m_delivery.`delay` as `Delay`,
					m_delivery.`delays` as `Delays`,

					m_delivery.`dsn` as `dsn`,
					m_delivery.`status` as `Status`,
					m_delivery.`details` as `Details`

					from m_delivery
					left join m_from on m_delivery.mess_id=m_from.mess_id
					left join m_client on m_delivery.mess_id=m_client.mess_id
					where";

		if(!empty($search['sent'])) {

			if(!empty($search['deferred'])) {
				$conditions .= " (m_delivery.status_b = '".$search['deferred']."' or m_delivery.status_b = '".$search['sent']."') AND";
			}
			elseif(!empty($search['bounced'])) {
				$conditions .= " (m_delivery.status_b = '".$search['bounced']."' or m_delivery.status_b = '".$search['sent']."') AND";
			}
			elseif((empty($search['bounced'])) && (empty($search['deferred']))) {
				$conditions .= " m_delivery.status_b = '".$search['sent']."' AND";
			}
		}
		elseif(!empty($search['deferred'])) {
			
			if(!empty($search['sent'])) {
				$conditions = " (m_delivery.status_b = '".$search['sent']."' or m_delivery.status_b = '".$search['deferred']."') AND";
			}
			elseif(!empty($search['bounced'])) {
				$conditions .= " (m_delivery.status_b = '".$search['bounced']."' or m_delivery.status_b = '".$search['deferred']."') AND";
			}
			elseif((empty($search['bounced'])) && (empty($search['sent']))) {
				$conditions .= " m_delivery.status_b = '".$search['deferred']."' AND";
			}
		}
		elseif(!empty($search['bounced'])) {

			if(!empty($search['sent'])) {
				$conditions = " (m_delivery.status_b = '".$search['sent']."' or m_delivery.status_b = '".$search['bounced']."') AND";
			}
			elseif(!empty($search['deferred'])) {
				$conditions .= " (m_delivery.status_b = '".$search['deferred']."' or m_delivery.status_b = '".$search['bounced']."') AND";
			}
			elseif((empty($search['deferred'])) && (empty($search['sent']))) {
				$conditions .= " m_delivery.status_b = '".$search['bounced']."' AND";
			}
		}
		elseif((empty($search['deferred'])) && (empty($search['bounced'])) && (empty($search['sent']))) {
			$conditions .= " (m_delivery.status_b = 1) AND";
		}

		if(!empty($search['Search'])) {

			if($search['type'] == 'TO') {
				$conditions .= " m_delivery.to_addr like '%".$search['Search']."%' AND";
			}
			elseif($search['type'] == 'FROM') {
				$conditions .= " m_from.from_addr like '%".$search['Search']."%' AND";
			}
			elseif($search['type'] == 'CLIENT') {
				$conditions .= " m_client.client like '%".$search['Search']."%' AND";
			}
			elseif($search['type'] == 'RELAY') {
				$conditions .= " m_delivery.relay like '%".$search['Search']."%' AND";
			}
		}

		$date = explode(' - ', $search['daterange']);

		if($date[0] == $date[1]) {
			$conditions .= " DATE(m_delivery.date) = '".$date[0]."' group by m_delivery.id";
		}
		else {
			$conditions .= " (DATE(m_delivery.date) BETWEEN '".$date[0]."' AND '".$date[1]."') group by m_delivery.id";
		}

		if($search['order'] == '1') {
			$conditions .= " order by m_delivery.`date` desc";
		}
		elseif($search['order'] == '2') {
			$conditions .= " order by m_delivery.`date` asc";
		}

		if(!empty($search['numOFrows'])) {
			$conditions .= " limit ".$search['numOFrows']."";
		}

		$query .= $conditions;

		$conn = $this->ConnectToDB();
		$result = $conn->query($query) or die ("Search failed");
		$output = "sep=|\n";
		if($result->num_rows > 0) {
			$head = $result->fetch_assoc();
			$sep = "";
			foreach($head as $key => $value) {
				$output .= $sep . '"' . $key . '"';
				$sep = "|";
			}
			$output .= "\n";

			$result->data_seek(0);
			while($row = $result->fetch_assoc()) {
				$b_sep = "";
				foreach ($row as $key => $value) {
					$output .= $b_sep . '"' . $value . '"';
					$b_sep = "|";
				}
				$output .= "\n";
			}
		}
		$result->free();
    	$conn->close();
    	return $output;
	}

	private function CSVFile($file,$data) {
		$fp = fopen($file,"w");
		fputs($fp, $data);
		fclose($fp);
		return 'yes';
	}

	public function download($data) {
		if(!empty($data['export_bounces'])) {
			return $this->CSVFile('uploads/bounces.csv',$this->ExportBounce($data));
		}
		elseif(!empty($data['export_log'])) {
			return $this->CSVFile('uploads/maillog.csv',$this->ExportLog($data));
		}
	}
}
?>