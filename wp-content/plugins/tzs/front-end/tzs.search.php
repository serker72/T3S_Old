<?php

function tzs_print_weight($name) {
	tzs_print_array_options($GLOBALS['tzs_weight_enum'], 'т', $name);
}

function tzs_print_volume($name) {
	tzs_print_array_options($GLOBALS['tzs_volume_enum'], 'м³', $name);
}

function tzs_validate_search_parameters() {
	$errors = array();
	$res = array();
	
	// get parameters from _POST
	$country_from = get_param_def('country_from', '0');
	$country_to = get_param_def('country_to', '0');
	
	$region_from = get_param_def('region_from', '0');
	$region_to = get_param_def('region_to', '0');
	
	$cargo_city_from = isset($_POST['cargo_city_from']);
	$cargo_city_to = isset($_POST['cargo_city_to']);
	
	$cargo_cityname_from = $cargo_city_from ? get_param('cargo_cityname_from') : null;
	$cargo_cityname_to = $cargo_city_to ? get_param('cargo_cityname_to') : null;
	
	$data_from = get_param_def('data_from', null);
	$data_to = get_param_def('data_to', null);
	
	$weight_from = get_param_def('weight_from', '0');
	$weight_to = get_param_def('weight_to', '0');
	
	$volume_from = get_param_def('volume_from', '0');
	$volume_to = get_param_def('volume_to', '0');
	
	$trans_type = get_param_def('trans_type', '0');
	$sh_type = get_param_def('sh_type', '0');
        
        $cargo_city_from_radius_check = isset($_POST['cargo_city_from_radius_check']);
        $cargo_city_from_radius_value = get_param_def('cargo_city_from_radius_value', 0);

	$price_from = get_param_def('price_from', '0');
	$price_to = get_param_def('price_to', '0');

	$price_km_from = get_param_def('price_km_from', '0');
	$price_km_to = get_param_def('price_km_to', '0');
        
        
	// validate and parse parameters
	if (is_valid_num_zero($cargo_city_from_radius_value)) {
		$cargo_city_from_radius_value = intval($cargo_city_from_radius_value);
	} else {
		array_push($errors, "Неверно выбран радиус");
	}
        
	if (is_valid_num_zero($country_from)) {
		// use float not int because ID can be long
		$country_from = floatval($country_from);
	} else {
		array_push($errors, "Неверно выбрана страна погрузки");
	}
	if (is_valid_num_zero($country_to)) {
		// use float not int because ID can be long
		$country_to = floatval($country_to);
	} else {
		array_push($errors, "Неверно выбрана страна выгрузки");
	}
	
	if (is_valid_num_zero($region_from)) {
		// use float not int because ID can be long
		$region_from = floatval($region_from);
	} else {
		array_push($errors, "Неверно выбран регион погрузки");
	}
	if (is_valid_num_zero($region_to)) {
		// use float not int because ID can be long
		$region_to = floatval($region_to);
	} else {
		array_push($errors, "Неверно выбран регион выгрузки");
	}
	
	if ($cargo_cityname_from != null && strlen($cargo_cityname_from) == 0) {
		$cargo_cityname_from = null;
	}
	if ($cargo_cityname_to != null && strlen($cargo_cityname_to) == 0) {
		$cargo_cityname_to = null;
	}
	
	if ($data_from != null && strlen($data_from) > 0) {
		$data_from = is_valid_date($data_from);
		if ($data_from == null) {
			array_push($errors, "Неверный формат даты (с)");
		}
	} else {
		$data_from = null;
	}
	
	if ($data_to != null && strlen($data_to) > 0) {
		$data_to = is_valid_date($data_to);
		if ($data_to == null) {
			array_push($errors, "Неверный формат даты (по)");
		}
	} else {
		$data_to = null;
	}
	
	if (is_valid_num_zero($weight_from)) {
		$weight_from = intval($weight_from);
	} else {
		array_push($errors, "Неверно выбрана масса (от)");
	}
	if (is_valid_num_zero($weight_to)) {
		$weight_to = intval($weight_to);
	} else {
		array_push($errors, "Неверно выбрана масса (до)");
	}
	
	if (is_valid_num_zero($volume_from)) {
		$volume_from = intval($volume_from);
	} else {
		array_push($errors, "Неверно выбран объем (от)");
	}
	if (is_valid_num_zero($volume_to)) {
		$volume_to = intval($volume_to);
	} else {
		array_push($errors, "Неверно выбран объем (до)");
	}
	
	if (is_valid_num_zero($trans_type)) {
		$trans_type = intval($trans_type);
	} else {
		array_push($errors, "Неверно выбран тип транспорта");
	}
	
	if (is_valid_num_zero($sh_type)) {
		$sh_type = intval($sh_type);
	} else {
		array_push($errors, "Неверно выбран тип груза");
	}
	
	$cargo_cityname_from_ids = null;
	if ($cargo_cityname_from != null && count($errors) == 0) {
		$r = tzs_city_to_ids($cargo_cityname_from, $region_from, $country_from);
		if (isset($r['error']))
			array_push($errors, $r['error']);
		else
			$cargo_cityname_from_ids = isset($r['ids']) ? $r['ids'] : null;
	}
	
	if (is_valid_num_zero($price_from)) {
		$price_from = intval($price_from);
	} else {
		array_push($errors, "Неверно выбрана стоимость (от)");
	}
	if (is_valid_num_zero($price_to)) {
		$price_to = intval($price_to);
	} else {
		array_push($errors, "Неверно выбрана стоимость (до)");
	}
	
	if (is_valid_num_zero($price_km_from)) {
		$price_km_from = intval($price_km_from);
	} else {
		array_push($errors, "Неверно выбрана цена 1 км (от)");
	}
	if (is_valid_num_zero($price_km_to)) {
		$price_km_to = intval($price_km_to);
	} else {
		array_push($errors, "Неверно выбрана цена 1 км (до)");
	}
        
        // KSK - добавляем выбор ids для городов в радиусе
        $cargo_city_from_radius_ids = null;
        if ($cargo_city_from_radius_check && $cargo_cityname_from != null && count($errors) == 0) {
            $r = tzs_city_from_radius_to_ids($cargo_cityname_from, $region_from, $country_from, $cargo_city_from_radius_value);
            if (isset($r['error']))
                array_push($errors, $r['error']);
            else
                $cargo_city_from_radius_ids = isset($r['ids']) ? $r['ids'] : null;
        }
	
	$cargo_cityname_to_ids = null;
	if ($cargo_cityname_to != null && count($errors) == 0) {
		$r = tzs_city_to_ids($cargo_cityname_to, $region_to, $country_to);
		if (isset($r['error']))
			array_push($errors, $r['error']);
		else
			$cargo_cityname_to_ids = isset($r['ids']) ? $r['ids'] : null;
	}
	
	if (count($errors) == 0) {
		if ($country_from > 0)
			$res['country_from'] = $country_from;
		if ($country_to > 0)
			$res['country_to'] = $country_to;
		if ($region_from > 0)
			$res['region_from'] = $region_from;
		if ($region_to > 0)
			$res['region_to'] = $region_to;
		if ($cargo_cityname_from != null)
			$res['cargo_cityname_from'] = $cargo_cityname_from;
		if ($cargo_cityname_to != null)
			$res['cargo_cityname_to'] = $cargo_cityname_to;
		if ($data_from != null) {
			$res['data_from'] = $data_from;
			$res['data_from_str'] = get_param('data_from');
		}
		if ($data_to != null) {
			$res['data_to'] = $data_to;
			$res['data_to_str'] = get_param('data_to');
		}
		if ($weight_from > 0)
			$res['weight_from'] = $weight_from;
		if ($weight_to > 0)
			$res['weight_to'] = $weight_to;
		if ($volume_from > 0)
			$res['volume_from'] = $volume_from;
		if ($volume_to > 0)
			$res['volume_to'] = $volume_to;
		if ($trans_type > 0)
			$res['trans_type'] = $trans_type;
		if ($sh_type > 0)
			$res['sh_type'] = $sh_type;
		if ($cargo_cityname_from_ids != null)
			$res['cargo_cityname_from_ids'] = $cargo_cityname_from_ids;
		if ($cargo_cityname_to_ids != null)
			$res['cargo_cityname_to_ids'] = $cargo_cityname_to_ids;
                if ($cargo_city_from_radius_ids != null)
			$res['cargo_city_from_radius_ids'] = $cargo_city_from_radius_ids;
                if ($cargo_city_from_radius_value != null)
                    $res['cargo_city_from_radius_value'] = $cargo_city_from_radius_value;
		if ($price_from > 0)
			$res['price_from'] = $price_from;
		if ($price_to > 0)
			$res['price_to'] = $price_to;
		if ($price_km_from > 0)
			$res['price_km_from'] = $price_km_from;
		if ($price_km_to > 0)
			$res['price_km_to'] = $price_km_to;
	}
	
	$res['errors'] = $errors;
	return $res;
}

