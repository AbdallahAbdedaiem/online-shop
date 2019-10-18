<?php
	//start the session
	ob_start();
	session_start();
	session_unset();//unset the data
	session_destroy();//destroy the session
	header('Location:index.php');
	exit();
	ob_end_flush();