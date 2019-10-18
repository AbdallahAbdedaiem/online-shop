<?php
	/*
	==============================================Manage members page
	===you can < Add | Edit | Delete >
	=========================================
	*/
	ob_start();
	session_start();
	$pageTitle = 'Members';
	if (isset($_SESSION['username'])){
		include 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'manage';
		if($do == 'manage') {
			//manage members page
			$query = '';
			if(isset($_GET['page']) && $_GET['page'] == 'pending'){
				$query  = ' AND regstatus = 0';
			}

			/*abd: select all users except admins*/
			$fetchUsersQuery = 'SELECT * FROM users WHERE groupID != 1 ORDER BY userID'
			. $query;
			$stmt = $conn->prepare($fetchUsersQuery);
			$stmt->execute();
			$rows = $stmt->fetchAll();
			?>
			<h1 class="text-center my-5">Manage members</h1>
			<div class="container">
				<a class="add-el btn btn-primary" href='members.php?do=add'>
				<i class="fa fa-plus"></i>New Member</a>
				<?php if (!empty($rows)){?>
				<div class='table-responsive manage-members'>
					<table
					class="main-table text-center table table-bordered">
											<tr>
												<td>#id</td>
												<td>Avatar</td>
												<td>username</td>
												<td>email</td>
												<td>full name</td>
												<td>registered date</td>
												<td>control</td>
											</tr>
							<?php
								foreach($rows as $row){
									echo "<tr>";
									echo "<td>" .
									$row['userID'] .
									"</td>";
									echo "<td>";
										if(empty($row['avatar'])){
											echo "<img class ='rounded-circle' src='https://placekitten.com/300/300' alt ='placeholder'>";
										} else {
										echo "<img class ='rounded-circle' src ='uploads/avatars/" .
										$row['avatar'] .
										"' alt=''>";
										}
									echo "</td>";
									echo "<td>" .
									 $row['username'] .
									 "</td>";
									echo "<td>" .
									 $row['email'] .
									 "</td>";
									 echo "<td>" .
									 $row['fullname'] .
									 "</td>";
									 echo "<td>" . $row['regdate'] . "</td>";
									 echo
									 '<td>
										<a href="members.php?do=edit&userid='
										. $row['userID'] .
										'" class="abd-btn-xs btn btn-success"><i class = "fa fa-edit"></i>Edit</a>
										<a href="members.php?do=delete&userid='
										. $row['userID'] .
										'" class="abd-btn-xs btn btn-danger confirm"><i class = "fa fa-trash"></i>Delete</a>';
									if($row['regstatus'] == 0){
										echo
										'<a href="members.php?do=activate&userid='
										. $row['userID'] .'" class="abd-btn-xs btn btn-info activate"><i class = "fa fa-check"></i>Activate</a>';
										}
									 echo '</td>';
									echo "</tr>";
								}

											?>
										</table>
									</div>
								<?php } else {
									echo
									"<p class='py-4 alert alert-info text-center'>
									There's no registered members for now. You can add one!</p>";
								}?>
								</div>

		<?php }

		elseif($do == 'add'){
		//add members page
		?>
			<h1 class="mt-3 text-center">Add New Member</h1>
			<div class="container">
				<!--
					abd:
					**default enctype for form
					**encype = "application/x-www-form-urlencoded"
					**for file upload
					enctype = "multipart/form-data"
				-->
				<form class="mt-5"
				action='?do=insert' method="POST"
				enctype = "multipart/form-data">
					<!--username field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Username</label>
						<div class="col-sm-8">
							<input
							type="text" name = 'username' class="form-control" autocomplete="off"
							required='required'
							placeholder = 'Username'>
						</div>
					</div>
					<!--password field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Password</label>
						<div class="col-sm-8">
							<input type="password" name = 'password'
							class="password form-control" autocomplete="new-password"
							required='required'
							placeholder="password">
							<i class="show-pass fa fa-eye fa-2x"></i>
						</div>
					</div>
					<!--email field-->
					<div class="form-group row">
						<label
						class="col-sm-2 control-label text-center">Email</label>
						<div class="col-sm-8">
							<input
							type="email" name = 'email' class="form-control"
							required='required'
							placeholder = 'please enter a valid email'>
						</div>
					</div>
					<!--full name field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Full Name</label>
						<div class="col-sm-8">
							<input
							type="text" name = 'full' class="form-control"
							required='required'
							placeholder = 'full name appears in profile page'>
						</div>
					</div>
					<!--avatar field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">User Avatar</label>
						<div class="col-sm-8">
							<input
							type="file" name = 'avatar'
							class="form-control" required = 'required'
							>
						</div>
					</div>
					<!--submit-->
					<div class="form-group row">
						<div class="col-sm-8 offset-sm-2">
							<input class="btn btn-primary" type="submit" value = 'Add'>
						</div>
					</div>
				</form>
			</div>
		<?php }
		elseif($do == 'insert') {
		//insert member page
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			echo "<div class='container'>";
			echo "<h1 class='text-center'>Insert</h1>";

			//upload file
			$avatar = $_FILES['avatar'];
			$avatarName = $avatar['name'];
			$avatarType = $avatar['type'];
			$avatarTmp = $avatar['tmp_name'];
			$avatarError = $avatar['error'];
			$avatarSize = $avatar['size'];
			//get the extention
			$avExplode = explode(".", $avatarName);
			$avatarExtention = strtolower(end($avExplode));

			//list of allowed files to upload
			$avatarAllowedExtentions = array("jpeg", "jpg", "png", "gif");



			//get variables from the form
			$user = $_POST['username'];
			/*abd:
			**sha1 isn't empty even for empty string*/
			$pass = $_POST['password'];
			$email = $_POST['email'];
			$name = $_POST['full'];
			$hashPass = sha1($pass);
			//validate the form
			$formErrors = array();
			if(empty($user)){
				$formErrors[] = "username can't be empty";
			}
			elseif(strlen($user) < 4) {
				$formErrors[] = "username can't be less than 4 characters!";
			}
			elseif(strlen($user) > 20) {
				$formErrors[] = "<div class='username can't be more than 20 characters!";
			}
			if(empty($pass)){
				$formErrors[] = "password can't be empty";
			}
			if (empty($name)){
				$formErrors[] = "fullname can't be empty";
			}
			if(empty($email)){
				$formErrors[] = "email can't be empty";
			}
			if(!empty($avatarName) && !in_array($avatarExtention,
									 $avatarAllowedExtentions)){
				$formErrors[] = "This extention is  <strong>not allowed</strong>";
			}
			if(empty($avatarName)){
				$formErrors[] = "Avatar is <strong>Required</strong>!";
			}
			/*size in bytes
			**4Mb=>4 *1024 *1024
			*/
			$Mb = 1024 *1024;
			if($avatarSize > 4 * $Mb){
				$formErrors[] = "Avatar size can't be larger than <strong>4Mb</strong>!";
			}

			foreach ($formErrors as $error) {
				echo "<div class='alert alert-danger'>" .
					$error  .
					"</div>";
			}


			//check if there is no errors
			if (empty($formErrors)){
				$avatar = rand(0,1000000) . '_' . $avatarName;
				echo $avatar;
				//built in function to save file
				//move_uploaded_file(filename, destination)
				move_uploaded_file($avatarTmp, "uploads\avatars\\" . $avatar);


				//abd: check if user already exists in database
				$check = checkItem("username", "users",$user);
				if($check == 1){
					$msg =  "<div class = 'alert alert-danger'>Sorry! This username is already taken</div>";
					redirectHome($msg, 'back');
				} else {
				//insert user into database
				$addUserQuery = "INSERT INTO users(username,password,email,fullname,regstatus,regdate,avatar)
				VALUES(:zuser,:zpass,:zmail,:zname,1,now(),:zavatar)";
				/*abd:
				**u can also use this syntax with indexed array in execute
				+++++++++++++++++++++
				$addUserQuery = "INSERT INTO users(username,password,email,fullname)
				VALUES(?,?,?,?)"
				+++++++++++++++++++++
				*/
				$stmt = $conn->prepare($addUserQuery);
				$stmt->execute(array(
					'zuser' 	=> $user,
					'zpass' 	=> $hashPass,
					'zmail' 	=> $email,
					'zname' 	=> $name,
					'zavatar' => $avatar
				)
				);
				//Success message
				$msg = "<div class='alert alert-success'>" . $stmt->rowCount() . " Record inserted!" . "</div>";
				redirectHome($msg, 'back',4);
				}
			}


		} else {
			echo "<div class = 'container'>";
			$msg =  '<div class = "alert alert-danger>
			"you can\'t access this page directly</div>';
			redirectHome($msg);
			echo "</div>";
		}
		echo '</div>';
		}

		elseif ($do == 'edit') {
		//edit page
		/*abd:
		**can't find out why userid changes to userID in query parameters on browser
		*/
		$userid =
			isset($_GET['userid']) && is_numeric($_GET['userid'])?
			intval($_GET['userid']):
			0;

		$query = "SELECT *
				FROM users
				WHERE userID = ?
				/*abd:
				**limit 1 isn't needed inn this case
				*/
				LIMIT 1";
		$stmt = $conn->prepare($query);
		$stmt->execute(
			array($userid)
		);
		/*return as array*/
		$row = $stmt->fetch();
		$count = $stmt->rowCount();
		if($count > 0){

			?>

			<h1 class="mt-3 text-center">Edit Member</h1>
			<div class="container">
				<form class="mt-5" action='?do=update' method="POST">
					<input type='hidden' name="userid"
					value='<?php echo $userid;?>'
					>
					<!--username field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Username</label>
						<div class="col-sm-8">
							<input
							value='<?php echo $row['username']?>'
							type="text" name = 'username' class="form-control" autocomplete="off"
							required='required'>
						</div>
					</div>
					<!--password field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Password</label>
						<div class="col-sm-8">
							<input type='hidden' name='oldpassword'
							value=
							'<?php echo $row['password'];?>'>
							<input type="password" name = 'newpassword' class="form-control" autocomplete="new password"
							 placeholder="leave blank if you don't want to change!">
						</div>
					</div>
					<!--email field-->
					<div class="form-group row">
						<label
						value='<?php echo $row['email']?>'
						class="col-sm-2 control-label text-center">Email</label>
						<div class="col-sm-8">
							<input
							value='<?php echo $row['email']?>'
							type="email" name = 'email' class="form-control"
							required='required'>
						</div>
					</div>
					<!--full name field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Full Name</label>
						<div class="col-sm-8">
							<input
							value='<?php echo $row['fullname']?>'
							type="text" name = 'full' class="form-control"
							required='required'>
						</div>
					</div>
					<!--submit-->
					<div class="form-group row">
						<div class="col-sm-8 offset-sm-2">
							<input class="btn btn-primary" type="submit" value = 'Submit'>
						</div>
					</div>
				</form>
			</div>


		<?php
		}
		else {
			echo "<div class = 'container'>";
			$theMsg =  "<div class = 'alert alert-danger'>There is no such id</div>";
			redirectHome($theMsg);
			echo "</div>";
		}
	} elseif($do == 'update') {
	//update page
		echo "<h1 class='text-center'>This is update page</h1>";
		echo "<div class='container'>";
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			//get variables from the form
			$id = $_POST['userid'];
			$user = $_POST['username'];
			$email = $_POST['email'];
			$name = $_POST['full'];

			//password trick
			$pass =
				empty($_POST['newpassword'])?
				$_POST['oldpassword'] :
				sha1($_POST['newpassword']);

			//validate the form
			$formErrors = array();
			if(empty($user)){
				$formErrors[] = "username can't be empty";
			}
			elseif(strlen($user) < 4) {
				$formErrors[] = "username can't be less than 4 characters!";
			}
			elseif(strlen($user) > 20) {
				$formErrors[] = "<div class='username can't be more than 20 characters!";
			}
			if (empty($name)){
				$formErrors[] = "fullname can't be empty";
			}
			if(empty($email)){
				$formErrors[] = "email can't be empty";
			}

			foreach ($formErrors as $error) {
				echo "<div class='alert alert-danger'>" .
					$error  .
					"</div>";
			}
			//check if there is no errors
			if (empty($formErrors)){
				/****************************/
				$checkUsernameQuery = 'SELECT * FROM users WHERE username =? AND userID != ?';
				$stmt = $conn->prepare($checkUsernameQuery);
				$stmt->execute(array($user, $id));
				$countUsername = $stmt->rowCount();
				if($countUsername == 0){
					echo 'very good!';
				//update database
				$updateUserQuery = "UPDATE users SET
					username = ?,
					email = ?,
					fullname = ?,
					password = ?
					WHERE userID = ?";
				$stmt = $conn->prepare($updateUserQuery);
				$stmt->execute(array($user,$email,$name,$pass,$id));
				$msg =  "<div class='alert alert-success'>" . $stmt->rowCount() . " Record updated!" . "</div>";
				redirectHome($msg, 'back');
				} else {
					$msg =  "<div class = 'alert alert-danger'>This username is taken!</div>";
					redirectHome($msg, 'back');
				}
				/******************************/
			}

		} else {
			$msg =  "<div class = 'alert alert-danger'>you can't access this page directly</div>";
			redirectHome($msg, 'back');
		}
		echo '</div>';
	} elseif($do == 'delete') {
		//delete member page
		echo "<h1 class = 'text-center'>Delete Member</h1>";
		echo "<div class='container'>";
			$userid =
				isset($_GET['userid']) && is_numeric($_GET['userid'])?
				intval($_GET['userid']):
				0;
			$check = checkItem('userid', 'users', $userid);
			if($check > 0){
				$deleteUserQuery = "Delete FROM users WHERE userID = :theuser";
				$stmt = $conn->prepare($deleteUserQuery);
				$stmt->bindParam("theuser", $userid);
				$stmt->execute();
				//Success message
				$msg = "<div class='alert alert-success'>"
					. $stmt->rowCount()
					. " Record deleted!"
					. "</div>";
				redirectHome($msg,'back');
			} else {
				$msg = "<div class = 'alert alert-danger'>this id is not found</div>";
				redirectHome($msg);
			}
		echo "</div>";
	} elseif($do == 'activate'){
				//delete member page
		echo "<h1 class = 'text-center'>Activate Member</h1>";
		echo "<div class='container'>";
			$userid =
				isset($_GET['userid']) && is_numeric($_GET['userid'])?
				intval($_GET['userid']):
				0;
			$check = checkItem('userid', 'users', $userid);
			if($check > 0){
				$activateUserQuery = "UPDATE users SET regstatus = 1 WHERE userID = :theuser";
				$stmt = $conn->prepare($activateUserQuery);
				$stmt->bindParam("theuser", $userid);
				$stmt->execute();
				//Success message
				$msg = "<div class='alert alert-success'>"
					. $stmt->rowCount()
					. " Record Activated!"
					. "</div>";
				redirectHome($msg);
			} else {
				$msg =
				"<div class = 'alert alert-danger'>this id is not found!</div>";
				redirectHome($msg);
			}
		echo "</div>";
	}

		include $tpl . 'Footer.php';

	} else {
		header('location:index.php');
		exit();
	}
	ob_end_flush();