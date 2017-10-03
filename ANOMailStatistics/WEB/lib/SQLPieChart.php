<?php
class SQLPieChart {

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

	private function BouncedChart($data) {

		$conn = $this->ConnectToDB();

		$query = "select `b_code`, sum(`b_count`) as `b_count` from BouncePieChart where";

		if(!empty($data['domain_id'])) {

			$query .= ' `domain_id` in ( ';
			foreach($data['domain_id'] as $id) {
				$query .= $coma . $id;
				$coma = ',';
			}
			$query .= ' ) AND';
		}

		if($data['StartDate'] == $data['EndDate']) {
			$query .= " date = '".$data['StartDate']."' group by `b_code` order by `b_count` desc";
		}
		else {
			$query .= " (date BETWEEN '".$data['StartDate']."' AND '".$data['EndDate']."') group by `b_code` order by `b_count` desc";
		}

		//print_r($query);

		$rows = array( array( 'b_code'=>'No Data', 'b_count'=>0 ) );
		$result = $conn->query($query);
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if(!empty($row['b_count'])) {
					array_push($rows, $row);
				}
			}
		}
		$result->free();
    	$conn->close();

		return $rows;
	}

	private function BounceCode($code) {

		$conn = $this->ConnectToDB();

		if($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$query = "select * from bounce_codes where b_code = '".$code."'";

		$row = array();
		$result = $conn->query($query);
		if($result->num_rows > 0) {
			$row = $result->fetch_assoc();
		}
	    $result->free();
	    $conn->close();
	    return $row;
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
		if($type == 'chart') {
			return $this->BouncedChart($data);
		}
		elseif($type == 'bcode') {
			return $this->BounceCode($data);
		}
		elseif($type == 'domains') {
			return $this->domains();
		}
	}
}
?>