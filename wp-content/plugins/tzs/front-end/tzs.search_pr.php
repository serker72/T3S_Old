<?php

function tzs_validate_pr_search_parameters() {
	$errors = array();
	$res = array();
	
    // Проверим защиту nonce
    if (isset($_POST['type_id_nonce']) && wp_verify_nonce($_POST['type_id_nonce'], 'type_id')) {
	// get parameters from _POST
        $form_type = get_param_def('form_type', '');
        $type_id = get_param_def('type_id', '0');
        $cur_type_id = get_param_def('cur_type_id', '0');
        $rootcategory = get_param_def('rootcategory', '0');
        $sale_or_purchase = get_param_def('sale_or_purchase', '0');
        $fixed_or_tender = get_param_def('fixed_or_tender', '0');
        $payment = get_param_def('payment', '0');
        $nds = get_param_def('nds', '0');
        
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

	if (is_valid_num_zero($rootcategory)) {
		$rootcategory = intval($rootcategory);
	} else {
		array_push($errors, "Неверно выбрана категория");
	}
        
	if (is_valid_num_zero($auction_type)) {
		$auction_type = intval($auction_type);
	} else {
		array_push($errors, "Неверно выбран тип тендера");
	}
        
	if (is_valid_num_zero($sale_or_purchase)) {
		$sale_or_purchase = intval($sale_or_purchase);
	} else {
		array_push($errors, "Неверно выбран тип заявки");
	}
        
	if (is_valid_num_zero($fixed_or_tender)) {
		$fixed_or_tender = intval($fixed_or_tender);
	} else {
		array_push($errors, "Неверно выбран тип тендера");
	}
        
	if (is_valid_num_zero($payment)) {
		$payment = intval($payment);
	} else {
		array_push($errors, "Неверно выбран форма оплаты");
	}
        
	if (is_valid_num_zero($nds)) {
		$nds = intval($nds);
	} else {
		array_push($errors, "Неверно выбран НДС");
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
            if ($rootcategory > 0)
                    $res['rootcategory'] = $rootcategory;
            if ($sale_or_purchase > 0)
                    $res['sale_or_purchase'] = $sale_or_purchase;
            if ($fixed_or_tender > 0)
                    $res['fixed_or_tender'] = $fixed_or_tender;
            if ($payment > 0)
                    $res['payment'] = $payment;
            if ($nds > 0)
                    $res['nds'] = $nds;

            if ($cityname_from_ids != null)
                    $res['cityname_from_ids'] = $cityname_from_ids;
	}
    } else {
	array_push($errors, "Проверка формы не пройдена. Свяжитесь, пожалуйста, с администрацией сайта.");
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
    
    // Type_Id
    if (isset($p['type_id']) && ($p['type_id'] > 0)) {
        //if ((isset($p['rootcategory']) == false) || ($p['rootcategory'] < 1)) {
            $sql .= ' AND type_id = '.$p['type_id'];
        //}
    } else if (isset($p['cur_type_id']) && ($p['cur_type_id'] > 0)) {
        if ((isset($p['rootcategory']) == false) || ($p['rootcategory'] < 1)) {
                $sql .= ' AND type_id = '.$p['cur_type_id'];
        }
    }

    if (isset($p['sale_or_purchase']))
            $sql .= ' AND sale_or_purchase = '.$p['sale_or_purchase'];

    if (isset($p['fixed_or_tender']))
            $sql .= ' AND fixed_or_tender = '.$p['fixed_or_tender'];

    if (isset($p['payment']))
            $sql .= ' AND payment = '.$p['payment'];

    if (isset($p['nds']))
            $sql .= ' AND nds = '.$p['nds'];
    
    
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

function tzs_front_end_search_pr_form() {
    tzs_copy_get_to_post();
    $product_auction = get_param_def('product_auction', 'products');
    $pa_root_id = ($product_auction === 'auctions') ? ''.TZS_AU_ROOT_CATEGORY_PAGE_ID : ''.TZS_PR_ROOT_CATEGORY_PAGE_ID;
    $p_id = get_the_ID();
    ?>
    <form class="search_pr_form" id="search_pr_form1" name="search_pr_form" method="POST">
        <table name="search_param" border="0">
            <tr>
                <th colspan="2">Укажите критерии поиска товаров и услуг</th>
            </tr>
            <tr>
                <td>Категория:<br>
                    <!--select name="type_id" <?php //echo (isset($_POST['cur_type_id']) && ($_POST['cur_type_id'] === $pa_root_id)) ? '' : ' disabled="disabled"'; ?> -->
                    <!--select name="type_id" <?php //echo (isset($_POST['rootcategory']) && ($_POST['rootcategory'] === '1')) ? '' : ' disabled="disabled"'; ?> -->
                    <select name="type_id" <?php echo ($p_id == $pa_root_id) ? '' : ' disabled="disabled"'; ?> >
                        <option value="0">все категории</option>
			<option disabled>- - - - - - - -</option>
                        <?php
                            tzs_build_product_types('type_id', $pa_root_id);
			?>
                    </select>
                    <?php wp_nonce_field( 'type_id', 'type_id_nonce' ); ?>
                </td>
                <td>Местонахождение: страна:<br>
                    <select name="country_from">
                        <?php
                            tzs_build_countries('country_from');
			?>
                    </select>
                </td>
            <tr>
                <td>Тип заявки:<br>
                    <select name="sale_or_purchase">
                        <option value="0" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 0) echo 'selected="selected"'; ?> >Все</option>
                        <option value="1" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 1) echo 'selected="selected"'; ?> >Продажа</option>
                        <option value="2" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 2) echo 'selected="selected"'; ?> >Покупка</option>
                    </select>
                </td>
                <td>Местонахождение: регион:<br>
                    <select name="region_from">
                        <option value="0">все области</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Участник тендера:<br>
                    <select name="fixed_or_tender">
                        <option value="0" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 0) echo 'selected="selected"'; ?> >Все предложения</option>
                        <option value="1" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 1) echo 'selected="selected"'; ?> >Цена зафиксирована</option>
                        <option value="2" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 2) echo 'selected="selected"'; ?> >Тендерное предложение</option>
                    </select>
                </td>
                <td>Местонахождение: город:<br>
                    <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>" size="30">
                </td>
            </tr>
            <tr>
                <td>Форма оплаты:<br>
                    <select name="payment">
                        <option value="0" <?php if (isset($_POST['payment']) && $_POST['payment'] == 0) echo 'selected="selected"'; ?> >Любая</option>
                        <option value="1" <?php if (isset($_POST['payment']) && $_POST['payment'] == 1) echo 'selected="selected"'; ?> >Наличная</option>
                        <option value="2" <?php if (isset($_POST['payment']) && $_POST['payment'] == 2) echo 'selected="selected"'; ?> >Безналичная</option>
                    </select>
                </td>
                <td>НДС:<br>
                    <select name="nds">
                        <option value="0" <?php if (isset($_POST['nds']) && $_POST['nds'] == 0) echo 'selected="selected"'; ?> >Все</option>
                        <option value="1" <?php if (isset($_POST['nds']) && $_POST['nds'] == 1) echo 'selected="selected"'; ?> >Без НДС</option>
                        <option value="2" <?php if (isset($_POST['nds']) && $_POST['nds'] == 2) echo 'selected="selected"'; ?> >Включая НДС</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Стоимость: от:<br>
                    <input type="text" name="price_from" value="<?php echo_val('price_from'); ?>" size="10">
                </td>
                <td>Стоимость: до:<br>
                    <input type="text" name="price_to" value="<?php echo_val('price_to'); ?>" size="10">
                </td>
            </tr>
            <tr>
                <td>Дата размещения: от:<br>
                    <input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="10">
                </td>
                <td>Дата размещения: до:<br>
                    <input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="10">
                </td>
            </tr>
            <tr>
                <td>Описание:<br>
                    <input type="text" name="pr_title" value="<?php echo_val('pr_title'); ?>" size="30">
                </td>
                <td>
                    <div style="text-align:right; vertical-aligment: middle;">
                        <a href="JavaScript:tblTHeadShowSearchForm();" title="Скрыть форму изменения условий поиска"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/form_hide.png" width="110px" height="24px"></a>&nbsp;&nbsp;
                        <a href="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/eraser.png" width="24px" height="24px"></a>&nbsp;&nbsp;
                        <a href="javascript:onTblSearchButtonClick();" title="Выполнить поиск по текущим условиям фильтра"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/find-1.png" width="24px" height="24px"></a>
                    </div>
                </td>
            </tr>
        </table>
    </form>
    <?php
}

function tzs_front_end_search_pr_handler($atts) {
    ob_start();
    
    tzs_front_end_search_pr_form();
    
    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>