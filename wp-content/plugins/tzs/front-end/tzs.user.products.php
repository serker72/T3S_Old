<?php

function tzs_front_end_user_products_handler($atts) {
    // Определяем атрибуты 
    // [tzs-view-user-products user_id="1"] - указываем на странице раздела
    // [tzs-view-products] - указываем на страницах подразделов
    extract( shortcode_atts( array(
            'user_id' => '0',
    ), $atts, 'tzs-view-user-products' ) );
        
    ob_start();

    $sql1 = ' AND user_id='.$user_id;
        global $wpdb;
        $page = current_page_number();
        $url = current_page_url();
         $pp = TZS_RECORDS_PER_PAGE; 
         $sql = "SELECT COUNT(*) as cnt FROM ".TZS_PRODUCTS_TABLE." WHERE active=1 $sql1 ";
        $res = $wpdb->get_row($sql);
        if (count($res) == 0 && $wpdb->last_error != null) {
            print_error('Не удалось отобразить список товаров. Свяжитесь, пожалуйста, с администрацией сайта -count');
        } else {
            $records = $res->cnt;
            $pages = ceil($records / $pp);
            if ($pages == 0)
                    $pages = 1;
            if ($page > $pages)
                    $page = $pages;
            $from = ($page-1) * $pp;
            $sql = "SELECT * FROM ".TZS_PRODUCTS_TABLE." WHERE active=1 $sql1 ORDER BY created DESC LIMIT $from,$pp; ";
            $res = $wpdb->get_results($sql);
            if (count($res) == 0 && $wpdb->last_error != null) {
                print_error('Не удалось отобразить список товаров. Свяжитесь, пожалуйста, с администрацией сайта - record');
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
                            <th id="tbl_products_img">Фото</th>
                            <th id="tbl_products_dtc">Дата размещения</th>
                            <th id="title">Описание товара</th>
                            <th id="price">Стоимость товара</th>
                            <th id="descr">Форма оплаты</th>
                            <th id="cities">Город</th>
                            <th id="comm">Комментарии</th>
                        </tr>
                        <?php
                        foreach ( $res as $row ) {
                            ?>
                            <tr rid="<?php echo $row->id;?>">
                                <td><?php echo $row->id;?></td>
                                <td>
                                    <?php
                                    if (strlen($row->image_id_lists) > 0) {
                                        //$img_names = explode(';', $row->pictures);
                                        $main_image_id = $row->main_image_id;
                                        // Вначале выведем главное изображение
                                        $attachment_info = wp_get_attachment_image_src($main_image_id, 'thumbnail');
                                        if ($attachment_info !== false) {
                                        //if (file_exists(ABSPATH . $img_names[0])) {
                                            //echo '<img src="'.get_site_url().'/'.$img_names[0].'" alt="">';
                                            echo '<img src="'.$attachment_info[0].'" alt="">';
                                            // width="50px" height="50px"
                                        } else {
                                            echo '&nbsp;';
                                        }
                                    } else {
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                                <td><?php echo convert_date($row->created); ?></td>
                                <td><?php echo htmlspecialchars($row->title);?></td>
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
                ?>

                function showSearchDialog() {
                        doSearchDialog('products', post, null);
                        //doSearchDialog('auctions', post, null);
                }

                jQuery(document).ready(function(){
                        jQuery('#tbl_products').on('click', 'td', function(e) {  
                                var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                                var id = this.parentNode.getAttribute("rid");
                                if (!nonclickable)
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