<?php

//include_once(TZS_PLUGIN_DIR.'/functions/tzs.trade.functions.php');

add_action( 'wp_ajax_tzs_pr_get_regions', 'tzs_pr_get_regions_callback' );
add_action( 'wp_ajax_nopriv_tzs_pr_get_regions', 'tzs_pr_get_regions_callback' );

function tzs_pr_get_regions_callback() {
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

function tzs_pr_build_countries($name) {
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

function tzs_validate_pr_search_parameters() {
	$errors = array();
	$res = array();
	
	// get parameters from _POST
        $type_id = get_param_def('type_id', '0');
        $cur_type_id = get_param_def('cur_type_id', '0');
        
	$country_from = get_param_def('country_from', '0');
	$region_from = get_param_def('region_from', '0');
	$cityname_from = get_param('cityname_from');
        
	$pr_title = get_param('pr_title');
	
	$price_from = get_param_def('price_from', '0');
	$price_to = get_param_def('price_to', '0');

	$data_from = get_param_def('data_from', null);
	$data_to = get_param_def('data_to', null);
        
	$auction_type = get_param_def('auction_type', '0');
        
        $rate_from = get_param_def('rate_from', '0');
	$rate_to = get_param_def('rate_to', '0');
	
	// validate and parse parameters
	if (is_valid_num_zero($country_from)) {
		// use float not int because ID can be long
		$country_from = floatval($country_from);
	} else {
		array_push($errors, "Неверно выбрана страна");
	}
	if (is_valid_num_zero($region_from)) {
		// use float not int because ID can be long
		$region_from = floatval($region_from);
	} else {
		array_push($errors, "Неверно выбран регион");
	}
        
	if (is_valid_num_zero($price_from)) {
		$price_from = floatval($price_from);
	} else {
		array_push($errors, "Неверно указано начальное значение стоимости");
	}
        
	if (is_valid_num_zero($price_to)) {
		$price_to = floatval($price_to);
	} else {
		array_push($errors, "Неверно указано конечное значение стоимости");
	}
        
	if (is_valid_num_zero($rate_from)) {
		$rate_from = floatval($rate_from);
	} else {
		array_push($errors, "Неверно указано начальное значение ставки");
	}
        
	if (is_valid_num_zero($rate_to)) {
		$rate_to = floatval($rate_to);
	} else {
		array_push($errors, "Неверно указано конечное значение ставки");
	}

	if (is_valid_num_zero($type_id)) {
		$type_id = intval($type_id);
	} else {
		array_push($errors, "Неверно выбрана категория");
	}
        
	if (is_valid_num_zero($cur_type_id)) {
		$cur_type_id = intval($cur_type_id);
	} else {
		array_push($errors, "Неверно выбрана категория");
	}
        
	if (is_valid_num_zero($auction_type)) {
		$auction_type = intval($auction_type);
	} else {
		array_push($errors, "Неверно выбран тип тендера");
	}
        
	if ($cityname_from != null && strlen($cityname_from) == 0) {
		$cityname_from = null;
	}
        
	if ($pr_title != null && strlen($pr_title) == 0) {
		$pr_title = null;
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
	
	
	$cityname_from_ids = null;
	if ($cityname_from != null && count($errors) == 0) {
		$r = tzs_city_to_ids($cityname_from, 0, 0);
		if (isset($r['error']))
			array_push($errors, $r['error']);
		else
			$cityname_from_ids = isset($r['ids']) ? $r['ids'] : null;
	}
        
        // Если нет ошибок - то заполняем результирующий массив
	if (count($errors) == 0) {
		if ($country_from > 0)
			$res['country_from'] = $country_from;
		if ($region_from > 0)
			$res['region_from'] = $region_from;
            if ($cityname_from != null)
                    $res['cityname_from'] = $cityname_from;
            if ($pr_title != null)
                    $res['pr_title'] = $pr_title;
            if ($data_from != null) {
                    $res['data_from'] = $data_from;
                    $res['data_from_str'] = get_param('data_from');
            }
            if ($data_to != null) {
                    $res['data_to'] = $data_to;
                    $res['data_to_str'] = get_param('data_to');
            }
            if ($price_from > 0)
                    $res['price_from'] = $price_from;
            if ($price_to > 0)
                    $res['price_to'] = $price_to;
            if ($rate_from > 0)
                    $res['rate_from'] = $rate_from;
            if ($rate_to > 0)
                    $res['rate_to'] = $rate_to;
            if ($auction_type > 0)
                    $res['auction_type'] = $auction_type;
            if ($type_id > 0)
                    $res['type_id'] = $type_id;
            if ($cur_type_id > 0)
                    $res['cur_type_id'] = $cur_type_id;

            if ($cityname_from_ids != null)
                    $res['cityname_from_ids'] = $cityname_from_ids;
	}
	
	$res['errors'] = $errors;
	return $res;
}

function tzs_search_pr_parameters_to_sql($p, $pref) {
    $sql = '';
    
    if (isset($p['pr_title']))
            $sql .= ' AND lower(title) LIKE "%'.strtolower($p['pr_title']).'%"';
    
    if (isset($p['country_from']))
            $sql .= ' AND from_cid='.$p['country_from'];
    if (isset($p['region_from']))
            $sql .= ' AND from_rid='.$p['region_from'];

    if (isset($p['cityname_from_ids'])) {
        $ids = $p['cityname_from_ids'];

        $ids_str = '';
        foreach ($ids as $id) {
            if (strlen($ids_str) > 0)
                $ids_str .= ',';
            $ids_str .= $id;
        }
        $sql .= " AND from_sid IN ($ids_str)";
    }

    if (isset($p['data_from'])) {
            $d = $p['data_from'];
            $dt = date('Y-m-d', mktime(0, 0, 0, $d['month'], $d['day'], $d['year']));
            $sql .= " AND created>='$dt'";
    }
    if (isset($p['data_to'])) {
            $d = $p['data_to'];
            $dt = date('Y-m-d', mktime(0, 0, 0, $d['month'], $d['day'], $d['year']));
            $sql .= " AND created<='$dt'";
    }

    if (isset($p['price_from']))
            $sql .= ' AND price >= '.$p['price_from'];
    if (isset($p['price_to']))
            $sql .= ' AND price <= '.$p['price_to'];
    
    if (isset($p['type_id']) && ($p['type_id'] > 0))
            $sql .= ' AND type_id = '.$p['type_id'];
    
    
    if (isset($p['auction_type']) && ($p['auction_type'] > 0)) {
        if ($p['auction_type'] == 1) 
            $sql .= 'AND is_lot = 1';
        else 
            $sql .= 'AND (is_lot IS NULL OR is_lot <> 1)';
    }
    
    if (isset($p['rate_from']) || isset($p['rate_to'])) {
        $sql1 = "SELECT auction_id FROM ".TZS_AUCTION_RATES_TABLE." WHERE reviewed IS NULL";
        
        if (isset($p['rate_from']))
            $sql1 .= ' AND rate >= '.$p['rate_from'];
        if (isset($p['rate_to']))
            $sql1 .= ' AND rate <= '.$p['rate_to'];
        
        $sql .= ' AND id IN ('.$sql1.')';
    }
    
    return $sql;
}

function tzs_search_pr_parameters_to_str($p) {
	$sql = '';
	
	if (isset($p['country_from']) || isset($p['region_from']) || isset($p['cityname_from'])) {
		if (strlen($sql) > 0)
			$sql .= ' ';
		//$sql .= 'местонахождение:';
		
		if (isset($p['country_from']))
			$sql .= ' '.tzs_get_country($p['country_from']);
		if (isset($p['region_from']))
			$sql .= ' '.tzs_get_region($p['region_from']);
		if (isset($p['cityname_from'])) {
			$name = $p['cityname_from'];
			$sql .= " $name";
		}
	}

        if (isset($p['type_id']) && ($p['type_id'] > 0)) {
            if (!isset($p['cur_type_id']) || ($p['cur_type_id'] < 1) || ($p['cur_type_id'] !== $p['type_id'])) {
		if (strlen($sql) > 0)
			$sql .= ' * ';
                $sql .= get_the_title($p['type_id']);
            }
        }
        
	if (isset($p['pr_title'])) {
		if (strlen($sql) > 0)
			$sql .= ' * ';
                $name = $p['pr_title'];
		$sql .= "описание содержит '$name'";
	}
	
	if (isset($p['data_from_str']) || isset($p['data_to_str'])) {
		if (strlen($sql) > 0)
			$sql .= ' * ';
		$sql .= 'добавлены ';
		if (isset($p['data_from_str'])) {
			$sql .= ' с '.$p['data_from_str'];
		}
		if (isset($p['data_to_str'])) {
			$sql .= ' по '.$p['data_to_str'];
		}
	}
	
	if (isset($p['price_from']) || isset($p['price_to'])) {
		if (strlen($sql) > 0)
			$sql .= ' * ';
		$sql .= 'стоимость ';
		if (isset($p['price_from']))
			$sql .= ' от '.$p['price_from'];
		if (isset($p['price_to']))
			$sql .= ' до '.$p['price_to'];
	}
	
        if (isset($p['auction_type']) && ($p['auction_type'] > 0)) {
            if (strlen($sql) > 0)
                $sql .= ' * ';
            $sql .= 'тип тендера: ';
            if ($p['auction_type'] == 1) $sql .= 'продажа';
            else $sql .= 'покупка';
        }
        
	if (isset($p['rate_from']) || isset($p['rate_to'])) {
		if (strlen($sql) > 0)
			$sql .= ' * ';
		$sql .= 'ставки ';
		if (isset($p['rate_from']))
			$sql .= ' от '.$p['rate_from'];
		if (isset($p['rate_to']))
			$sql .= ' до '.$p['rate_to'];
	}
	
	return $sql;
}

function tzs_front_end_search_pr_handler($atts) {
    ob_start();
    tzs_copy_get_to_post();
    $product_auction = get_param_def('product_auction', 'products');
    $pa_root_id = ($product_auction === 'auctions') ? ''.TZS_AU_ROOT_CATEGORY_PAGE_ID : ''.TZS_PR_ROOT_CATEGORY_PAGE_ID;
    ?>
    <form name="search_pr_form" method="POST">
        <table name="search_param" border="0">
            <tr>
                <th colspan="7"><?php if ($product_auction === 'auctions') { echo 'Тендеры'; } else { echo 'Товары и услуги'; } ?></th>
            </tr>
            <tr>
                <td width="10">&nbsp;</td>
                <td>Категория:</td>
                <td>&nbsp;</td>
                <td colspan="3">
                    <select name="type_id" <?php echo (isset($_POST['cur_type_id']) && ($_POST['cur_type_id'] === $pa_root_id)) ? '' : ' disabled="disabled"'; ?> >
                        <option value="0">все категории</option>
			<option disabled>- - - - - - - -</option>
                        <?php
                            tzs_build_product_types('type_id', $pa_root_id);
			?>
                    </select>
                </td>
                <td width="10">&nbsp;</td>
            </tr>
            <tr>
                <td width="10">&nbsp;</td>
                <td>Местонахождение:</td>
                <td>страна:</td>
                <td colspan="3">
                    <select name="country_from">
                        <?php
                            tzs_pr_build_countries('country_from');
			?>
                    </select>
                </td>
                <td width="10">&nbsp;</td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td>регион:</td>
                <td colspan="3">
                    <select name="region_from">
                        <option>все области</option>
                    </select>
                </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td>город:</td>
                <td colspan="3">
                    <!--input autocomplete="city" type="text" name="cityname_from" value="<?php //echo_val('cityname_from'); ?>" size="25" autocomplete="off"-->
                    <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>" size="30">
                </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td>Описание:</td>
                <td> </td>
                <td colspan="3">
                    <input type="text" name="pr_title" value="<?php echo_val('pr_title'); ?>" size="30">
                </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td>Стоимость:</td>
                <td>от:</td>
                <td>
                    <input type="text" name="price_from" value="<?php echo_val('price_from'); ?>" size="10">
                </td>
                <td>до:</td>
                <td>
                    <input type="text" name="price_to" value="<?php echo_val('price_to'); ?>" size="10">
                </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td>Дата размещения:</td>
                <td>от:</td>
                <td>
                    <input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="10">
                </td>
                <td>до:</td>
                <td>
                    <input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="10">
                </td>
                <td> </td>
            </tr>
            <?php if ($product_auction === 'auctions') { ?>
            <tr>
                <td> </td>
                <td>Тип тендера:</td>
                <td> </td>
                <td colspan="3">
                    <select name="auction_type">
                        <option value="0" <?php if (isset($_POST['auction_type']) && $_POST['auction_type'] == 0) echo 'selected="selected"'; ?> >Все</option>
                        <option value="1" <?php if (isset($_POST['auction_type']) && $_POST['auction_type'] == 1) echo 'selected="selected"'; ?> >Продажа</option>
                        <option value="2" <?php if (isset($_POST['auction_type']) && $_POST['auction_type'] == 2) echo 'selected="selected"'; ?> >Покупка</option>
                    </select>
                </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td>Ставка:</td>
                <td>от:</td>
                <td>
                    <input type="text" name="rate_from" value="<?php echo_val('rate_from'); ?>" size="10">
                </td>
                <td>до:</td>
                <td>
                    <input type="text" name="rate_to" value="<?php echo_val('rate_to'); ?>" size="10">
                </td>
                <td> </td>
            </tr>
            <?php } ?>
        </table>
    </form>

                                        
                                        
	<script>
		function doAjax(id, rid, to_el) {
			jQuery(to_el).attr("disabled", "disabled");
			jQuery(to_el).html('<option value=\"0\">Загрузка</option>');
			
			var data = {
				'action': 'tzs_pr_get_regions',
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
		}
		
		jQuery(document).ready(function() {
			jQuery('[name=country_from]').change(function() {
				onCountryFromSelected();
			});
			onCountryFromSelected();
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