function tzs_search_parameters_to_sql($p, $pref) {
	$sql = '';
	if (isset($p['country_from']))
		$sql .= ' AND from_cid='.$p['country_from'];
	if (isset($p['country_to']))
		$sql .= ' AND to_cid='.$p['country_to'];
	if (isset($p['region_from']))
		$sql .= ' AND from_rid='.$p['region_from'];
	if (isset($p['region_to']))
		$sql .= ' AND to_rid='.$p['region_to'];
	
	if (isset($p['cargo_cityname_from_ids']) || isset($p['cargo_city_from_radius_ids'])) {
            if (isset($p['cargo_city_from_radius_ids']))
                $ids = $p['cargo_city_from_radius_ids'];
            else
                $ids = $p['cargo_cityname_from_ids'];
            
            $ids_str = '';
            foreach ($ids as $id) {
                if (strlen($ids_str) > 0)
                    $ids_str .= ',';
		$ids_str .= $id;
            }
            $sql .= " AND from_sid IN ($ids_str)";
	}
        
	if (isset($p['cargo_cityname_to_ids'])) {
		$ids = $p['cargo_cityname_to_ids'];
		$ids_str = '';
		foreach ($ids as $id) {
			if (strlen($ids_str) > 0)
				$ids_str .= ',';
			$ids_str .= $id;
		}
		$sql .= " AND to_sid IN ($ids_str)";
	}
	
	if (isset($p['data_from'])) {
		$d = $p['data_from'];
		$dt = date('Y-m-d', mktime(0, 0, 0, $d['month'], $d['day'], $d['year']));
		$sql .= ' AND '.$pref."_date_from>='$dt'";
	}
	if (isset($p['data_to'])) {
		$d = $p['data_to'];
		$dt = date('Y-m-d', mktime(0, 0, 0, $d['month'], $d['day'], $d['year']));
		$sql .= ' AND '.$pref."_date_from<='$dt'";
	}
		
	if (isset($p['weight_from']))
		$sql .= ' AND '.$pref.'_weight >= '.$p['weight_from'];
	if (isset($p['weight_to']))
		$sql .= ' AND '.$pref.'_weight <= '.$p['weight_to'];
	if (isset($p['volume_from']))
		$sql .= ' AND '.$pref.'_volume >= '.$p['volume_from'];
	if (isset($p['volume_to']))
		$sql .= ' AND '.$pref.'_volume <= '.$p['volume_to'];
	if (isset($p['trans_type']))
		$sql .= ' AND trans_type = '.$p['trans_type'];
	if (isset($p['sh_type']))
		$sql .= ' AND sh_type = '.$p['sh_type'];
        
	if (isset($p['price_from']))
		$sql .= ' AND price >= '.$p['price_from'];
	if (isset($p['price_to']))
		$sql .= ' AND price <= '.$p['price_to'];
        
	if (isset($p['price_km_from']))
		$sql .= ' AND (price/distance) >= '.$p['price_km_from'];
	if (isset($p['price_km_to']))
		$sql .= ' AND (price/distance) <= '.$p['price_km_to'];
        
	return $sql;
}

