<?php
require_once('../functions/db.php');
require_once('../functions/common_function.php');
class Edit extends Common_Functions{
	public $error = false;
	public $message = '';
	public $form_data;
	function __construct(){
		parent::__construct();
		$this->page_heading = 'Add Articles';
		$this->page_url     = BASE_URL.'/articles/edit.php'.(isset($_GET['edit']) ? ("?edit=".$_GET['edit']) : "");
		$this->table_name   = TABLE_PREFIX.'articles';
		$this->return_url   = BASE_URL.'/articles';
		$this->index();
	}
	public function index(){
		$this->form_data = $_POST;
		$this->tags = $this->get_data("*", TABLE_PREFIX."tags", "active = '1'");
		$this->categories = $this->get_data("*", TABLE_PREFIX."category", "active = '1'");
		if(
			isset($_GET['edit']) && $_GET['edit'] != '' && 
			!isset($_POST['doSubmit'])
		){
			$this->form_data = $this->fetch_exist_data("*", $this->table_name, "id='".$_GET['edit']."'");
			$this->exist_files = $this->get_data("*", TABLE_PREFIX."article_files", "article_id='".$_GET['edit']."'");
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
		if( (isset($form_data['title']) && $form_data['title'] == '') 
			OR !isset($form_data['title'])
		){
			$this->error = true;
			$this->message = "<p>Title Name Required</p>";
		}
		if( (isset($form_data['description']) && $form_data['description'] == '') 
			OR !isset($form_data['description'])
		){
			$this->error = true;
			$this->message .= "<p>Description Name Required</p>";
		}
		if( (isset($form_data['category']) && $form_data['category'] == '') 
			OR !isset($form_data['category'])
		){
			$this->error = true;
			$this->message .= "<p>Category Name Required</p>";
		}
		$this->file_validate();
		
	}
	public function file_upload( $id = ''){
		$message = '';
		$upload_dir = '../assets/images/'; 
	    $allowed_types = array('jpg', 'png', 'jpeg', 'gif'); 
	      
	    // Define maxsize for files i.e 2MB 
	    $maxsize = 2 * 1024 * 1024; 
		if(!empty(array_filter($_FILES['files']['name']))) { 
			// Loop through each file in files[] array 
	        foreach ($_FILES['files']['tmp_name'] as $key => $value) { 
	              
	            $file_tmpname = $_FILES['files']['tmp_name'][$key]; 
	            $file_name = $_FILES['files']['name'][$key]; 
	            $file_size = $_FILES['files']['size'][$key]; 
	            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION); 
	  
	            // Set upload file path 
	            $filepath = $upload_dir.$file_name; 
	  
	            // Check file type is allowed or not 
	            if(in_array(strtolower($file_ext), $allowed_types)) { 
	  
	                // Verify file size - 2MB max  
	                if ($file_size > $maxsize)     
	                    $message .=  "<p>Error: File size is larger than the allowed limit.<p>";  
	  
	                // If file with name already exist then append time in 
	                // front of name of the file to avoid overwriting of file 
	                if(file_exists($filepath)) { 
	                    $filepath = $upload_dir.time().$file_name; 
	                      
	                    if( move_uploaded_file($file_tmpname, $filepath)) { 
	                        $message .= "{$file_name} successfully uploaded <br />"; 
	                        $insert_array = [
								'file_name' => $file_name,
								'article_id' => $id,
								'file_ext' => $file_ext
							];
							$response = $this->insert_data(TABLE_PREFIX."article_files", $insert_array);
							// print_r($response); die;
	                    }  
	                    else {                      
	                        echo "Error uploading {$file_name} <br />";  
	                    } 
	                } 
	                else { 
	                  
	                    if( move_uploaded_file($file_tmpname, $filepath)) { 
	                    	$message .= "{$file_name} successfully uploaded <br />"; 
	                        $insert_array = [
								'file_name' => $file_name,
								'article_id' => $id,
								'file_ext' => $file_ext
							];
							$response = $this->insert_data(TABLE_PREFIX."article_files", $insert_array);
							// print_r($response); die;
	                        $message =  "{$file_name} successfully uploaded <br />"; 
	                    } 
	                    else {                      
	                        $message =  "Error uploading {$file_name} <br />";  
	                    } 
	                } 
	            } 
	            else { 
	                $message .= "<p>Error uploading {$file_name} </p>";  
	                $message .= "<p>({$file_ext} file type is not allowed)</p>"; 
	            }  
	        } 
		}
	}
	public function file_validate(){
		$upload_dir = '../assets/images/'; 
	    $allowed_types = array('jpg', 'png', 'jpeg', 'gif'); 
	      
	    // Define maxsize for files i.e 2MB 
	    $maxsize = 2 * 1024 * 1024; 
		if(!empty(array_filter($_FILES['files']['name']))) { 
			// Loop through each file in files[] array 
	        foreach ($_FILES['files']['tmp_name'] as $key => $value) { 
	              
	            $file_tmpname = $_FILES['files']['tmp_name'][$key]; 
	            $file_name = $_FILES['files']['name'][$key]; 
	            $file_size = $_FILES['files']['size'][$key]; 
	            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION); 
	  
	            // Set upload file path 
	            $filepath = $upload_dir.$file_name; 
	  
	            // Check file type is allowed or not 
	            if(in_array(strtolower($file_ext), $allowed_types)) { 
	  
	                // Verify file size - 2MB max  
	                if ($file_size > $maxsize)  
	                	$this->error  = 1;         
	                    $this->message .=  "<p>Error: File size is larger than the allowed limit.<p>"; 
	                
	            } 
	            else { 
	                $this->error  = 1;   
	                // If file extention not valid 
	                $this->message .= "<p>Error uploading {$file_name} </p>";  
	                $this->message .= "<p>({$file_ext} file type is not allowed)</p>"; 
	            }  
	        } 
		}
	}
	public function add_callback(){
		$this->form_validation();
		if($this->error === false){
			

			$insert_array = [
				'title' => $this->xss_clean($_POST['title']),
				'description' => $this->xss_clean($_POST['description']),
				'tags' => ((isset($_POST['tags']) && is_array($_POST['tags']) ) ? (implode(',', $_POST['tags'])) : ""),
				'category' => $_POST['category'],
				'active' => (isset($_POST['active']) ? '1' : '0')
			];
			$response = $this->insert_data($this->table_name, $insert_array);
			$this->file_upload($response['insert_id']);
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
		$this->exist_files = $this->get_data("*", TABLE_PREFIX."article_files", "article_id='".$_GET['edit']."'");
		if($this->error === false){
			$insert_array = [
				'title' => $this->xss_clean($_POST['title']),
				'description' => $this->xss_clean($_POST['description']),
				'tags' => ((isset($_POST['tags']) && is_array($_POST['tags']) ) ? (implode(',', $_POST['tags'])) : ""),
				'category' => $_POST['category'],
				'active' => (isset($_POST['active']) ? '1' : '0')
			];
			$response = $this->update_data($this->table_name, $insert_array, "id = '$id'");
			$this->file_upload($id);
			$submit_files = (isset($_POST['exist_files']) && is_array($_POST['exist_files']) ? $_POST['exist_files'] : array()); 
			if($this->exist_files && is_array($this->exist_files)){
				foreach ($this->exist_files as $key => $value) {
					if(!in_array($value['id'], $submit_files)){
						$upload_dir = '../assets/images/';
						unlink($upload_dir.$value['file_name']);
						$response = $this->delete_data(TABLE_PREFIX."article_files", "id = '".$value['id']."'");
					}
				}
			}
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
				          	<input placeholder="Title" name="title"
				          	value="<?=(isset($obj->form_data['title']) ? $obj->form_data['title'] : "")?>"
				          	 id="tag" type="text" class="validate" />
				            <label for="tag">Title</label>
				            <span class="helper-text" data-error="Required field"></span>
				        </div>
				        <div class="input-field col s12">
				          <textarea id="textarea1" name="description" class="materialize-textarea"><?=(isset($obj->form_data['description']) ? $obj->form_data['description'] : "")?></textarea>
				          <label for="textarea1">Textarea</label>
				        </div>
				        <div class=" col s12"><strong>Category</strong></div>
				        <div class="input-field col s12">
				        	<select name="category">
						      <option value=""  selected>Choose a Category</option>
						      <?php
					         	if($obj->categories){
					         		foreach ($obj->categories as $key => $value) {
					         			?>
					         			<option <?=((isset($obj->form_data['category']) && $obj->form_data['category'] == $value['id']) ? "selected" : "")?> value="<?=$value['id']?>" ><?=$value['name']?></option>
					         			<?php
					         		}
					         	}
					         	?>
						    </select>
						    <!-- <?php echo print_r($obj->form_data);?> -->
				        </div>
				         <div class=" col s12"><strong>Tags</strong></div>
				         <div class="input-field col s12">
				         	<?php
				         	if($obj->tags){
				         		$tags_list = [];
				         		if((isset($obj->form_data['tags'])) && !is_array($obj->form_data['tags']) ){
				         			$tags_list = explode(",", $obj->form_data['tags']);
				         		}
				         		if((isset($obj->form_data['tags'])) && is_array($obj->form_data['tags']) ){
				         			$tags_list =  $obj->form_data['tags'];
				         		}
				         		foreach ($obj->tags as $key => $value) {
				         			?>
				         			<p class="input-field" style="padding:5px !important;">
								      <label>
								        <input type="checkbox" <?=(in_array($value['id'], $tags_list)? "checked" : "")?> name="tags[]" value="<?=$value['id']?>" />
								        <span><?=$value['name']?></span>
								      </label>
								    </p>
				         			<?php
				         		}
				         	}
				         	?>
				          
				        </div>
				        <div class="input-field col s12">
				        <div class="file-field input-field">
					      <div class="btn">
					        <span>Files</span>
					        <input type="file" name="files[]" multiple>
					      </div>
					      <div class="file-path-wrapper">
					        <input class="file-path validate" type="text">
					      </div>
					      <div class="item-wrapper input-field col s12">
					      	<?php 
					      	if(isset($obj->exist_files) && is_array($obj->exist_files)){
					      		foreach ($obj->exist_files as $key => $value) {
					      			?>
					      				<div class="item">
					      					<input type="hidden" name="exist_files[]" value="<?=$value['id']?>" />
					      					<?=$value['file_name']?>
					      					<i onclick=" $(this).parent('div.item').remove();"class="material-icons">close</i>
					      				</div>
					      			<?php
					      		}
					      	}
					      	?>
					      </div>
					    </div>
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