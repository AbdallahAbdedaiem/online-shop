<?php
	/*
	Categories ->
	[Manage | Edit | Update | Add | Insert | Delete | Stats]
	*/
	//test if he comes with a get action
	$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
	//if it's main page
	if($do == 'Manage') {
		echo 'Welcome you are in Manage category page';
		echo "<a href = '?do=Add'>Add a New Category +</a>";
	} elseif ($do == 'Add') {
		echo 'Welcome you are in Add category page';
	} elseif ($do == 'Insert') {
		echo 'Welcome you are in Insert category page';
	} else {
		echo 'There\'s no page with this name';
	}