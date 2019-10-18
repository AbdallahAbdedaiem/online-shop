  <?php
  ob_start();
  session_start();
  $pageTitle = 'Profile';
  include 'init.php';
  if(isset($_SESSION['user'])) {
    $getUser = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $getUser->execute(array($sessionUser));
    $infos = $getUser->fetch();
  ?>
  <h1 class="my-5 text-center">My Profile</h1>
  <div class="infos mt-5">
    <div class="container">
      <!--start info-->
      <div class="card mt-3 info">
        <div class="card-header bg-primary">
          My informations
        </div>
        <div class="card-body">
          <ul class="list-unstyled">
          <li>
            <i class="fa fa-unlock-alt fa-fw"></i>
            <span>Name</span>
             : <?php echo $infos['username'] . '<br>';?>
           </li>
          <li>
            <i class="fa fa-envelope-o fa-fw"></i>
            <span>Email</span>
             : <?php echo $infos['email'] . '<br>';?>
           </li>
          <li>
            <i class="fa fa-user fa-fw"></i>
            <span>Full name</span>
             : <?php echo $infos['fullname'] . '<br>';?>
           </li>
          <li>
            <i class="fa fa-calendar fa-fw"></i>
            <span>Registered</span>
             : <?php echo $infos['regdate'] . '<br>';?>
           </li>
          <li>
            <i class="fa fa-tags fa-fw"></i>
            <span>Favourite Category</span> :
          </li>
        </ul>
        <a href='#' class="btn btn-primary mt-3">Edit Info</a>
        </div>
      </div>





      <!--start Ads-->
      <div class="card mt-3 ads" id='my-ads'>
        <div class="card-header bg-primary">
          Ads
        </div>

        <div class="card-body">
          <?php
          $memberAds =
          getItems('userid' ,$infos['userID'],'all');
          if(!empty($memberAds)){
            echo '<div class="row">';
            foreach
              (getItems('userid' ,$infos['userID'],'all') as $item) {
          ?>
            <div class="col-10 offset-1 col-sm-6 offset-sm-0 col-md-3">
              <div class="card mb-3 item-box">
                <?php
                if($item['approve'] == 0){
                  echo '<span class="approve-span">Waiting Approval</span>';
                }

                ?>
                <span class="price-tag text-center">$<?php echo $item['price'];?>
                </span>
                <img class="card-img-top image-fluid" style='width:100%;height: auto;' src="https://www.placehold.it/150/150" alt="Card image cap">
                <div class="card-body">
                  <h5 class="card-title">
                    <a href="items.php?itemid=<?php echo $item['id'];?>"><?php echo $item['name']?></a></h5>
                  <p class="card-text"><?php echo $item['description']?></p>
                  <div class="date">
                    <?php echo $item['add_date'];?>
                  </div>
                </div>
              </div>
            </div>
          <?php
            } echo "</div>";
            } else {
            echo '
            <div class="bg-secondary p-2">
              You didn\'t publish any ads till now, create a <a href="newad.php">New Ad</a>
            </div>';
           }
          ?>
        </div><!--end Ads card-body-->
      </div><!--end Ads card-->
      <!--end Ads-->


      <!--start comments-->
      <div class="card mt-3">
        <div class="card-header bg-primary">
          Latest comments
        </div>

        <div class="card-body">
          <?php

            $fetchCommQuery =
            'SELECT comment
            FROM comments
            WHERE user_id = ?';
            $stmt = $conn->prepare($fetchCommQuery);
            $stmt->execute(array($infos['userID']));
            $comments = $stmt->fetchAll();
            if(!empty($comments)){
              foreach ($comments as $comment) {
                echo '<p>' . $comment['comment'] .
                      '</p>';
              }

            } else {
              echo '<div class="bg-secondary p-2">There\'s no comments to show</div>';
            }
          ?>
        </div>
      </div>
    </div>
  </div>
  <?php
  include $tpl . 'Footer.php';
  } else {
    header('location:login.php');
    exit();
  }
  ob_end_flush();
  ?>