<?php

gatekeeper();

global $log_file; //TODO remove log file

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
	global $log_file;
	$log_file = fopen('/home/esgott/public_html/output.txt', 'w');
	$file_pointer = fopen($file, 'rb');
	if ($file_pointer != false){

		$courses = get_courses_from_file($file_pointer);

		foreach ($courses as $course_id => $course_name) {
			fwrite($log_file, "$course_id => $course_name\n");
			create_group($course_id, $course_name);
		}

		fclose($log_file);
		fclose($file_pointer);
	}
}

function get_courses_from_file($file_pointer) {
	$courses = array();

	while (!feof($file_pointer)) {
		$line = fgets($file_pointer);
		$match = preg_match('/^SUMMARY:(.*) \(([a-zA-Z0-9]+)\).*$/', $line, $matches);
		if ($match != false) {
			$course_id = $matches[2];
			$course_name = $matches[1];
			$courses[$course_id] = $course_name;
		}
	}

	return $courses;
}

function create_group($id, $name) {
	global $log_file;
	$options = array (
			'metadata_names' => array('course'),
			'metadata_values' => array($id)
	);
	$group = elgg_get_entities_from_metadata($options);

	if ($group) {
		$group = $group[0];
		$guid = $group->guid;
		fwrite($log_file, "found: $guid\n");
	} else {
		fwrite($log_file, "not found, creating new\n");
		$group = new_group($id, $name);
	}

	join_user_to_group($group);
}

function new_group($id, $name) {
	$group = new ElggGroup();
	$group->subtype = 'haver_group';
	$group->access_id = 1;
	$group->name = $name;
	$group->description = $id;
	$group->save();
	$group->course = $id;
	return $group;
}

function join_user_to_group(ElggGroup $group) {
	global $log_file;
	$user = elgg_get_logged_in_user_entity();
	$new_connection = $group->join($user);
	if($new_connection) {
		fwrite($log_file, "Was not a member\n");
	} else {
		fwrite($log_file, "Was already a member\n");
	}
}

?>
