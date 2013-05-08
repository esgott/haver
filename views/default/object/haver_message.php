<?php

$message = $vars['entity'];

echo elgg_view_title($vars['entity']->title);
echo elgg_view('output/longtext', array('value' => $message->description));

$message_guid = $message->getGUID();
$user_guid = elgg_get_logged_in_user_guid();

if (check_entity_relationship($user_guid, 'haver_like', $message_guid) === false) {

	echo elgg_view('output/url', array(
			'text' => 'Like',
			'href' => "action/haver/like?message_guid=$message_guid",
			'is_action' => true
	));

} else {

	echo elgg_view('output/url', array(
			'text' => 'Dislike',
			'href' => "action/haver/dislike?message_guid=$message_guid",
			'is_action' => true
	));

}

$group_guid = $message->owner_guid;
$group = elgg_get_entities(array(
		'guids' => $group_guid
));
$group = $group[0];
$group_score = $group->getAnnotationsSum('haver_likes');
$group_votes = $group->countAnnotations('haver_likes');

echo elgg_view('output/text', array(
		'value' => "Group $group_guid score: $group_score votes: $group_votes"
));

?>
