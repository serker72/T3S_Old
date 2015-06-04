<?php

function tzs_front_end_auctions_handler($atts) {
    // Определяем атрибуты 
    // [tzs-view-auctions rootcategory="1"] - указываем на странице раздела
    // [tzs-view-auctions] - указываем на страницах подразделов
    extract( shortcode_atts( array(
            'rootcategory' => '0',
    ), $atts, 'tzs-view-auctions' ) );
        
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

        $sql = "SELECT COUNT(*) as cnt FROM ".TZS_AUCTIONS_TABLE." WHERE active=1 $sql1 $s_sql;";
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
            $sql = "SELECT a.*,(SELECT COUNT(*) FROM ".TZS_AUCTION_RATES_TABLE." c WHERE c.auction_id = a.id) AS rate_count FROM ".TZS_AUCTIONS_TABLE." a  WHERE a.active=1 $sql1 $s_sql ORDER BY a.created DESC LIMIT $from,$pp;";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список тендеров. Свяжитесь, пожалуйста, с администрацией сайта.');
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
                        <table id="tbl_products">
                        <tr>
                            <th id="tbl_products_id">Номер</th>
                            <th id="tbl_auctions_lot">Тип<br>Кол-во ставок</th>
                            <th id="tbl_products_img">Фото</th>
                            <th id="tbl_products_dtc">Дата размещения<br>Дата окончания</th>
                            <th id="tbl_auctions_title">Описание</th>
                            <th id="tbl_auctions_copies">Кол-во</th>
                            <th id="tbl_products_price">Цена за единицу</th>
                            <th id="tbl_products_payment">Форма оплаты</th>
                            <th id="tbl_products_cities">Город</th>
                            <th id="tbl_products_comm">Комментарии</th>
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

                function showSearchDialog() {
                        doSearchDialog('auctions', post, null);
                }

                jQuery(document).ready(function(){
                        jQuery('#tbl_products').on('click', 'td', function(e) {  
                                var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                                var id = this.parentNode.getAttribute("rid");
                                if (!nonclickable)
                                        document.location = "/account/view-auction/?id="+id;
                        });
                        hijackLinks(post);
                });
        </script>
	<?php
    
    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}
?>