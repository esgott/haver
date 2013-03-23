<?php

if (is_uploaded_file($_FILES['upload']['tmp_name'])) {
	$filePointer = fopen($_FILES['upload']['tmp_name'], "rb");
	if ($filePointer != false){
		while (!feof($filePointer)) {
			fgets($file);
		}
		
		fclose($file);
	}
}

?>


