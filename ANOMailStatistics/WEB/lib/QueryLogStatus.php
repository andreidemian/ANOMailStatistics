<?php
class QueryLogStatus {

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

	private function LogStatus() {
		$connect = $this->ConnectToDB();
		$result = $connect->query('select * from logvar');
		$rows = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($rows, $row);
			}
		}
		$result->close();
		return $rows;
	}

	function GET() {
		return $this->LogStatus();
	}
}
?>