<?php

add_action( 'wp_ajax_tzs_delete_shipment', 'tzs_delete_shipment_callback' );

function tzs_delete_shipment_callback() {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval( $_POST['id'] ) : 0;
    $is_delete = isset($_POST['is_delete']) && is_numeric($_POST['is_delete']) ? intval( $_POST['is_delete'] ) : 0;
    $user_id = get_current_user_id();
    
    if ($id <= 0) {
        echo "Груз не найден";
    } else if ($user_id == 0) {
        echo "Вход в систему обязателен";
    } else {
        global $wpdb;

        $sql = "SELECT * FROM ".TZS_SHIPMENT_TABLE." WHERE id=$id AND user_id=$user_id;";
        $row = $wpdb->get_row($sql);
        if (count($row) === 0 && $wpdb->last_error != null) {
            echo 'Не удалось получить список грузов. Свяжитесь, пожалуйста, с администрацией сайта';
            echo $wpdb->last_error;
        } else if ($row === null) {
            echo "Груз не найден (id=$id AND user_id=$user_id)";
        } else {
            if ($is_delete === 1) {
                $sql = "DELETE FROM ".TZS_SHIPMENT_TABLE." WHERE id=$id AND user_id=$user_id;";
            } else {
                $sql = "UPDATE ".TZS_SHIPMENT_TABLE." SET active=0 WHERE id=$id AND user_id=$user_id;";
            }

            if (false === $wpdb->query($sql)) {
                if ($is_delete === 1) {
                    echo "Не удалось удалить Ваш груз. Свяжитесь, пожалуйста, с администрацией сайта ";
                } else {
                    echo "Не удалось перенести в архив Ваш груз. Свяжитесь, пожалуйста, с администрацией сайта ";
                }
            } else {
                    echo "1";
            }
        }
    }
    
    die();
}

