<?php

$file = $_FILES['upload']['tmp_name'];
$extension = end(explode(".", $_FILES["upload"]["name"]));
$allowed_extensions = array("ics");
$file_type = $_FILES['upload']['type'];
$file_size = $_FILES['upload']['size'];

if (is_uploaded_file($file) &&
		$file_type == 'text/calendar' &&
		$file_size < 100000 &&
		in_array($extension, $allowed_extensions)) {
	process_file($file);
} else {
	register_error(elgg_echo("Failed to upload file. Type: $file_type, size: $file_size, extension: $extension"));
}

function process_file($file) {
	$file_pointer = fopen($file, 'rb');
	$output_file = fopen('/home/esgott/public_html/output.txt', 'w');
	if ($file_pointer != false){

		$courses = get_courses_from_file($file_pointer);

		foreach ($courses as $course) {
			fwrite($output_file, "$course\n");
		}

		fclose($output_file);
		fclose($file_pointer);
	}
}

function get_courses_from_file($file_pointer) {
	$courses = array();

	while (!feof($file_pointer)) {
		$line = fgets($file_pointer);
		$match = preg_match('/^SUMMARY.*\(([a-zA-Z0-9]+)\).*$/', $line, $matches);
		if ($match != false) {
			array_push($courses, $matches[1]);
		}
	}

	return array_unique($courses);
}

?>
