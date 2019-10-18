<?php

	include 'config.php';
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
	/*abd
	**include navbar in all pages except the pages with noNavbar property
	*/
	if(!isset($noNavbar)){
		include $tpl . 'Navbar.php';
	}