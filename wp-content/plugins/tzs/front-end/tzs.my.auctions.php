<?php

add_action( 'wp_ajax_tzs_delete_auction', 'tzs_delete_auction_callback' );

function tzs_delete_auction_callback() {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval( $_POST['id'] ) : 0;
    $is_delete = isset($_POST['is_delete']) && is_numeric($_POST['is_delete']) ? intval( $_POST['is_delete'] ) : 0;
    $user_id = get_current_user_id();
    $errors = array();
    
    if ($id <= 0) {
        echo "Номер не найден";
    } else if ($user_id == 0) {
        echo "Вход в систему обязателен";
    } else {
        global $wpdb;

        // Добавить проверку наличия ставок - если есть ставки, то удаление невозможно
        $sql = "SELECT COUNT(*) as cnt FROM ".TZS_AUCTION_RATES_TABLE." WHERE auction_id = $id";
        $row = $wpdb->get_row($sql);
        if (count($row) === 0 && $wpdb->last_error != null) {
            echo 'Не удалось отобразить информацию о ставках по тендеру. Свяжитесь, пожалуйста, с администрацией сайта.';
            echo $wpdb->last_error;
        } else if ($row === null) {
            echo 'Ставки по тендеру не найдены.';
        } else if ($row->cnt > 0) {
            echo 'Обнаружены ставки по тендеру, удаление записи невозможно.';
        } else {
            if ((strlen($row->image_id_lists) > 0) && ($is_delete === 1)) {
                $img_names = explode(';', $row->image_id_lists);
                
                for ($i=0;$i < count($img_names);$i++) {
                    if( false === wp_delete_attachment($img_names[$i], true) ) {
                        echo "Не удалось удалить файл с изображением: ".$img_names[$i]->get_error_message();
                        array_push($errors, "Не удалось удалить файл с изображением: ".$img_names[$i]->get_error_message());
                    }
                }
            }
            
            if (count($errors) === 0) {
                if ($is_delete === 1) {
                    $sql = "DELETE FROM ".TZS_AUCTIONS_TABLE." WHERE id=$id AND user_id=$user_id;";
                } else {
                    $sql = "UPDATE ".TZS_AUCTIONS_TABLE." SET active=0 WHERE id=$id AND user_id=$user_id;";
                }


                if (false === $wpdb->query($sql)) {
                    if ($is_delete === 1) {
                        echo "Не удалось удалить Ваш тендер. Свяжитесь, пожалуйста, с администрацией сайта ";
                    } else {
                        echo "Не удалось перенести в архив Ваш тендер. Свяжитесь, пожалуйста, с администрацией сайта ";
                    }
                } else {
                    echo "1";
                }
            }
        }
    }
    die();
}

