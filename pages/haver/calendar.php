<?php

gatekeeper();

$title = "Upload calendar";

$content = elgg_view_title($title);
$content .= elgg_view("haver/upload");

$sidebar = "";

$body = elgg_view_layout('one_sidebar', array(
		'content' => $content,
		'sidebar' => $sidebar
));

echo elgg_view_page($title, $body);

?>
