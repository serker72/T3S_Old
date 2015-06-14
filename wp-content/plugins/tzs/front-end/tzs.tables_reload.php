<?php

/*
 * Вывод одной строки таблицы в виде html
 */
function tzs_products_table_record_out($row) {
    $user_info = tzs_get_user_meta($row->user_id);

    $output_tbody = '<tr rid="'.$row->id.'" id="';

    if ($row->sale_or_purchase == 1) { $output_tbody .= 'tbl_auctions_tr_lot_1'; } else { $output_tbody .= 'tbl_auctions_tr_lot_0'; }

    $output_tbody .= '">
            <td>
                <div class="record_number">
                    <span class="middle" title="Номер заявки">
                           № '.$row->id.'
                    </span>
                </div>
                <div>
                    <span class="time_label" title="Время добавления">
                        '.convert_time_only($row->created).'
                    </span>
                </div>
            </td>
            <td>
                <div>
                    <span title="Тип заявки">
                        <strong>';

    if ($row->sale_or_purchase == 1) { $output_tbody .= 'Продажа'; } else { $output_tbody .= 'Покупка'; }

    $output_tbody .= '</strong>
                    </span>
                </div>
            </td>
            <td>
                <div>';

                if ($row->fixed_or_tender == 1) {
                    $output_tbody .= 'Цена зафиксирована<br/>
                <a class="btnBlue" title="Купить товар по фиксированной цене">Купить</a>';
                } else {
                    $output_tbody .= 'Тендерное предложение<br/>
                    <a class="btnBlue" title="Предложить свою цену за товар">Предложить свою цену</a>';
                }

    $output_tbody .= '</div>
            </td>
            <td>
                <div>
                    '.convert_date($row->created).'<br/>
                    <span class="expired_label" title="Дата окончания публикации">
                        '.convert_date($row->expiration).'
                    </span>
                </div>
            </td>
            <td>
                <div class="ienlarger">';

                if (strlen($row->image_id_lists) > 0) {
                    $main_image_id = $row->main_image_id;
                    // Вначале выведем главное изображение
                    $attachment_info = wp_get_attachment_image_src($main_image_id, 'full');
                    if ($attachment_info !== false) {
                            $output_tbody .= '<a href="#nogo">
                                <img src="'.$attachment_info[0].'" alt="thumb" class="resize_thumb">
                                <span>
                                    '.trim($row->title).'<br/>
                                    <img src="'.$attachment_info[0].'" alt="large"/>
                                </span>
                            </a>';
                    }
                }

                $output_tbody .= '</div>
                <div class="title_text">
                    <span title="Краткое описание товара">
                        '.trim($row->title).'
                    </span>
                </div>
            </td>

            <td>
                <div>
                    <span class="price_label" title="Цена товара">
                        <strong>'.$row->price.'</strong> '.$GLOBALS['tzs_pr_curr'][$row->currency].'
                    </span>
                    <br>
                    <span class="payment_label" title="Форма оплаты">
                        '.$GLOBALS['tzs_pr_payment'][$row->payment].'<br/>
                        '.$GLOBALS['tzs_pr_nds'][$row->nds].'
                    </span>
                    <br>
                    <span class="copies_label" title="Количество товара">
                        <strong>'.$row->copies.'</strong> '.$GLOBALS['tzs_pr_unit'][$row->unit].'
                    </span>
                </div>
            </td>
            <td>
                <div>
                    '.tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from).'
                </div>
            </td>
            <td>
                <div class="tbl_products_contact" title="Контактные данные ';
    if ($row->sale_or_purchase == 1) { $output_tbody .= 'продавца'; } else { $output_tbody .= 'покупателя'; }

    $output_tbody .= '">
                    <a href="">';

    if ($user_info['company'] != '') { $output_tbody .= $user_info['company']; } else { $output_tbody .= $user_info['fio']; }

    $output_tbody .= '</a>
                    <span>';

    $meta=explode(',', $user_info['adress']); 
    $output_tbody .= $meta[0].'</span>';
    if (($user_id == 0) && ($GLOBALS['tzs_au_contact_view_all'] == false)) {
        $output_tbody .= '<div class="tzs_au_contact_view_all" phone-user-not-view="'.$row->user_id.'">Для просмотра контактов необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></div>';
    }

    if ($user_info['company'] != '') {
        $phone_list = explode(';', $user_info['tel_fax']);
    } else {
        $phone_list = explode(';', $user_info['telephone']);
    }

    for ($i=0;$i < count($phone_list);$i++) {
        $output_tbody .= '<div class="tbl_products_contact_phone" phone-user="'.$row->user_id.'">
        <b>'.preg_replace("/^(.\d{2})(\d{3})(\d{3})(\d{2})(\d{1,2})/", '$1 ($2)', $phone_list[$i]).'</b>
        <span>'.preg_replace("/^(.\d{2})(\d{3})(\d{3})(\d{2})(\d{1,2})/", '$1 ($2) $3-$4-$5', $phone_list[$i]).'</span>
        <a onclick="showUserContacts(this, '.$row->user_id.', ';

        if (($user_id == 0) && ($GLOBALS['tzs_au_contact_view_all'] == false)) { $output_tbody .= 'true'; } else { $output_tbody .= 'false'; }

        $output_tbody .= ');">Показать</a>
        </div>';
    }

    $output_tbody .= '</div>
            </td>
        </tr>';
    
    return $output_tbody;
}

