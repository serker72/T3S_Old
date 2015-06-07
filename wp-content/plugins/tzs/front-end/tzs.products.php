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
    $product_auction = get_param_def('product_auction', 'products');
    $pa_root_id = ($product_auction === 'auctions') ? ''.TZS_AU_ROOT_CATEGORY_PAGE_ID : ''.TZS_PR_ROOT_CATEGORY_PAGE_ID;
                    ?>
                    <div id="slick-1">
<!------------------------------------------------------------------------->                        
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
<!------------------------------------------------------------------------->                        
                    </div>
                    <div>
                        <table  id="tbl_products">
                            <thead>
                                <tr>
                                    <th id="tbl_products_id">Номер<br/>время заявки</th>
                                    <th id="tbl_products_sale">Покупка<br/>Продажа</th>
                                    <th id="tbl_products_cost">Участник тендера</th>
                                    <th id="tbl_products_dtc">Период публикации</th>
                                    <th id="tbl_products_title">Описание товара</th>

                                    <!--th id="tbl_products_img">Фото</th-->
                                    <th id="tbl_products_price">Цена<br/>Форма оплаты<br/>Кол-во</th>
                                    <th id="tbl_products_cities">Место нахождения</th>
                                    <th id="tbl_products_comm">Контакты</th>
                                </tr>
                                <!--tr id="tbl_products_filter">
                                    <form name="search_pr_form_new" method="POST">
                                        <th>
                                            <input type="radio" tag="cargo_trans_cargo" name="cargo_trans" value="cargo" <?php if (isset($_POST['cargo_trans']) && $_POST['cargo_trans'] == "cargo") echo 'checked="checked"'; ?> >№
                                            <input type="radio" tag="cargo_trans_cargo" name="cargo_trans" value="cargo" <?php if (isset($_POST['cargo_trans']) && $_POST['cargo_trans'] == "cargo") echo 'checked="checked"'; ?> >Вр<br>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="checkbox" name="cityname_from_check" value="" <?php if (isset($_POST['cityname_from_check'])) echo 'checked="checked"'; ?>/>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                        <th>
                                            <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>">
                                        </th>
                                    </form>
                                </tr-->
                            </thead>
                            <tbody>
                        <?php
                        foreach ( $res as $row ) {
                            $user_info = tzs_get_user_meta($row->user_id);
                            ?>
                            <tr rid="<?php echo $row->id;?>" id="<?php echo $row->sale_or_purchase == 1 ? 'tbl_auctions_tr_lot_1' : 'tbl_auctions_tr_lot_0'; ?>">
                                <td>
                                    <div class="record_number">
                                        <span class="middle" title="Номер заявки">
                                               № <?php echo $row->id;?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="time_label" title="Время добавления">
                                            <?php echo convert_time_only($row->created);?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span title="Тип заявки">
                                            <strong><?php echo $row->sale_or_purchase == 1 ? 'Продажа' : 'Покупка'; ?></strong>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                    <?php
                                    if ($row->fixed_or_tender == 1) {
                                        echo 'Цена зафиксирована<br/>';?>
                                    <a class="btnBlue" title="Купить товар по фиксированной цене">Купить</a>
                                    <?php } else {
                                        echo 'Тендерное предложение<br/>';?>
                                        <a class="btnBlue" title="Предложить свою цену за товар">Предложить свою цену</a>
                                    <?php }
                                ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo convert_date($row->created); ?><br/>
                                        <span class="expired_label" title="Дата окончания публикации">
                                            <?php echo convert_date($row->expiration); ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                <!--/td>
                                
                                <td>
                                    <div-->
                                    <div class="ienlarger">
                                    <?php
                                    if (strlen($row->image_id_lists) > 0) {
                                        $main_image_id = $row->main_image_id;
                                        // Вначале выведем главное изображение
                                        $attachment_info = wp_get_attachment_image_src($main_image_id, 'full');
                                        if ($attachment_info !== false) { ?>
                                                <a href="#nogo">
                                                    <img src="<?php echo $attachment_info[0]; ?>" alt="thumb" class="resize_thumb">
                                                    <span>
                                                        <?php echo trim($row->title); ?><br/>
                                                        <img src="<?php echo $attachment_info[0]; ?>" alt="large"/>
                                                    </span>
                                                </a>
                                        <?php }
                                    }
                                    ?>
                                    </div>
                                    <div class="title_text">
                                        <span title="Краткое описание товара">
                                            <?php echo trim($row->title);?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td>
                                    <div>
                                        <span class="price_label" title="Цена товара">
                                            <?php echo "<strong>".$row->price."</strong> ".$GLOBALS['tzs_pr_curr'][$row->currency];?>
                                        </span>
                                        <br>
                                        <span class="payment_label" title="Форма оплаты">
                                            <?php echo $GLOBALS['tzs_pr_payment'][$row->payment];?><br/>
                                            <?php echo $GLOBALS['tzs_pr_nds'][$row->nds];?>
                                        </span>
                                        <br>
                                        <span class="copies_label" title="Количество товара">
                                            <?php echo "<strong>".$row->copies."</strong> ".$GLOBALS['tzs_pr_unit'][$row->unit]; ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo tzs_city_to_str($row->from_cid, $row->from_rid, $row->from_sid, $row->city_from);?>
                                    </div>
                                </td>
                                <td>
                                    <div class="tbl_products_contact" title="Контактные данные <?php echo $row->sale_or_purchase == 1 ? 'продавца' : 'покупателя'; ?>">
                                        <a href=""><?php echo $user_info['company'] != '' ? $user_info['company'] : $user_info['fio'];?></a>
                                        <span><?php $meta=explode(',', $user_info['adress']); echo $meta[0];?></span>
                                        <?php 
                                        //echo htmlspecialchars($row->comment);
                                        if (($user_id == 0) && ($GLOBALS['tzs_au_contact_view_all'] == false)) {?>
                                        <div class="tzs_au_contact_view_all" phone-user-not-view="<?php echo $row->user_id;?>">Для просмотра контактов необходимо <a href="/account/login/">войти</a> или <a href="/account/registration/">зарегистрироваться</a></div>
                                        <?php }
                                        
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
                                            <a onclick="showUserContacts(this, <?php echo $row->user_id;?>, <?php echo (($user_id == 0) && ($GLOBALS['tzs_au_contact_view_all'] == false)) ? 'true' : 'false'; ?>);">Показать</a>
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
                            </tbody>
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

                function showUserContacts(obj, user_id, is_hide) {
                    var container = jQuery('div[phone-user="'+user_id+'"]');
                    var container1 = jQuery('div[phone-user-not-view="'+user_id+'"]');
                    
                    if (is_hide) {
                        container.hide();
                        container1.show();
                    } else {
                        container.find('a, b').hide();
                        container.find('span').show();
                    }
                }

                function showSearchDialog() {
                        doSearchDialog('products', post, null);
                        //doSearchDialog('auctions', post, null);
                }

                jQuery(document).ready(function(){
                        jQuery('#tbl_products').on('click', 'td', function(e) {  
                                var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                                var id = this.parentNode.getAttribute("rid");
                                if (!nonclickable && (this.cellIndex != 7))
                                        document.location = "/account/view-product/?id="+id;
                        });
                        hijackLinks(post);
                        
                        ///
	jQuery('#slick-1').dcSlick({
			location: 'left',
			align: 'top',
			offset: '200px',
			speed: 'slow',
			tabText: 'Поиск',
			autoClose: false
	});
                        
                });
        </script>
	<?php
////
    
    $output = ob_get_contents();
    
    ob_end_clean();
	
    return $output;
}
?>