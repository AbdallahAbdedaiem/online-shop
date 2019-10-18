<?php
	ob_start();
	session_start();
	$pageTitle = 'Categories';
	if (isset($_SESSION['username'])) {
		include 'init.php';
		$do = isset($_GET['do'])?
			$_GET['do'] : 'manage';
		if($do == 'manage') {
			$sort = 'ASC';
			/*note: u can use `key word` to disable keyword sql
			**exp: `FROM`
			*/
			$sort_array = array("ASC", "DESC","asc","desc");
			if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)){
				$sort = $_GET['sort'];
			}
			$fetchCatsQuery =
			'SELECT * FROM categories WHERE parent_cat = 0 ORDER BY ordering ' . $sort;
			$stmt = $conn->prepare($fetchCatsQuery);
			$stmt->execute();
			$cats = $stmt->fetchAll();?>
		<h1 class="text-center my-5">Manage Categories</h1>
		<div class="container categories">
			<a href = '?do=add' class="add-el btn btn-primary"><i class="fa fa-plus"></i>New Category</a>
			<?php if (!empty($cats)){?>
			<div class="card mb-4">
				<div class="card-title">
					<h4>
						<i class="fa fa-edit"></i>
						Manage Categories
						<div class='options pull-right'>
							<i class="fa fa-sort"></i>
							Ordering:
							[<a class="<?php
							if($sort =='ASC' || $sort == 'asc')
								echo "active";
							?>"
							href="?sort=asc">Asc</a> |
							<a class="<?php
							if($sort =='DESC' || $sort == 'desc')
								echo "active";
							?>"
							href="?sort=desc">Desc</a>]
							<i class="fa fa-eye"></i>View: [<span class="active" data-view = 'full'>Full</span> |
							<span data-view = 'classic'>Classic</span>]
						</div>
					</h4>

				</div>
		  	<div class="card-body">
		  		<?php
		  		foreach ($cats as $cat) {
		  			echo "<div class = 'cat'>";
		  				echo "<div class = 'hidden-btns'>";
		  					echo "<a href = '?do=edit&catid=" . $cat['id'] . "' class='btn btn-sm btn-primary'><i class = 'fa fa-edit'></i>Edit</a>";
		  					echo "<a href = '?do=delete&catid=" . $cat['id'] . "' class='confirm btn btn-sm btn-danger'><i class = 'fa fa-close'></i>Delete</a>";
		  				echo "</div>";
			  			echo "<h5>" . $cat['name']
			  			 . "</h5>";
			  			echo "<hr>";
			  			/****************/
			  			echo "<div class='full-view'>";
			  			echo "<p>";
			  			if($cat['description'] == ''){
			  				echo 'This Category has no description!';
			  			} else {
			  				echo $cat['description'];
			  			}
			  			echo "</p>";
			  			if ($cat['visibility'] == 1) {
			  				echo
			  				"<span class='vis'>
			  					<i class='fa fa-eye-slash'></i> Hidden
			  				</span>";
			  			}
			  			if ($cat['allow_comment'] == 1) {
			  				echo "<span class='comm'><i class='fa fa-close'></i> Comment Disabled</span>";
			  			}
			  			if ($cat['allow_ads'] == 1) {
			  				echo "<span class='ads'><i class='fa fa-close'></i> Ads Disabled</span>";
			  			}
			  		/*start get child categories*/
		  			$childCats =
		  			ultimateGetAll("*", "categories", 'id', "WHERE parent_cat = {$cat['id']}");
		  			if(!empty($childCats)){
		  				echo '<h6>Child Categories</h6>';
		  				echo '<ul class="list-unstyled ml-3">';
		  				foreach ($childCats as $child) {
		  				echo '<li>' . "<a href = '?do=edit&catid=" . $child['id'] . "'>" .
		  				 $child['name'] . "</a>" .
		  				 "<a href = '?do=delete&catid=" . $child['id'] . "' class='confirm'>
		  				 	<i class = 'fa fa-trash'></i>
		  				 </a>
		  				 </li>";
		  				}
		  				echo '</ul>';
		  			}
		  			/*end get child categories*/
			  			echo "</div>";
			  			/**************/
		  			echo "</div>";
		  			echo "<hr>";
		  		}
		  		?>
		  	</div>
			</div>
		<?php } else {
			echo
				"<p class='py-4 alert alert-info text-center'>
					There's no categories for now. You can add one!
				</p>";
		}?>
		</div>

		<?php
		} elseif ($do == 'add') {?>
<!--****start add category****-->
			<h1 class="mt-3 text-center">Add New Category</h1>
			<div class="container">
				<form class="mt-5" action='?do=insert' method="POST">
					<!--name field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">name</label>
						<div class="col-sm-8">
							<input
							type="text" name = 'name' class="form-control" autocomplete="off"
							required='required'
							placeholder = 'category name'>
						</div>
					</div>
					<!--description field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">description</label>
						<div class="col-sm-8">
							<input type="text" name = 'description'
							class="form-control"
							placeholder="describe the category">
						</div>
					</div>
					<!--ordering field-->
					<div class="form-group row">
						<label
						class="col-sm-2 control-label text-center">Ordering</label>
						<div class="col-sm-8">
							<input
							type="text" name = 'ordering' class="form-control"
							placeholder = 'number to arrange the category'>
						</div>
					</div>
					<!--parent_cat field-->
					<div class="form-group row">
						<label
						class="col-sm-2 control-label text-center">Parent</label>
						<div class="col-sm-8">
							<select name='parent'>
								<option value="0">None</option>
								<?php
								$bigCats = ultimateGetAll('*',"categories",
									'id',
									"WHERE parent_cat = 0",
									"ASC");
								foreach ($bigCats as $cat) {
									echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
								}

								?>
							</select>
						</div>
					</div>
					<!--visibility field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Visible</label>
						<div class="col-sm-8">
							<div>
								<input id='vis-yes' type="radio" name = 'visibility' value = "0" checked/>
								<label for = "vis-yes">Yes</label>
							</div>
							<div>
								<input id = "vis-no" type="radio" name = 'visibility' value = "1"/>
								<label for = "vis-no">No</label>
							</div>
						</div>
					</div>
					<!--commenting field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Allow Comments</label>
						<div class="col-sm-8">
							<div>
								<input id='comm-yes' type="radio" name = 'commenting' value = "0" checked/>
								<label for = "comm-yes">Yes</label>
							</div>
							<div>
								<input id = "comm-no" type="radio" name = 'commenting' value = "1"/>
								<label for = "comm-no">No</label>
							</div>
						</div>
					</div>
					<!--Ads field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Allow Ads</label>
						<div class="col-sm-8">
							<div>
								<input id='ads-yes' type="radio" name = 'ads' value = "0" checked/>
								<label for = "ads-yes">Yes</label>
							</div>
							<div>
								<input id = "ads-no" type="radio" name = 'ads' value = "1"/>
								<label for = "ads-no">No</label>
							</div>
						</div>
					</div>

					<!--submit-->
					<div class="form-group row">
						<div class="col-sm-8 offset-sm-2">
							<input class="btn btn-primary" type="submit" value = 'Add Category'>
						</div>
					</div>
				</form>
			</div>
<!--****end add category****-->
		<?php
		} elseif ($do == 'insert') {

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			echo "<div class='container'>";
			echo "<h1 class='text-center'>Insert Category</h1>";

			$name = $_POST['name'];
			$desc = $_POST['description'];
			$parent = $_POST['parent'];
			$order = $_POST['ordering'];
			$visible = $_POST['visibility'];
			$comment = $_POST['commenting'];
			$ads = $_POST['ads'];


			/*abd: check if category already exists in database*/
			$check =
			 checkItem("name", "categories",$name);
			if($check == 1){
				$msg =  "<div class = 'alert alert-danger'>Sorry! This category exists!</div>";
				redirectHome($msg, 'back');
			} else {
			//insert user into database
			$addCatQuery = "INSERT INTO
			categories(name,description,parent_cat,ordering,visibility,allow_comment,allow_ads)
			VALUES(:zname,:zdesc,:zparent,:zorder,:zvisible,:zcomment,:zads)";
			$stmt = $conn->prepare($addCatQuery);
			$stmt->execute(array(
				'zname'    => $name,
				'zdesc'    => $desc,
				'zparent'  => $parent,
				'zorder'   => $order,
				'zvisible' => $visible,
				'zcomment' => $comment,
				'zads'     => $ads
				)
			);
			$msg = "<div class='alert alert-success'>" . $stmt->rowCount() . " Record inserted!" . "</div>";
			redirectHome($msg, 'back');
			}


		} else {
			echo "<div class = 'container'>";
			$msg =  '<div class = "alert alert-danger>
			"you can\'t access this page directly</div>';
			redirectHome($msg, 'back',4);
			echo "</div>";
		}
		echo '</div>';

		} elseif ($do == 'edit') {
		//edit page
		$catid =
			isset($_GET['catid']) && is_numeric($_GET['catid'])?
			intval($_GET['catid']):
			0;

		$query = "SELECT *
				FROM categories
				WHERE id = ?";
		$stmt = $conn->prepare($query);
		$stmt->execute(
			array($catid)
		);
		$cat = $stmt->fetch();
		$count = $stmt->rowCount();
		if($count > 0){

			?>

<!--****start edit category****-->
			<h1 class="mt-3 text-center">Edit Category</h1>
			<div class="container">
				<form class="mt-5" action='?do=update' method="POST">
					<input type="hidden" name="catid" value = '<?php echo $catid;?>'>
					<!--name field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">name</label>
						<div class="col-sm-8">
							<input
							type="text" name = 'name' class="form-control"
							required='required'
							placeholder = 'category name'
							value = '<?php echo $cat['name']?>'>
						</div>
					</div>
					<!--description field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">description</label>
						<div class="col-sm-8">
							<input type="text" name = 'description'
							class="form-control"
							placeholder="describe the category"
							value = "<?php echo $cat['description']?>">
						</div>
					</div>
					<!--parent_cat field-->
					<div class="form-group row">
						<label
						class="col-sm-2 control-label text-center">Parent</label>
						<div class="col-sm-8">
							<select name='parent'>
								<option value="0">
								None
							</option>
							<?php
								$bigCats = ultimateGetAll(
									'*',
									"categories",
									'id',
									"WHERE parent_cat = 0",
									"ASC");
								foreach ($bigCats as $C) {
									echo "<option value='" . $C['id'] . "'";
								if($cat['parent_cat'] == $C['id']){
									echo " selected";
								}
								echo ">" . $C['name'] . "</option>";
								}

							?>
							</select>
						</div>
					</div>
					<!--ordering field-->
					<div class="form-group row">
						<label
						class="col-sm-2 control-label text-center">Ordering</label>
						<div class="col-sm-8">
							<input
							type="text" name = 'ordering' class="form-control"
							placeholder = 'number to arrange the category'
							value = "<?php
								echo $cat['ordering']?>">
						</div>
					</div>
					<!--visibility field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Visible</label>
						<div class="col-sm-8">
							<div>
								<input id='vis-yes' type="radio" name = 'visibility' value = "0"
								<?php
									if($cat['visibility'] == 0) {
										echo 'checked';
									}
								?>/>
								<label for = "vis-yes">Yes</label>
							</div>
							<div>
								<input id = "vis-no" type="radio" name = 'visibility' value = "1"
								<?php
									if($cat['visibility'] == 1) {
										echo 'checked';
									}
								?>
								/>
								<label for = "vis-no">No</label>
							</div>
						</div>
					</div>
					<!--commenting field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Allow Comments</label>
						<div class="col-sm-8">
							<div>
								<input id='comm-yes' type="radio" name = 'commenting' value = "0"
								<?php
									if($cat['allow_comment'] == 0) {
										echo 'checked';
									}
								?>
								/>
								<label for = "comm-yes">Yes</label>
							</div>
							<div>
								<input id = "comm-no" type="radio" name = 'commenting' value = "1"
								<?php
									if($cat['allow_comment'] == 1) {
										echo 'checked';
									}
								?>
								/>
								<label for = "comm-no">No</label>
							</div>
						</div>
					</div>
					<!--Ads field-->
					<div class="form-group row">
						<label class="col-sm-2 control-label text-center">Allow Ads</label>
						<div class="col-sm-8">
							<div>
								<input id='ads-yes' type="radio" name = 'ads' value = "0"
								<?php
									if($cat['allow_ads'] == 0) {
										echo 'checked';
									}
								?>
								/>
								<label for = "ads-yes">Yes</label>
							</div>
							<div>
								<input id = "ads-no" type="radio" name = 'ads' value = "1"
								<?php
									if($cat['allow_ads'] == 1) {
										echo 'checked';
									}
								?>
								/>
								<label for = "ads-no">No</label>
							</div>
						</div>
					</div>

					<!--submit-->
					<div class="form-group row">
						<div class="col-sm-8 offset-sm-2">
							<input class="btn btn-primary" type="submit" value = 'Save Changes'>
						</div>
					</div>
				</form>
			</div>
<!--****end edit category****-->



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
		echo "<h1 class='text-center'>update Category</h1>";
		echo "<div class='container'>";
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $_POST['catid'];
			$name = $_POST['name'];
			$desc = $_POST['description'];
			$parent = $_POST['parent'];
			$order = $_POST['ordering'];
			$visible = $_POST['visibility'];
			$comm = $_POST['commenting'];
			$ads = $_POST['ads'];


			//update database
			$updateUserQuery = "UPDATE categories SET
				name = ?,
				description = ?,
				parent_cat = ?,
				ordering = ?,
				visibility = ?,
				allow_comment = ?,
				allow_ads = ?
				WHERE id = ?";
			$stmt = $conn->prepare($updateUserQuery);
			$stmt->execute(
				array(
					$name, $desc,
					$parent, $order,
					$visible, $comm,
					$ads, $id
				)
			);
			$msg =  "<div class='alert alert-success'>" . $stmt->rowCount() . " Category updated!" . "</div>";
			redirectHome($msg, 'back');


		} else {
			$msg =  "<div class = 'alert alert-danger'>You can't access this page directly</div>";
			redirectHome($msg, 'back');
		}
		echo '</div>';

		} else if($do == 'delete') {
		//delete category page
		echo "<h1 class = 'text-center'>Delete Category</h1>";
		echo "<div class='container'>";
			$catid =
				isset($_GET['catid']) && is_numeric($_GET['catid'])?
				intval($_GET['catid']):
				0;
			$check = checkItem('id', 'categories', $catid);
			if($check > 0){
				$deleteCatQuery = "Delete FROM categories WHERE id = :thecat";
				$stmt = $conn->prepare($deleteCatQuery);
				$stmt->bindParam("thecat", $catid);
				$stmt->execute();
				//Success message
				$msg = "<div class='alert alert-success'>"
					. $stmt->rowCount()
					. " Category deleted!"
					. "</div>";
				redirectHome($msg,'back');
			} else {
				$msg = "<div class = 'alert alert-danger'>this id is not found</div>";
				redirectHome($msg);
			}
		echo "</div>";
		} else {

		}
		include $tpl . 'Footer.php';

	} else {

		header('location: index.php');
		exit();

	}

	ob_end_flush();
	?>