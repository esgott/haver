<?php

$options = array (
		'types' => array('group'),
		'subtypes' => array('haver_group')
);

$groups = elgg_get_entities($options);
$names = array();

foreach ($groups as $group) {
	$id = $group->guid;
	$names[$id] = $group->name;
}

?>

<div>
	<label>To</label><br />
	<?php
	echo elgg_view('input/dropdown', array(
		'options_values' => $names,
		'name' => 'to'));
	//TODO choose multiple
	?>
</div>

<div>
	<label>Message</label><br />
	<?php
	echo elgg_view('input/longtext',array('name' => 'message'));
	?>
</div>

<div>
	<?php
	echo elgg_view('input/submit', array('value' => 'Send'));
	?>
</div>
