<?php
class SQLBounceCodes {

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

	private function QueryBounceCodes() {
		$connect = $this->ConnectToDB();
		$result = $connect->query('select * from bounce_codes order by `id` asc');
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

	private function AddBounceCodes($data) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('insert into bounce_codes (`b_code`,`b_type`,`message`) values (?,?,?)');
		$result->bind_param('sss',$data['b_code'],$data['b_type'],$data['message']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function EditBounceCodes($data) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('update bounce_codes set `b_code` = ?,`b_type` = ?,`message` = ? where `id` = ?');
		$result->bind_param('sssi',$data['b_code'],$data['b_type'],$data['message'],$data['id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function DelBounceCodes($data) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('delete from bounce_codes where id = ?');
		$result->bind_param('i',$data['delete_id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	function GET() {
		return $this->QueryBounceCodes();
	}

	function PUT($data) {
		if($data['formtype'] == 'Add') {
			$this->AddBounceCodes($data);
		}
		elseif($data['formtype'] == 'Edit') {
			$this->EditBounceCodes($data);
		}
		elseif($data['formtype'] == 'delete') {
			$this->DelBounceCodes($data);
		}
	}
}
?>