function tzs_search_parameters_to_str($p) {
	$sql = '';
	
	if (isset($p['country_from']) || isset($p['region_from']) || isset($p['cargo_cityname_from'])) {
		if (strlen($sql) > 0)
			$sql .= ' ';
		$sql .= 'из';
		
		if (isset($p['country_from']))
			$sql .= ' '.tzs_get_country($p['country_from']);
		if (isset($p['region_from']))
			$sql .= ' '.tzs_get_region($p['region_from']);
		if (isset($p['cargo_cityname_from'])) {
			$name = $p['cargo_cityname_from'];
			$sql .= " $name";
		}
            
            if (isset($p['cargo_city_from_radius_ids'])) {
                $sql .= " (погрузка в радиусе ".$p['cargo_city_from_radius_value']." км)";
            }
	}
	
	if (isset($p['country_to']) || isset($p['region_to']) || isset($p['cargo_cityname_to'])) {
		if (strlen($sql) > 0)
			$sql .= ' ';
		$sql .= 'в';
		
		if (isset($p['country_to']))
			$sql .= ' '.tzs_get_country($p['country_to']);
		if (isset($p['region_to']))
			$sql .= ' '.tzs_get_region($p['region_to']);
		if (isset($p['cargo_cityname_to'])) {
			$name = $p['cargo_cityname_to'];
			$sql .= " $name";
		}
	}
	
	if (isset($p['data_from_str']) || isset($p['data_to_str'])) {
		if (strlen($sql) > 0)
			$sql .= ', ';
		$sql .= 'погрузка';
		if (isset($p['data_from_str'])) {
			$sql .= ' с '.$p['data_from_str'];
		}
		if (isset($p['data_to_str'])) {
			$sql .= ' по '.$p['data_to_str'];
		}
	}
	
	if (isset($p['weight_from']) || isset($p['weight_to'])) {
		if (strlen($sql) > 0)
			$sql .= ', ';
		$sql .= 'масса';
		if (isset($p['weight_from']))
			$sql .= ' от '.$p['weight_from'].'т';
		if (isset($p['weight_to']))
			$sql .= ' до '.$p['weight_to'].'т';
	}
	
	if (isset($p['volume_from']) || isset($p['volume_to'])) {
		if (strlen($sql) > 0)
			$sql .= ', ';
		$sql .= 'объем';
		if (isset($p['volume_from']))
			$sql .= ' от '.$p['volume_from'].'м³';
		if (isset($p['volume_to']))
			$sql .= ' до '.$p['volume_to'].'м³';
	}
	
	if (isset($p['trans_type'])) {
		$type = isset($GLOBALS['tzs_tr_types'][$p['trans_type']]) ? $GLOBALS['tzs_tr_types'][$p['trans_type']] : "?";
		if (strlen($sql) > 0)
			$sql .= ', ';
		$sql .= "тип транспорта: $type";
	}
	
	if (isset($p['sh_type'])) {
		$type = isset($GLOBALS['tzs_sh_types'][$p['sh_type']]) ? $GLOBALS['tzs_sh_types'][$p['sh_type']] : "?";
		if (strlen($sql) > 0)
			$sql .= ', ';
		$sql .= "тип груза: $type";
	}
	return $sql;
}

