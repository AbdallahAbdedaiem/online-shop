<?php
  ob_start();
  session_start();
  $pageTitle = 'Login';
  if(isset($_SESSION['user'])){
    header('location:index.php');
    exit();
  }
  include 'init.php';

  //check HTTP POST Request
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['login'])){
      $user = $_POST['username'];
      $pass = $_POST['password'];
      $hashedPass = sha1($pass);

      $query = "SELECT userID
            FROM users
            WHERE username = ?
            AND password = ?
            LIMIT 1";
      $stmt = $conn->prepare($query);
      $stmt->execute(
        array($user,$hashedPass)
      );

      $count = $stmt->rowCount();
      if($count > 0){
        $_SESSION['user'] = $user;
        $get = $stmt->fetch();
        $_SESSION['uid'] = $get['userID'];
        header('location: index.php');
        exit();
      }
    } else {
      //Sign up process
      $formErrors = array();

      $username = $_POST['username'];
      $full = $_POST['fullname'];
      $password = $_POST['password'];
      $passwordvalid = $_POST['passvalid'];
      $email = $_POST['email'];
      //username check
      if(isset($username)){
        $filteredUsername = filter_var($username, FILTER_SANITIZE_STRING);
        if(strlen($filteredUsername) < 4){
          $formErrors[] = 'Username must be 4 characters or more!';
        }
      }

      //username check
      if(isset($full)){
        $filteredFull = filter_var(
          $full, FILTER_SANITIZE_STRING);
        if(strlen($filteredFull) < 6){
          $formErrors[] = 'Full name must be 6 characters or more!';
        }
      }

      //password check
      if(isset($password) && isset($passwordvalid) ){
        if(empty($password)) {
          $formErrors[] = 'Password can\'t be empty string';
        }
        if(sha1($password) !== sha1($passwordvalid)){
          $formErrors[] = 'Password and Password validation should be the same';
        }
      }

      //email check
      if(isset($email)){
        $filteredEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(filter_var($filteredEmail, FILTER_VALIDATE_EMAIL) != true){
          $formErrors[] = 'Please enter a valid email';
        }
      }


      //check if there is no errors
      if (empty($formErrors)){

        /*abd: check if user already exists in database*/
        $check = checkItem("username", "users",
          $username);
        if($check == 1){
          $formErrors[] = 'Sorry! This username is already taken';
        } else {
        //insert user into database
        $addUserQuery = "INSERT INTO users(username,fullname,password,email,regstatus,regdate)
        VALUES(:zuser,:zfull,:zpass,:zmail,0,now())";
        $stmt = $conn->prepare($addUserQuery);
        $stmt->execute(array(
          'zuser' => $username,
          'zfull' => $full,
          'zpass' => sha1($password),
          'zmail' => $email,
        )
        );
        //Success message
        $successMsg = 'Congrats! You registered successfully.';
        }
      }//end errors empty if

    }
  }
  ?>


  <div class="container auth-page text-center">
    <h1 class="mt-5">
      <span data-class='login' class="login active">Log In</span>
       |
      <span
      data-class='signup'
      class="signup">Sign Up</span>
    </h1>
    <!--start login form-->
    <form class="login text-center"
    action = '<?php echo $_SERVER['PHP_SELF']; ?>'
    method= "POST">
      <div class="input-container">
        <input class="form-control"
        type="text" name = 'username'
        placeholder="Username" autocomplete="off"
        required='required'/>
      </div>
      <div class="input-container">
        <input class="form-control"
        type = 'password' name = 'password'
        placeholder="Password"
        autocomplete="new-password"
        required='required'/>
      </div>
      <input name='login'  class="btn btn-primary btn-block" type="submit" value ='Log In'>
    </form>
    <!--end login form-->

    <!--start signup form-->
    <form class="signup text-center"
    action = '<?php echo $_SERVER['PHP_SELF'] ?>'
    method= "POST">
      <div class="input-container">
        <!--abd: pattern works with required field
          title refers to the error message shown on input in case it doesn't match the pattern on submit
        -->
        <input
        pattern = ".{4,}"
        title = 'Username must be 4 characters or more'
        class="form-control" type="text"
        name = 'username' required='required'
        placeholder="Username" autocomplete="off"/>
      </div>
      <div class="input-container">
        <input class="form-control"
        pattern = ".{6,}"
        title = 'Fullname must be 6 characters or more'
        type="text" required='required'
        name = 'fullname'
        placeholder="Fullname"/>
      </div>
      <div class="input-container">
        <input class="form-control"
        type="email" required='required'
        name = 'email'
        placeholder="Email"/>
      </div>
      <div class="input-container">
        <!--abd: min-length doesn't work with all browsers-->
        <input class="form-control"
        minlength="4"
        type = 'password' name = 'password' placeholder="Password" required='required'
        autocomplete="new-password"/>
      </div>
      <div class="input-container">
        <input minlength="4"
        class="form-control" required='required'
        type = 'password' name = 'passvalid' placeholder="Password validation"
        autocomplete="new-password"
        />
      </div>
      <input name='signup'  class="btn btn-success btn-block mt-3" type="submit" value ='Sign Up'>
    </form>
    <!--end signup form-->
      <?php
        if(!empty($formErrors)){
          foreach ($formErrors as $error) {
            echo
            "<div class='my-toast error'><p>" . $error . '</p></div>';
          }
        }
        if(isset($successMsg)){
                      echo
            "<div class='my-toast success'><p>" . $successMsg . '</p></div>';
        }
      ?>
  </div>
<?php
  include $tpl . 'footer.php';
  ob_end_flush();
?>