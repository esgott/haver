<?php

echo elgg_view_title($vars['entity']->title);
echo elgg_view('output/longtext', array('value' => $vars['entity']->description));

?>
