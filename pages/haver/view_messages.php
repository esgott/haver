<?php

gatekeeper();

global $message_score_weight;
global $group_score_weight;
global $author_score_weight;
$message_score_weight = 0.33;
$group_score_weight = 0.33;
$author_score_weight = 0.33;


global $log_file;
$log_file = fopen('/home/esgott/public_html/output.txt', 'w');

function receive_new_messages() {
	global $log_file;
	fwrite($log_file, "Receiving messages started\n");
	$inbox = get_inbox();
	fwrite($log_file, "Inserting new messages\n");
	insert_new_messages($inbox);
}

function get_inbox() {
	global $log_file;
	$user_guid = elgg_get_logged_in_user_guid();

	$options = array(
			'relationship' => 'inbox',
			'relationship_guid' => $user_guid
	);
	$inbox = elgg_get_entities_from_relationship($options);

	if ($inbox) {
		$inbox = $inbox[0];
		fwrite($log_file, "Inbox already created\n");
	} else {
		fwrite($log_file, "Creating inbox\n");
		$inbox = create_inbox($user_guid);
	}

	return $inbox;
}

function create_inbox($user_guid) {
	global $log_file;
	$inbox = new ElggObject();
	$inbox->subtype = 'haver_inbox';
	$inbox->access_id = ACCESS_PRIVATE;
	$inbox->owner_guid = $user_guid;
	$inbox->title = 'no title';
	$inbox->description = 'no description';
	$inbox->save();
	fwrite($log_file, "Inbox created\n");

	add_entity_relationship($user_guid, 'inbox', $inbox);
	fwrite($log_file, "Relatioship added\n");

	return $inbox;
}

function insert_new_messages($inbox) {
	global $log_file;
	$current_user = elgg_get_logged_in_user_entity();
	$groups = $current_user->getGroups('haver_group');

	if ($inbox->message_list) {
		fwrite($log_file, "Message list exists\n");
		$message_list_guid = $inbox->message_list;
		$message_list_head = elgg_get_entities(array(
				'guids' => $message_list_guid
		));
		$message_list_head = $message_list_head[0];
	} else {
		fwrite($log_file, "Creating message list\n");
		$message_list_head = create_message_list();
		$message_list_guid = $message_list_head->getGUID();
		$inbox->message_list = $message_list_guid;
	}

	fwrite($log_file, "Message list head guid: $message_list_guid\n");
	receive_messages($message_list_head, $inbox, $groups);
}

function create_message_list($message_guid = -1) {
	$message_list = new ElggObject();
	$message_list->subtype = 'haver_message_list_element';
	$message_list->access_id = ACCESS_PRIVATE;
	$message_list->owner_guid = elgg_get_logged_in_user_guid();
	$message_list->save();
	$message_list->message_guid = $message_guid;
	return $message_list;
}

function receive_messages($message_list_head, $inbox, $groups) {
	global $log_file;
	$previous_message_list_element = $message_list_head;

	$messages = get_new_messages($inbox, $groups);

	if(!$messages) {
		return;
	}

	$message_count = count($messages);
	fwrite($log_file, "Messages queried: $message_count return value: $messages\n");

	if ($message_count > 0) {
		$newest_message = $messages[0]->getTimeCreated();
	}

	$order_of_messages = get_order($messages);

	foreach ($messages as $message) {
		$body = $message->description;
		fwrite($log_file, " Message: $body $message->guid $message->owner_guid \n");

		if ($message_list_head->message_guid == -1) {
			$message_list_head->message_guid = $message->guid;
			fwrite($log_file, "  Only message guid set\n");
		} else {
			$message_list_element = create_message_list($message->getGUID());
			$message_list_element->next_element_guid = $message_list_head->getGUID();
			$message_list_head = $message_list_element;
			$current_element_guid = $message_list_element->getGUID();
			$inbox->message_list = $current_element_guid;
			fwrite($log_file, "  New message list element created ($current_element_guid), next element of head: $message_list_head->next_element_guid \n");
		}

		$creation_time = $message->getTimeCreated();
		fwrite($log_file, "  Creation time of message: $creation_time\n");
		if ($creation_time > $newest_message) {
			$newest_message = $creation_time;
		}
	}

	if ($message_count > 0) {
		$inbox->newest_message = $newest_message;
	}

	fwrite($log_file, "New message head: $inbox->message_list newest message: $inbox->newest_message \n");
}

