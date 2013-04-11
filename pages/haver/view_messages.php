<?php

gatekeeper();

$title = 'Messages';
$content = elgg_view_title($title);
$content .= elgg_list_entities(array(
		'type' => 'object',
		'subtype' => 'haver_message',
));

$sidebar = '';

$body = elgg_view_layout('one_sidebar', array(
		'content' => $content,
		'sidebar' => $sidebar
));

echo elgg_view_page($title, $body);

?>