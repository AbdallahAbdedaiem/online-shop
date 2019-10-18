<?php
  ob_start();
  session_start();
  $pageTitle = 'Items';
  if (isset($_SESSION['username'])) {

    include 'init.php';
    $do = isset($_GET['do'])?
      $_GET['do'] : 'manage';
    if($do == 'manage') {
      //manage items page
      $fetchItemsQuery =
      'SELECT
        items.*,
        categories.name AS category,
        users.username AS user
      FROM items
      INNER JOIN
        categories ON
          categories.id = items.catid
      INNER JOIN
        users ON
          users.userID = items.userid
      ORDER BY items.id DESC';
      $stmt = $conn->prepare($fetchItemsQuery);
      $stmt->execute();
      $items = $stmt->fetchAll();
      ?>

      <h1 class="text-center my-5">Manage Items</h1>
      <div class="container">
        <a class="add-el btn btn-primary" href='items.php?do=add'>
        <i class="fa fa-plus"></i>New Item</a>
        <?php if (!empty($items)){?>
        <div class='table-responsive'>
          <table
          class="main-table text-center table table-bordered">
                      <tr>
                        <td>#ID</td>
                        <td>Name</td>
                        <td>Description</td>
                        <td>Price</td>
                        <td>Adding date</td>
                        <td>Category</td>
                        <td>Member</td>
                        <td>control</td>
                      </tr>
              <?php
                foreach($items as $item){
                  echo "<tr>";
                    echo "<td>" .
                      $item['id'] .
                    "</td>";
                    echo "<td>" .
                      $item['name'] .
                    "</td>";
                    echo "<td>" .
                      $item['description'] .
                    "</td>";
                    echo "<td>" .
                      $item['price'] .
                    "</td>";
                    echo "<td>" .
                      $item['add_date'] .
                    "</td>";
                    echo "<td>" .
                      $item['category'] .
                    "</td>";
                    echo "<td>" .
                      $item['user'] .
                    "</td>";
                   echo
                   '<td>
                    <a href="items.php?do=edit&itemid='
                    . $item['id'] .
                    '" class="btn btn-success abd-btn-xs"><i class = "fa fa-edit"></i>Edit</a>
                    <a href="items.php?do=delete&itemid='
                    . $item['id'] .
                    '" class="btn btn-danger confirm abd-btn-xs"><i class = "fa fa-trash"></i>Delete</a>';
                    if($item['approve'] == 0){
                    echo
                    '<a href="items.php?do=approve&itemid='
                    . $item['id'] .'" class="btn btn-info activate abd-btn-xs"><i class = "fa fa-check"></i>Approve</a>';
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
                  There's no Items for now. You can add one!</p>";
                }?>
                </div>
      <?php }
      elseif ($do == 'add') {?>
      <!--****start add category****-->
      <h1 class="mt-3 text-center">Add New Item</h1>
      <div class="container">
        <form class="mt-5" action='?do=insert' method="POST">
          <!--name field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Name</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'name' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'item name'>
            </div>
          </div>
          <!--description field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Description</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'description' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'item description'>
            </div>
          </div>
          <!--price field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Price</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'price' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'price of the item'>
            </div>
          </div>
          <!--made_in field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Country</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'made' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'country of the item'>
            </div>
          </div>
          <!--status field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Status</label>
            <div class="col-sm-8">
              <select name="status">
                <option value="0">...
                </option>
                <option value="1">New
                </option>
                <option value="2">like new
                </option>
                <option value="3">used
                </option>
                <option value="4">old
                </option>
              </select>
            </div>
          </div>
          <!--member field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Member</label>
            <div class="col-sm-8">
              <select name="member">
                <option value="0">...</option><?php
                $stmt = $conn->prepare('SELECT * FROM users');
                $stmt->execute();
                $users = $stmt->fetchAll();
                foreach ($users as $user) {
                  echo
                  "<option value='"
                  . $user['userID'] . "'>"
                      . $user['username']
                  . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <!--category field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Category</label>
            <div class="col-sm-8">
              <select name="category">
                <option value="0">...
                </option>
              <?php
                $stmt = $conn->prepare('SELECT * FROM categories WHERE parent_cat = 0');
                $stmt->execute();
                $cats = $stmt->fetchAll();
                foreach ($cats as $cat) {
                  echo
                  "<option
                    value = '".$cat['id']."'>"
                    .$cat['name'].
                  "</option>";
                  $childCats = ultimateGetAll('*', "categories",
                    'id',
                    "WHERE parent_cat = {$cat['id']}");
                    foreach ($childCats as $child) {
                  echo
                  "<option
                    value = '".$child['id']."'>---"
                    .$child['name'].
                  "</option>";
                    }

                  }
                ?>
              </select>
            </div>
          </div>
          <!--tags field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Tags</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'tags' class="form-control" autocomplete="off"
              placeholder = 'separate tags with ","'>
            </div>
          </div>
          <!--submit-->
          <div class="form-group row">
            <div class="col-sm-8 offset-sm-2">
              <input class="btn btn-primary" type="submit" value = 'Add Item'>
            </div>
          </div>
        </form>
      </div>
    <?php
    } elseif ($do == 'insert') {

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      echo "<div class='container'>";
      echo "<h1 class='text-center'>This is insert page</h1>";
      $name = $_POST['name'];
      $desc = $_POST['description'];
      $price = $_POST['price'];
      $made = $_POST['made'];
      $status = $_POST['status'];
      $member = $_POST['member'];
      $cat = $_POST['category'];
      $tags = $_POST['tags'];

      //validate the form
      $formErrors = array();
      if(empty($name)){
        $formErrors[] = "name can't be empty!";
      }
      if(empty($desc)){
        $formErrors[] = "description can't be empty!";
      }
      if(empty($price)){
        $formErrors[] = "price can't be empty!";
      }
      if (empty($made)){
        $formErrors[] = "country can't be empty!";
      }
      if($status == 0){
        $formErrors[] = "You must choose a status for the item!";
      }
      if($member == 0){
        $formErrors[] = "You must choose the member!";
      }
      if($cat == 0){
        $formErrors[] = "You must choose the category!";
      }

      foreach ($formErrors as $error) {
        echo "<div class='alert alert-danger'>" .
          $error  .
          "</div>";
      }
      //check if there is no errors
      if (empty($formErrors)){
        //insert item into database
        $addItemQuery = "INSERT INTO items(
            name,description,
            price,made_in,
            status, add_date,catid,userid,tags)
          VALUES(
            :zname,:zdesc,
            :zprice,:zmade,
            :zstatus,now(),
            :zcat,:zmember,:ztags)";
        $stmt = $conn->prepare($addItemQuery);
        $stmt->execute(array(
          'zname'   => $name,
          'zdesc'   => $desc,
          'zprice'  => $price,
          'zmade'   => $made,
          'zstatus' => $status,
          'zcat'    => $cat,
          'zmember' => $member,
          'ztags'   => $tags
          )
        );
        //Success message
        $msg = "<div class='alert alert-success'>" . $stmt->rowCount() . " Record inserted!" . "</div>";
        redirectHome($msg, 'back',4);

      }

    } else {
      echo "<div class = 'container'>";
      $msg =  '<div class = "alert alert-danger>
      "you can\'t access this page directly</div>';
      redirectHome($msg);
      echo "</div>";
    }
    echo '</div>';
    } elseif ($do == 'edit') {
    //edit page
    $itemid =
      isset($_GET['itemid']) && is_numeric($_GET['itemid'])?
      intval($_GET['itemid']):
      0;

    $query = "SELECT *
              FROM items
              WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute( array($itemid) );
    /*return as array*/
    $item = $stmt->fetch();
    $count = $stmt->rowCount();
    if($count > 0){

      ?>

      <h1 class="mt-3 text-center">Edit Item</h1>
      <div class="container">
        <form class="mt-5" action='?do=update' method="POST">
          <input type='hidden' name="id"
          value='<?php echo $itemid;?>'>
          <!--name field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Name</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'name' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'item name'
              value='<?php echo $item['name'] ?>'>
            </div>
          </div>
          <!--description field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Description</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'description' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'item description'
              value='<?php echo $item['description'] ?>'>
            </div>
          </div>
          <!--price field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Price</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'price' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'price of the item'
              value='<?php echo $item['price']?>'>
            </div>
          </div>
          <!--made_in field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Country</label>
            <div class="col-sm-8">
              <input
              type="text" name = 'made' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'country of the item'
              value='<?php echo $item['made_in'] ?>'>
            </div>
          </div>
          <!--status field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Status</label>
            <div class="col-sm-8">
              <select name="status">
                <option value="0">...</option>

                <option value="1"
                <?php
                if($item['status']==1){
                  echo 'selected';
                }
                ?>>
                New</option>

                <option value="2"
                <?php
                if($item['status']==2){
                  echo 'selected';
                }
                ?>>
                like new</option>

                <option value="3"
                <?php
                if($item['status']==3){
                  echo 'selected';
                }?>>
                used</option>

                <option value="4"
                <?php
                if($item['status']==4){
                  echo 'selected';
                }
                ?>>
                old</option>
              </select>
            </div>
          </div>
          <!--member field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Member</label>
            <div class="col-sm-8">
              <select name="member">
                <option value="0">...</option><?php
                $stmt = $conn->prepare('SELECT * FROM users');
                $stmt->execute();
                $users = $stmt->fetchAll();
                foreach ($users as $user) {
                  echo
                  "<option value='"
                  . $user['userID']."'";
                  if($user['userID'] ==$item['userid']){
                    echo ' selected ';
                  }
                  echo ">" . $user['username']
                  . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <!--category field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Category</label>
            <div class="col-sm-8">
              <select name="category">
                <option value="0">...</option><?php
                $stmt = $conn->prepare('SELECT * FROM categories');
                $stmt->execute();
                $cats = $stmt->fetchAll();
                foreach ($cats as $cat) {
                  echo
                  "<option value='"
                  . $cat['id'] . "'";
                  if($cat['id'] ==$item['catid']){
                    echo ' selected ';
                  }
                  echo ">"
                      . $cat['name']
                  . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <!--tags field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Tags</label>
            <div class="col-sm-8">
              <input
              value = "<?php echo $item['tags'];?>"
              type="text" name = 'tags' class="form-control" autocomplete="off"
              placeholder = 'separate tags with ","'>
            </div>
          </div>
          <!--submit-->
          <div class="form-group row">
            <div class="col-sm-8 offset-sm-2">
              <input class="btn btn-primary" type="submit" value = 'Save changes'>
            </div>
          </div>
        </form>
      <!--++++++++++++++++++++++++++++++++++++++-->

      <?php
      $fetchCommQuery =
      'SELECT comments.*,users.username AS user
      FROM comments
      INNER JOIN users ON
      users.userID = comments.user_id
      WHERE item_id = ?';
      $stmt = $conn->prepare($fetchCommQuery);
      $stmt->execute(array($itemid));
      $rows = $stmt->fetchAll();
      if(!empty($rows)){

      ?>

      <h3 class="mt-5">
        <?php
        echo 'Manage [ ' . $item['name'] . ' ] Comments';?>
      </h3>

        <div class='table-responsive mb-5'>
          <table
          class="main-table text-center table table-bordered">
                      <tr>
                        <td>comment</td>
                        <td>User</td>
                        <td>Date</td>
                        <td>Control</td>
                      </tr>
              <?php
                foreach($rows as $row){
                  echo "<tr>";
                  echo "<td>" .
                   $row['comment'] .
                   "</td>";
                   echo "<td>" .
                   $row['user'] .
                   "</td>";
                   echo "<td>"
                      . $row['comment_date'] .
                  "</td>";
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
                <?php }?>
    <!--+++++++++++++++++++++-->

      </div>



    <?php
    }
    else {
      echo "<div class = 'container'>";
      $theMsg =  "<div class = 'alert alert-danger'>There is no such id</div>";
      redirectHome($theMsg);
      echo "</div>";
    }
    } elseif ($do == 'update') {
    //update page
    echo "<h1 class='text-center'>This is update page</h1>";
    echo "<div class='container'>";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      //get variables from the form
      $id = $_POST['id'];
      $name = $_POST['name'];
      $desc = $_POST['description'];
      $price = $_POST['price'];
      $made = $_POST['made'];
      $status = $_POST['status'];
      $member = $_POST['member'];
      $cat = $_POST['category'];
      $tags = $_POST['tags'];

     //validate the form
      $formErrors = array();
      if(empty($name)){
        $formErrors[] = "name can't be empty!";
      }
      if(empty($desc)){
        $formErrors[] = "description can't be empty!";
      }
      if(empty($price)){
        $formErrors[] = "price can't be empty!";
      }
      if (empty($made)){
        $formErrors[] = "country can't be empty!";
      }
      if($status == 0){
        $formErrors[] = "You must choose a status for the item!";
      }
      if($member == 0){
        $formErrors[] = "You must choose the member!";
      }
      if($cat == 0){
        $formErrors[] = "You must choose the category!";
      }

      foreach ($formErrors as $error) {
        echo "<div class='alert alert-danger'>" .
          $error  .
          "</div>";
      }
      //check if there is no errors
      if (empty($formErrors)){
        //update database
        $updateItemQuery = "UPDATE items SET
          name = ?,
          description = ?,
          price = ?,
          made_in = ?,
          status = ?,
          catid = ?,
          userid = ?,
          tags = ?
          WHERE id = ?";
        $stmt = $conn->prepare($updateItemQuery);
        $data = array(
                  $name, $desc,
                  $price, $made,
                  $status, $cat,
                  $member, $tags,
                  $id

                );
        $stmt->execute($data);
        $msg =  "<div class='alert alert-success'>" . $stmt->rowCount() . " Record updated!" . "</div>";
        redirectHome($msg, 'back');
      }

    } else {
      $msg =  "<div class = 'alert alert-danger'>you can't access this page directly</div>";
      redirectHome($msg, 'back');
    }
    echo '</div>';
    } elseif ($do == 'delete') {
    //delete item
    echo "<h1 class = 'text-center'>Delete Item</h1>";
    echo "<div class='container'>";
      $itemid =
        isset($_GET['itemid']) && is_numeric($_GET['itemid'])?
        intval($_GET['itemid']):
        0;
      $check = checkItem('id', 'items', $itemid);
      if($check > 0){
        $deleteItemQuery = "Delete FROM items WHERE id = :theitem";
        $stmt = $conn->prepare($deleteItemQuery);
        $stmt->bindParam("theitem", $itemid);
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
    } elseif ($do == 'approve') {
    //approve item
    echo "<h1 class = 'text-center'>Approve Item</h1>";
    echo "<div class='container'>";
      $itemid =
        isset($_GET['itemid']) && is_numeric($_GET['itemid'])?
        intval($_GET['itemid']):
        0;
      $check = checkItem('id', 'items', $itemid);
      if($check > 0){
        $approveItemQuery = "UPDATE items SET approve = 1 WHERE id = :theitem";
        $stmt = $conn->prepare($approveItemQuery);
        $stmt->bindParam("theitem", $itemid);
        $stmt->execute();
        //Success message
        $msg = "<div class='alert alert-success'>"
          . $stmt->rowCount()
          . " Item Approved!"
          . "</div>";
        redirectHome($msg,'back');
      } else {
        $msg =
        "<div class = 'alert alert-danger'>this id is not found!</div>";
        redirectHome($msg);
      }
    echo "</div>";
    }//end approve

    include $tpl . 'Footer.php';

  } else {

    header('location: index.php');
    exit();

  }

  ob_end_flush();
  ?>