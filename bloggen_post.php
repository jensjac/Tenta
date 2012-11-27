<?php
require_once('conn.php');

$dbConn = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);

if(mysqli_connect_errno())
{
	echo "Det blev fel.";
	exit();
}
if(isset($_POST['postcontent']))
{
global $dbConn;

createThumb ();
$catID = (int) $_POST['catID'];
$content = $_POST['postcontent'];
$image = file_get_contents($_FILES['filen']['tmp_name']);
$image = mysqli_real_escape_string($dbConn, $image);
$thumb = "thumb" . $_FILES['filen']['name'];

$sql = "INSERT INTO userposts (uContent, catID, bild, thumb)
VALUES('$content', $catID, '$image', '$thumb' )";

mysqli_query ($dbConn, $sql);
echo "Inlägg sparat";
}
?>
<html>
<head>
	<title>Inlägg</title>
</head>
<body>
<form action="bloggen_post.php" method="post" enctype="multipart/form-data">
Skriv ditt inlägg<br>
<textarea name="postcontent"></textarea><br>
Lägg till en bild<br>
<input type="file" name="filen"><br>
Välj kategori<br>
<?php getCategory();?>
<input type="submit" value="Skicka">
</form>
<br>
<a href="bloggen.php">Tillbaka till bloggen</a>

</body>
</html>
<?php
	function getCategory()
	{
		global $dbConn;
		
		$sql ="SELECT catID, catName FROM kategori";
		$res = mysqli_query($dbConn, $sql);
		
		while ($row = mysqli_fetch_assoc($res))
		{
			$catID = $row['catID'];
			$catName = $row['catName'];
			echo "<input type='radio' name='catID' value='$catID'>$catName</>";
		}
	}
function createThumb()
{
$image = imagecreatefromjpeg($_FILES['filen']['tmp_name']);

$orgWidth = imagesx ($image);
$orgHeight = imagesy ($image);

$thumbwidth = ceil ( ($orgWidth / $orgHeight) * 100);

$thumb = imagecreatetruecolor($thumbwidth, 100);

imagecopyresampled ($thumb, $image,0,0,0,0, $thumbwidth,100, $orgWidth, $orgHeight);

$thumbname = "thumb" . $_FILES['filen']['name'];

imagejpeg ($thumb, $thumbname, 75);

imagedestroy($thumb);
imagedestroy($image);
}	
?>