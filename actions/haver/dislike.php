<?php

gatekeeper();

$message_guid = get_input('message_guid');

$message = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'haver_message',
		'guids' => $message_guid
));

if ($message) {
	$message = $message[0];
	$user_guid = elgg_get_logged_in_user_guid();
	$already_liked = check_entity_relationship($user_guid, 'haver_like', $message_guid);

	if ($already_liked === false) {
		system_message("Not liked");
	} else {
		remove_entity_relationship($user_guid, 'haver_like', $message_guid);
		update_group_of_message($message);
		system_message("Message disliked");
	}
} else {
	register_error("Message $message_guid not found");
}

function update_group_of_message($message) {
	$group_guid = $message->owner_guid;
	$group = elgg_get_entities(array(
			'guids' => $group_guid
	));

	if(!group) {
		register_error("Group $group_guid not found");
	}

	$group = $group[0];
	$group->annotate('haver_dislikes', 1, ACCESS_LOGGED_IN);
}

?>
