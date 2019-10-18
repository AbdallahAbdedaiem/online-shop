<?php
	/*
	====================================
	====================================
	====================================
	===========   FRONTEND   ===========
	====================================
	====================================
	====================================
	*/


	/*
	**get records(cats) function V1.0
	**Function to retrieve categories from db
	*/
	function getCat() {
		global $conn;
		$getcat = $conn->prepare("SELECT * FROM categories ORDER BY id");
		$getcat->execute();
		$cats = $getcat->fetchAll();
		return $cats;
	}

	/*
	**get ITEMS function V2.0
	**Function to retrieve categories from db
	*/
	function getItems($where, $value,
		$approve = NULL) {
		global $conn;
		$sql  = $approve == NULL ? 'AND approve = 1' : '';
		$getitem = $conn->prepare("SELECT * FROM items WHERE $where = ? $sql ORDER BY id DESC");
		$getitem->execute(array($value));
		$items = $getitem->fetchAll();
		return $items;
	}

	/*
	**check if user is activated(regstatus)
	*/
	function checkUserStatus($user){
		global $conn;
		    $query = "SELECT username,regstatus
          FROM users
          WHERE username = ?
          AND regstatus = 0";
    $stmt = $conn->prepare($query);
    $stmt->execute(
      array($user)
    );

    $status = $stmt->rowCount();

    return $status;
	}

	/*
	**Function check V1.0
	**Function to check if item exists in DB
	**$select = item to select(user,category...)
	**$from = table to select from
	**$value = the value of select(exp: abdallah)
	*/
	function checkItem($select, $from, $value){
		global $conn;
		$query = "SELECT $select FROM $from WHERE $select = ?";
		$statement = $conn->prepare($query);
		$statement->execute(array($value));
		$count = $statement->rowCount();
		return $count;
	}

	/*Function getAll v1.0
	**used to get all records of a table
	*/
	function getAll($table,$where = NULL){
	global $conn;
	$sql  = $where == NULL ? "" : $where;
	$stmt = $conn->prepare("SELECT * FROM $table $sql" );
  $stmt->execute();
  $records = $stmt->fetchAll();
  return $records;
	}

	/*
	====================================
	========   Ultimate getAll   =======
	====================================
	The idea is to create a function that replaces many functions(can be created at the end of a project to be customized depending on the project needs)
	*/
	function ultimateGetAll($field,$table, $orderField, $where = NULL, $ordering = "DESC"){
		global $conn;
		$whereVal  = $where == NULL ? "" : $where;
		$stmt = $conn->prepare("SELECT * FROM $table $whereVal ORDER BY $orderField $ordering" );
	  $stmt->execute();
	  $records = $stmt->fetchAll();
	  return $records;
	}






	/*
	====================================
	====================================
	====================================
	==========    BACKEND    ===========
	====================================
	====================================
	====================================
	*/
	/*
	**title function for page title in case it has var pageTitle or print the default title
	*/
	function getTitle(){
		global $pageTitle;
		if(isset($pageTitle)){
			echo $pageTitle;
		} else {
			echo 'Default';
		}
	}

	/*
	**redirect funcion with params
	**$mesage[error | success | warning]
	**$url: where to go
	**$seconds
	*/
	/*abd: recommended to avoid using bootstrap css classes for exp in functions*/
	function redirectHome($message, $url = null, $seconds = 3) {
		if($url === null) {
			$url = "index.php";
			$link = "Homepage";
		} else {
			/*HTTP_REFERER is undefined cause we're in the same page*/
			$url =
			(isset($_SERVER['HTTP_REFERER']) &&
				$_SERVER['HTTP_REFERER'] !== ''
			)?
			$_SERVER['HTTP_REFERER'] :
			"index.php";
			if($url == 'index.php'){
				$link = 'Homepage';
			} else {
				$link = 'Previous Page';
			}
		}
		echo $message;
		echo "<div class='alert alert-info'>
				You will redirect to " . $link . " in "
				 . $seconds . " seconds.</div>";
		header("refresh:$seconds;url=$url");
		exit();
	}


	/*
	**count number of items V1.0
	**$item: item to count
	**$table: table to count from
	*/

	function countItems($item,$table){
		global $conn;
		$query = "SELECT COUNT($item) FROM $table";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		return $stmt->fetchColumn();
	}

	/*
	**get latest records function V1.0
	**exp: users,items,comments...
	**$select: field to select
	**$table: table to select from
	**$limit: max of records to get
	**$order: the ordering by field
	*/
	function getLatest($select,$table,$order,$limit = 5) {
		global $conn;
		$getstmt = $conn->prepare("SELECT $select FROM $table ORDER BY $order LIMIT $limit");
		$getstmt->execute();
		$rows = $getstmt->fetchAll();
		return $rows;
	}