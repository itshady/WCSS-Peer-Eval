<?php
    require("functions.php");
    session_start();
	$invalid = 0;

    if (serverPOST() && loginAuth($_POST['user'],$_POST['pass'])) {
        $role = userType($_SESSION['userID']);
        //print_r($role);

        if ($role == "student") header("Location: ./student.php");
        else if ($role == "teacher" || $role == "admin") header("Location: ./teacher.php");
        //else if ($role == "admin") header("Location: ./admin.php");
    }
	else if (serverPOST()) {
		$invalid = 1;
	}
	

?>


<!DOCTYPE html>
<!-- Developed by Hady Ibrahim and Shushawn Saha -->
<html lang="en">
<head>
	<title>Welcome | Peer Evaluator</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="indexAddons/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="indexAddons/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="indexAddons/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="indexAddons/css/util.css">
	<link rel="stylesheet" type="text/css" href="indexAddons/css/main.css">
<!--===============================================================================================-->
	<style>
		.alert {
			display:flex;
		}
	</style>
</head>
<body style="background-color: #666666;">

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" action="" method="post">
					<span class="login100-form-title p-b-43">
						Login to Continue
					</span>
					
					
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@ocdsb.ca">
						<input class="input100" type="email" id="user" name="user" value="">
						<span class="focus-input100"></span>
						<span class="label-input100">Email</span>
					</div>
					
					
					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100" type="password" id="pass" name="pass">
						<span class="focus-input100"></span>
						<span class="label-input100">Password</span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>


					<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
					  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
					    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
					  </symbol>
					</svg>
					<div class="alert alert-danger align-items-center mt-5 justify-content-center" role="alert" style="display: none;">
						<svg class="bi flex-shrink-0 me-2 justify-self-start" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
						<div class='w-100 text-center'>
							Invalid credentials
						</div>
						<svg class="bi flex-shrink-0 me-2 justify-self-start" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
					</div>
				</form>

				

				<div class="login100-more" style="background-image: url(PEER%20EVALUATOR.png)">
				
				
				</div>
			</div>
		</div>
	</div>

	<!--
	<form action="" method="post" class="mr-auto" style="width:fit-content;">
		<label for="user" style="width:fit-content;">Username</label>
		<input type="text" id="user" name="user" value="hibra12@ocdsb.ca"><br>
		<label for="pass" style="width:fit-content;">Pass</label>
		<input type="password" id="pass" name="pass"><br>
		<button type="submit">Login</button>
	</form>
	<form action="" method="post" class="ml-auto top-0" style="width:fit-content; position:absolute; right:0;">
		<label for="user" style="width:fit-content;">Username</label>
		<input type="text" id="user" name="user" value="tkhal1@ocdsb.ca"><br>
		<label for="pass" style="width:fit-content;">Pass</label>
		<input type="password" id="pass" name="pass"><br>
		<button type="submit">Login</button>
	</form>
	<form action="" method="post" class="ml-auto" style="width:fit-content; position:absolute; bottom:0;">
		<label for="user" style="width:fit-content;">Username</label>
		<input type="text" id="user" name="user" value="shannon.adams@ocdsb.ca"><br>
		<label for="pass" style="width:fit-content;">Pass</label>
		<input type="password" id="pass" name="pass"><br>
		<button type="submit">Login</button>
	</form>
	<form action="" method="post" class="ml-auto" style="width:fit-content; position:absolute; right:0; bottom:0;">
		<label for="user" style="width:fit-content;">Username</label>
		<input type="text" id="user" name="user" value="stephen.emmell@ocdsb.ca"><br>
		<label for="pass" style="width:fit-content;">Pass</label>
		<input type="password" id="pass" name="pass"><br>
		<button type="submit">Login</button>
	</form>

	<div class="position-absolute top-0" style="margin:auto; width:fit-content; margin-top:100px; font-size:30px; font-weight:900; text-align:center;">West Carelton Pear Evaluater
<?php
	//echo "<br><br>";
	//print_r($_SESSION);
?>
</div>-->

<!--===============================================================================================-->
	<script src="indexAddons/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="indexAddons/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="indexAddons/vendor/bootstrap/js/popper.js"></script>
	<script src="indexAddons/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="indexAddons/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="indexAddons/vendor/daterangepicker/moment.min.js"></script>
	<script src="indexAddons/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="indexAddons/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="indexAddons/js/main.js"></script>
	<script>
		var invalid = <?php echo $invalid; ?>;
		if (invalid == 1) $(".alert").fadeIn(200);
		setTimeout(function() {$(".alert").fadeOut(300)}, 2000);
	</script>

</body>
</html>