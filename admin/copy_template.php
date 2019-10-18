<?php
	ob_start();
	session_start();
	$pageTitle = '';
	if (isset($_SESSION['username'])) {

		include 'init.php';
		$do = isset($_GET['do'])?
			$_GET['do'] : 'manage';
		if($do == 'manage') {
			echo 'Welcome!';
		} elseif ($do == 'add') {
		} elseif ($do == 'insert') {
		} elseif ($do == 'edit') {
		} elseif ($do == 'update') {
		} elseif ($do == 'activate') {
		} else {
		}
		include $tpl . 'Footer.php';

	} else {

		header('location: index.php');
		exit();

	}

	ob_end_flush();
	?>