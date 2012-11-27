<?php
require_once('conn.php');

if(isset($_POST['user']))
{
$dbConn = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);



	$user = $_POST['user'];
	$pass = $_POST['pass'];
	
	$slump = time() . "gubben" . $user;	
	$salt = hash('sha256', $slump);
	$pass = hash('sha256', $salt.$pass);
	$pass = $salt.$pass;

$user = mysqli_real_escape_string($dbConn, $user);
$user = htmlspecialchars($user);

$sql = "INSERT INTO users (username, password) VALUE ('$user', '$pass')";

mysqli_query($dbConn, $sql);
	
}

?>
<html>
<head>
<title>Skapa användare</title>


</head>
<body>
	<form method="post" action="createuser.php">
		Användarnamn:<input type="text" name="user"> <br>
		Lösenord:<input type="password" name="pass"><br>
		<input type="submit" value="Skapa">
	</form>
</body>
</html>