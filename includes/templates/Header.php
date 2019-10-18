	<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php getTitle()?></title>
		<link rel="stylesheet"
		href = '<?php echo $css?>bootstrap.min.css'>
		<link rel="stylesheet"
		href = '<?php echo $css?>font-awesome.min.css'>
		<link rel="stylesheet"
		href = '<?php echo $css?>jquery-ui.min.css'>
		<link rel="stylesheet"
		href = '<?php echo $css?>jquery.selectBoxIt.css'>
		<link rel="stylesheet"
		href = '<?php echo $css?>front.css'>
	</head>
	<!--abd:
	*this is just a method can open body on each page separately-->
	<body>
		<div class="upper-bar">
			<div class="container-fluid" style='overflow: hidden;'>
				<!--*******************-->
				<?php
				if(isset($_SESSION['user'])){ ?>



        <div class="btn-group" role="group" aria-label="Button group with nested dropdown">

          <a href = "profile.php" class="btn btn-secondary">Profile</a>
          <a href = "newad.php" class="btn btn-secondary">New Ad</a>
          <a href = "profile.php#my-ads" class="btn btn-secondary">My Ads</a>
          <a href = "logout.php" class="btn btn-secondary">Log out</a>

          <div class="btn-group" role="group">

            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            	<img class="img-fluid rounded-circle" src = 'https://placekitten.com/300/300' width='30px'/>
              <?php echo $sessionUser;?>
            </button>

            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              <a class="dropdown-item" href="#">Dropdown link</a>
              <a class="dropdown-item" href="#">Dropdown link</a>
            </div>

          </div>

        </div>



			<?php
				} else {
			?>
				<a href='login.php' class="">
					<span class="pull-right">Log in/Sign up</span></a>
			<?php } ?>
			</div>
		</div>
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-fixed-top">
			<div class="container">
			  <a href = "index.php" class="navbar-brand">	Home
			  </a>
			  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			    <span class="navbar-toggler-icon"></span>
			  </button>
			  <div class="collapse navbar-collapse" id="app-nav">
			    <ul class="navbar-nav ml-auto">
			    	<!--***********************-->
			    	<!--***********************-->
	<?php
  	foreach (ultimateGetAll("*","categories", 'id', 'WHERE parent_cat = 0') as $cat) {
  		echo
  			'<li class="nav-item">
    			<a class="nav-link" href="categories.php?id='.$cat['id'].'">'.$cat['name'].
          '</a>
  			</li>';
  	}
  ?>
			    </ul>
			  </div>
			</div>
		</nav>
