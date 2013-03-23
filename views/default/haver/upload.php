<?php

$form_body = elgg_view('input/file', array(
	'name' => 'upload'
));

$form_body .= elgg_view('input/submit', array(
	'value' => 'Upload Now'
));

echo elgg_view('input/form', array(
	'body' => $form_body,
	'enctype' => 'multipart/form-data',
	'action' => 'action/calendar_file_upload'
));

?>
