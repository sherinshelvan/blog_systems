<?php 
class DB{
	private $db;
	function __construct(){
		$this->init_db();
	}
	public function init_db(){
		$servername = "localhost";
		$username   = "root";
		$password   = "root";
		$db_name    = "blog_systems";

		// Create connection
		$this->db = new mysqli($servername, $username, $password, $db_name);
		// Check connection
		if ($this->db->connect_error) {
		    die("Connection failed: " . $this->db->connect_error);
		}
	}
	public function query($sql = ""){
		$result = $this->db->query($sql);
		$result = $result->fetch_all(MYSQLI_ASSOC);
		return $result;
	}
	public function delete_data($table_name, $where=""){
		// sql to delete a record
		$response = [
			"status" => true,
			"message" => "Record deleted successfully"
		];
		$sql = "DELETE FROM {$table_name} WHERE {$where}";
		if ($this->db->query($sql) === FALSE) {
			$response = [
				"status" => false,
				"message" =>  "Error deleting record: " . $this->db->error
			];
		} 
		return $response;
	}
	public function update_data($table_name, $update_data = "", $where = ""){
		$status = false;
		$message = "";
		if(is_array($update_data) && count($update_data) > 0){ 
			if($where){
				$sql = "UPDATE {$table_name} SET ";
				$inc = 0;
				foreach ($update_data as $key => $value) {
					if($inc != 0){
						$sql .= ', ';
					}
					$sql .= "{$key}='{$value}'";
					$inc++;
				}
				$sql .= " WHERE {$where}";
				if ($this->db->query($sql) === TRUE) {
				    $message = "Record updated successfully";
				    $status = true;
				} else {
				    $message = "Error updating record: " . $this->db->error;
				}				
			}
			else{
				$message = "Please provide where condition to update row.";
			}
		}
		else{
			$message = "Please provide update data as array format.";
		}	
		$response = [
			"status" => $status,
			"message" => $message
		];
		return $response;
	}
	public function insert_data($table_name, $insert_data = ""){
		if(is_array($insert_data) && count($insert_data) > 0){ 
			$insert_id = "";
			$status    = true;
			$keys      = implode(", ", array_keys($insert_data));
			$values    = implode("', '", $insert_data);
			$sql       = "INSERT INTO {$table_name} ({$keys}) VALUES ('{$values}')";
			if ($this->db->query($sql) === TRUE) {
				$message   = "New record created successfully.";
				$insert_id = $this->db->insert_id;
			}
			else{
				$message = "Error: " . $sql . "<br>" . $this->db->error;
				$status = false;
			}
			$response = [
				"status"    => $status,
				"insert_id" => $insert_id,
				"message"   => $message,
			];
		}
		else{
			$response = [
				"status" => false,
				"message" => "Please provide insert data as array format"
			];
		}	
		return $response;

	}
	public function get_data($fields = "*", $table_name, $where = null, $order_by = null){
		$sql = "SELECT {$fields} FROM {$table_name}";
		if($where != null){
			$sql .= " WHERE {$where}";
		}
		if($order_by != null){
			$sql .= " ORDER BY {$order_by}";
		}
		$result = $this->db->query($sql);
		$result = $result->fetch_all(MYSQLI_ASSOC);
		return $result;
	}
	public function fetch_exist_data($fields = "*", $table_name, $where){
		$sql = "SELECT {$fields} FROM {$table_name} WHERE {$where}";
		$result = $this->db->query($sql);
		$result = $result->fetch_assoc();
		return $result;
	}

}
?>