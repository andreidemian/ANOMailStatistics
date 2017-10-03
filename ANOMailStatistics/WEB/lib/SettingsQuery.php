
<?php
class SQLMailLog {

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

	private function TableQuery() {

		$connect = $this->ConnectToDB();
		
		$result = $connect->query('select * from config_log order by `id` asc;');
		
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

	private function AddLog($data) {
		$logtype;
		if($data['logtype1']) {
			$logtype = $data['logtype1'];
		}
		elseif ($data['logtype2']) {
			$logtype = $data['logtype2'];
		}

		$time = explode(':',$data['time']);

		if(empty($data['active'])) {
			$data['active'] = 0;
		}

		if(empty($data['logrotate'])) {
			$data['logrotate'] = 0;
		}

		$connect = $this->ConnectToDB();
		$result = $connect->prepare('insert into config_log ( `logrotate`,`log`,`logtype`,`iteration_num`,`R_H`,`R_M`,`R_W`,`del_older_rows`,`del_older_logs`,`active` ) values ( ?,?,?,?,?,?,?,?,?,? );');
		$result->bind_param('isiiiiiiii',$data['logrotate'],$data['log'],$logtype,$data['ITN'],$time[0],$time[1],$data['week'],$data['deldb'],$data['dellog'],$data['active']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function UpdateLog($data) {
		$logtype;
		if($data['logtype1']) {
			$logtype = $data['logtype1'];
		}
		elseif ($data['logtype2']) {
			$logtype = $data['logtype2'];
		}

		$time = explode(':',$data['time']);

		if(empty($data['active'])) {
			$data['active'] = 0;
		}

		if(empty($data['logrotate'])) {
			$data['logrotate'] = 0;
		}

		$connect = $this->ConnectToDB();
		$result = $connect->prepare('update config_log set logrotate = ?, log = ?, logtype = ?, iteration_num = ?, R_H = ?, R_M = ?, R_W = ?, del_older_rows = ?, del_older_logs = ?, active = ? where id = ?');
		$result->bind_param('isiiiiiiiii',$data['logrotate'],$data['log'],$logtype,$data['ITN'],$time[0],$time[1],$data['week'],$data['deldb'],$data['dellog'],$data['active'],$data['id']);
		$result->execute();
		$result->close();
		$connect->close();
	}

	private function DeleteLog($id) {
		$connect = $this->ConnectToDB();
		$result = $connect->prepare('delete from config_log where id = ?');
		$result->bind_param('i',$id);
		$result->execute();
		$result->close();
		$connect->close();
	}

	function PUT($data) {
		if($data['formtype'] == 'Add') {
			$this->AddLog($data);
		}
		elseif ($data['formtype'] == 'Edit') {
			$this->UpdateLog($data);
		}
		elseif($data['formtype'] == 'delete') {
			$this->DeleteLog($data['delete_id']);
		}
	}

	function GET() {
		return $this->TableQuery();
	}
}
?>