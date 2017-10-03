<?php
class SQLDomains {

	public function __construct() {
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

	private function QueryDomains() {
		$conn = $this->ConnectToDB();
		
		$DM = $conn->query('select * from domains');
		
		$domains = array();
		if($DM->num_rows > 0) {
			while($domain = $DM->fetch_assoc()) {
				array_push($domains, $domain);
			}
		}
		return $domains;
		$DM->close();
		$conn->close();
	}

	private function InsertDomains($data) {
		$conn = $this->ConnectToDB();
		$DM = $conn->prepare('insert into domains ( `domain` ) values ( ? );');
		$DM->bind_param('s',$data['domain']);
		$DM->execute();
		$DM->close();
		$conn->close();
	}

	private function UpdateDomains($data) {
		$conn = $this->ConnectToDB();
		$DM = $conn->prepare('update domains set `domain` = ? where `id` = ?;');
		$DM->bind_param('si',$data['domain'],$data['id']);
		$DM->execute();
		$DM->close();
		$conn->close();
	}

	private function DeleteDomains($data) {
		$conn = $this->ConnectToDB();
		$DM = $conn->prepare('delete from domains where `id` = ?;');
		$DM->bind_param('i',$data['delete_id']);
		$DM->execute();
		$DM->close();
		$conn->close();
	}

	public function GET() {
		return $this->QueryDomains();
	}

	public function PUT($data) {
		if($data['formtype'] == 'Add') {
			$this->InsertDomains($data);
		}
		elseif($data['formtype'] == 'Edit') {
			$this->UpdateDomains($data);
		}
		elseif($data['formtype'] == 'delete') {
			$this->DeleteDomains($data);
		}
	}
}
?>