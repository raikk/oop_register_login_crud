<?php
require_once 'DBConfigs.php';

if($db->is_loggedin())
{
  $db->redirect('home.php');
}
$display = '';
if(isset($_POST['submit'])){
	$res = $db->login($_POST['username'], $_POST['username'], $_POST['password'], $_POST['token']);
	//print_r($res);
	//print_r($_SESSION['token']);
	
	if($res == 1){
		header("Location: home.php");
	}
	else{
       // echo $res;
        $display ='<div class="alert alert-danger">'.$res.'</div>';
        //echo $display;
		//echo $db->is_loggedin();		
		//header("Location: home.php");
	}
}
$token = $_SESSION['token'] = md5(uniqid(mt_rand(),true));

include 'header.php';
?>
<div class="container">
    	<div class="row log-reg" >
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading">
						<div class="row">
							<!--<div class="col-xs-6">
								<a href="#" class="active" id="login-form-link">Login</a>
							</div>-->
							<div class="text-center">
								<a href="#" class="active" id="register-form-link">Login</a>
							</div>
						</div>
						<hr>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form  action="<?=$_SERVER['PHP_SELF'];?>" method="post" role="form" >

                                    <?=(isset($display) ? $display:'' )?>
								
									<div class="form-group">
								
                                        *<input type="username" name="username" id="username" tabindex="1" class="form-control" placeholder="Username" value="">
                                    </div>
									<div class="form-group">
										
                                        *<input type="password" name="password" id="password" tabindex="1" class="form-control" placeholder="Password" value="">
									</div>
									
									<div class="form-group">
										<div class="row">
										<div class="col-sm-6 col-sm-offset-3">
                                            <input type="hidden" name="token" value="<?=$token;?>">
	                                        <input type="submit" name="submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Login">
                                        </div>
									</div>
								</div>
							</form>
						</div>
                        <div class="text-center">
                            <a href="/crud_core_php/register.php" class="active" id="register-form-link">New User??? Register here</a>
                        </div>    
					</div>
				</div>
			</div>
		</div>
	</div>
</div>