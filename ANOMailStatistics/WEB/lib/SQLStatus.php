<?php
class SQLStatus {
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

	private function MlogStatus() {
		$conn = $this->ConnectToDB();

		$config_log = $conn->query('select * from config_log');

		$mlog_status = array();
		if($config_log->num_rows > 0) {
			while($row = $config_log->fetch_assoc()) {
				array_push($mlog_status, $row);
			}
		}
		return $mlog_status;
	}

	private function MboxStatus() {
		$conn = $this->ConnectToDB();

		$config_mbb = $conn->query('select * from config_mbb');

		$mbb_status = array();
		if($config_mbb->num_rows > 0) {
			while($row = $config_mbb->fetch_assoc()) {
				array_push($mbb_status, $row);
			}
		}
		return $mbb_status;
	}



	public function GET($data) {
		if($data == 'mlog') {
			return $this->MlogStatus();
		}
		elseif($data == 'mbox') {
			return $this->MboxStatus();
		}
	}
}
?>