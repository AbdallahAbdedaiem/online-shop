<?php
  ob_start();
  session_start();
  include 'init.php';
  ?>

  <div class="container category">
    <?php
    if (isset($_GET['name'])){
    $tag = $_GET['name'];
    echo '<h1 class="my-5 text-center">
      ' . $tag . '
    </h1>';
    $tagItems = ultimateGetAll(
                '*',
                'items',
                'id',
                "WHERE tags LIKE '%$tag%' AND approve = 1"
              );

    ?>
    <div class="row">
    <?php
      foreach ($tagItems as $item) {?>
      <div class="col-10 offset-1 col-sm-6 offset-sm-0 col-md-3">
        <div class="card mb-3 item-box">
          <span class="price-tag text-center">
            <?php echo $item['price'];?>
          </span>
          <img class="card-img-top image-fluid" style='width:100%;height: auto;' src="https://www.placehold.it/150/150" alt="Card image cap">
          <div class="card-body">
            <h5 class="card-title">
              <a href="items.php?itemid=<?php echo $item['id'];?>"><?php echo $item['name']?></a>
            </h5>
            <p class="card-text"><?php echo $item['description']?></p>
            <div class="date">
                    <?php echo $item['add_date'];?>
            </div>
          </div>
        </div>
      </div>
      <?php }
    ?>
    </div>
    <?php
      } else {?>
        <div class=" my-5 alert alert-info">
          You must enter a tag name
        </div>
    <?php
      }
    ?>

  </div>



<?php
  include $tpl . 'Footer.php';
  ob_end_flush();
  ?>