function tzs_front_end_my_auctions_handler($atts) {
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
        $sql = "SELECT COUNT(*) as cnt FROM ".TZS_AUCTIONS_TABLE." WHERE user_id=$user_id AND active=$active;";
        $res = $wpdb->get_row($sql);
        if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список тендеров. Свяжитесь, пожалуйста, с администрацией сайта');
        } else {
            $records = $res->cnt;
            $pages = ceil($records / $pp);
            if ($pages == 0)
                    $pages = 1;
            if ($page > $pages)
                    $page = $pages;

            $from = ($page-1) * $pp;
            $sql = "SELECT a.*,(SELECT COUNT(*) FROM ".TZS_AUCTION_RATES_TABLE." c WHERE c.auction_id = a.id) AS rate_count FROM ".TZS_AUCTIONS_TABLE." a  WHERE a.user_id=$user_id AND a.active=$active ORDER BY a.created DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                    print_error('Не удалось отобразить список тендеров. Свяжитесь, пожалуйста, с администрацией сайта');
            } else {
                ?>
                <div id="my_auctions_wrapper">
                    <div id="my_products_button">
                        <?php if ($active === '1') { ?>
                            <button id="view_del" onClick="javascript: window.open('/account/my-auction/?active=0', '_self');">Показать архивные</button>
                        <?php } else { ?>
                            <button id="view_edit" onClick="javascript: window.open('/account/my-auction/?active=1', '_self');">Показать действующие</button>
                        <?php } ?>
                        <button id="view_add" onClick="javascript: window.open('/account/add-auction/', '_self');">Добавить тендер</button>
                    </div>

                    <div id="my_auctions_table">
                        <h3>Список <?php echo ($active === '1') ? 'действующих' : 'архивных'; ?> тендеров</h3>
                        <table id="tbl_products">
                        <tr>
                            <th id="tbl_products_id">Номер</th>
                            <th id="tbl_auctions_lot">Тип<br>Кол-во ставок</th>
                            <th id="tbl_products_img">Фото</th>
                            <th id="tbl_products_dtc">Дата размещения<br>Дата окончания</th>
                            <th id="title">Описание</th>
                            <th id="copies">Кол-во</th>
                            <th id="price">Цена за единицу</th>
                            <th id="descr">Форма оплаты</th>
                            <th id="cities">Город</th>
                            <th id="comm">Комментарии</th>
                            <th id="actions" nonclickable="true">Действия</th>
                        </tr>
                        <?php
                        foreach ( $res as $row ) {
                            ?>
                            <tr rid="<?php echo $row->id;?>" id="<?php echo $row->is_lot == 1 ? 'tbl_auctions_tr_lot_1' : 'tbl_auctions_tr_lot_0'; ?>">
                                <td><?php echo $row->id;?></td>
                                <td><?php echo $row->is_lot == 1 ? 'Продам' : 'Куплю';?><br><br><?php echo 'Ставок-'.$row->rate_count; ?></td>
                                <td>
                                    <?php
                                    if (strlen($row->image_id_lists) > 0) {
                                        $main_image_id = $row->main_image_id;
                                        // Вначале выведем главное изображение
                                        //$attachment_info = wp_get_attachment_image_src($main_image_id, 'thumbnail');
                                        $attachment_info = wp_get_attachment_image_src($main_image_id, 'full');
                                        if ($attachment_info !== false) { ?>
                                            <div class="ienlarger">
                                                <a href="#nogo">
                                                    <img src="<?php echo $attachment_info[0]; ?>" alt="thumb" class="resize_thumb">
                                                    <span>
                                                        <?php echo htmlspecialchars($row->title); ?><br/>
                                                        <img src="<?php echo $attachment_info[0]; ?>" alt="large"/>
                                                    </span>
                                                </a>
                                            </div>
                                        <?php } else {
                                            echo '&nbsp;';
                                        }
                                    } else {
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                                <td><?php echo convert_date($row->created); ?><br><?php echo convert_date($row->expiration); ?></td>
                                <td><?php echo htmlspecialchars($row->title);?></td>
                                <td><?php echo $row->copies." ".$GLOBALS['tzs_au_unit'][$row->unit];?></td>
                                <td><?php echo $row->price." ".$GLOBALS['tzs_pr_curr'][$row->currency];?></td>
                                <td><?php echo $GLOBALS['tzs_pr_payment'][$row->payment];?></td>
                                <td><?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from);?></td>
                                <td><?php echo htmlspecialchars($row->comment);?></td>
                                <td>
                                        <a href="javascript:doDisplay(<?php echo $row->id;?>);" at="<?php echo $row->id;?>" id="icon_set">Действия</a>
                                        <div id="menu_set" id2="menu" for="<?php echo $row->id;?>" style="display:none;">
                                                <ul>
                                                        <a href="/account/view-auction/?id=<?php echo $row->id;?>">Смотреть</a>
                                                        <a href="/account/edit-auction/?id=<?php echo $row->id;?>">Изменить</a>
                                                        <a href="javascript: promptDelete(<?php echo $row->id.', '.$row->active;?>);" id="red">Удалить</a>
                                                </ul>
                                        </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </table>
                    </div>
                </div>

            <script>
                jQuery(document).ready(function(){
                        jQuery('table').on('click', 'td', function(e) {  
                                var nonclickable = 'true' === e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                                var id = this.parentNode.getAttribute("rid");
                                if (!nonclickable)
                                        document.location = "/account/view-auction/?id="+id;
                        });
                        
                          //align element in the middle of the screen
                        jQuery.fn.alignCenter = function() {
                           //get margin left
                           //var marginLeft = Math.max(40, parseInt(jQuery(window).width()/2 - jQuery(this).width()/2)) + 'px';
                           var marginLeft = - jQuery(this).width()/2 + 'px';
                           //get margin top
                           //var marginTop = Math.max(40, parseInt(jQuery(window).height()/2 - jQuery(this).height()/2)) + 'px';
                           var marginTop = - jQuery(this).height()/2 + 'px';
                           //return updated element
                           //return jQuery(this).css({'margin-left':marginLeft*(-1), 'margin-top':marginTop*(-1)});
                           return jQuery(this).css({'margin-left':'120px', 'margin-top':marginTop});
                        };
                });

                function doDisplay(id) {
                        var el = jQuery('div[for='+id+']');
                        if (el.attr('style') === null) {
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
                        var s_text = '<div><h2>Удалить запись '+id+' или перенести в архив ?</h2><hr/><p>Запись из архива можно в любой момент снова опубликовать.</p><p>При удалении записи будут так же удалены все прикрепленные изображения.</p></div>';
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
                        var s_text = '<div><h2>Удалить запись '+id+' из архива ?</h2><hr/><p>Запись из архива можно в любой момент снова опубликовать.</p><p>При удалении записи будут так же удалены все прикрепленные изображения.</p></div>';
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
                                'action': 'tzs_delete_auction',
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