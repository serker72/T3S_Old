<?php
function tzs_find_latest_truck_rec() {
	global $wpdb;
	
	$user_id = get_current_user_id();
	
	$sql = "SELECT id FROM ".TZS_TRUCK_TABLE." WHERE user_id=$user_id ORDER BY id DESC LIMIT 1;";
	
	$row = $wpdb->get_row($sql);
	if ($row != null && count($row) != 0 && $wpdb->last_error == null)
		return $row->id;
	return 0;
}
?>