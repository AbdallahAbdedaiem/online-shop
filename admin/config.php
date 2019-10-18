<?php
	/*abd:
	**config is to connect with db and many other settings(debug mode like showing errors)
	**elzero declares as connect cause he use it only to connect to db*/

	/*abd: connect to db*/
	$dsn = 'mysql:host=localhost;dbname=online_shop';
	$user = 'root';
	$pass = '';
	$options =array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
	);

	try {
		$conn = new PDO($dsn, $user, $pass, $options);
		/*abd:activating exception mode to display errors*/
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	catch (PDOException $e){
		echo 'Failed to connect' . $e->getMessage();
	}