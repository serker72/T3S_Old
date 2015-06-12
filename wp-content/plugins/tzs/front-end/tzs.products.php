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
        //$sql1 = ' AND type_id IN ('.tzs_build_product_types_id_str($p_id).')';
        $p_name = '';
    } else {
        //$sql1 = ' AND type_id='.$p_id;
        $p_name = get_post_field( 'post_name', $p_id );
    }

    $page = current_page_number();

	?>
    <!--div>
        <table id="pr_info" width="100%">
            <tr>
                <td width="65px"></td>
                <td>
                    <div id="search_info"><?php 
                    //echo $p_title;
                    //echo strlen($s_title) > 0 ?  ' * '. $s_title : '';
                    ?></div>
                </td>
                <td>
                    <div id="errors" class="errors" style="float: left;">
                    </div>
                </td>
                <td>
                    <div>
                        <button tag="page" id="realod_btn" onClick="javascript:TblTbodyReload()">Обновить</button>
                    </div>
                </td>
            </tr>
        </table>
    </div-->
<!------------------------------------------------------------------------->                        
    <div>
        <table  id="tbl_products">
            <thead>
    <form class="search_pr_form" name="search_pr_form1" method="POST">
                <tr>
                    <th id="tbl_products_id">Номер<br/>время заявки</th>
                    <th id="tbl_products_sale">
                        <div>
                            Покупка<br/>Продажа
                        </div>
                    </th>
                    <th id="tbl_products_cost">Участник тендера</th>
                    <th id="tbl_products_dtc">Период публикации</th>
                    <th id="tbl_products_title">Описание и фото товара</th>
                    <th id="tbl_products_price">Цена<br/>Форма оплаты<br/>Кол-во</th>
                    <th id="tbl_products_cities">Место нахождения</th>
                    <th id="tbl_products_comm">Контакты</th>
                </tr>
                <tr>
                    <th>
                        <div id="tbl_thead_search_button_1" class="tbl_thead_search_button" title="Фильтр по категории">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_1', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_1" class="tbl_thead_search_div">
                                Категория:<br>
                              <select name="type_id" <?php echo ($rootcategory === '1') ? '' : ' disabled="disabled"'; ?> >
                                <option value="0">все категории</option>
                                <option disabled>- - - - - - - -</option>
                                <?php
                                    tzs_build_product_types('type_id', TZS_PR_ROOT_CATEGORY_PAGE_ID);
                                ?>
                            </select>
                            <?php //wp_nonce_field( 'type_id', 'type_id_nonce' ); ?>
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_2" class="tbl_thead_search_button" title="Фильтр по типу заявок">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_2', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_2" class="tbl_thead_search_div">
                                Тип заявки:<br>
                                <select name="sale_or_purchase">
                                    <option value="0" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 0) echo 'selected="selected"'; ?> >Все</option>
                                    <option value="1" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 1) echo 'selected="selected"'; ?> >Продажа</option>
                                    <option value="2" <?php if (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] == 2) echo 'selected="selected"'; ?> >Покупка</option>
                                </select><br>
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_3" class="tbl_thead_search_button" title="Фильтр по участнику тендера">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_3', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_3" class="tbl_thead_search_div">
                                Участник тендера:<br>
                                <select name="fixed_or_tender">
                                    <option value="0" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 0) echo 'selected="selected"'; ?> >Все предложения</option>
                                    <option value="1" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 1) echo 'selected="selected"'; ?> >Цена зафиксирована</option>
                                    <option value="2" <?php if (isset($_POST['fixed_or_tender']) && $_POST['fixed_or_tender'] == 2) echo 'selected="selected"'; ?> >Тендерное предложение</option>
                                </select>
                            </div>
                    <th>
                        <div id="tbl_thead_search_button_4" class="tbl_thead_search_button" title="Фильтр по периоду публикации">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_4', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_4" class="tbl_thead_search_div">
                                Период публикации: от:<br>
                                <input type="text" name="data_from" value="<?php echo_val('data_from'); ?>" size="10"><br>
                                Период публикации: до:<br>
                                <input type="text" name="data_to" value="<?php echo_val('data_to'); ?>" size="10">
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_5" class="tbl_thead_search_button" title="Фильтр по описанию товара">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_5', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_5" class="tbl_thead_search_div">
                                Описание:<br>
                                <input type="text" name="pr_title" value="<?php echo_val('pr_title'); ?>" size="30">
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_6" class="tbl_thead_search_button" title="Фильтр по стоимости товара">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_6', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_6" class="tbl_thead_search_div">
                                Стоимость: от:<br>
                                <input type="text" name="price_from" value="<?php echo_val('price_from'); ?>" size="10"><br>
                                Стоимость: до:<br>
                                <input type="text" name="price_to" value="<?php echo_val('price_to'); ?>" size="10"><br>
                                Форма оплаты:<br>
                                <select name="payment">
                                    <option value="0" <?php if (isset($_POST['payment']) && $_POST['payment'] == 0) echo 'selected="selected"'; ?> >Любая</option>
                                    <option value="1" <?php if (isset($_POST['payment']) && $_POST['payment'] == 1) echo 'selected="selected"'; ?> >Наличная</option>
                                    <option value="2" <?php if (isset($_POST['payment']) && $_POST['payment'] == 2) echo 'selected="selected"'; ?> >Безналичная</option>
                                </select><br>
                                НДС:<br>
                                <select name="nds">
                                    <option value="0" <?php if (isset($_POST['nds']) && $_POST['nds'] == 0) echo 'selected="selected"'; ?> >Все</option>
                                    <option value="1" <?php if (isset($_POST['nds']) && $_POST['nds'] == 1) echo 'selected="selected"'; ?> >Без НДС</option>
                                    <option value="2" <?php if (isset($_POST['nds']) && $_POST['nds'] == 2) echo 'selected="selected"'; ?> >Включая НДС</option>
                                </select>
                            </div>
                    </th>
                    <th>
                        <div id="tbl_thead_search_button_7" class="tbl_thead_search_button" title="Фильтр по местонахождению товара">
                                <img chk="1" src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_<?php echo (isset($_POST['sale_or_purchase']) && $_POST['sale_or_purchase'] > 0) ? 'checked' : 'unchecked'; ?>.png" width="16px" height="16px">
                                <a href="JavaScript:tblTHeadShowForm('#tbl_thead_search_div_7', '.tbl_thead_search_div');"><img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/navigate-down.png" width="16px" height="16px"></a>
                        </div>
                            <div id="tbl_thead_search_div_7" class="tbl_thead_search_div">
                                Местонахождение: страна:<br>
                                <select name="country_from">
                                    <?php
                                        tzs_pr_build_countries('country_from');
                                    ?>
                                </select><br>
                                Местонахождение: регион:<br>
                                <select name="region_from">
                                    <option value="0">все области</option>
                                </select><br>
                                Местонахождение: город:<br>
                                <input type="text" name="cityname_from" value="<?php echo_val('cityname_from'); ?>" size="30">
                            </div>
                    </th>
                    <th>
                        <div class="tbl_thead_search_button">
                            <button type="button" id="tbl_thead_button_clear" onclick="javascript:onTblTheadButtonClearClick();" title="Очистить все условия фильтра">Очистить</button>
                            <button type="button" id="tbl_thead_button_search" onclick="javascript:onTblTheadButtonSearchClick();" title="Выполнить поиск по текущим условиям фильтра">Искать</button>
                        </div>
                    </th>
                </tr>
    </form>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="slick-1">
        <?php tzs_front_end_search_pr_form(); ?>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="preloader">
        <img src="<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/ajax-loader.gif" alt="Loading..."/>
    </div>
