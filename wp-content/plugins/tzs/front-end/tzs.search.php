<?php

add_action( 'wp_ajax_tzs_get_regions', 'tzs_get_regions_callback' );
add_action( 'wp_ajax_nopriv_tzs_get_regions', 'tzs_get_regions_callback' );

function tzs_get_regions_callback() {
	$id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval( $_POST['id'] ) : 0;
	$rid = isset($_POST['rid']) && is_numeric($_POST['rid']) ? intval( $_POST['rid'] ) : 0;
	if ($id <= 0) {
		?>
			<option value="0">все области</option>
		<?php
	} else {
		global $wpdb;
		
		$sql = "SELECT * FROM ".TZS_REGIONS_TABLE." WHERE country_id=$id ORDER BY title_ru ASC;";
		$res = $wpdb->get_results($sql);
		if (count($res) == 0 && $wpdb->last_error != null) {
			?>
				<option value="0">все области</option>
			<?php
		} else {
			?>
				<option value="0">все области</option>
			<?php
			$found = false;
			foreach ( $res as $row ) {
				if (!$found) {
					$found = true;
					?>
						<option disabled>- - - - - - - -</option>
					<?php
				}
				$region_id = $row->region_id;
				$title = $row->title_ru;
				?>
					<option value="<?php echo $region_id;?>" <?php
						if ($rid == $region_id) {
							echo 'selected="selected"';
						}
					?> ><?php echo $title;?></option>
				<?php
			}
		}
	}
	die();
}

function tzs_build_countries($name) {
	global $wpdb;
	
	$sql = "SELECT * FROM ".TZS_COUNTRIES_TABLE." ORDER BY FIELD(code, 'BY', 'RU', 'UA') DESC, title_ru ASC;";
	$res = $wpdb->get_results($sql);
	if (count($res) == 0 && $wpdb->last_error != null) {
		// do nothink
	} else {
		?>
			<option value="0">все страны</option>
			<option disabled>- - - - - - - -</option>
		<?php
		$counter = 0;
		foreach ( $res as $row ) {
			$country_id = $row->country_id;
			$title = $row->title_ru;
			?>
				<option value="<?php echo $country_id;?>" <?php
					if ((isset($_POST[$name]) && $_POST[$name] == $country_id)) {
						echo 'selected="selected"';
					}
				?>
				><?php echo $title;?></option>
			<?php
			if ($counter == 2) {
				?>
					<option disabled>- - - - - - - -</option>
				<?php
			}
			$counter++;
		}
	}
}

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
        
        $cargo_city_from_radius_check = isset($_POST['cargo_city_from_radius_check']);
        $cargo_city_from_radius_value = get_param_def('cargo_city_from_radius_value', 0);

	if (is_valid_num_zero($cargo_city_from_radius_value)) {
		$cargo_city_from_radius_value = intval($cargo_city_from_radius_value);
	} else {
		array_push($errors, "Неверно выбран радиус");
	}
        
	// validate and parse parameters
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
	
	$cargo_cityname_from_ids = null;
	if ($cargo_cityname_from != null && count($errors) == 0) {
		$r = tzs_city_to_ids($cargo_cityname_from, $region_from, $country_from);
		if (isset($r['error']))
			array_push($errors, $r['error']);
		else
			$cargo_cityname_from_ids = isset($r['ids']) ? $r['ids'] : null;
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
		if ($cargo_cityname_from_ids != null)
			$res['cargo_cityname_from_ids'] = $cargo_cityname_from_ids;
		if ($cargo_cityname_to_ids != null)
			$res['cargo_cityname_to_ids'] = $cargo_cityname_to_ids;
                if ($cargo_city_from_radius_ids != null)
			$res['cargo_city_from_radius_ids'] = $cargo_city_from_radius_ids;
                if ($cargo_city_from_radius_value != null)
                    $res['cargo_city_from_radius_value'] = $cargo_city_from_radius_value;
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
	return $sql;
}