/*
 * Вывод формы поиска транспорта/груза
 */
function tzs_front_end_search_tr_form($form_type) {
    tzs_copy_get_to_post();
    ?>
    <form class="search_pr_form" id="search_pr_form1" name="search_pr_form" method="POST">
        <table name="search_param" border="0">
            <tr>
                <th colspan="4">Укажите критерии поиска <?php echo ($form_type === 'transport') ? 'транспорта' : 'грузов';?></th>
            </tr>
            <tr>
                <th class="td_border_right_dotted">
                    <div style="color: #F5C034; font-weight: bold;">
                        ПОГРУЗКА
                    </div>
                </th>
                <th class="td_border_right_dotted">
                    <div style="color: #F5C034; font-weight: bold;">
                        ВЫГРУЗКА
                    </div>
                </th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <td class="td_border_right_dotted">Страна:<br>
                    <select name="country_from">
                        <?php
                            tzs_build_countries('country_from');
			?>
                    </select>
                    <?php wp_nonce_field( 'country_from">', 'type_country_from">' ); ?>
                </td>
                <td class="td_border_right_dotted">Страна:<br>
                    <select name="country_to">
                        <?php
                            tzs_build_countries('country_to');
			?>
                    </select>
                </td>
                
                <td>Масса: от:<br>
                    <select name="weight_from">
                            <?php tzs_print_weight('weight_from'); ?>
                    </select>
                </td>
                <td>Масса: до:<br>
                    <select name="weight_to">
                            <?php tzs_print_weight('weight_to'); ?>
                    </select>
                </td>
            <tr>
                <td class="td_border_right_dotted">Регион:<br>
                    <select name="region_from">
                                <option>все области</option>
                    </select>
                </td>
                <td class="td_border_right_dotted">Регион:<br>
                    <select name="region_to">
                                <option>все области</option>
                    </select>
                </td>
                
                <td>Объем: от:<br>
                    <select name="volume_from">
                            <?php tzs_print_volume('volume_from'); ?>
                    </select>
                </td>
                <td>Объем: до:<br>
                    <select name="volume_to">
                            <?php tzs_print_volume('volume_to'); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="td_border_right_dotted">Населенный пункт:&nbsp;<input type="checkbox" name="cargo_city_from" value="" <?php if (isset($_POST['cargo_city_from'])) echo 'checked="checked"'; ?>/><br>
                    <input type="text" name="cargo_cityname_from" value="<?php echo_val('cityname_from'); ?>" size="10">
                </td>
                <td class="td_border_right_dotted">Населенный пункт:&nbsp;<input type="checkbox" name="cargo_city_to" value="" <?php if (isset($_POST['cargo_city_to'])) echo 'checked="checked"'; ?>/><br>
                    <input type="text" name="cargo_cityname_to" value="<?php echo_val('cargo_cityname_to'); ?>" size="10">
                </td>
                <td>Cтоимость: от:<br>
                    <input type="text" name="price_from" value="<?php echo_val('price_from'); ?>" size="10"><br>
                </td>
                <td>Cтоимость: до:<br>
                    <input type="text" name="price_to" value="<?php echo_val('price_to'); ?>" size="10"><br>
                </td>
            </tr>
            <tr>
                <td class="td_border_right_dotted">Пункт загрузки в радиусе<sup>*</sup>:&nbsp;<input type="checkbox" name="cargo_city_from_radius_check" value="" <?php if (isset($_POST['cargo_city_from_radius_check'])) echo 'checked="checked"'; ?>/><br>
                    <select name="cargo_city_from_radius_value">
                        <?php
                            foreach ($GLOBALS['tzs_city_from_radius_value'] as $key => $val) {
                                echo '<option value="'.$key.'" ';
                                if ((isset($_POST['cargo_city_from_radius_value']) && $_POST['cargo_city_from_radius_value'] == $key) || (!isset($_POST['cargo_city_from_radius_value']) && $key == 0)) {
                                    echo 'selected="selected"';
                                }
                                echo '>'.htmlspecialchars($val).'</option>';
                            }
                        ?>
                    </select>
                </td>
                <td class="td_border_right_dotted">&nbsp;</td>
                
                <td>Цена 1 км: от:<br>
                    <input type="text" name="price_km_from" value="<?php echo_val('price_from'); ?>" size="10"><br>
                </td>
                <td>Цена 1 км: до:<br>
                    <input type="text" name="price_km_to" value="<?php echo_val('price_to'); ?>" size="10"><br>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_border_right_dotted td_border_top_dotted">
                    <div style="color: #F5C034; font-style: italic; font-weight: bold;">
                        <sup>*</sup>Для выбора радиуса укажите страну и город пункта погрузки.
                    </div>
                </td>
                
                <td>Тип транспорта:<br>
                    <select name="trans_type">
                        <?php
                            foreach ($GLOBALS['tzs_tr_types_search'] as $key => $val) {
                                    echo '<option value="'.$key.'" ';
                                    if ((isset($_POST['trans_type']) && $_POST['trans_type'] == $key) || (!isset($_POST['trans_type']) && $key == 0)) {
                                            echo 'selected="selected"';
                                    }
                                    echo '>'.htmlspecialchars($val).'</option>';
                            }
                        ?>
                    </select>
                </td>
                <td>
                    <?php if ($form_type === 'shipments') { ?>
                    Тип груза:<br>
                    <select name="sh_type">
                        <?php
                            foreach ($GLOBALS['tzs_sh_types_search'] as $key => $val) {
                                    echo '<option value="'.$key.'" ';
                                    if ((isset($_POST['sh_type']) && $_POST['sh_type'] == $key) || (!isset($_POST['sh_type']) && $key == 0)) {
                                            echo 'selected="selected"';
                                    }
                                    echo '>'.htmlspecialchars($val).'</option>';
                            }
                        ?>
                    </select>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td class="td_border_right_dotted td_border_top_dotted">Дата:<br>
                    <input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="10">
                </td>
                <td class="td_border_right_dotted td_border_top_dotted">Дата:<br>
                    <input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="10">
                </td>
                
                <td colspan="2" class="td_border_top_dotted">
                    <div style="text-align:right; vertical-aligment: middle;">
                        <a href="JavaScript:tblTHeadShowSearchForm();" title="Скрыть форму изменения условий поиска"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/form_hide.png" width="150px" height="26px"></a>&nbsp;&nbsp;
                        <a href="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/eraser.png" width="24px" height="24px"></a>&nbsp;&nbsp;
                        <a href="javascript:onTblSearchButtonClick();" title="Выполнить поиск по текущим условиям фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/find-1.png" width="24px" height="24px"></a>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <?php
}


function tzs_front_end_search_handler($atts) {
	ob_start();
	tzs_copy_get_to_post();
	$following = isset($_POST['following']);
        
        tzs_front_end_search_tr_form('transport');
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>