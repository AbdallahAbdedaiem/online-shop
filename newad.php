  <?php
  ob_start();
  session_start();
  $pageTitle = 'New Item';
  include 'init.php';
  if(isset($_SESSION['user'])) {
    /*start POST*/
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $name =  filter_var($_POST['name'],FILTER_SANITIZE_STRING);
      $desc =  filter_var($_POST['description'],FILTER_SANITIZE_STRING);
      $price =  filter_var($_POST['price'],FILTER_SANITIZE_NUMBER_INT);
      $made =  filter_var($_POST['made'],FILTER_SANITIZE_STRING);
      $status =  filter_var($_POST['status'],FILTER_SANITIZE_NUMBER_INT);
      $cat =  filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT);
      $tags =  filter_var($_POST['tags'],FILTER_SANITIZE_STRING);
      $formErrors = array();
      //check item name
      if(strlen($name) < 4){
        $formErrors[] = 'Item name should be 4 characters or more';
      }
      //check item desc
      if(strlen($desc) < 4){
        $formErrors[] = 'Item description should be 10 characters or more';
      }
      //check item country
      if(strlen($made) < 2){
        $formErrors[] = 'Item country should be at least 2 characters';
      }
      //check item price
      if(empty($price)){
        $formErrors[] = 'You must give a price to the item';
      }
      //check item status
      if(empty($status)){
        $formErrors[] = 'You must specify the item status';
      }
      //check item category
      if(empty($cat)){
        $formErrors[] = 'You must specify the item category';
      }
            //check if there is no errors
      if (empty($formErrors)){
        //insert item into database
        $addItemQuery = "INSERT INTO items(
            name,description,
            price,made_in,
            status, add_date,
            catid,userid,tags)
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
          'zmember' => $_SESSION['uid'],
          'ztags'   => $tags
          )
        );
        if($stmt){
          $successMsg = 'Item added successfully!';
        }

      }

    }
    /*end POST*/


  ?>
  <h1 class="my-5 text-center">New Ad</h1>
  <div class="create-ad mt-5">

    <div class="container">
      <!--start info-->
      <div class="card mt-3 info">
        <div class="card-header bg-primary">
          <?php echo $pageTitle; ?>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
          <?php
          if(isset($successMsg)){
            echo "<div class='my-toast success'><p>" . $successMsg . '</p></div>';
          }
          ?>

          <form class="add-item mt-4"
          action='
              <?php
                echo $_SERVER['PHP_SELF'];
              ?>'
          method="POST">
          <!--name field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Name</label>
            <div class="col-sm-10">
              <input
              pattern = ".{4,}"
              title="This field require at least 4 characters"
              type="text" name = 'name'
              class="form-control live" autocomplete="off"
              required='required'
              data-class='.live-title'
              placeholder = 'item name'>
            </div>
          </div>
          <!--description field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Description</label>
            <div class="col-sm-10">
              <input
              pattern = ".{10,}"
              title="This field require at least 10 characters"
              type="text" name = 'description'
              class="form-control live" autocomplete="off" required='required'
              data-class='.live-desc'
              placeholder = 'item description'>
            </div>
          </div>
          <!--price field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Price</label>
            <div class="col-sm-10">
              <input
              type="text" name = 'price'
              class="form-control  live" autocomplete="off"
              data-class='.live-price'
              required='required'
              placeholder = 'price of the item'>
            </div>
          </div>
          <!--made_in field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Country</label>
            <div class="col-sm-10">
              <input
              type="text" name = 'made' class="form-control" autocomplete="off"
              required='required'
              placeholder = 'country of the item'>
            </div>
          </div>
          <!--status field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Status</label>
            <div class="col-sm-10">
              <select name="status"
              required="required">
                <option value="">...</option>
                <option value="1">New</option>
                <option value="2">like new</option>
                <option value="3">used</option>
                <option value="4">old</option>
              </select>
            </div>
          </div>
          <!--category field-->
          <div class="form-group row">
            <label class="col-sm-2 control-label text-center">Category</label>
            <div class="col-sm-10">
              <select required="required" name="category">
                <option value="">...</option>
                <?php
                $cats = getAll("categories");
                foreach ($cats as $cat) {
                  echo
                  "<option value='"
                  . $cat['id'] . "'>"
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
            <div class="col-sm-10">
              <input
              type="text" name = 'tags'
              class="form-control"
              autocomplete="off"
              placeholder = 'Separate tags with ","'>
            </div>
          </div>
          <!--submit-->
          <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
              <input class="btn btn-primary" type="submit" value = 'Add Item'>
            </div>
          </div>
        </form>
            </div>

      <!--start add preview-->
      <div class="col-md-4 live-preview">
        <div class="card mb-3 item-box">
          <span class="price-tag text-center">$
            <span class="live-price">0</span>
          </span>
          <img class="card-img-top image-fluid" style='width:100%;height: auto;' src="https://www.placehold.it/150/150" alt="Card image cap">
          <div class="card-body">
           <h5 class="card-title live-title">Item Name</h5>
            <p class="card-text live-desc">Description here</p>
          </div>
        </div>
      </div>
      <!--end add preview-->

      </div><!--end row-->
          <?php
          if(!empty($formErrors)){
            foreach ($formErrors as $error) {
              echo '<div class="my-toast alert alert-danger">' . $error . '</div>';
            }
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