<?php

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	include 'admin/config.php';
	$sessionUser = '';
	if(isset($_SESSION['user'])){
		$sessionUser = $_SESSION['user'];
	}
	//routes
	$tpl = 'includes/templates/';//template directory
	$lang = 'includes/languages/';
	$func = 'includes/functions/';
	$css = 'layout/css/';//css directory
	$js = 'layout/js/';//js directory
	//include the important files
	include $func . 'functions.php';
	include $lang . 'en.php';
	include $tpl . 'Header.php';
