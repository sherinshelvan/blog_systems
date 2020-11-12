<?php
require_once('../functions/db.php');
require_once('../functions/common_function.php');
class Edit extends Common_Functions{
	public $error = false;
	public $message = '';
	public $form_data;
	function __construct(){
		parent::__construct();
		$this->page_heading = 'Add Category';
		$this->page_url     = BASE_URL.'category/edit.php'.(isset($_GET['edit']) ? ("?edit=".$_GET['edit']) : "");
		$this->table_name   = TABLE_PREFIX.'category';
		$this->return_url   = BASE_URL.'category';
		$this->index();
	}
	public function index(){
		$this->form_data = $_POST;
		if(
			isset($_GET['edit']) && $_GET['edit'] != '' && 
			!isset($_POST['doSubmit'])
		){
			$this->form_data = $this->fetch_exist_data("*", $this->table_name, "id='".$_GET['edit']."'");
		}
		if(
			isset($_GET['edit']) && $_GET['edit'] != '' && 
			isset($_POST['doSubmit']) && $_POST['doSubmit'] == 'doSubmit' && 
			is_numeric($_GET['edit'])
		){

			$this->edit_callback();

		}
		if(
			!isset($_GET['edit']) && 
			isset($_POST['doSubmit']) && $_POST['doSubmit'] == 'doSubmit' 
		){
			$this->add_callback();
		}
		
		$this->result = $this->get_data("*", $this->table_name);
	}
	public function form_validation($id = ''){
		$form_data = $_POST;
		if( (isset($form_data['name']) && $form_data['name'] == '') 
			OR !isset($form_data['name'])
		){
			$this->error = true;
			$this->message = "<p>Category Name Required</p>";
		}
		if(isset($form_data['name']) && $form_data['name'] != ''){
			$condition = "1 AND name='".$form_data['name']."' ";
			if($id != ''){
				$condition .= " AND id != '".$id."'";
			}
			$exist = $this->fetch_exist_data("*", $this->table_name, $condition);
			if($exist){
				$this->error = true;
				$this->message = "<p>Category Name already exist</p>";
			}
		}
	}
	public function add_callback(){
		$this->form_validation();
		if($this->error === false){
			$insert_array = [
				'name' => $this->xss_clean($_POST['name']),
				'active' => (isset($_POST['active']) ? '1' : '0')
			];
			$response = $this->insert_data($this->table_name, $insert_array);
			if(!$response['status']){
				$this->error   = $response['status'];
				$this->message = $response['message'];
			}
			else{
				header("location: index.php");
			}
		}

	}
	public function edit_callback(){
		$id = $_GET['edit'];
		$this->form_validation($id);
		if($this->error === false){
			$insert_array = [
				'name' => $this->xss_clean($_POST['name']),
				'active' => (isset($_POST['active']) ? '1' : '0')
			];
			$response = $this->update_data($this->table_name, $insert_array, "id = '$id'");
			if(!$response['status']){
				$this->error   = $response['status'];
				$this->message = $response['message'];
			}
			else{
				header("location: index.php");
			}
		}
	}
}
$obj = new Edit();

require_once('../includes/header.php');
require_once('../includes/side_navigation.php');
?>
<div class="main-wrapper">
	<div class="top-bg gradient-45deg-indigo-purple"></div>
	<div id="main-content">
		<div class="container container-list">
			<div class="page-title"><h3 class="white-text"><?=$obj->page_heading?></h3></div>
			<div class="card">				
				<form action="<?=$obj->page_url?>" method="POST" enctype="multipart/form-data">				
					
					<div class="add-new left">
						<a href="<?=$obj->return_url?>" class="waves-effect waves-light btn deep-purple darken-2"><i class="material-icons left">arrow_back</i></a>
					</div>
					<?php
					if($obj->error && $obj->message != ''){
						?>
						<div class="row" style="margin-bottom: 0px;"><div class="col s12"><div class="error-message"><?=$obj->message?></div></div></div>
						<?php
					}
					?>
					<div class="row">
				        <div class="input-field col s12">
				          	<input placeholder="Category" name="name"
				          	value="<?=(isset($obj->form_data['name']) ? $obj->form_data['name'] : "")?>"
				          	 id="category" type="text" class="validate" />
				            <label for="category">Category</label>
				            <span class="helper-text" data-error="Required field"></span>
				        </div>
				        <!-- Switch -->
						<div class="switch col s12">
						    <label>
						      Inactive
						      <input type="checkbox" <?=((isset($obj->form_data['active']) && $obj->form_data['active'] == 1) ? "checked" : "")?> name="active" value="1">
						      <span class="lever"></span>
						      Active
							</label>
						</div>
				        <div class="input-field col s12">
				        	<button type="submit" name="doSubmit"n value="doSubmit" class="waves-effect waves-light btn deep-purple darken-2" >
				        		Save<i class="material-icons right">send</i>
				        	</button>
				        </div>
				    </div>
				</form>
				
			</div>
			
		</div>
	</div>
</div>
 <!-- Modal Structure -->
<div id="deleteModal" class="modal delete confirmation">
	<div class="modal-content center">
	  <span class="close-img"><i class="material-icons">close</i></span>
	  <h2>Are you sure you want to delete this item?</h2>
	  <a href="#!" data-link="/delete/" class="waves-effect red lighten-1 btn delete confirm">Delete</a>
	  <a href="#!" class="modal-close waves-effect btn teal lighten-2">No</a>
	</div>
</div>
<?php

require_once('../includes/footer.php');

?>