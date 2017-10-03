<?php
class SQLSmtpInOut {

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

	private function QuerySmtpInOut() {

		$connect = $this->ConnectToDB();

		$result = $connect->query('select * from relays order by `id` asc');

		$rows = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($rows, $row);
			}
		}
		$result->close();
		$connect->close();
		return $rows;
	}

	private function AddInOut($data) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('insert into relays (`include_relay`,`exclude_relay`) values (?,?);');
		$result->bind_param('ss',$data['include_relay'],$data['exclude_relay']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function EditInOut($data) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('update relays set include_relay = ?, exclude_relay = ? where id = ?');
		$result->bind_param('ssi',$data['include_relay'],$data['exclude_relay'],$data['id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function DelInOut($data) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('delete from relays where id = ?');
		$result->bind_param('i',$data['delete_id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	function GET() {
		return $this->QuerySmtpInOut();
	}

	function PUT($data) {
		if($data['formtype'] == 'Add') {
			$this->AddInOut($data);
		}
		elseif($data['formtype'] == 'Edit') {
			$this->EditInOut($data);
		}
		elseif($data['formtype'] == 'delete') {
			$this->DelInOut($data);
		}
	}
}
?>