/*
 * Вывод одной строки таблицы в виде html
 */
function tzs_tr_sh_table_record_out($row) {
    $user_info = tzs_get_user_meta($row->user_id);

    $type = trans_types_to_str($row->trans_type, $row->tr_type);
    
    $output_tbody = '<tr rid="'.$row->id.'">';

    $output_tbody .= '
            <td>
                <div class="record_number">
                    <span class="middle" title="Номер заявки">
                           № '.$row->id.'
                    </span>
                </div>
                <div>
                    <span class="time_label" title="Время добавления">
                        '.convert_date($row->time).'
                    </span>
                </div>
            </td>
            <td>
                <div>'.tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->tr_city_from);?><br/><?php echo tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, $row->tr_city_to);
    
    if ($row->distance > 0) {
        $output_tbody .= '<br/>'.tzs_make_distance_link($row->distance, false, array($row->tr_city_from, $row->tr_city_to));
    }

    $output_tbody .= '
                </div>
            </td>
            <td>
                <div>
                    '.convert_date($row->tr_date_from).'<br/>
                    <span class="expired_label" title="Дата окончания публикации">
                        '.convert_date($row->tr_date_to).'
                    </span>
                </div>
            </td>
            <td>
                <div>';
    
    if ($row->tr_weight > 0) {
        $output_tbody .= remove_decimal_part($row->tr_weight).' т<br>';
    }

    if ($row->tr_volume > 0) {
        $output_tbody .= remove_decimal_part($row->tr_volume).' м³';
    }

    $output_tbody .= '        </div>
            </td>
            <td>
                <div>';
    
    if (isset($GLOBALS['tzs_sh_types'][$row->sh_type])) {
        $output_tbody .= $GLOBALS['tzs_tr_types'][$row->sh_type];
    }
                    
    $output_tbody .= '        </div>
            </td>
            <td>
                <div>'.$type.'
                </div>
            </td>
            <td>
                <div>
                </div>
            </td>
            <td>
                <div>
                </div>
            </td>
            <td>
                <div>
                </div>
            </td>
            <td>
                <div>
                </div>
            </td>
        </tr>';
/*    
				?>
				<tr rid="<?php echo $row->id;?>">
				<td><?php echo $row->id;?></td>
				<td><b><?php echo convert_date_no_year($row->time); ?></b><br/><?php echo convert_time_only($row->time);?></td>
				<td><?php echo convert_date_no_year($row->tr_date_from);?><br/><?php echo convert_date_no_year($row->tr_date_to);?></td>
				<td>
					<?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->tr_city_from);?><br/><?php echo tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, $row->tr_city_to);?>
					<?php if ($row->distance > 0) {?>
						<br/>
						<?php echo tzs_make_distance_link($row->distance, false, array($row->tr_city_from, $row->tr_city_to)); ?>
					<?php } ?>
				</td>
				
				<?php if ($row->tr_weight > 0) {?>
					<td><?php echo remove_decimal_part($row->tr_weight);?> т</td>
				<?php } else {?>
					<td>&nbsp;</td>
				<?php }?>
				
				<?php if ($row->tr_volume > 0) {?>
					<td><?php echo remove_decimal_part($row->tr_volume);?> м³</td>
				<?php } else {?>
					<td>&nbsp;</td>
				<?php }?>
				
				<td><?php echo $type;?></td>
				<td><?php echo tzs_cost_to_str($row->cost);?></td>
				<td><?php echo htmlspecialchars($row->comment);?></td>
*/
    
    return $output_tbody;
}

/*
 * Выборка данных на основании фильтра и формирование строк таблицы с данными
 */
