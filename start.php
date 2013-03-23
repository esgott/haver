<?php

elgg_register_page_handler('calendar', 'calendar_handler');

function calendar_handler($segments) {
	include elgg_get_plugins_path() . 'haver/pages/haver/calendar.php';
	return true;
}

elgg_register_action('calendar_file_upload', elgg_get_plugins_path() . 'haver/actions/calendar_file_upload.php');

?>
