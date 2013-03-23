<?php

elgg_register_page_handler('calendar', 'calendar_handler');

function calendar_handler($segments) {
	include elgg_get_plugins_path() . 'haver/pages/calendar.php';
	return true;
}

?>
