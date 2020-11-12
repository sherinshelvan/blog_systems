<?php
require_once('../functions/db.php');
require_once('../functions/common_function.php');
class Articles extends Common_Functions{
	public $message = '';
	function __construct(){
		parent::__construct();
		$this->page_heading = 'Articles';
		$this->page_url     = BASE_URL.'articles/article.php?id='.$_GET['id'];
		$this->return_url     = BASE_URL.'articles';
		$this->table_name   = TABLE_PREFIX.'articles';
		$this->index();
	}
	public function index(){
		$id = $_GET['id'];
		if(
			isset($_GET['id']) && $_GET['id'] != '' && 
			isset($_POST['doSubmit'])
		){
			$this->submit_comment();
		}
		if(isset($id) && $id != ''){
			$this->article = $this->fetch_exist_data("*", $this->table_name, "id='$id'");
			$this->comments = $this->get_data("*", TABLE_PREFIX."article_comments", "article_id = '$id'");
			$this->page_heading = $this->article['title'];
		}
		else{
			header("location: index.php");
		}
		
	}
	public function submit_comment(){
		$form_data = $_POST;
		if( (isset($form_data['comment']) && $form_data['comment'] == '') 
			OR !isset($form_data['comment'])
		){
			$this->message = "<p>Comment field Required</p>";
		}
		else{
			$insert_array = [
				'article_id' => $_GET['id'],
				'comment' => $this->xss_clean($_POST['comment']),
			];
			$response = $this->insert_data(TABLE_PREFIX."article_comments", $insert_array);
			$this->message = "<p>Comment Successfully Updated</p>";
			if(!$response['status']){
				$this->message = $response['message'];
			}
			
		}
	}
}
$obj = new Articles();

require_once('../includes/header.php');
// require_once('../includes/side_navigation.php');
?>
<div class="main-wrapper">
	<div class="top-bg gradient-45deg-indigo-purple"></div>
	<div >
		<div class="container container-list">
			
			<div class="page-title">
				<a href="<?=$obj->return_url?>" class="white-text"><i class="material-icons left">arrow_back</i></a>
				<h3 class="white-text"><?=$obj->page_heading?></h3>
			</div>
			<div class="card">
				<div class="discription"><?=$obj->article['description']?></div>
				<?php 
				if($obj->comments){
					?>
					<div class="comment-wrapper">
						<h3>Comments</h3>
					<?php
					foreach ($obj->comments as $key => $value) {
						?>
						<div class="item">
							<?=$value['comment']?>
						</div>
						<div class="divider"></div>
						<?php
					}
					?>
					</div>
					<?php
				}
				?>						
			</div>
			<div class="card">
				<?php
					if( $obj->message != ''){
						?>
						<div class="row" style="margin-bottom: 0px;"><div class="col s12"><div class="error-message"><?=$obj->message?></div></div></div>
						<?php
					}
				?>
				<form action="<?=$obj->page_url?>" method="POST" enctype="multipart/form-data">	
					<div class="input-field col s12">
						 <textarea id="textarea1" name="comment" class="materialize-textarea"></textarea>
				          <label for="textarea1">Comment</label>
			            <span class="helper-text" data-error="Required field"></span>
			        </div>
			        <div class="input-field col s12">
				        	<button type="submit" name="doSubmit"n value="doSubmit" class="waves-effect waves-light btn deep-purple darken-2" >
				        		Send<i class="material-icons right">send</i>
				    	</button>

				    </div>
				</form>
			</div>
			
		</div>
	</div>
</div>
<?php

require_once('../includes/footer.php');

?>