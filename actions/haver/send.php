<?php
$group_id = get_input('to');
$to_group = get_entity($group_id);
$message_body = get_input('message');

$message = new ElggObject();
$message->subtype = "haver_message";
$message->title = 'Message';
$message->description = $message_body;
$message->access_id = ACCESS_LOGGED_IN;
$message->owner_guid = $group_id;

$message_guid = $message->save();

if ($message_guid) {
	system_message("Your message was sent");
	forward($message->getURL());
} else {
	register_error("Unsuccessful message sending");
	forward(REFERER); //REFERER is a global variable that defines the previous page
}

?>
