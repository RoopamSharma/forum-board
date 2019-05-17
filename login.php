<html>
<head>
<title>
Login
</title>
<link type="text/css" rel="stylesheet" href="board.css"> 
</head>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors','On');
if (isset($_SESSION['user'])){
	header('Location: board.php');
}
if (isset($_POST["username"])&&isset($_POST["passwd"])){
	$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$sql = 'select username,fullname from users where username = :user and password = :pass';
	$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$stmt->execute(array(":user"=>$_POST["username"],":pass"=>md5($_POST['passwd'])));
	$usr = $stmt->fetchAll();
	if(sizeof($usr)==1){
		$_SESSION['user'] = $usr[0]['username'];
		$_SESSION['fullname'] = $usr[0]['fullname'];
		header('Location: board.php');
	}
	else{
		echo "Enter the correct username and Password";
	}
}
?>
<body>
<form action="login.php" method="POST">
<div id="login">
<label>Username</label>
<input type="text" name="username"/>
<label>Password</label>
<input type="password" name="passwd"/>
<input class="btnsbt" type="submit" value="Login"/>
</div>
</form>
</body>
</html>