function get_new_messages($inbox, $groups) {
	global $log_file;

	if (count($groups) == 0) {
		fwrite($log_file, "No groups for this user\n");
		return array();
	}

	$group_guids = array();
	foreach ($groups as $group) {
		$guid = $group->guid;
		fwrite($log_file, "Adding group guid: $guid\n");
		array_push($group_guids, $group->guid);
	}

	$options = array(
			'type' => 'object',
			'subtypes' => 'haver_message',
			'owner_guids' => $group_guids
	);

	if ($inbox->newest_message) {
		$options['created_time_lower'] = $inbox->newest_message + 1;
	}

	fwrite($log_file, "Options:\n");
	foreach ($options as $key => $value) {
		if (is_array($value)) {
			fwrite($log_file, " Key: $key; Value:");
			foreach ($value as $element) {
				fwrite($log_file, " $element");
			}
			fwrite($log_file, "\n");
		} else {
			fwrite($log_file, " Key: $key; Value: $value\n");
		}
	}

	return elgg_get_entities($options);
}

function get_order($messages) {
	$message_count = count($messages);
	$scores = array();

	for ($i = 0; $i < $message_count; $i++) {
		$current_message = $messages[$i];
		$message_score = get_message_score($current_message);
		$group_score = get_group_score($current_message);
		$author_score = get_author_score($current_message);
		$scores[$i] = $message_score + $group_score + $author_score;
	}

	$order = array();
	$reverse_sorted_scores = $scores;
	rsort($reverse_sorted_scores);

	for ($i = 0; $i < $message_count; $i++) {
		$position = get_position($reverse_sorted_scores, $scores[$i]);
		$order[$position] = $i;
	}
}

function get_message_score($message) {
	global $message_score_weight;
	$relationships = get_entity_relationships($message->getGUID());
	$likes_count = 0;

	foreach ($relationships as $relationship) {
		if ($relationship->getSubtype == 'haver_like') {
			$likes_count++;
		}
	}

	return $likes_count * $message_score_weight;
}

function get_group_score($message) {
	global $group_score_weight;
	$group = get_entity($message->owner_guid);
	$likes_count = $group->countAnnotations('haver_likes');
	$dislikes_count = $group->countAnnotations('haver_dislikes');
	if ($likes_count > $dislikes_count) {
		return ($likes_count - $dislikes_count) * $group_score_weight;
	} else {
		return 0;
	}
}

function get_author_score($current_message) {
	global $author_score_weight;
	$author = get_entity($message->author);
	if ($author) {
		$likes_count = $author->countAnnotations('haver_likes');
		$dislikes_count = $author->countAnnotations('haver_dislikes');
		if ($likes_count > $dislikes_count) {
			return ($likes_count - $dislikes_count) * $group_score_weight;
		} else {
			return 0;
		}
	}
}

function get_position($reverse_sorted_array, $element) {
	for ($i = 0; $i < count($reverse_sorted_array); $i++) {
		if ($reverse_sorted_array[$i] == $element) {
			return $i;
		}
	}

	return false;
}

receive_new_messages();

$title = 'Messages';
$content = elgg_view_title($title);

$inbox = elgg_get_entities_from_relationship(array(
		'relationship' => 'inbox',
		'relationship_guid' => elgg_get_logged_in_user_guid()
));

$message_element = elgg_get_entities(array(
		'guids' => $inbox->message_list
));

fwrite($log_file, "Iterating through messages\n");
for ($i = 0; $i < 10; $i++) {
	$message_element = $message_element[0];
	$message_guid = $message_element->message_guid;
	fwrite($log_file, " Message list element $message_element->guid message $message_guid next message list element $message_element->next_element_guid \n");
	if ($message_guid > 0) {
		$message = elgg_get_entities(array(
				'guids' => $message_guid
		));
		$message = $message[0];
		fwrite($log_file, " Message body: $message->description \n");
		$content .= elgg_view_list_item($message);
	}
	if ($message_element->next_element_guid) {
		$message_element = elgg_get_entities(array(
				'guids' => $message_element->next_element_guid
		));
	} else {
		break;
	}
}

$sidebar = '';

$body = elgg_view_layout('one_sidebar', array(
		'content' => $content,
		'sidebar' => $sidebar
));

echo elgg_view_page($title, $body);

fclose($log_file);

?>
