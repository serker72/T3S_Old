<?php

/*
 * Вывод одной строки таблицы в виде html
 */
function tzs_products_table_record_out($row, $form_type) {
//    $user_info = tzs_get_user_meta($row->user_id);

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
                    '.tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from, 'Местонахождение товара').'
                </div>
            </td>
            <td>';
                
    $output_tbody .= tzs_print_user_contacts($row, $form_type, 1);
    $output_tbody .= '        </td>
        </tr>';
    
    return $output_tbody;
}

/*
 * Вывод одной строки таблицы в виде html
 */
function tzs_tr_sh_table_record_out($row, $form_type) {
//    $user_info = tzs_get_user_meta($row->user_id);

    if ($form_type === 'shipments') { $prefix = 'sh';}
    else { $prefix = 'tr'; }
    
    $type = trans_types_to_str($row->trans_type, $row->tr_type);
    
    $cost = tzs_cost_to_str($row->cost, true);
    
    $output_tbody = '<tr rid="'.$row->id.'">';

    $output_tbody .= '
            <td>
                <div class="record_number">
                    <span class="middle" title="Номер заявки">
                           № '.$row->id.'
                    </span>
                </div>
                <div>
                    <span class="time_label" title="Дата и время публикации заявки">
                        '.convert_date_year2($row->time).'<br>
                        '.convert_time_only($row->time).'
                    </span>
                </div>
            </td>
            <td>
                <div>'.tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, (($prefix === 'tr') ? $row->tr_city_from : $row->sh_city_from),'Пункт погрузки').'<br/>'.tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, (($prefix === 'tr') ? $row->tr_city_to : $row->sh_city_to), 'Пункт выгрузки');
    
    if (($row->distance > 0) && ($prefix === 'tr')) {
        $output_tbody .= '<br/>'.tzs_make_distance_link($row->distance, false, array($row->tr_city_from, $row->tr_city_to));
    }
    else if (($row->distance > 0) && ($prefix === 'sh')) {
        $output_tbody .= '<br/>'.tzs_make_distance_link($row->distance, false, array($row->sh_city_from, $row->sh_city_to));
    }

    $output_tbody .= '
                </div>
            </td>
            <td>
                <div><strong>
                    <span class="expired_label" title="Дата погрузки">
                    '.convert_date_year2(($prefix === 'tr') ? $row->tr_date_from : $row->sh_date_from).'<br/>
                    </span><br>
                    <span class="expired_label" title="Дата выгрузки">
                        '.convert_date_year2(($prefix === 'tr') ? $row->tr_date_to : $row->sh_date_to).'
                    </span></strong>
                </div>
            </td>';
    
    if ($prefix === 'sh') {
        $output_tbody .= '<td>
                <div title="Тип груза">'.(isset($GLOBALS['tzs_sh_types'][$row->sh_type]) ? $GLOBALS['tzs_sh_types'][$row->sh_type] : '').'</div>
            </td>';
        
        $output_tbody .= '<td><div>';
        if (($row->tr_weight > 0) || ($row->sh_weight > 0)) {
            $output_tbody .= '<span title="Вес груза">'.remove_decimal_part(($prefix === 'tr') ? $row->tr_weight : $row->sh_weight).' т</span><br>';
        }

        if (($row->tr_volume > 0) || ($row->sh_volume > 0)) {
            $output_tbody .= '<span title="Объем груза">'.remove_decimal_part(($prefix === 'tr') ? $row->tr_volume : $row->sh_volume).' м³</span>';
        }
        $output_tbody .= '</div></td>
            <td><div title="Описание груза">'.$row->sh_descr.'</div></td>';
    } else {
        $output_tbody .= '<td>
                <div title="Тип транспортного средства">'.$type.'</div>
            </td>
            <td><div title="Описание транспортного средства">';
        
        $tr_ds1 = '';
        $tr_ds2 = '';
        if ($row->tr_length > 0) {
            $tr_ds1 .= 'Д';
            $tr_ds2 .= intval($row->tr_length);
        }
        
        if ($row->tr_width > 0) {
            if ($tr_ds1 !== '') $tr_ds1 .= 'x';
            if ($tr_ds2 !== '') $tr_ds2 .= 'x';
            $tr_ds1 .= 'Ш';
            $tr_ds2 .= intval($row->tr_width);
        }
        
        if ($row->tr_height > 0) {
            if ($tr_ds1 !== '') $tr_ds1 .= 'x';
            if ($tr_ds2 !== '') $tr_ds2 .= 'x';
            $tr_ds1 .= 'В';
            $tr_ds2 .= intval($row->tr_height);
        }
            
        if (($tr_ds1 !== '') && ($tr_ds2 !== '')) 
            $output_tbody .= $tr_ds1.': '.$tr_ds2.' м<br>';
        
        if ($row->tr_weight > 0)
            $output_tbody .= remove_decimal_part($row->tr_weight).' т<br>';

        if($row->tr_volume > 0)
            $output_tbody .= remove_decimal_part($row->tr_volume).' м³<br>';
                                    
        if ($row->tr_descr && (strlen($row->tr_descr) > 0))
            $output_tbody .= $row->tr_descr.'<br>';
            
        $output_tbody .= '</div></td>
            <td><div title="Желаемый груз">'.$row->sh_descr.'</div></td>';
    }

                
    

    $output_tbody .= '<td><div title="Стоимость перевозки груза">';
    if ($row->price > 0) {
        $output_tbody .= $row->price.' '.$GLOBALS['tzs_curr'][$row->price_val].'<br><br>'.
                round($row->price / $row->distance, 2).' '.$GLOBALS['tzs_curr'][$row->price_val].'/км'; 
    } else {
        $output_tbody .= $cost[0];
    }

    $output_tbody .= '</div>
            </td>
            <td>
                <div title="Форма оплаты услуг по перевозке груза">'.$cost[1].'</div>
            </td>';
    
    if ($prefix === 'tr') {
        //$output_tbody .= '<td><div title="Комментарии">'.$row->comment.'</div></td>';
    }
    
    $output_tbody .= '<td>'.tzs_print_user_contacts($row, $form_type, 1).'</td>
        </tr>';
    
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
    if (($rootcategory === '1') && ($type_id === '0')) {
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
            $table_order_by = 'created';
            break;
        }

        case 'trucks': {
            $table_name = TZS_TRUCK_TABLE;
            $table_error_msg = 'транспорта';
            $table_order_by = 'time';
            $table_prefix = 'tr';
            break;
        }

        case 'shipments': {
            $table_name = TZS_SHIPMENT_TABLE;
            $table_error_msg = 'грузов';
            $table_order_by = 'time';
            $table_prefix = 'sh';
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
            $s_sql = tzs_search_parameters_to_sql($sp, $table_prefix);
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
            $sql = "SELECT * FROM ".$table_name." WHERE active=1 $sql1 $s_sql ORDER BY ".$table_order_by." DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                $output_error .= '<div>Не удалось отобразить список '.$table_error_msg.'. Свяжитесь, пожалуйста, с администрацией сайта.</div>';
            } else {
                if (count($res) == 0) {
                    $output_error .= '<div>По Вашему запросу ничего не найдено.</div>';
                } else {
                    foreach ( $res as $row ) {
                        if ($form_type === 'products') {
                            $output_tbody .= tzs_products_table_record_out($row, $form_type);
                        } else {
                            $output_tbody .= tzs_tr_sh_table_record_out($row, $form_type);
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