function tzs_front_end_tables_reload() {
    // Возвращаемые переменные
    $output_info = '';
    $output_error = '';
    $output_tbody = '';
    $output_pnav = '';
    $lastrecid = 0;
    
    $form_type = get_param_def('form_type', '');
    $type_id = get_param_def('type_id', '0');
    $rootcategory = get_param_def('rootcategory', '0');
    $cur_type_id = get_param_def('cur_type_id', '0');
    $cur_post_name = get_param_def('cur_post_name', '');
    $p_title = get_param_def('p_title', '');
    $page = get_param_def('page', '1');
    $records_per_page = get_param_def('records_per_page', ''.TZS_RECORDS_PER_PAGE);
    

    //$p_id = get_the_ID();
    //$p_title = the_title('', '', false);
    
    // Если указан параметр rootcategory, то выводим все товары раздела
    // Иначе - товары категории
    if ($rootcategory === '1') {
        $sql1 = ' AND type_id IN ('.tzs_build_product_types_id_str($cur_type_id).')';
        $p_name = '';
    } else {
        //$sql1 = ' AND type_id='.$type_id;
        $sql1 = '';
        $p_name = get_post_field( 'post_name', $type_id );
    }
    
    if ($form_type === 'products') {
        $sp = tzs_validate_pr_search_parameters();
    } else {
        $sp = tzs_validate_search_parameters();
    }
    
    $errors = $sp['errors'];

    switch ($form_type) {
        case 'products': {
            $table_name = TZS_PRODUCTS_TABLE;
            $table_error_msg = 'товаров';
            break;
        }

        case 'trucks': {
            $table_name = TZS_TRUCK_TABLE;
            $table_error_msg = 'транспорта';
            break;
        }

        case 'shipments': {
            $table_name = TZS_SHIPMENT_TABLE;
            $table_error_msg = 'грузов';
            break;
        }        
        
        default: {
            array_push($errors, "Неверно указан тип формы");
        }
    }
    
    if (count($errors) > 0) {
        $output_error = print_errors($errors);
    }
        
    
    if (count($errors) == 0) {
        if ($form_type === 'products') {
            $s_sql = tzs_search_pr_parameters_to_sql($sp, '');
            $s_title = tzs_search_pr_parameters_to_str($sp);
        } else {
            $s_sql = tzs_search_parameters_to_sql($sp, 'tr');
            $s_title = tzs_search_parameters_to_str($sp);
        }
	
	$output_info = $p_title;
        if (strlen($s_title) > 0) {
            $output_info .= ' * '. $s_title;
        }
        
        //$page = current_page_number();

        global $wpdb;

        //$url = current_page_url();

        $pp = floatval($records_per_page);

        $sql = "SELECT COUNT(*) as cnt FROM ".$table_name." WHERE active=1 $sql1 $s_sql;";
        $res = $wpdb->get_row($sql);
        if (count($res) == 0 && $wpdb->last_error != null) {
            $output_error .= '<div>Не удалось отобразить список '.$table_error_msg.'. Свяжитесь, пожалуйста, с администрацией сайта.</div>';
        } else {
            $records = $res->cnt;
            $pages = ceil($records / $pp);
            if ($pages == 0)
                    $pages = 1;
            if ($page > $pages)
                    $page = $pages;

            $from = ($page-1) * $pp;
            $sql = "SELECT * FROM ".$table_name." WHERE active=1 $sql1 $s_sql ORDER BY created DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                $output_error .= '<div>Не удалось отобразить список '.$table_error_msg.'. Свяжитесь, пожалуйста, с администрацией сайта.</div>';
            } else {
                if (count($res) == 0) {
                    $output_error .= '<div>По Вашему запросу ничего не найдено.</div>';
                } else {
                    foreach ( $res as $row ) {
                        if ($form_type === 'products') {
                            $output_tbody .= tzs_products_table_record_out($row);
                        } else {
                            $output_tbody .= tzs_tr_sh_table_record_out($row);
                        }
                        
                        $lastrecid = $row->id;
                    }
                }

                // Пагинация
                if ($pages > 1) {
                    if ($page > 1) {
                        $page0 = $page - 1;
                        $output_pnav .= '<a tag="page" page="'.$page0.'" href="javascript:TblTbodyReload('.$page0.')">« Предыдущая</a>&nbsp;';
                    }
                    
                    $start = 1;
                    $stop = $pages;
                    
                    for ($i = $start; $i <= $stop; $i++) {
                        if ($i == $page) {
                            $output_pnav .= '&nbsp;&nbsp;<span>'.$i.'</span>&nbsp;';
                        } else {
                            $output_pnav .= '&nbsp;&nbsp;<a tag="page" page="'.$i.'" href="javascript:TblTbodyReload('.$i.')">'.$i.'</a>&nbsp;';
                        }
                    }
                    
                    if ($page < $pages) {
                        $page1 = $page + 1;
                        $output_pnav .= '&nbsp;&nbsp;<a tag="page" page="'.$page1.'" href="javascript:TblTbodyReload('.$page1.')">Следующая »</a>';
                    }
                }
            }
        }
    }

    $output = array(
        'output_info' => $output_info, 
        'output_error' => $output_error, 
        'output_tbody' => $output_tbody,
        'output_pnav' => $output_pnav,
        'output_tbody_cnt' => count($res),
        'lastrecid' => $lastrecid,
        'type_id' => $type_id,
        'rootcategory' => $rootcategory,
        'sql' => $sql,
        'sql1' => $sql1,
        's_sql' => $s_sql,
    );

    //echo json_encode($output);
    return $output;
}
?>