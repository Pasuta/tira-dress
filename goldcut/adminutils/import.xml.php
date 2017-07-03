<?php 
gc_enable();

$imported_callback_each = function($created) {
	println($created->urn,1,TERM_GRAY);
};
$imported_callback_before = function($entity) {
	println("urn-".$entity->name." ", TERM_BLUE);
};
$imported_callback_after = function($entity) {
	if (is_web_request()) echo '<br>'; 
	print "\n";
};

XMLData::iterateXMLfolders(null, null, $imported_callback_each, $imported_callback_before, $imported_callback_after);

?>