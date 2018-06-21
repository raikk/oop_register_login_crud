<?php
require_once 'DBConfigs.php';
$res = $db->logout();
if($res == true){
	$db->redirect('login.php');
}
?>