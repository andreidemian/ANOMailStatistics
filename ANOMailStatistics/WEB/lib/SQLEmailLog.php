<?php
class SQLEmailLog {

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

	private function RetrieveLog($search) {

		$conditions;
		$query = "select m_delivery.`date`, m_delivery.`mess_id`, m_from.`from_addr`, m_delivery.`to_addr`, m_client.`client`, m_delivery.`relay`, m_delivery.`status`, m_delivery.`status_b` from m_delivery
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
		$row;
		$all_rows = array();

		$conn = $this->ConnectToDB();
		$result = $conn->query($query) or die ("Search failed");

		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($all_rows, $row);
			}
		}

		return $all_rows;
		$result->free();
    	$conn->close();
	}

	private function RetrieveBounce($search) {

		$conditions;	

		$query = "select * from bounce_report where";

		if(!empty($search['Search'])) {

			if($search['type'] == 'TO') {
				$conditions .= " to_addr like '%".$search['Search']."%' AND";
			}
			elseif($search['type'] == 'FROM') {
				$conditions .= " from_addr like '%".$search['Search']."%' AND";
			}
			elseif($search['type'] == 'MTA') {
				$conditions .= " `remote-mta` like '%".$search['Search']."%' AND";
			}
			elseif($search['type'] == 'STATUS') {
				$conditions .= " `status` like '%".$search['Search']."%' AND";
			}
		}

		$date = explode(' - ',$search['daterange']);

		if($date[0] == $date[1]) {
			$conditions .= " DATE(`date`) = '".$date[0]."'";
		}
		else {
			$conditions .= " (DATE(`date`) BETWEEN '".$date[0]."' AND '".$date[1]."')";
		}

		if($search['order'] == '1') {
			$conditions .= " order by `date` desc";
		}
		elseif($search['order'] == '2') {
			$conditions .= " order by `date` asc";
		}

		if(!empty($search['numOFrows'])) {
			$conditions .= " limit ".$search['numOFrows']."";
		}

		$query .= $conditions;

		$row;
		$all_rows = array();
		$conn = $this->ConnectToDB();
		$result = $conn->query($query);
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($all_rows, $row);
			}
		}
		return $all_rows;
		$result->free();
    	$conn->close();
	}

	private function BounceDetails($data) {
		$query = "select * from bounce_report where id = ".$data['id']."";
		$conn = $this->ConnectToDB();
		$result = $conn->query($query);
		if($result->num_rows > 0) {
			return $result->fetch_assoc();
		}
		$result->free();
		$conn->close();
	}

	private function LogDetails($data) {

	$query = "select 

			m_client.`log_id` as `client_log_id`,
			m_client.`srv` as `client_host`,
			m_client.`inst` as `client_inst`,
			m_client.`proc` as `client_proc`,
			m_client.`mess_id` as `client_mess_id`,
			m_client.`client` as `client`,
			m_client.`sasl_method` as `client_sasl_method`,
			m_client.`sasl_username` as `client_sasl_username`,
			m_client.`date` as `client_date`,

			m_from.`log_id` as `from_log_id`,
			m_from.`srv` as `from_host`,
			m_from.`inst` as `from_inst`,
			m_from.`proc` as `from_proc`,
			m_from.`mess_id` as `from_mess_id`,
			m_from.`from_addr` as `from_addr`,
			m_from.`size` as `from_size`,
			m_from.`date` as `from_date`,

			m_delivery.`log_id` as `delivery_log_id`,
			m_delivery.`srv` as `delivery_host`,
			m_delivery.`inst` as `delivery_inst`,
			m_delivery.`proc` as `delivery_proc`,
			m_delivery.`mess_id` as `delivery_mess_id`,
			m_delivery.`to_addr` as `delivery_to_addr`,
			m_delivery.`orig_to` as `delivery_orig_to`,
			m_delivery.`relay` as `delivery_relay`,
			m_delivery.`delay` as `delivery_delay`,
			m_delivery.`delays` as `delivery_delays`,
			m_delivery.`dsn` as `delivery_dsn`,
			m_delivery.`status` as `delivery_status`,
			m_delivery.`details` as `delivery_details`,
			m_delivery.`date` as `delivery_date`
			  
			from m_delivery 
			left join m_from on m_delivery.mess_id=m_from.mess_id 
			left join m_client on m_delivery.mess_id=m_client.mess_id 
			where 
				m_delivery.`status_b` = ".$data['status']."
			AND
				m_delivery.`to_addr` = '".$data['to_addr']."'
			AND
				m_delivery.`mess_id` = '".$data['mid']."'
			group by m_delivery.id";

		$rows = array();
		$conn = $this->ConnectToDB();
			$result = $conn->query($query);
			if($result->num_rows > 0)  {
				$rows = $result->fetch_assoc();
			}
		$result->free();
    	$conn->close();
		return $rows;
	}

	function GET($data) {
		if($data['q_type'] == 'RL') {
			return $this->RetrieveLog($data);
		}
		elseif($data['q_type'] == 'RB') {
			return $this->RetrieveBounce($data);
		}
		elseif($data['q_type'] == 'LD') {
			return $this->LogDetails($data);
		}
		elseif($data['q_type'] == 'BD') {
			return $this->BounceDetails($data);
		}
	}
}
?>