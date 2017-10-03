<?php
class SQLDeliveryChart {

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

	private function GraphGen($data) {

		$conn = $this->ConnectToDB();

		$query = "select `date`, sum(`sent`) as `sent`, sum(`incoming`) as `incoming`, sum(`deferred`) as `deferred`, sum(`bounced`) as `bounced` from SentChart where";

		if(!empty($data['domain_id'])) {

			$query .= " `domain_id` in ( ";
			foreach($data['domain_id'] as $id) {
				$query .= $coma . $id;
				$coma = ',';
			}
			$query .= " ) AND";
		}

		if($data['StartDate'] == $data['EndDate']) {
			$query .= " `date` = '".$data['StartDate']."' group by `date` order by `date` asc";
		}
		else {
			$query .= " (`date` BETWEEN '".$data['StartDate']."' AND '".$data['EndDate']."') group by `date` order by `date` asc";
		}

		$rows = array();
		$result = $conn->query($query);
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($rows, $row);
			}
		}
	    $result->free();
	    $conn->close();
		return $rows;
	}

	private function domains() {
		$conn = $this->ConnectToDB();

		$domains = array();
		$DM = $conn->query('select * from domains;');
		if($DM->num_rows > 0) {
			while($domain = $DM->fetch_assoc()) {
				array_push($domains, $domain);
			}
		}
		return $domains;
	}

	function GET($type,$data) {
		if($type == 'graph') {
			return $this->GraphGen($data);
		}
		elseif($type == 'domains') {
			return $this->domains();
		}
	}
}
?>