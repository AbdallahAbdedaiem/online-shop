<?php
  /*
  ==========================Manage comments page
  ===you can < Approve | Edit | Delete >
  =========================================
  */
  ob_start();
  session_start();
  $pageTitle = 'Comments';
  if (isset($_SESSION['username'])){
    include 'init.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    if($do == 'manage') {
      //manage comments page

      $fetchCommQuery =
      'SELECT comments.*,items.name AS item,users.username AS user
      FROM comments
      INNER JOIN items ON
      items.id = comments.item_id
      INNER JOIN users ON
      users.userID = comments.user_id
      ORDER BY id DESC';
      $stmt = $conn->prepare($fetchCommQuery);
      $stmt->execute();
      $rows = $stmt->fetchAll();

      ?>

      <h1 class="text-center my-5">Manage Comments
      </h1>
      <div class="container">
        <?php if(!empty($rows)){?>
        <div class='table-responsive'>
          <table
          class="main-table text-center table table-bordered">
                      <tr>
                        <td>#ID</td>
                        <td>comment</td>
                        <td>Item</td>
                        <td>User</td>
                        <td>Date</td>
                        <td>Control</td>
                      </tr>
              <?php
                foreach($rows as $row){
                  echo "<tr>";
                  echo "<td>" .
                  $row['id'] .
                  "</td>";
                  echo "<td>" .
                   $row['comment'] .
                   "</td>";
                  echo "<td>" .
                   $row['item'] .
                   "</td>";
                   echo "<td>" .
                   $row['user'] .
                   "</td>";
                   echo "<td>" . $row['comment_date'] . "</td>";
                   echo
                   '<td>
                    <a href="comments.php?do=edit&commentid='
                    . $row['id'] .
                    '" class="abd-btn-xs btn btn-success"><i class = "fa fa-edit"></i>Edit</a>
                    <a href="comments.php?do=delete&commentid='
                    . $row['id'] .
                    '" class="abd-btn-xs btn btn-danger confirm"><i class = "fa fa-trash"></i>Delete</a>';
                  if($row['status'] == 0){
                    echo
                    '<a href="comments.php?do=approve&commentid='
                    . $row['id'] .'" class="abd-btn-xs btn btn-info activate"><i class = "fa fa-check"></i>Approve</a>';
                    }
                   echo '</td>';
                  echo "</tr>";
                }

                      ?>
                    </table>
                  </div>
                <?php
                  } else {
                      echo
                        "<p class='py-4 alert alert-info text-center'>
                          There's no comments for now.
                        </p>";
                  }
                 ?>
                </div>

    <?php }

    elseif($do == 'add'){
    //add members page
    ?>
      <h1 class="mt-3 text-center">Add New Member</h1>
      <div class="container">
        <form class="mt-5" action='?do=insert' method="POST">
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

      foreach ($formErrors as $error) {
        echo "<div class='alert alert-danger'>" .
          $error  .
          "</div>";
      }
      //check if there is no errors
      if (empty($formErrors)){

        /*abd: check if user already exists in database*/
        $check = checkItem("username", "users",$user);
        if($check == 1){
          $msg =  "<div class = 'alert alert-danger'>Sorry! This username is already taken</div>";
          redirectHome($msg, 'back');
        } else {
        //insert user into database
        $addUserQuery = "INSERT INTO users(username,password,email,fullname,regstatus,regdate)
        VALUES(:zuser,:zpass,:zmail,:zname,1,now())";
        /*abd:
        **u can also use this syntax with indexed array in execute
        +++++++++++++++++++++
        $addUserQuery = "INSERT INTO users(username,password,email,fullname)
        VALUES(?,?,?,?)"
        +++++++++++++++++++++
        */
        $stmt = $conn->prepare($addUserQuery);
        $stmt->execute(array(
          'zuser' => $user,
          'zpass' => $hashPass,
          'zmail' => $email,
          'zname' => $name
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
    $commid =
      isset($_GET['commentid']) && is_numeric($_GET['commentid'])?
      intval($_GET['commentid']):
      0;

    $query = "SELECT *
        FROM comments
        WHERE id = ?
        /*abd:
        **limit 1 isn't needed inn this case
        */
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute(
      array($commid)
    );
    /*return as array*/
    $row = $stmt->fetch();
    $count = $stmt->rowCount();
    if($count > 0){

      ?>

      <h1 class="mt-3 text-center">Edit Comment</h1>
      <div class="container">
        <form class="mt-5" action='?do=update' method="POST">
          <input type='hidden' name="commentid"
          value='<?php echo $commid;?>'
          >
          <!--username field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Comment</label>
            <div class="col-sm-8">
              <textarea name= 'comment'
              class="form-control" required='required'><?php echo $row['comment']?></textarea>
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
    echo "<h1 class='text-center'>Update Comment</h1>";
    echo "<div class='container'>";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      //get variables from the form
      $id = $_POST['commentid'];
      $comment = $_POST['comment'];
      $updateCommQuery = "UPDATE comments SET
        comment = ?
        WHERE id = ?";
      $stmt = $conn->prepare($updateCommQuery);
      $stmt->execute(array($comment ,$id));
      $msg =  "<div class='alert alert-success'>" . $stmt->rowCount() . " Record updated!" . "</div>";
      redirectHome($msg, 'back');


    } else {
      $msg =  "<div class = 'alert alert-danger'>you can't access this page directly</div>";
      redirectHome($msg, 'back');
    }
    echo '</div>';
  } elseif($do == 'delete') {
    //delete comment
    echo "<h1 class = 'text-center'>Delete Comment</h1>";
    echo "<div class='container'>";
      $commid =
        isset($_GET['commentid']) && is_numeric($_GET['commentid'])?
        intval($_GET['commentid']):
        0;
      $check = checkItem('id', 'comments', $commid);
      if($check > 0){
        $deleteCommQuery = "Delete FROM comments WHERE id = :theid";
        $stmt = $conn->prepare($deleteCommQuery);
        $stmt->bindParam("theid", $commid);
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
  } elseif($do == 'approve'){
    //approve comment
    echo "<h1 class = 'text-center'>Approve Comment</h1>";
    echo "<div class='container'>";
      $commid =
        isset($_GET['commentid']) && is_numeric($_GET['commentid'])?
        intval($_GET['commentid']):
        0;
      $check = checkItem('id', 'comments', $commid);
      if($check > 0){
        $approveCommQuery = "UPDATE comments SET status = 1 WHERE id = :thecomm";
        $stmt = $conn->prepare($approveCommQuery);
        $stmt->bindParam("thecomm", $commid);
        $stmt->execute();
        //Success message
        $msg = "<div class='alert alert-success'>"
          . $stmt->rowCount()
          . " Comment Approved!"
          . "</div>";
        redirectHome($msg, 'back');
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