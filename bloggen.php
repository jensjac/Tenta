<?php  
require_once('conn.php');	  
if (isset($_GET['ID'])) {
$currentCatID = (int) $_GET['ID'];
if ($currentCatID == 0)
{
	$currentCatID =1;
}

}else {
$currentCatID = 1;
}
$dbConn = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);

if(mysqli_connect_errno())
{
	echo "Det blev fel.";
	exit();
}
if(isset($_POST['content']))
{
$content= safeInsert ($_POST['content']);
$postname = safeInsert ($_POST['postname']);

$catID= (int) $_POST['catID'];

$sql = "INSERT INTO poster (content, postname, posted, catID)
VALUES('$content', '$postname', NOW(), $catID )";

mysqli_query ($dbConn, $sql);
}
if(isset($_POST['user']))
{
$user = $_POST['user'];
$pass = $_POST['pass'];

$user = mysqli_real_escape_string($dbConn, $user);
$user = htmlspecialchars($user);
$pass = mysqli_real_escape_string($dbConn, $pass);

$sql = "SELECT username,password from users where username = '$user'";

$res = mysqli_query($dbConn, $sql);
if($row = mysqli_fetch_assoc($res))
{
	$dbpass = $row['password'];
	$salt = substr($dbpass, 0,64);
	$sentPass = hash('sha256', $salt.$_POST['pass']);
	$skickatmedsalt = $salt . $sentPass;
	echo $skickatmedsalt;
	if ($skickatmedsalt == $dbpass)
	{
		session_start();
		$_SESSION['inloggad'] = "japp";
		header("Location:bloggen_post.php");
		echo "korrekt inloggning";
	}
	else
	{
		echo "felaktig inloggning - fel pass";
		die();
	}
}	
else
{
	echo "fel lösen/användarnamn";
	die();
}
}

?>
<!doctype html>
<html>
<head>
	<title>Bloggen</title>
	<link href="stylesheet.css" rel="stylesheet" type="text/css">
	<style>
	#header	{background:<?php headerPic ($currentCatID) ?>;}	
	</style>
</head>
<body>
<div id="wrapper">
	<div id="header">
	<h1>Jens Blogg</h1>
	</div>
	<div id="category">
	<ul>
	<?php
	displayCategories($currentCatID);
	?>
	</ul>
	</div>
	<div id="sidebar">
	<p>Logga in för att skriva inlägg</p>
	<form method="post" action="bloggen.php" class="login">
	Användarnamn<br>
	<input type="text" name="user">
	<br>
	Lösenord<br>
	<input type="password" name="pass">
	<br>
	<input type="submit" value="Logga In">
	</form>
	<br>
	<a href="createuser.php" class="login">Skapa användare</a>
	
	</div>
	<div id="content">
	<h2>Senaste inlägget</h2>
	<?php
		writePosts ($currentCatID);
	?>
	
	<form class="box" action="bloggen.php?ID=<?php echo $currentCatID;?>" method="post">
	Namn <br>
	<input type="text" name="postname">
	<br>
	Kommentar<br>
	<textarea name="content"></textarea>
	<br>
	<input type="submit" value="Skicka">
	
	<input type="hidden" name="catID" value="<?php echo $currentCatID;?>">
	
	</form>
	
	<?php
		writecomments ($currentCatID);
	?>
	</div>
	<div id="footer">
	Jens "Bloggaren" Jacobsen
	</div>
</div>	
</body>

</html>
<?php
function writecomments($currentCatID)
{
global $dbConn;


$sql = "SELECT posted, content, postname FROM poster WHERE catID=$currentCatID ORDER BY postID DESC";
$res = mysqli_query ($dbConn, $sql);
while ($row = mysqli_fetch_assoc($res))
	{
	$content = $row['content'];
	$posted = $row['posted'];
	$postName = $row ['postname'];
	
		echo "<div class='box'> $content ";
		echo "<p>$postName - $posted </p>";
		echo "</div>";
		
	}
}	

function displayCategories($currentCatID)
{
global $dbConn;
$sql = "SELECT catID, catName FROM kategori";

$res = mysqli_query($dbConn, $sql);
while ($row = mysqli_fetch_assoc($res))
{
$catID = $row['catID'];
$catName = $row['catName'];
echo "<li><a href='bloggen.php?ID=$catID'>$catName</a></li>";

}

}
function writePosts($currentCatID)
{
global $dbConn;


$sql = "SELECT ucontent, thumb FROM userposts WHERE catID=$currentCatID ORDER BY upostID DESC LIMIT 0,1";
$res = mysqli_query ($dbConn, $sql);
while ($row = mysqli_fetch_assoc($res))
	{
	$content = $row['ucontent'];
	$thumb = $row['thumb'];
	
		echo "<div class='posts'><img src='$thumb'><p>$content</p> ";
		echo "</div>";
		
	}
}
function safeInsert($string)
{
global $dbConn;
	$string = htmlentities($string);
	
	$string = mysqli_real_escape_string($dbConn, $string);
	
	return $string;
}
function headerPic ($currentCatID)
{if ($currentCatID == 1)
{
echo "url(spel.jpg)";
}
elseif ($currentCatID == 2)
{
echo "url(film.jpg)";
}
elseif ($currentCatID == 3)
{
echo "url(vadret.jpg)";
}

}	

?>