<!------------------------------------------------------------------------->                        
    <div id="pages_container">

    </div>
<!------------------------------------------------------------------------->                        
                <?php
//                }

                //build_pages_footer($page, $pages);
//            }
//        }
//    }
////
    //echo '<div>'.tzs_front_end_TblTbodyReload($p_id, $rootcategory).'</div>';
/////
    ?>
    <script>
        function doAjax(id, rid, to_el) {
            jQuery(to_el).attr("disabled", "disabled");
            jQuery(to_el).html('<option value=\"0\">Загрузка</option>');

            var data = {
                    'action': 'tzs_regions_reload',
                    'id': id,
                    'rid': rid
            };

            jQuery.post(ajax_url, data, function(response) {
                    jQuery(to_el).html(response);
                    jQuery(to_el).removeAttr("disabled");
                    enableDisable(to_el);
            }).fail(function(response) {
                    jQuery(to_el).html("<option value='0'>все области(!)</option>");
                    jQuery(to_el).removeAttr("disabled");
                    enableDisable(to_el);
            });
        }

        function enableDisable(obj) {
                if (jQuery(obj).children().length <= 1) {
                        jQuery(obj).attr("disabled", "disabled");
                } else {
                        jQuery(obj).removeAttr("disabled");
                }
        }

        function onCountryFromSelected() {
            var rid = <?php echo isset($_POST["region_from"]) ? $_POST["region_from"] : 0; ?>;
            doAjax(jQuery('[name=country_from]').val(), rid, jQuery('[name=region_from]'));

            if ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=cityname_from]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onCityNameFromSelected() {
            if ((jQuery('[name=country_from]').val() > 0) || (jQuery('[name=region_from]').val() > 0) || (jQuery('[name=cityname_from]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_7 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onTypeIdSelected() {
            if (jQuery('[name=type_id]').val() > 0) {
                jQuery("#tbl_thead_search_button_1 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_1 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onSaleOrPurchaseSelected() {
            if (jQuery('[name=sale_or_purchase]').val() > 0) {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_2 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }

        function onFixedOrTenderSelected() {
            if (jQuery('[name=fixed_or_tender]').val() > 0) {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_3 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }        

        function onDataFromSelected() {
            if ((jQuery('[name=data_from]').val().length > 7) || (jQuery('[name=data_to]').val().length > 7)) {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_4 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }                

        function onPrTitleSelected() {
            if (jQuery('[name=pr_title]').val().length > 0) {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_5 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }                

        function onPriceFromSelected() {
            if ((jQuery('[name=payment]').val() > 0) || (jQuery('[name=nds]').val() > 0) || (jQuery('[name=price_from]').val().length > 0) || (jQuery('[name=price_to]').val().length > 0)) {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_checked.png");
            } else {
                jQuery("#tbl_thead_search_button_6 img[chk=1]").attr("src", "<?php echo get_site_url(); ?>/wp-content/plugins/tzs/assets/images/checkbox_unchecked.png");
            }
        }
        
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
        
        //
        function onForm1Change() {
            onCountryFromSelected();
            onTypeIdSelected();
            onSaleOrPurchaseSelected();
            onFixedOrTenderSelected();
            onDataFromSelected();
            onPrTitleSelected();
            onPriceFromSelected();
        }
        
        function FormToFormCopy(form_to, form_from) {
            if ((form_to !== undefined) && (form_to !== '') && (form_from !== undefined) && (form_from !== '')) {
                var elem = document.getElementsByName(form_to)[0].elements;
                for (i=0;i < elem.length;i++) {
                    if ((elem[i].type == 'text') || (elem[i].type == 'select-one')) {
                        console.log(form_to + " before: " + elem[i].tagName + " " + elem[i].type + " " + elem[i].name + " " + elem[i].value);
                        var elem1 = document.getElementsByName(form_from)[0].elements.namedItem(elem[i].name);
                        console.log(form_from + " before: " + elem1.tagName + " " + elem1.type + " " + elem1.name + " " + elem1.value);
                        
                        switch (elem[i].type) {
                            case 'select-one': {
                                if (elem[i].value > 0) { elem1.value = elem[i].value; }
                                else if (elem1.value > 0) { elem[i].value = elem1.value; }
                                break;
                            }
                            
                            case 'text': {
                                if (elem[i].value.length > 0) { elem1.value = elem[i].value; }
                                else if (elem1.value.length > 0) { elem[i].value = elem1.value;}
                                break;
                            }
                        }
                        
                        console.log(form_to + " after: " + elem[i].tagName + " " + elem[i].type + " " + elem[i].name + " " + elem[i].value);
                        console.log(form_from + " after: " + elem1.tagName + " " + elem1.type + " " + elem1.name + " " + elem1.value);
                    }
                }
                
                onForm1Change();
            }
        }
        
        function FormClear(form_name) {
            if ((form_name !== undefined) && (form_name !== '')) {
                var elem = document.getElementsByName(form_name)[0].elements;
                document.getElementsByName(form_name)[0].reset();
                onForm1Change();
                return false;
            }
        }
        
        function onTblTheadButtonSearchClick() {
            tblTHeadShowForm('', '.tbl_thead_search_div');
            FormToFormCopy("search_pr_form1", "search_pr_form");
            TblTbodyReload(false, <?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            // Для исключения повторного обновления страницы - return false
            return false;
        }
        
        function onTblTheadButtonClearClick() {
            FormClear("search_pr_form1");
            FormClear("search_pr_form");
            TblTbodyReload(false, <?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            // Для исключения повторного обновления страницы - return false
            return false;
        }
        
        function onTblSearchButtonClick() {
            tblTHeadShowForm('', '.tbl_thead_search_div');
            FormToFormCopy("search_pr_form1", "search_pr_form");
            TblTbodyReload(true, <?php echo isset($_POST['page']) ? $_POST['page'] : '1';?>);
            // Для исключения повторного обновления страницы - return false
            return false;
        }
        
        function TblTbodyReload(is_close_slick, page) {
            if (is_close_slick === true) { jQuery('.tab').click(); }
            
            if (page !== undefined) { addHidden("[name=search_pr_form]", 'page', page); }

            // Очистим
            jQuery("#errors").html('');
            jQuery("#search_info").html('');
            jQuery("#tbl_products tbody").html('');
            jQuery("#pages_container").html('');

            jQuery('#preloader').fadeIn('fast');
            //jQuery('#preloader').show();
            //jQuery('#tbl_products_search_status th').html('Подождите...Выполняется операция поиска записей...');

            fd = jQuery('form[name="search_pr_form"]').serialize();
            jQuery.ajax({
                url: "/wp-admin/admin-ajax.php?action=tzs_tables_reload",
                type: "POST",
                data: fd,
                dataType: 'json',
                success: function(data) {
                    if ((data.output_tbody !== 'undefined') && (data.output_tbody !== '')) {
                        jQuery("#tbl_products tbody").html(data.output_tbody);
                    }

                    if ((data.output_info !== 'undefined') && (data.output_info !== '')) {
                        //jQuery("#search_info").html(data.output_info);
                    }

                    if ((data.output_error !== 'undefined') && (data.output_error !== '')) {
                        //jQuery("#errors").html(data.output_error);
                        //jQuery("#errors").css('display', 'block');
                        var td_count = tbl_products.rows[1].cells.length;
                        var o_err = '<tr><td colspan="' + td_count + '"><div class="tbl_tbody_errors">' + data.output_error + '</div></td><\tr>';
                        jQuery("#tbl_products tbody").html(o_err);
                    }

                    if ((data.output_pnav !== 'undefined') && (data.output_pnav !== '')) {
                        jQuery("#pages_container").html(data.output_pnav);
                    }

                    jQuery('#preloader').fadeOut('fast');
                },
                error: function(data) {
                    if (data.responsetext !== 'undefined') {
                        jQuery("#errors").html(data.responsetext);
                    }

                    jQuery('#preloader').fadeOut('fast');
                }			
            });		   
        }

        // Create a hidden input element, and append it to the form:
        function addHidden(theForm, key, value) {
            if (jQuery(theForm).find('input[type=hidden]').is('[name='+key+']') == false) {
                var input = jQuery('<input type="hidden"/>');
                jQuery(input).attr('name', key);
                jQuery(input).attr('value', value);
                jQuery(theForm).append(input);                        
            }
            else {
                jQuery(theForm+' [name='+key+']').attr('value', value);
            }
            /*var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;//'name-as-seen-at-the-server';
            input.value = value;

            theForm.appendChild(input);*/
        }

        function tblTHeadShowForm(div_id, div_class) {
            if ((div_id !== undefined) && (div_id !== '') && (jQuery(div_id).is(':visible'))) {
                jQuery(div_class).css('display', 'none');
            } else {
                jQuery(div_class).css('display', 'none');
                if ((div_id !== undefined) && (div_id !== '')) { jQuery(div_id).css('display', 'block'); }
            }
        }

        jQuery(document).ready(function(){
                jQuery('#tbl_products').on('click', 'td', function(e) {  
                        var nonclickable = 'true' == e.delegateTarget.rows[0].cells[this.cellIndex].getAttribute('nonclickable');
                        var id = this.parentNode.getAttribute("rid");
                        if (!nonclickable && (this.cellIndex != 7))
                                document.location = "/account/view-product/?id="+id;
                });

                //
                //var theForm = document.forms['search_pr_form'];
                //var theForm = "[name=search_pr_form]";
                var theForm = "#search_pr_form";
                //addHidden(theForm, 'type_id', '<?php echo $p_id; ?>');
                addHidden(theForm, 'rootcategory', '<?php echo $rootcategory; ?>');
                addHidden(theForm, 'cur_type_id', '<?php echo $p_id; ?>');
                addHidden(theForm, 'cur_post_name', '<?php echo $p_name; ?>');
                addHidden(theForm, 'p_title', '<?php echo $p_title; ?>');
                jQuery('<tr><td>&nbsp;</td><td><button type="button" id="pr_search_button" onclick="javascript:onTblSearchButtonClick();">Поиск</button></td></tr>').appendTo('.search_pr_form table');

                //
                jQuery('[name=country_from]').change(function() { onCountryFromSelected(); });
                //var elem = document.getElementsByName('search_pr_form')[0].elements.namedItem('country_from');
                //elem.onchange(function() { onCountryFromSelected(); });
                jQuery('[name=cityname_from]').change(function() { onCityNameFromSelected(); });
                jQuery('[name=type_id]').change(function() { onTypeIdSelected(); });
                jQuery('[name=sale_or_purchase]').change(function() { onSaleOrPurchaseSelected(); });
                jQuery('[name=fixed_or_tender]').change(function() { onFixedOrTenderSelected(); });
                jQuery('[name=data_from]').change(function() { onDataFromSelected(); });
                jQuery('[name=data_to]').change(function() { onDataFromSelected(); });
                jQuery('[name=pr_title]').change(function() { onPrTitleSelected(); });
                jQuery('[name=price_from]').change(function() { onPriceFromSelected(); });
                jQuery('[name=price_to]').change(function() { onPriceFromSelected(); });
                jQuery('[name=payment]').change(function() { onPriceFromSelected(); });
                jQuery('[name=nds]').change(function() { onPriceFromSelected(); });
                //
                onForm1Change();
                //
                jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ru']);
                jQuery("[name=data_from]").datepicker({ dateFormat: "dd.mm.yy" });
                jQuery("[name=data_to]").datepicker({ dateFormat: "dd.mm.yy" });


                TblTbodyReload(false, 1);

                //hijackLinks(post);

                ///
                jQuery('#slick-1').dcSlick({
                    location: 'left',
                    align: 'top',
                    offset: '120px',
                    speed: 'slow',
                    tabText: '',
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