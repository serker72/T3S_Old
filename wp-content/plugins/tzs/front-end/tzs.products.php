<?php

function tzs_front_end_products_handler($atts) {
    // Определяем атрибуты 
    // [tzs-view-products rootcategory="1"] - указываем на странице раздела
    // [tzs-view-products] - указываем на страницах подразделов
    extract( shortcode_atts( array(
            'rootcategory' => '0',
    ), $atts, 'tzs-view-products' ) );
        
    ob_start();

    $p_id = get_the_ID();
    $p_title = the_title('', '', false);
    
    // Если указан параметр rootcategory, то выводим все товары раздела
    // Иначе - товары категории
    if ($rootcategory === '1') {
        $sql1 = ' AND type_id IN ('.tzs_build_product_types_id_str($p_id).')';
        $p_name = '';
    } else {
        $sql1 = ' AND type_id='.$p_id;
        $p_name = get_post_field( 'post_name', $p_id );
    }
    
    $sp = tzs_validate_pr_search_parameters();
    $errors = $sp['errors'];
	
    if (count($errors) > 0)
        print_errors($errors);
    
    ?>
	<a href="javascript:showSearchDialog();" id="edit_search">Изменить параметры поиска</a>
    <?php
    
    if (count($errors) == 0) {
	$s_sql = tzs_search_pr_parameters_to_sql($sp, '');
	$s_title = tzs_search_pr_parameters_to_str($sp);
	
	?>
            <div id="search_info"><?php 
            echo $p_title;
            echo strlen($s_title) > 0 ?  ' * '. $s_title : '';
            ?></div>
	<?php

        $page = current_page_number();

	?>
	<a tag="page" id="realod_btn" href="<?php echo build_page_url($page); ?>">Обновить</a>
	<?php
        
        global $wpdb;

        $url = current_page_url();

        $pp = TZS_RECORDS_PER_PAGE;

        $sql = "SELECT COUNT(*) as cnt FROM ".TZS_PRODUCTS_TABLE." WHERE active=1 $sql1 $s_sql;";
        $res = $wpdb->get_row($sql);
        if (count($res) == 0 && $wpdb->last_error != null) {
            print_error('Не удалось отобразить список товаров. Свяжитесь, пожалуйста, с администрацией сайта');
        } else {
            $records = $res->cnt;
            $pages = ceil($records / $pp);
            if ($pages == 0)
                    $pages = 1;
            if ($page > $pages)
                    $page = $pages;

            $from = ($page-1) * $pp;
            $sql = "SELECT * FROM ".TZS_PRODUCTS_TABLE." WHERE active=1 $sql1 $s_sql ORDER BY created DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список товаров. Свяжитесь, пожалуйста, с администрацией сайта');
            } else {
                if (count($res) == 0) {
                    ?>
                    <div style="clear: both;"></div>
                    <div class="errors">
                        <div id="info error">По Вашему запросу ничего не найдено.</div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div>
                        <table  class="tbl_products">
                        <tr>
                            <th class="tbl_products_id">Номер<br/>время заявки</th>
                            <th class="tbl_auctions_lot">Покупка<br/>Продажа</th>
                            <th class="tbl_products_">Участник тендера</th>
                            <th class="tbl_products_dtc">Период публикации</th>
                            <th class="tbl_products_title">Описание товара</th>
                            
                            <th class="tbl_products_img">Фото</th>
                            <th class="tbl_products_price">Цена<br/>Форма оплаты</th>
                            <th class="tbl_products_cities">Место нахождения</th>
                            <th class="tbl_products_comm">Контакты</th>
                        </tr>
                        <?php
                        foreach ( $res as $row ) {
                            $user_info = tzs_get_user_meta($row->user_id);
                            ?>
                            <tr rid="<?php echo $row->id;?>" id="<?php echo $row->sale_or_purchase == 1 ? 'tbl_auctions_tr_lot_1' : 'tbl_auctions_tr_lot_0'; ?>">
                                <td>
                                    <div class="record_number">
                                        <span class="middle">
                                               № <?php echo $row->id;?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="time_label" title="Время добавления">
                                            <?php echo convert_time_only($row->created);?>
                                        </span>
                                    </div>
                                </td>
                                <td><?php echo $row->sale_or_purchase == 1 ? 'Продажа' : 'Покупка'; ?></td>
                                <td><?php
                                    if ($row->fixed_or_tender == 1) {
                                        echo 'Цена зафиксирована<br/>';?>
                                    <a class="btnBlue" title="Купить товар">Купить</a>
                                    <?php } else {
                                        echo 'Тендерное предложение<br/>';?>
                                        <a class="btnBlue">Предложить свою цену</a>
                                    <?php }
                                ?></td>
                                <td class="tbl_products_dtc"><?php echo convert_date($row->created); ?><br/><?php echo convert_date($row->expiration); ?></td>
                                <td class="tbl_products_title">
                                    <div>
                                        <?php echo trim($row->title);?>
                                    </div>
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
                                                        <?php echo trim($row->title); ?><br/>
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
                                
                                <td><?php echo $row->price." ".$GLOBALS['tzs_pr_curr'][$row->currency];?><br/>
                                <?php echo $GLOBALS['tzs_pr_payment'][$row->payment];?><br/>
                                <?php echo $GLOBALS['tzs_pr_nds'][$row->nds];?>
                                </td>
                                <td><?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from);?></td>
                                <td>
                                    <div class="tbl_products_contact">
                                        <a href=""><?php echo $user_info['company'] != '' ? $user_info['company'] : $user_info['fio'];?></a>
                                        <span><?php echo explode(',', $user_info['adress'])[0];?></span>
                                        <?php 
                                        //echo htmlspecialchars($row->comment);
                                        if ($user_info['company'] != '') {
                                            $phone_list = explode(';', $user_info['tel_fax']);
                                        } else {
                                            $phone_list = explode(';', $user_info['telephone']);
                                        }
                                        
                                        for ($i=0;$i < count($phone_list);$i++) {
                                            ?>
                                        <div class="tbl_products_contact_phone" phone-user="<?php echo $row->user_id;?>">
                                            <b><?php echo preg_replace("/^(.\d{2})(\d{3})(\d{3})(\d{2})(\d{1,2})/", '$1 ($2)', $phone_list[$i]); ?></b>
                                            <span><?php echo preg_replace("/^(.\d{2})(\d{3})(\d{3})(\d{2})(\d{1,2})/", '$1 ($2) $3-$4-$5', $phone_list[$i]); ?></span>
                                            <a onclick="showUserContacts(this, <?php echo $row->user_id;?>);">Показать</a>
                                        </div>
                                            <?php
                                        }
                                        ?>
                                        
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </table>
                    </div>
                <?php
                }

                build_pages_footer($page, $pages);
            }
        }
    }
