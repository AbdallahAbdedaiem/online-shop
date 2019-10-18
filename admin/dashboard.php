<?php
	// start output buffering(store in memory all but header)
	//nothing will be sent when output buffering is opened
	//ob_start should be before session_start
	ob_start();
	//ob_start("ob_gzhandler")content will be compressed
	session_start();
	if (isset($_SESSION['username'])){
		$pageTitle = 'Dashboard';
		include 'init.php';
		$theLatestRegistered =
		getLatest("*", "users", "userID");
		$theLatestItems = getLatest("*", "items", "id");
		?>


	<!--/************
		start page
		*************/-->
	<div class="container text-center home-stats">
		<h1>Dashboard</h1>
		<div class="row">
			<div class="col-lg-3 col-md-6 mb-2">
				<div class="stat st-members">
					<i class="fa fa-users"></i>
					<div class="info">
					Total Members
					<span>
						<a href = 'members.php'>
						<?php echo
						countItems	('userID','users');
						?></a>
					</span>
				</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 mb-2">
				<div class="stat st-pending">
					<i class="fa fa-user-plus"></i>
					<div class="info">
					Pending Members
					<span><a href = 'members.php?do=manage&page=pending'>
					<?php echo
	checkItem('regstatus','users',0)?>
					</a></span>
				</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 mb-2">
				<div class="stat st-items">
					<i class="fa fa-tag"></i>
					<div class="info">
					Total Items
					<span><a href='items.php'><?php echo
						countItems('id','items');
						?></a></span>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 mb-2">
				<div class="stat st-comments">
					<i class="fa fa-comments"></i>
					<div class="info">
					Total Comments
					<span>
						<a href='comments.php'>
							<?php
							echo countItems('id','comments');
							?>
							</a>
					</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--===========================-->
	<div class="container latest">
		<div class="row">
			<div class="col-sm-6">
				<div class="card mb-3">
					<h5 class="card-title">
						<i class="fa fa-users"></i>Latest registered users
						<i class="fa fa-minus pull-right"></i>
					</h5>
				  <div class="card-body">
				  	<ul class="list-unstyled latest-users">
<?php
if(!empty($theLatestRegistered)){
foreach($theLatestRegistered as $user){
	echo
	"<li>"
		. $user["username"];
		if($user['regstatus'] == 0){
		echo
		"<a href='members.php?do=activate&userid="
		. $user['userID'] ."'>
			<span class = 'btn btn-info pull-right activate'>
			<i class='fa fa-check'></i>Activate
			</span>
		</a>";
		}

		echo "<a href='members.php?do=edit&userid="
		. $user['userID'] ."'>
			<span class = 'btn btn-success pull-right'>
			<i class='fa fa-edit'></i>Edit
			</span>
		</a>";
	echo  "</li>";
	}}
	else {
				echo "<p class='no-records text-center'>No records to show for now!</p>";
	}
	?>
						</ul>
				  	</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="card mb-3">
					<h5 class="card-title"><i class="fa fa-tag"></i>Latest Items<i class="fa fa-minus pull-right"></i></h5>
				  	<div class="card-body">
				  			<ul class="list-unstyled latest-users">
<?php
if(!empty($theLatestItems)){
foreach($theLatestItems as $item){
	echo
	"<li>"
		. $item["name"];
		if($item['approve'] == 0){
		echo
		"<a href='items.php?do=approve&itemid="
		. $item['id'] ."'>
			<span class = 'btn btn-info pull-right activate'>
			<i class='fa fa-check'></i>Approve
			</span>
		</a>";
		}

		echo "<a href='items.php?do=edit&itemid="
		. $item['id'] ."'>
			<span class = 'btn btn-success pull-right'>
			<i class='fa fa-edit'></i>Edit
			</span>
		</a>";
	echo  "</li>";
}}
	else {
		echo "<p class='no-records text-center'>No records to show for now!</>";
	}
?>
						</ul>
				  	</div>
				</div>
			</div>
		</div>
		<!--***********start latest comments***************-->
		 <?php $fetchCommQuery =
      'SELECT comments.*,users.username AS user
      FROM comments
      INNER JOIN users ON
      users.userID = comments.user_id
      ORDER BY comment_date DESC
      LIMIT 5';
      $stmt = $conn->prepare($fetchCommQuery);
      $stmt->execute();
      $comments = $stmt->fetchAll();?>
		<div class="row">
			<div class="col-sm-6">
				<div class="card mb-3">
					<h5 class="card-title">
						<i class="fa fa-comments"></i>Latest Comments
						<i class="fa fa-minus pull-right"></i>
					</h5>
				  <div class="card-body">
				  	<ul class="list-unstyled latest-users comments">
<?php
if(!empty($comments)){
foreach($comments as $comment){
	echo
	"<li class='comm-box'>
		<b>". $comment["user"] . '</b>
		<p>' . $comment['comment'] . "</p>
	</li>";

}} else {
			echo "<p class='no-records text-center'>No records to show for now!</>";
}?>
						</ul>
				  	</div>
				</div>
			</div>
		</div>
		<!--***********end latest comments***************-->
	</div>


	<!--/************
		end page
		*************/-->
		<?php
		include $tpl . 'Footer.php';

	} else {

		header('location:index.php');

		exit();
	}
	//with code below we'll send data stored in memory_get_usage()
	ob_end_flush();
?>
