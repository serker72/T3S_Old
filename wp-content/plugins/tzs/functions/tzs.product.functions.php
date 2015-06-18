<?php
function tzs_find_latest_product_rec() {
	global $wpdb;
	
	$user_id = get_current_user_id();
	
	$sql = "SELECT id FROM ".TZS_PRODUCTS_TABLE." WHERE user_id=$user_id ORDER BY id DESC LIMIT 1;";
	
	$row = $wpdb->get_row($sql);
	if ($row != null && count($row) != 0 && $wpdb->last_error == null)
		return $row->id;
	return 0;
}
/*******************************************************************************
 * 
 * tzs_get_user_meta - получение информации из таблицы wp_user_meta
 * 
 *******************************************************************************/
function tzs_get_user_meta($user_id) {
    if ($user_id && ($user_id > 0)) {
	$user_info = get_userdata($user_id);
        
        return array(
            'id' => $uid,
            'user_login' => $user_info->user_login,
            'user_nicename' => $user_info->user_nicename,
            'user_email' => $user_info->user_email,
            'user_status' => $user_info->user_status,
            'fio' => get_user_meta($user_id, 'fio', true),
            'skype' => get_user_meta($user_id, 'skype', true),
            'telephone' => get_user_meta($user_id, 'telephone', true),
            'company' => get_user_meta($user_id, 'company', true),
            'company_description' => get_user_meta($user_id, 'company_description', true),
            'company_logo' => get_user_meta($user_id, 'company_logo', true),
            'description' => get_user_meta($user_id, 'description', true),
            'kod_edrpou' => get_user_meta($user_id, 'kod_edrpou', true),
            'adress' => get_user_meta($user_id, 'adress', true),
            'tel_fax' => get_user_meta($user_id, 'tel_fax', true),
        );
//            '' => get_user_meta($user_id, '', true),
    } else {
        return array();
    }
}
?>