////
	?>
        <script src="/wp-content/plugins/tzs/assets/js/search.js"></script>
        <script>
                var post = [];
                <?php
                    echo "// POST dump here\n";
                    foreach ($_POST as $key => $value) {
                        echo "post[".tzs_encode2($key)."] = ".tzs_encode2($value).";\n";
                    }
                    if (!isset($_POST['type_id'])) {
                        echo "post[".tzs_encode2("type_id")."] = ".tzs_encode2($p_id).";\n";
                    }
                    if (!isset($_POST['cur_type_id'])) {
                        echo "post[".tzs_encode2("cur_type_id")."] = ".tzs_encode2($p_id).";\n";
                    }
                    if (!isset($_POST['cur_post_name']) && ($p_name !== '')) {
                        echo "post[".tzs_encode2("cur_post_name")."] = ".tzs_encode2($p_name).";\n";
                    }
                ?>

                function showUserContacts(obj, user_id) {
                    var container = jQuery('div[phone-user="'+user_id+'"]');
                    container.find('a, b').hide();
                    container.find('span').show();
                }

                function showSearchDialog() {
                        doSearchDialog('products', post, null);
                        //doSearchDialog('auctions', post, null);
                }

                jQuery(document).ready(function(){
                        jQuery('#tbl_products').on('click', 'td', function(e) {  
                                var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                                var id = this.parentNode.getAttribute("rid");
                                if (!nonclickable && (this.cellIndex != 8))
                                        document.location = "/account/view-product/?id="+id;
                        });
                        hijackLinks(post);
                });
        </script>
	<?php
////
    
    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}
?>