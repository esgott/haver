<?php

if (is_uploaded_file($_FILES['upload']['tmp_name'])) {
	$filePointer = fopen($_FILES['upload']['tmp_name'], 'rb');
	$outputFile = fopen('/home/esgott/public_html/output.txt', 'w');
	if ($filePointer != false){
		$courses = array();
		while (!feof($filePointer)) {
			$line = fgets($filePointer);
			$match = preg_match('/^SUMMARY.*\(([a-zA-Z0-9]+)\).*$/', $line, $matches);
			if ($match != false) {
				array_push($courses, $matches[1]);
			}
		}
		
		$courses = array_unique($courses);
		foreach ($courses as $course) {
			fwrite($outputFile, "$course\n");
		}

		fclose($filePointer);
		fclose($outputFile);
	}
}

?>