function tzs_front_end_search_handler($atts) {
	ob_start();
	tzs_copy_get_to_post();
	$following = isset($_POST['following']);
	?>
	<form name="search_form" method="POST">
	<table name="cargo_or_trans">
		<tr>
			<?php if ($following) {?>
				<td><input type="radio" tag="cargo_trans_following" name="cargo_trans" value="following" checked="checked" disabled="disabled" > Попутные грузы </td>
				<td>&nbsp;</td>
			<?php } else {?>
				<td><input type="radio" tag="cargo_trans_cargo" name="cargo_trans" value="cargo" <?php if (isset($_POST['cargo_trans']) && $_POST['cargo_trans'] == "cargo") echo 'checked="checked"'; ?> > Грузы </td>
				<td><input type="radio" tag="cargo_trans_transport" name="cargo_trans" value="transport" <?php if (isset($_POST['cargo_trans']) && $_POST['cargo_trans'] == "transport") echo 'checked="checked"'; ?> > Транспорт </td>
			<?php }?>
		</tr>
	</table>
	<table name="search_param" border=1>
		<tr>
			<td>Откуда:</td>
			<td> </td>
			<td>Куда:</td>
			<td> </td>
			<td>Дата:</td>
			<td> </td>
			<td>Масса:</td>
			<td> </td>
			<td>Объем:</td>
		</tr>
		<tr>
			<td>
				<select name="country_from">
					<?php
						tzs_build_countries('country_from');
					?>
				</select>
			</td>
			<td> </td>
			<td>
				<select name="country_to">
					<?php
						tzs_build_countries('country_to');
					?>
				</select>
			</td>
			<td>с</td>
			<td>
				<input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="5">
			</td>
			<td>от</td>
			<td>
				<select name="weight_from">
					<?php tzs_print_weight('weight_from'); ?>
				</select>
			</td>
			<td>от</td>
			<td>
				<select name="volume_from">
					<?php tzs_print_volume('volume_from'); ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>
			    <select name="region_from">
					<option>все области</option>
				</select>
			</td>
			<td> </td>
			<td>
				<select name="region_to">
					<option>все области</option>
				</select>
			</td>
			<td>по</td>
			<td>
				<input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="5">
			</td>
			<td>до</td>
			<td>
				<select name="weight_to">
					<?php tzs_print_weight('weight_to'); ?>
				</select>
			</td>
			<td>до</td>
			<td>
				<select name="volume_to">
					<?php tzs_print_volume('volume_to'); ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><input type="checkbox" name="cargo_city_from" value="" <?php if (isset($_POST['cargo_city_from'])) echo 'checked="checked"'; ?>/>Указать город</td>
			<td></td>
			<td><input type="checkbox" name="cargo_city_to" value="" <?php if (isset($_POST['cargo_city_to'])) echo 'checked="checked"'; ?>/>Указать город</td>
			<td></td>
			<td>Тип транспорта:</td>
			<td colspan="4">
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
		</tr>
		
		<tr>
			<td><input type="text" name="cargo_cityname_from" value="<?php echo_val('cargo_cityname_from'); ?>" size="10"></td>
			<td></td>
			<td><input type="text" name="cargo_cityname_to" value="<?php echo_val('cargo_cityname_to'); ?>" size="10"></td>
		</tr>
<!-- KSK Add form field for search from radius -->
		<tr>
                    <td><input type="checkbox" name="cargo_city_from_radius_check" value="" <?php if (isset($_POST['cargo_city_from_radius_check'])) echo 'checked="checked"'; ?>/>Пункт загрузки в радиусе<sup>*</sup></td>
                    <td></td>
                    <td colspan="7">
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
		</tr>
		<tr>
                    <td colspan="9"><i><sup>*</sup>Для активации выбора радиуса укажите страну и город для пункта загрузки.</i></td>
		</tr>
	</table>
	</form>
	
	<script>
		function doAjax(id, rid, to_el) {
			jQuery(to_el).attr("disabled", "disabled");
			jQuery(to_el).html('<option value=\"0\">Загрузка</option>');
			
			var data = {
				'action': 'tzs_get_regions',
				'id': id,
				'rid': rid
			};
			
			jQuery.post(ajax_url, data, function(response) {
				jQuery(to_el).html(response);
				jQuery(to_el).removeAttr("disabled");
				enableDisable(to_el);
			}).fail(function() {
				jQuery(to_el).html("<option value='0'>все области(!)</option>");
				jQuery(to_el).removeAttr("disabled");
				enableDisable(to_el);
			});
		}
		
		function enableDisable(obj) {
			if (jQuery(obj).children().length <= 1) {
				jQuery(obj).attr("disabled", "disabled");
			} else {
				jQuery(obj).removeAttr("disabled");
			}
		}
	
		function onCountryFromSelected() {
			var rid = <?php echo isset($_POST["region_from"]) ? $_POST["region_from"] : 0; ?>;
			doAjax(jQuery('[name=country_from]').val(), rid, jQuery('[name=region_from]'));
                        
                            if (jQuery('[name=cargo_cityname_from]').val().length > 2 && jQuery('[name=country_from]').val() > 0) {
				jQuery('[name=cargo_city_from_radius_check]').removeAttr('disabled');
                                
                                if (jQuery('[name=cargo_city_from_radius_check]').is(':checked')) {
                                    jQuery('[name=cargo_city_from_radius_value]').removeAttr('disabled');
                                } else {
                                    jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                                }
                            } else {
                                jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
				jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
				jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                            }
		}
		
		function onCountryToSelected() {
			var rid = <?php echo isset($_POST["region_to"]) ? $_POST["region_to"] : 0; ?>;
			doAjax(jQuery('[name=country_to]').val(), rid, jQuery('[name=region_to]'));
		}
		
		function onCityFromSelected() {
			if (jQuery('[name=cargo_city_from]').is(':checked')) {
				jQuery('[name=cargo_cityname_from]').removeAttr('disabled');
                                
                            if (jQuery('[name=cargo_cityname_from]').val().length > 2 && jQuery('[name=country_from]').val() > 0) {
				jQuery('[name=cargo_city_from_radius_check]').removeAttr('disabled');
                                
                                if (jQuery('[name=cargo_city_from_radius_check]').is(':checked')) {
                                    jQuery('[name=cargo_city_from_radius_value]').removeAttr('disabled');
                                } else {
                                    jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                                }
                            } else {
                                jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
				jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
				jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
                            }
			} else {
				jQuery('[name=cargo_cityname_from]').attr('disabled', 'disabled');
                                
                                jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
				jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
				jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
			}
		}
		
		function onCityToSelected() {
			if (jQuery('[name=cargo_city_to]').is(':checked')) {
				jQuery('[name=cargo_cityname_to]').removeAttr('disabled');
			} else {
				jQuery('[name=cargo_cityname_to]').attr('disabled', 'disabled');
			}
		}
		
		function onCityNameFromChanged() {
			if (jQuery('[name=cargo_cityname_from]').val().length > 2 && jQuery('[name=country_from]').val() > 0) {
				jQuery('[name=cargo_city_from_radius_check]').removeAttr('disabled');
			} else {
                                jQuery('[name=cargo_city_from_radius_check]').prop('checked', false);
				jQuery('[name=cargo_city_from_radius_check]').attr('disabled', 'disabled');
				jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
			}
		}
		
		function onCityFromRadiusSelected() {
			if (jQuery('[name=cargo_city_from_radius_check]').is(':checked')) {
				jQuery('[name=cargo_city_from_radius_value]').removeAttr('disabled');
			} else {
				jQuery('[name=cargo_city_from_radius_value]').attr('disabled', 'disabled');
			}
		}
		
		jQuery(document).ready(function() {
			jQuery('[name=country_from]').change(function() {
				onCountryFromSelected();
			});
			jQuery('[name=country_to]').change(function() {
				onCountryToSelected();
			});
			jQuery('[name=cargo_city_from]').change(function() {
				onCityFromSelected();
			});
			jQuery('[name=cargo_cityname_from]').change(function() {
				onCityNameFromChanged();
			});
			jQuery('[name=cargo_city_to]').change(function() {
				onCityToSelected();
			});
			jQuery('[name=cargo_city_from_radius_check]').change(function() {
				onCityFromRadiusSelected();
			});
			onCountryFromSelected();
			onCountryToSelected();
			onCityFromSelected();
                        onCityNameFromChanged();
			onCityToSelected();
                        onCityFromRadiusSelected();
			jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
			jQuery("[name=data_from]" ).datepicker({ dateFormat: "dd.mm.yy" });
			jQuery("[name=data_to]" ).datepicker({ dateFormat: "dd.mm.yy" });
		});
	</script>
	
	<?php
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>