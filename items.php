  <?php
  ob_start();
  session_start();
  $pageTitle = 'show Items';
  include 'init.php';
  $itemid =
  isset($_GET['itemid']) && is_numeric($_GET['itemid'])?
  intval($_GET['itemid']):
  0;

  $query = "SELECT items.*, categories.name AS category,users.username AS user FROM items
            INNER JOIN categories ON items.catid = categories.id
            INNER JOIN users ON items.userid = users.userID
            WHERE items.id = ?
            AND approve = 1
            ";
  $stmt = $conn->prepare($query);
  $stmt->execute(array($itemid));
  $count = $stmt->rowCount();
  if($count > 0){
  $item = $stmt->fetch();
  ?>
  <h1 class="my-5 text-center"><?php echo $item['name']?></h1>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <img src="https://www.placehold.it/150/150"
        alt = '...' class="img-fluid img-thumbnail" style ='width:100%;height:auto'>
      </div>
      <div class="col-md-9 item-info">
          <h2><?php echo $item['name']?></h2>
          <p><?php echo $item['description']?></p>
          <ul class="list-unstyled">
          <li><i class="fa fa-calendar"></i><span>Added Date</span><span><?php echo $item['add_date']?></span></li>
          <li><div><i class="fa fa-money"></i><span>Price</span>$<?php echo $item['price']?></div></li>
          <li><div><i class="fa fa-calendar"></i><span>Made In</span><?php echo $item['made_in']?></div></li>
          <li><div><i class="fa fa-tags"></i><span>Category</span><a href = 'categories.php?id=<?php echo $item['catid']?>&pagename=<?php
            echo $item['category']
            ?>'><?php echo $item['category']?></a></div></li>
          <li><div><i class="fa fa-user"></i><span>Added By</span><a href = '#'><?php echo $item['user'];?></a></div></li>
          <!--start tags-->
          <li>
            <div class = 'item-tags'>
              <i class="fa fa-user"></i>
              <span>Tags</span>
              <?php
              $allTags = explode(',', $item['tags'] );
              foreach ($allTags as $tag) {
                $tag = str_replace(" ", "", $tag);
                $tag = strtolower($tag);
                if(!empty($tag)){
                  echo " <a href = 'tags.php?name={$tag}'>" . $tag . "</a>";
                }
              }
              ?>
          </div></li>
          <!--end tags-->
        </ul>
      </div>
    </div>
    <hr class="custom-hr">
    <?php if(isset($_SESSION['user'])){?>
    <!--start add comment-->
    <div class="row">
      <div class="offset-md-3">
        <div class="add-comment">
          <h3>Add Your Comment!</h3>
          <form method="POST" action = "<?php
           echo $_SERVER['PHP_SELF'] . '?itemid=' . $item['id'] ?>">
            <textarea name='comment' required></textarea>
            <input class="btn btn-primary" type="submit" value = 'Add Comment'>
          </form>
          <?php
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
              $comment =  $_POST['comment'];
              $filteredComment = filter_var($comment,FILTER_SANITIZE_STRING);
              $userid = $_SESSION['uid'];
              $itemid = $item['id'];
              if(!empty($filteredComment)){
                $stmt = $conn->prepare(
                  'INSERT INTO
                  comments(comment, status, comment_date, item_id, user_id)
                  VALUES(:zcomment, 0, now(), :zitem, :zuser)');
                $stmt->execute(array(
                  'zcomment' => $filteredComment,
                  'zitem'    => $itemid,
                  'zuser'    => $userid
                ));
                if($stmt){
                  echo "<div class='alert alert-success'>Comment added</div>";
                }
              }
            }
          ?>
        </div>
      </div>
    </div>
    <!--end add comment-->
    <?php
      } else {
        echo '<a href="login.php">login</a> or <a href="login.php">register</a> to add a comment';
      }
    ?>

    <hr class="custom-hr">
    <?php
      $fetchCommQuery =
      'SELECT comments.*,users.username AS user
      FROM comments
      INNER JOIN users ON
      users.userID = comments.user_id
      WHERE item_id = ? AND status = 1
      ORDER BY id DESC';
      $stmt = $conn->prepare($fetchCommQuery);
      $stmt->execute(array($itemid));
      $comments = $stmt->fetchAll();
      foreach ($comments as $comment) {
    ?>
    <!--start comments-->
    <div class="comment-box">
      <div class="row">
        <div class="col-sm-2 text-center">
          <img src="https://www.placehold.it/150/150" alt = '...'
          class="img-fluid img-thumbnail rounded-circle d-block mx-auto mb-1"
          style ='max-width:100px;height:auto'>
          <?php echo $comment['user']?>
        </div>
        <div class="col-sm-10">
          <p class="lead"><?php echo $comment['comment']?></p>
        </div>
      </div>
    </div>
    <hr class="custom-hr">
    <!--end comments-->

  <?php }
   } else {
    echo
    "<div class='container my-5'>";
    $msg =   "<div class='alert alert-danger'>There's no such ID Or this Item is waiting for approval!</div>";
    redirectHome($msg,'back');
    echo "</div>";
   }
  include $tpl . 'Footer.php';
  ob_end_flush();
  ?>