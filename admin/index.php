<?php
	ob_start();
	session_start();
	$noNavbar = '';
	$pageTitle = 'Login';
	if (isset($_SESSION['username'])){
		header('location:dashboard.php');
	}
	include 'init.php';

	//check HTTP POST Request
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$username = $_POST['user'];
		$password = $_POST['pass'];
		$hashedPassword	= sha1($password);
		//user auth check(stmt means statement)
		$query = "SELECT userID,username, password
				  FROM users
				  WHERE username = ?
				  AND password = ?
				  AND groupID = 1
				  LIMIT 1";
		$stmt = $conn->prepare($query);
		$stmt->execute(
			array($username,$hashedPassword)
		);
		/*return as array*/
		$row = $stmt->fetch();
		$count = $stmt->rowCount();
		if($count > 0){
			$_SESSION['username'] = $username;//reg session
			$_SESSION['ID'] = $row['userID'];//reg session id
			header('location: dashboard.php');//redirect to dashboard page
			exit();
		}
	}
	?>
	<form class="login" action = "<?php echo $_SERVER['PHP_SELF']?>" method = 'POST'>
		<h4 class="text-center">Admin Login</h4>
		<input class="form-control" type="text" name = 'user'
		placeholder="Username" autocomplete="off"/>
		<input class="form-control" type = 'password' name = 'pass' placeholder="Password"
		autocomplete="
		<?php/*abd:this is used to prevent chrome password remember feature*/?>
		new-password" />
		<input class="btn btn-primary btn-block" type="submit" value ='Log In'>
	</form>
<?php
	include $tpl . 'Footer.php';
	ob_end_flush();
	?>