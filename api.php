<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once('./functions/db.php');
require_once('./functions/common_function.php');
class Api extends Common_Functions{
	public $message = '';
	function __construct(){
		parent::__construct();
		$this->index();
		/*$status = 'HTTP/1.1 200 OK';
		header($status);
		$response['name'] = 'sherin';
		echo json_encode($response);*/
	}
	public function index(){
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '' ;
		switch ($type) {
			case 'tags-with-article':
				$this->tags_with_article();
			break;
			case 'add-article':
				$this->add_article();
			break;
			default:
				header("TTP/1.1 404 Not Found");
			break;
		}
	}
	public function add_article(){
		$response = array();
		header("TTP/1.1 200 OK");
		
		echo json_encode($response); exit();
	}
	public function tags_with_article(){
		$response = array();
		header("TTP/1.1 200 OK");
		$sql = "SELECT *, COUNT(".TABLE_PREFIX."tags.id) as article_count, ".TABLE_PREFIX."tags.id as tag_id FROM `".TABLE_PREFIX."tags` LEFT JOIN ".TABLE_PREFIX."articles ON FIND_IN_SET(".TABLE_PREFIX."tags.id, ".TABLE_PREFIX."articles.tags) group by ".TABLE_PREFIX."tags.id";		
		$result = $this->query($sql);
		$response['result'] = $result;
		echo json_encode($response); exit();
	}
}
new Api();
?>