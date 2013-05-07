<?php

elgg_register_page_handler('haver', 'haver_page_handler');

function haver_page_handler($segments) {
	if ($segments[0] == 'send') {
		include elgg_get_plugins_path() . 'haver/pages/haver/send_message.php';
		return true;
	} else if ($segments[0] == 'calendar') {
		include elgg_get_plugins_path() . 'haver/pages/haver/calendar.php';
		return true;
	} else if ($segments[0] == 'view_messages') {
		include elgg_get_plugins_path() . 'haver/pages/haver/view_messages.php';
		return true;
	}
	return false;
}

elgg_register_action('haver/calendar_file_upload', elgg_get_plugins_path() . 'haver/actions/haver/calendar_file_upload.php');
elgg_register_action('haver/new_message', elgg_get_plugins_path() . 'haver/actions/haver/send.php');
elgg_register_action('haver/like', elgg_get_plugins_path() . 'haver/actions/haver/like.php');
elgg_register_action('haver/dislike', elgg_get_plugins_path() . 'haver/actions/haver/dislike.php');

$menuItem = new ElggMenuItem('haver_calendar_upload', 'Calendar upload', 'haver/calendar');
elgg_register_menu_item('site', $menuItem);
$menuItem = new ElggMenuItem('haver_send_message', 'Send message', 'haver/send');
elgg_register_menu_item('site', $menuItem);
$menuItem = new ElggMenuItem('haver_view_message', 'Messages', 'haver/view_messages');
elgg_register_menu_item('site', $menuItem);

?>
