<?php

add_action( 'wp_ajax_tzs_delete_product', 'tzs_delete_product_callback' );

function tzs_delete_product_callback() {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? intval( $_POST['id'] ) : 0;
    $is_delete = isset($_POST['is_delete']) && is_numeric($_POST['is_delete']) ? intval( $_POST['is_delete'] ) : 0;
    $user_id = get_current_user_id();
    $errors = array();
    
    if ($id <= 0) {
        echo "Товар/услуга не найден";
    } else if ($user_id == 0) {
        echo "Вход в систему обязателен";
    } else {
        global $wpdb;

        // Вначале попытаемся удалить изображения
        $sql = "SELECT * FROM ".TZS_PRODUCTS_TABLE." WHERE id=$id AND user_id=$user_id;";
        $row = $wpdb->get_row($sql);
        if (count($row) === 0 && $wpdb->last_error != null) {
            echo 'Не удалось получить список товаров. Свяжитесь, пожалуйста, с администрацией сайта';
            echo $wpdb->last_error;
        } else if ($row === null) {
            echo "Товар/услуга не найден (id=$id AND user_id=$user_id)";
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
                    $sql = "DELETE FROM ".TZS_PRODUCTS_TABLE." WHERE id=$id AND user_id=$user_id;";
                } else {
                    $sql = "UPDATE ".TZS_PRODUCTS_TABLE." SET active=0 WHERE id=$id AND user_id=$user_id;";
                }

                if (false === $wpdb->query($sql)) {
                    if ($is_delete === 1) {
                        echo "Не удалось удалить Ваш товар/услугу. Свяжитесь, пожалуйста, с администрацией сайта ";
                    } else {
                        echo "Не удалось перенести в архив Ваш товар/услугу. Свяжитесь, пожалуйста, с администрацией сайта ";
                    }
                } else {
                        echo "1";
                }
            }
        }
    }
    die();
}

function tzs_front_end_my_products_handler($atts) {
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
        $sql = "SELECT COUNT(*) as cnt FROM ".TZS_PRODUCTS_TABLE." WHERE user_id=$user_id AND active=$active;";
        $res = $wpdb->get_row($sql);
        if (count($res) == 0 && $wpdb->last_error != null) {
            print_error('Не удалось отобразить список товаров/услуг. Свяжитесь, пожалуйста, с администрацией сайта');
        } else {
            $records = $res->cnt;
            $pages = ceil($records / $pp);
            if ($pages == 0)
                    $pages = 1;
            if ($page > $pages)
                    $page = $pages;

            $from = ($page-1) * $pp;
            $sql = "SELECT * FROM ".TZS_PRODUCTS_TABLE."  WHERE user_id=$user_id AND active=$active ORDER BY created DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список товаров/услуг. Свяжитесь, пожалуйста, с администрацией сайта');
            } else {
                ?>
                <div id="my_products_wrapper">
                    <div id="my_products_table">
                        <table id="tbl_products">
                        <thead>
                            <tr id="tbl_thead_records_per_page">
                                <th colspan="4" id="thead_h1">
                                    <div class="div_td_left">
                                        <h3>Список <?php echo ($active === '1') ? 'публикуемых' : 'архивных'; ?> товаров</h3>
                                    </div>
                                </th>
                                
                                <th colspan="6">
                                    <div id="my_products_button">
                                        <?php if ($active === '1') { ?>
                                            <button id="view_del" onClick="javascript: window.open('/account/my-products/?active=0', '_self');">Показать архивные</button>
                                        <?php } else { ?>
                                            <button id="view_edit" onClick="javascript: window.open('/account/my-products/?active=1', '_self');">Показать публикуемые</button>
                                        <?php } ?>
                                        <button id="view_add" onClick="javascript: window.open('/account/add-product/', '_self');">Добавить товар</button>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th id="tbl_products_id">Номер</th>
                                <th id="tbl_products_sale">Покупка<br/>Продажа</th>
                                <th id="tbl_products_img">Фото</th>
                                <th id="tbl_products_dtc">Период публикации</th>
                                <th id="title">Описание товара</th>
                                <th id="price">Стоимость товара</th>
                                <th id="descr">Форма оплаты</th>
                                <th id="cities">Город</th>
                                <th id="comm">Комментарии</th>
                                <th id="actions" nonclickable="true">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ( $res as $row ) {
                            ?>
                            <tr rid="<?php echo $row->id;?>">
                                <td><?php echo $row->id;?></td>
                                <td>
                                    <?php echo ($row->sale_or_purchase == 1) ? 'Продажа' : 'Покупка'; ?><br><br>
                                    <?php echo ($row->fixed_or_tender == 1) ? 'Цена зафиксирована' : 'Тендерное предложение'; ?>
                                </td>
                                <td>
                                    <?php
                                    if (strlen($row->image_id_lists) > 0) {
                                        $main_image_id = $row->main_image_id;
                                        // Вначале выведем главное изображение
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
                                <td><?php echo convert_date($row->created).'<br>'.convert_date($row->expiration); ?></td>
                                <td><?php echo htmlspecialchars($row->title);?></td>
                                <td><?php echo $row->price." ".$GLOBALS['tzs_pr_curr'][$row->currency];?></td>
                                <td><?php echo $GLOBALS['tzs_pr_payment'][$row->payment];?></td>
                                <td><?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from);?></td>
                                <td><?php echo htmlspecialchars($row->comment);?></td>
                                <td>
                                        <a href="javascript:doDisplay(<?php echo $row->id;?>);" at="<?php echo $row->id;?>" id="icon_set">Действия</a>
                                        <div id="menu_set" id2="menu" for="<?php echo $row->id;?>" style="display:none;">
                                                <ul>
                                                        <a href="/account/view-product/?id=<?php echo $row->id;?>">Смотреть</a>
                                                        <a href="/account/edit-product/?id=<?php echo $row->id;?>">Изменить</a>
                                                        <a href="javascript: promptDelete(<?php echo $row->id.', '.$row->active;?>);" id="red">Удалить</a>
                                                </ul>
                                        </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        </table>
                    </div>
                </div>

    <script src="/wp-content/plugins/tzs/assets/js/jquery.stickytableheaders.min.js"></script>
                <script>
                jQuery(document).ready(function(){
                        jQuery('table').on('click', 'td', function(e) {  
                                var nonclickable = 'true' == e.delegateTarget.rows[1].cells[this.cellIndex].getAttribute('nonclickable');
                                var id = this.parentNode.getAttribute("rid");
                                if (!nonclickable)
                                        document.location = "/account/view-product/?id="+id;
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
                                'action': 'tzs_delete_product',
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