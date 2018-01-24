<?php
class SQLMailBox {

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

	private function QueryMailBox() {

		$connect = $this->ConnectToDB();

		$result = $connect->query('select * from config_mbb order by `id` asc');

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

	private function AddMailBox($data) {
		$status = 'off';
		$connect = $this->ConnectToDB();

		if(empty($data['active'])) {
			$data['active'] = 0;
		}

		if(empty($data['ssl'])) {
			$data['ssl'] = 0;
		}

		$result = $connect->prepare('insert into config_mbb ( `status`,`host`,`port`,`ssl`,`account`,`password`,`iteration_num`,`del_older_rows`,`active` ) values ( ?,?,?,?,?,?,?,?,? );');
		$result->bind_param('ssiissiii',$status,$data['host'],$data['port'],$data['ssl'],$data['account'],$data['password'],$data['iteration_num'],$data['deldb'],$data['active']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function UpdateMailBox($data) {
		
		if(empty($data['active'])) {
			$data['active'] = 0;
		}

		if(empty($data['ssl'])) {
			$data['ssl'] = 0;
		}

		$connect = $this->ConnectToDB();
		$result = $connect->prepare('update config_mbb set `host` = ?, `port` = ?, `ssl` = ?, `account` = ?, `password` = ?, `iteration_num` = ?, `del_older_rows` = ?, `active` = ? where `id` = ?');		
		$result->bind_param('siissiiii',$data['host'],$data['port'],$data['ssl'],$data['account'],$data['password'],$data['iteration_num'],$data['deldb'],$data['active'],$data['id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function DeleteMailBox($data) {
		$connect = $this->ConnectToDB();

		$result = $connect->prepare('delete from config_mbb where id = ?');
		$result->bind_param('i',$data['delete_id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	function GET() {
		return $this->QueryMailBox();
	}

	function PUT($data) {
		if($data['formtype'] == 'Add') {
			$this->AddMailBox($data);
		}
		elseif ($data['formtype'] == 'Edit') {
			$this->UpdateMailBox($data);
		}
		elseif ($data['formtype'] == 'delete') {
			$this->DeleteMailBox($data);
		}
	}
}
?>