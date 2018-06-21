<?php
require_once 'DBConfigs.php';
if($db->is_loggedin() == NULL)
{
  $db->redirect('login.php');
  
}
if($db->is_timeout())
{
	$res = $db->logout();
	if($res == true){
		$db->redirect('login.php');
	}
}
//User input
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = isset($_GET['per-page']) && $_GET['per-page'] <= 50 ? (int)$_GET['per-page'] : 3;
$rec = $task->showTask($_SESSION['user_session'], $page, $perPage);
//var_dump($db->is_loggedin());
if(isset($_GET['edit'])){
	$ures = $task->getUpdateDetails($_GET['edit'], $_SESSION['user_session']);
	//$uimg = $ures['image'];
	$utask = $ures['task'];
	
}
if(isset($_GET['delete'])){
	$dres = $task->deleteTask($_GET['delete'], $_SESSION['user_session']);
	if($dres){
		//echo "deleted....";
		header("Location: home.php");
		echo "DELETED.....";
	}
	else{
		echo $dres;
	}
	
}
$inserted = "";
if(isset($_POST['Add'])){
	$res = $task->inserstTask($_POST['task'], $_POST['user_id'], $_FILES, $_POST['token']);	
	if($res == 6){
		//header("Location: home.php");
		$inserted =  '<div class="alert alert-success">Inserted</div>';	
	}
	else{
		//echo $res;
	}
}
if(isset($_POST['cancel'])){
	$db->redirect('home.php');
}
if(isset($_POST['Edit'])){
	
	$etask = (isset($_POST['task'])?$_POST['task']:$ures['task']);
	$eimg = (isset($_FILES)?$_FILES:'uploads/'.$ures['image']);
	//print_r($eimg);
	$eres = $task->updateTask($etask, $_POST['id'], $_SESSION['user_session'], $eimg, $_POST['token']);	
	if($eres == 6){
		header("Location: home.php");
		
	}
	else{
		echo $eres;
	}
}
//print_r($ures['image']);
$token = $_SESSION['token'] = md5(uniqid(mt_rand(),true));
include 'header.php';
?>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Welcome <?php echo isset($_SESSION['user_name']) ? "".$_SESSION['user_name']."": "no session";?></a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<div class="well well-lg">
<form method="POST"  action="" enctype="multipart/form-data">
  <div class="form-group">
    <label for="email">Task:</label>
    <input class="form-control" type="text" value="<?php echo isset($_GET['edit']) ? $utask : ''; ?>" name="task">
  </div>
  <div class="form-group">
    <label for="file">File:</label>
    <input class="file" type="file" name="image" id="fileToUpload" accept="image/*">
  </div>
 
  
  	<?php
	if(isset($ures['image'])){
	?>	
	<img src="uploads/<?=$ures['image'];?>" alt="Smiley face" height="100" width="100">
	<?php
	}
	?>
	<input type="hidden" name="token" value="<?=$token;?>">
	<input type="hidden" name="id" value="<?=isset($_GET['edit'])?$_GET['edit']:'';?>">
	<input type="hidden" name="user_id" value="<?=$_SESSION['user_session'];?>">
	<input type="submit" class="btn btn-primary" name="<?=(isset($_GET['edit'])?'Edit': 'Add');?>" value="<?=(isset($_GET['edit'])?'Edit': 'Add ');?> Task">
	<?=isset($_GET['edit'])?"<input type='submit' name='cancel' value='cancel'>":"";?>
	
</form>
</div>
<?=(isset($inserted) ? $inserted:'' )?>
<?php
if($rec === 0){
	echo "no Tasks!!! Insert Task";
} 
else{
	
	//print_r($rec);
	?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Sl No.</th>
				<th>Task</th>
				<th>Image</th>
				<th>Edit  </th>
				<th>Delete  </th>
			</tr>
		</thead>
		<tbody>
			<?php
			//print_r($rec);
			$no = 1;
			$numItems = count($rec);
			$num2 = $numItems-1;
			$i = 0;
			//echo $rec['total'];
			foreach($rec as $r){
				if( ++$i === $num2) {
				   break;
				}
				$no++;
				?>
				<tr>
					<td><?=$no;?></td>
					<td><?php echo $r['task']; ?></td>
					<td><img src="uploads/<?=$r['image'];?>" alt="Smiley face" height="100" width="100"></td>
					<td><a href="home.php?edit=<?=$r['id'];?>" class="btn btn-primary">Edit</a></td>
					<td><a href="home.php?delete=<?=$r['id'];?>" class="btn btn-danger">Delete</a></td>
				</tr>

			<?php
			}
			?>
		</tbody>
	</table>
</container>	
</body>

<?php for($x =1; $x <= $rec['pages']; $x++):?>
<a  href="?page=<?php echo $x; ?>&per-page=<?php echo $perPage; ?>"<?php if($page === $x) {echo 'class="selected"';}?>><?php echo $x; ?></a>
<?php endfor; 
}
?>