function tzs_front_end_my_shipments_handler($atts) {
    ob_start();

    global $wpdb;

    $user_id = get_current_user_id();
    $url = current_page_url();
    $page = current_page_number();
    $pp = TZS_RECORDS_PER_PAGE;
    $active = isset($_GET['active']) ? trim($_GET['active']) : '1';
	
    if ($user_id == 0) {
        ?>
        <div>Для просмотра необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></div>
        <?php
    } else {
        $sql = "SELECT COUNT(*) as cnt FROM ".TZS_SHIPMENT_TABLE." WHERE user_id=$user_id AND active=$active;";
        $res = $wpdb->get_row($sql);
        if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список грузов. Свяжитесь, пожалуйста, с администрацией сайта');
        } else {
            $records = $res->cnt;
            $pages = ceil($records / $pp);
            if ($pages == 0)
                    $pages = 1;
            if ($page > $pages)
                    $page = $pages;

            $from = ($page-1) * $pp;
            $sql = "SELECT * FROM ".TZS_SHIPMENT_TABLE."  WHERE user_id=$user_id AND active=$active ORDER BY time DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список транспорта. Свяжитесь, пожалуйста, с администрацией сайта');
            } else {
                ?>
                <script src="/wp-content/plugins/tzs/assets/js/distance.js"></script>
                <div id="my_products_wrapper">
                    <div id="my_products_table">
                        <table id="tbl_products">
                        <thead>
                            <tr id="tbl_thead_records_per_page">
                                <th colspan="4" id="thead_h1">
                                    <div class="div_td_left">
                                        <h3>Список <?php echo ($active === '1') ? 'публикуемых' : 'архивных'; ?> грузов</h3>
                                    </div>
                                </th>
                                
                                <th colspan="6">
                                    <div id="my_products_button">
                                        <?php if ($active === '1') { ?>
                                            <button id="view_del" onClick="javascript: window.open('/account/my-shipments/?active=0', '_self');">Показать архивные</button>
                                        <?php } else { ?>
                                            <button id="view_edit" onClick="javascript: window.open('/account/my-shipments/?active=1', '_self');">Показать публикуемые</button>
                                        <?php } ?>
                                        <button id="view_add" onClick="javascript: window.open('/account/add-shipment/', '_self');">Добавить груз</button>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th id="tbl_trucks_id">№, дата и время заявки</th>
                                <th id="tbl_trucks_path" nonclickable="true">Пункты погрузки /<br>выгрузки</th>
                                <th id="tbl_trucks_dtc">Дата погрузки /<br>выгрузки</th>
                                <th id="tbl_trucks_ttr">Тип груза</th>
                                <th id="tbl_trucks_wv">Вес,<br>объем</th>
                                <th id="tbl_trucks_comm">Описание груза</th>
                                <th id="tbl_trucks_cost">Стоимость,<br/>цена 1 км</th>
                                <th id="tbl_trucks_payment">Форма оплаты</th>
                                <th id="comm">Комментарии</th>
                                <th id="actions" nonclickable="true">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ( $res as $row ) {
                                $type = trans_types_to_str($row->trans_type, $row->tr_type);
                                $cost = tzs_cost_to_str($row->cost, true);

                                ?>
                                <tr rid="<?php echo $row->id;?>">
                                <td>
                                    <?php echo $row->id;?><br>
                                    <?php echo convert_date_year2($row->time); ?><br/>
                                    <?php echo convert_time_only($row->time);?>
                                </td>
                                <td>
                                        <?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->sh_city_from);?><br/><?php echo tzs_city_to_str($row->to_cid, $row->to_rid, $row->to_sid, $row->sh_city_to); ?>
                                        <?php if ($row->distance > 0) {?>
                                                <br/>
                                                <?php echo tzs_make_distance_link($row->distance, false, array($row->sh_city_from, $row->sh_city_to)); ?>
                                        <?php } ?>
                                </td>
                                <td><?php echo convert_date($row->sh_date_from);?><br/><?php echo convert_date($row->sh_date_to);?></td>

                                <td><?php echo $GLOBALS['tzs_sh_types'][$row->sh_type];?></td>
                                
                                <td>
                                <?php 
                                    if ($row->sh_weight > 0) {
                                        echo remove_decimal_part($row->sh_weight).' т<br>';
                                    }

                                    if ($row->sh_volume > 0) {
                                        echo remove_decimal_part($row->sh_volume).' м³';
                                    }
                                ?>
                                </td>

                                <td><?php echo htmlspecialchars($row->sh_descr);?></td>
                                <td>
                                    <?php if ($row->price > 0) {
                                        echo $row->price.' '.$GLOBALS['tzs_curr'][$row->price_val].'<br><br>';
                                        echo round($row->price / $row->distance, 2).' '.$GLOBALS['tzs_curr'][$row->price_val].'/км'; 
                                    } ?>
                                </td>
                                <td><?php echo $cost[1]; ?></td>
                                <td><?php echo htmlspecialchars($row->comment);?></td>
                                <td>
                                        <a href="javascript:doDisplay(<?php echo $row->id;?>);" at="<?php echo $row->id;?>" id="icon_set">Действия</a>
                                        <div id="menu_set" id2="menu" for="<?php echo $row->id;?>" style="display:none;">
                                                <ul>
                                                        <a href="/account/view-shipment/?id=<?php echo $row->id;?>">Смотреть</a>
                                                        <a href="/account/edit-shipment/?id=<?php echo $row->id;?>">Изменить</a>
                                                        <a href="javascript: promptDelete(<?php echo $row->id.', '.$row->active; ?>);" id="red">Удалить</a>
                                                </ul>
                                        </div>
                                </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        </table>

    <script src="/wp-content/plugins/tzs/assets/js/jquery.stickytableheaders.min.js"></script>
                    <script>
                    jQuery(document).ready(function(){
                            jQuery('table').on('click', 'td', function(e) {  
                                    var nonclickable = 'true' == e.delegateTarget.rows[1].cells[this.cellIndex].getAttribute('nonclickable');
                                    var id = this.parentNode.getAttribute("rid");
                                    if (!nonclickable)
                                            document.location = "/account/view-shipment/?id="+id;
                            });
                        
                            jQuery("#tbl_products").stickyTableHeaders();
                    });

                    function doDisplay(id) {
                            var el = jQuery('div[for='+id+']');
                            if (el.attr('style') == null) {
                                    el.attr('style', 'display:none;');
                                    jQuery('a[at='+id+']').attr('id', 'icon_set');
                            } else {
                                    el.removeAttr('style');
                                    jQuery('a[at='+id+']').attr('id', 'icon_set_cur');
                            }
                            jQuery("div[id2=menu]").each(function(i) {
                                    var id2 = this.getAttribute('for');
                                    if (id2 != ''+id) {
                                            this.setAttribute('style', 'display:none;');
                                            jQuery('a[at='+id2+']').attr('id', 'icon_set');
                                    }
                            });
                    }

                    function promptDelete(id, active) {
                    if (active === 1) {
                        var s_text = '<div><h2>Удалить запись '+id+' или перенести в архив ?</h2><hr/><p>Запись из архива можно в любой момент снова опубликовать.</p></div>';
                        buttons1 = new Object({
                                                'В архив': function () {
                                                        jQuery(this).dialog("close");
                                                        doDelete(id, 0);
                                                },
                                                'Удалить': function () {
                                                        jQuery(this).dialog("close");
                                                        doDelete(id, 1);
                                                },
                                                'Отменить': function () {
                                                        jQuery(this).dialog("close");
                                                }
                                            });
                    } else {
                        var s_text = '<div><h2>Удалить запись '+id+' из архива ?</h2><hr/><p>Запись из архива можно в любой момент снова опубликовать.</p></div>';
                        buttons1 = new Object({
                                                'Удалить': function () {
                                                        jQuery(this).dialog("close");
                                                        doDelete(id, 1);
                                                },
                                                'Отменить': function () {
                                                        jQuery(this).dialog("close");
                                                }
                                            });
                    }
                            jQuery('<div></div>').appendTo('body')
                                    .html(s_text)
                                    .dialog({
                                            modal: true,
                                            title: 'Удаление',
                                            zIndex: 10000,
                                            autoOpen: true,
                                            width: 'auto',
                                            resizable: false,
                                            buttons: buttons1,
                                            close: function (event, ui) {
                                                    jQuery(this).remove();
                                            }
                                    });
                    }

                    function doDelete(id, is_delete) {
                            var data = {
                                    'action': 'tzs_delete_shipment',
                                    'id': id,
                                'is_delete': is_delete
                            };

                            jQuery.post(ajax_url, data, function(response) {
                                    if (response == '1') {
                                            location.reload();
                                    } else {
                                            alert('Не удалось удалить: '+response);
                                    }
                            });
                    }
                    </script>
                    <?php
                    build_pages_footer($page, $pages);
            }
        }
    }

    $output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}

?>