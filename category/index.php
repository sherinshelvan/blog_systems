<?php
require_once('../functions/db.php');
require_once('../functions/common_function.php');
class Category extends Common_Functions{
	function __construct(){
		parent::__construct();
		$this->page_heading = 'Category';
		$this->page_url     = BASE_URL.'category';
		$this->table_name   = TABLE_PREFIX.'category';
		$this->index();
	}
	public function index(){
		if(isset($_GET['delete']) && $_GET['delete'] != '' && is_numeric($_GET['delete'])){
			$this->delete_data($this->table_name, "id = '".$_GET['delete']."'");
		}
		$this->result = $this->get_data("*", $this->table_name);
	}
}
$obj = new Category();

require_once('../includes/header.php');
require_once('../includes/side_navigation.php');
?>
<div class="main-wrapper">
	<div class="top-bg gradient-45deg-indigo-purple"></div>
	<div id="main-content">
		<div class="container container-list">
			<div class="page-title"><h3 class="white-text"><?=$obj->page_heading?></h3></div>
			<div class="card">
				<div class="add-new right">
					<a href="<?=$obj->page_url?>/edit.php" class="waves-effect waves-light btn deep-purple darken-2"><i class="material-icons left">add</i>Add New</a>
				</div>
				<table id="list_table" class="striped highlight datatable" >
			       <thead>
				        <tr>
				          <th>ID</th>
				          <th>Category Name</th>
				          <th>Status</th>
				          <th>Actions</th>
				        </tr>
				    </thead>
				    <?php
				    if(count($obj->result) > 0){
				    	foreach ($obj->result as $key => $row) {
					    	?>
					    	<tbody>
					    		<td><?=$row['id']?></td>
					    		<td><?=$row['name']?></td>
					    		<td><?=($row['active'] == '1' ? "Active" : "Inactive")?></td>
					    		<td>
					    			<a href="<?=$obj->page_url?>/edit.php?edit=<?=$row['id']?>" class="">
					    				<i class="material-icons">mode_edit</i>
					    			</a>
					    			<a href="<?=$obj->page_url?>?delete=<?=$row['id']?>" class="" 
					    				onclick="return confirm('Are you sure do you want to delete?')"
					    				>
					    				<i class="material-icons">delete</i>
					    			</a>
					    		</td>
					    	</tbody>
					    	<?php
					    }
				    }
				    ?>
			      	<tfoot>
				      	<tr>
				          <th>ID</th>
				          <th>Category Name</th>
				          <th>Status</th>
				          <th>Actions</th>
				        </tr>
			     	 </tfoot>
			    </table>
				
			</div>
			
		</div>
	</div>
</div>
<?php

require_once('../includes/footer.php');

?>