<?php
	//error_reporting(0);
	require_once 'DBConfigs.php';
    function display($result){
	if($result === 6){
		$display1 ='<div class="alert alert-success">Registered Successfully</div>';	
		return $display1;
	}
	$display ='<div class="alert alert-danger">'.$result.'</div>';
	
	return $display;
	}
	$result = '';
	
	
	if(isset($_POST["submit"])){
		
		$res = $db->createUser($_POST["name"], $_POST["email"], $_POST["password"], $_POST["re-password"], $_POST['token']);
		//print_r($res);
	}	
		
    $token = $_SESSION['token'] = md5(uniqid(mt_rand(),true));
    include 'header.php';
	?>


<div class="container">
    	<div class="row log-reg">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading">
						<div class="row">
                                <div class="text-center">
								    <a href="#" class="active" id="register-form-link">Register</a>
                                </div>	
						</div>
						<hr>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form id="register-form" action="<?=$_SERVER['PHP_SELF'];?>" method="post" role="form" >
								   <?=(isset($res) ? display($res):'' )?>
								
									<div class="form-group">
										*<input type="text" name="name" id="username" tabindex="1" class="form-control" placeholder="Username" value="">
									</div>
									<div class="form-group">
										*<input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
									</div>
									<div class="form-group">
										*<input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
									</div>
									<div class="form-group">
										*<input type="password" name="re-password" id="confirm-password" tabindex="2" class="form-control" placeholder="Confirm Password">
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-6 col-sm-offset-3">
											<input type="hidden" name="token" value="<?=$token;?>"/>
												<input type="submit" name="submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register Now">
											</div>
										</div>
									</div>
								</form>
							</div>
                            <div class="text-center">
                                <a href="/crud_core_php/login.php" class="active" id="register-form-link">Login</a>
                            </div>   
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>