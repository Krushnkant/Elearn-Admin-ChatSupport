<!DOCTYPE html>
<html>
<head>
	<title>File Upload Example</title>
</head>
<body>
	<?php
	// handle file upload
	if(isset($_POST["submit"])) {
		$target_dir = "uploads/";
		
		// create the 'uploads' directory if it doesn't exist
		if (!file_exists($target_dir)) {
			mkdir($target_dir, 0777, true);
			
			// create the .htaccess file
			$htaccess_content = "Options -Indexes\n";
			$htaccess_content .= "Deny from all\n";
			$htaccess_content .= "Allow from all\n";
			$htaccess_file = $target_dir . ".htaccess";
			file_put_contents($htaccess_file, $htaccess_content);
		}
		
		$target_file = $target_dir . basename($_FILES["file"]["name"]);
		if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
			echo "The file " . basename($_FILES["file"]["name"]) . " has been uploaded.<br>";
			echo "You can access the file at <a href=\"" . $target_file . "\">" . $target_file . "</a>";
		} else {
			echo "Sorry, there was an error uploading your file.";
		}
	}
	?>

	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
		<label for="file">Select a file to upload:</label>
		<input type="file" name="file" id="file"><br><br>
		<input type="submit" name="submit" value="Upload">
	</form>
</body>
</html>
