<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
session_start();
if (isset($_GET['logout'])){
	session_destroy();
	header("Location: login.php");
}
if (!isset($_SESSION['user'])){
	header("Location: login.php");
}
if(isset($_GET['post']) && !isset($_GET['replyto'])){
	$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$dbh->beginTransaction();
	$sql = "insert into posts values(:id,:reply,:user,now(),:message)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(":id"=>uniqid(),":user"=>$_SESSION["user"],":reply"=>null,":message"=>$_GET['post']));
	$dbh->commit();
	header("Location: board.php");
}
if(!empty($_GET['replyto'])){
	$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$dbh->beginTransaction();
	$sql = "insert into posts values(:id,:reply,:user,now(),:message)";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(array(":id"=>uniqid(),":user"=>$_SESSION["user"],":reply"=>$_GET['replyto'],":message"=>$_GET['post']));
	$dbh->commit();
	header("Location: board.php");
}
?>
<html>
<head><title><?php if (isset($_SESSION['user'])){ echo $_SESSION['user'];} ?></title>
<link type="text/css" rel="stylesheet" href="board.css"> 
</head>
<body>
<a href="board.php?logout" style="float:right;display:block;"><input type="button" class="btnlgo" value="Logout"/></a><label style="float:left;display:block;margin-right:5px;">Welcome <?php echo $_SESSION['fullname'];?></label>

<form action = "board.php" method="GET">
<div id="myform">
<textarea name = "post" ></textarea>
<input type="submit" class="btnpost" value="New Post"/>
</div>
<?php 
$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$sql = "select id,replyto,postedby,datetime,message,fullname from posts inner join users on postedby=username order by datetime desc";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$res = $stmt->fetchAll();

if (count($res)>0){
	// $id = '?';
	// $id  .= str_repeat(',?', count($res) - 1);
	// $arr = array();
	// foreach ($res as $r){
		// $arr[] = $r["postedby"];
	// }
	// $stmt = $dbh->prepare("select fullname,username from users where username in ($id)");
	// $stmt->execute($arr);
	// $usr = $stmt->fetchAll();
	 $i = 0;
	foreach ($res as $r){ 
		echo "<div class='posts'>";
		echo "<label class='id'>Message Id: ".$r["id"]."</label>";
		echo "<label class='postedby'>User id: ".$r["postedby"]."</label>";
		echo "<label class='fullname'>Posted by: ".$r["fullname"]."</label>";
		if (isset($r["replyto"])){
			echo "<label class='replyto'>Reply to: ".$r["replyto"]."</label>";
		}
		echo "<label class='datetiem'>Date posted: ".$r["datetime"]."</label>";
		echo "<label class='message'>Message: <br>".$r["message"]."</label>";
		echo "<button name = 'replyto' type='submit' class='btnrep' value = '".$r["id"]."' formaction='board.php' >Reply</button>";
		echo "</div>";
		$i++;
	}
}

?>
</form>
</body>
</html>